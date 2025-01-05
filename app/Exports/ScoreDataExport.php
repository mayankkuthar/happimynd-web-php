<?php

namespace App\Exports;

use App\Models\Score;
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

class ScoreDataExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $start;
    protected $end;

    public function __construct($data)
    {
        if (isset($data['start_date'])) {
            $this->start = $data['start_date'];
            $this->end = $data['end_date'];
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
        $scores = Score::latest()->whereHas('user')->with('user');

        if ($this->start != NULL && $this->end != NULL) {
            $start_date = Carbon::parse($this->start)->toDateTimeString();
            $end_date = Carbon::parse($this->end)->toDateTimeString();

            $scores = $scores->whereBetween('created_at', [$start_date, $end_date])->get();
        } else {
            $scores = $scores->get();
        }

        $rows = [];
        $number = 0;

        foreach ($scores as $score) {
            $data = [];

            $data['#'] = $number++;
            $data['Username'] = $score->user->username ?? '-';
            $data['Nickname'] = $score->user->nickname ?? '-';
            $data['E-mail'] = $score->user->email ?? '-';
            $data['Mobile'] = $score->user->mobile ?? '-';
            $data['Organization'] = ($score->user->isOrganizationUser()) ? $score->user->userToken->token->organization()->withTrashed()->first()->name : '-';
            $data['Token'] = $score->user->isOrganizationUser() ? $score->user->userToken->token->token : '-';
            $data['Coupon Used'] = $score->user->getUsedCouponCodes() ?? '-';
            $data['Profile'] = $score->user->profileType->name ?? '-';
            $data['Result'] = $score->result;
            $data['Score'] = $score->score;
            $data['Smoothness'] = $score->smoothness;
            $data['Liveliness'] = $score->liveliness;
            $data['Control'] = $score->control;
            $data['Energy Range'] = $score->energy_range;
            $data['Clarity'] = $score->clarity;
            $data['Crispness'] = $score->crispness;
            $data['Speech Rate'] = $score->speech_rate;
            $data['Pause Duration'] = $score->pause_duration;
            $data['Inferred At'] = $score->inferred_at;

            array_push($rows, $data);
        }

        return new Collection($rows);
    }

    public function headings(): array
    {
        $headings = [
            '#',
            'Username',
            'Nickname',
            'E-mail',
            'Mobile',
            'Organization',
            'Token',
            'Coupon Used',
            'Profile',
            'Result',
            'Score',
            'Smoothness',
            'Liveliness',
            'Control',
            'Energy Range',
            'Clarity',
            'Crispness',
            'Speech Rate',
            'Pause Duration',
            'Inferred At',
        ];

        $this->headings = $headings;

        return $headings;
    }

    public function view(): View
    {
        $this->headings();

        return view('test')->with('headings', $this->headings);
    }
}
