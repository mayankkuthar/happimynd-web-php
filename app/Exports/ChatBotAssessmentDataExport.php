<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\ChatBot\ChatBotAssessment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ChatBotAssessmentDataExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * From.
     */
    protected $from;

    /**
     * To.
     */
    protected $to;

    /**
     * Constructor
     */
    public function __construct(array $dates)
    {
        $this->from = $dates['from'];
        $this->to = $dates['to'];
    }

    /**
     * Collection.
     */
    public function collection()
    {
        $chatBotAssessments = ChatBotAssessment::whereHas('user')->with('user')->get();

        $rows = $chatBotAssessments->map(function (ChatBotAssessment $chatBotAssessment) {
            $row = [];

            $row[] = $chatBotAssessment->id;
            $row[] = $chatBotAssessment->user->username;
            $row[] = $chatBotAssessment->user->nickname;
            $row[] = $chatBotAssessment->user->email;
            $row[] = $chatBotAssessment->user->mobile;

            if ($chatBotAssessment->user->isOrganizationUser()) {
                $organization = $chatBotAssessment->user->userToken->token->organization;

                if ($organization->isDeleted()) {
                    $row[] = $organization->name . ' (Deleted)';
                } else {
                    $row[] = $organization->name;
                }
            }

            $row[] = $chatBotAssessment->category->name;
            $row[] = $chatBotAssessment->score;
            $row[] = $chatBotAssessment->report->interpretation;
            $row[] = $chatBotAssessment->created_at;

            return $row;
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Asessment Id',
            'Username',
            'Nickname',
            'Email',
            'Mobile',
            'Organization',
            'Category',
            'Score',
            'Report',
            'Completed on',
        ];
    }

    public function view(): View
    {
        return view('download')->with('headings', $this->headings());
    }
}
