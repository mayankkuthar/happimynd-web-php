<?php

namespace App\Exports;

use App\Models\Assessment;
use App\Models\Category;
use App\Models\Organization;
use App\Models\ReportCharacteristic;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class AssessmentDataExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $start;
    protected $end;
    protected $org_id;
    protected $b2c;
    public function __construct($data)
    {
        if (isset($data['start_date'])) {
            $this->start = $data['start_date'];
            $this->end = $data['end_date'];
        }
        if (isset($data['organization_id'])) {
            $this->org_id = $data['organization_id'];
        }

        if (isset($data['b2c'])) {
            $this->b2c = true;
        }
    }

    function findInCollection(Collection $collection, $key, $value)
    {
        foreach ($collection as $item) {
            if (isset($item->$key) && !(strcasecmp($item->$key, $value))) {
                return $item;
            }
        }
        return FALSE;
    }

    function findInCollectionWithInRange(Collection $collection, $score)
    {
        foreach ($collection as $item) {
            if ($item->minimum_score <= $score && $item->maximum_score >= $score) {
                return $item;
            }
        }
        return FALSE;
    }

    public function collection()
    {
        $assessments = Assessment::latest()->with(['user' => function ($query) {
            $query->with(['userToken' => function ($query) {
                $query->with(['token.organization' => function ($query) {
                    $query->withTrashed();
                }]);
            }])->with('profileType');
        }])->with('score')->with(['batch.batchCategory' => function ($query) {
            $query->with('category.reportCharacteristics');
        }]);
        if ($this->start != NULL && $this->end != NULL) {

            $start_date = Carbon::parse($this->start)->toDateTimeString();
            $end_date = Carbon::parse($this->end)->toDateTimeString();
            // $end_date = $this->end.' '.'23:59:59';
            
            if(empty($this->org_id)){
                $assessments = $assessments->whereBetween('started_at', [$start_date, $end_date])->get();
            }
            else{
                $assessments = $assessments->whereBetween('started_at', [$start_date, $end_date])->get();
            }
        }
        // if (!empty($this->org_id)) {
        //     $organization = Organization::find($this->org_id);
        //     if ($organization) {
        //         $users = $organization->getUsers()->toArray();
        //         if (count($users) > 0) {
        //         $user_ids = $organization->getUsers()->pluck('id');
        //             $assessments = $assessments->whereIn('user_id', $user_ids)->get();
        //         }
        //     }
        // } else if ($this->b2c) {
        //     $assessments = $assessments->whereHas('user', function ($query) {
        //         $query->doesntHave('userToken');
        //     })->get();
        // }

         else {
            $assessments = $assessments->whereHas('user')->get();
        }
        $d = [];
        $c = 1;
        $headings = $this->headings();
        $date = Carbon::parse("27-05-2021");
        $o_data = array_flip(array_map('strtolower', $headings));
        $categories = Category::whereColumn('name', 'acronymn')->get();
        foreach ($assessments as $assessment) {
            $convertPoints = false;
            if ($assessment->ended_at <= $date) {
                $convertPoints = true;
            }
            $data = $o_data;
            foreach ($data as $i => $value) {
                $data[$i] = "-";
            }
            $data['#'] = $c++;
            $data['username'] = $assessment->user->username;
            $data['nickname'] = $assessment->user->nickname;

            $data['email'] = $assessment->user->email;
            $data['mobile'] = $assessment->user->mobile;

            $data['code'] = '-';
            $data['organization'] = "-";
            if ($assessment->user->isOrganizationUser()) {
                $data['code'] = $assessment->user->userToken->token->token;
                $organization = $assessment->user->userToken->token->organization;
                $data['organization'] = $organization->name;
                if ($organization->isDeleted()) {
                    $data['organization'] .= ' (Deleted Organization)';
                }
            }
            $data['profile'] = $assessment->user->profileType->name;
            $data['assessment started at'] = $assessment->started_at;
            $data['assessment ended at'] = $assessment->ended_at;
            if ($assessment->score) {
                $data['questions attempted'] = $assessment->score->attempts;
                $finalArray = [];
                foreach ($assessment->score->scores as $score) {
                    $scores = [];
                    $category = array_keys($score)[0];
                    if ($category == "personality") {
                        $scores['personality_1'] = (string)array_keys($score[$category])[0];
                        $scores['personality_2'] = (string)array_keys($score[$category])[1];
                        $scores['personality_1 score'] = (string)$score[$category][array_keys($score[$category])[0]]['score'];
                        $scores['personality_2 score'] = (string)$score[$category][array_keys($score[$category])[1]]['score'];
                    } else {
                        $key = $category;
                        $category = str_replace('_', ' ', $category);
                        $scores[$category] = (string)$score[$key]['score'];
                        $level = (string)$score[$key]['level'];
                        if ($convertPoints)
                            $scores[$category . ' level'] = (string)$level * 10;
                        else {
                            $scores[$category . ' level'] = (string)$level;
                        }
                        $reportScale = $this->findInCollection($categories, 'name', $category);
                        if($reportScale!=false){
                            $reportScale = $this->findInCollectionWithInRange($reportScale->reportCharacteristics, $score[$key]['score']);
                            if ($reportScale == false) {
                                $reportScale = '-';
                            } else {
                                $reportScale = $reportScale->meter_scale_level_name;
                            }
                            $scores[$category . ' report'] = $reportScale;
                        }
                        
                    }
                    $finalArray = array_merge($finalArray, $scores);
                }
                $data = (array_merge($data, $finalArray));
            }
            $used_coupon = $assessment->user->getUsedCouponCodes();
            if(!empty($used_coupon)){
                $data['coupon used'] = $used_coupon;
            }
            array_push($d, $data);
        }


        return new Collection($d);
    }

    public function headings(): array
    {
        $cats = Category::distinct('name')->where('name', '!=', 'Personality')->where('acronymn', '!=', 'Personality')->get('name')->pluck('name');
        $cats->push('personality_1');
        $cats->push('personality_2');
        $levels = collect();
        foreach ($cats as $cat) {
            if (strpos($cat, 'personality') !== false) {
                $cats->push($cat . ' score');
            } else {
                $cats->push($cat . ' report');
                $levels->push($cat . ' level');
            }
        }
        $cats = $cats->toArray();

        sort($cats);
        $levels = $levels->sort()->toArray();
        $cats = array_merge($cats, $levels);
        $headings = array_merge([
            '#',
            'username',
            'nickname',
            'email',
            'mobile',
            'organization',
            'code',
            'coupon used',
            'profile',
            'Assessment started at',
            'Assessment ended at',
            'Questions attempted'
        ], $cats);
        $this->headings = $headings;
        return $headings;
    }

    public function view(): View
    {
        $this->headings();

        return view('test')->with('headings', $this->headings);
    }
}
