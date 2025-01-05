<?php

namespace App\Exports;

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

use App\Models\User;


class UserPlanDateWiseExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */


    protected $start;
    protected $end;
    public function __construct($data)
    {
        if (isset($data['start_date'])) {
            $this->start = $data['start_date'];
            $this->end = $data['end_date'];
        } 
    }


    
    public function collection()
    {

        $records = User::with('profileType', 'bundleStatus.plans.package')
            ->whereBetween('created_at', [$this->start, $this->end])
            ->latest()
            ->get();

        $result = array();

        $i =0 ;

        foreach($records as $user){

            $plans_array = $user->bundleStatus->pluck('plans.package.name')->toArray();
              $search = 'HappiLIFE Summary Reading';
              $replace = 'HappiLearn';
              foreach ($plans_array as $key => $value) {
                  if ($value == $search) {
                      $plans_array[$key] = $replace;
                      break;
                  }
              }


              if($user->isOrganizationUser()){
                $status = 'B2B';
              } else{
                $status = 'B2C';
              }


           $result[] = array(
              'id'=> ++$i,
              'username' => $user->username .'(User id: '. $user->id .')',
              'email' => $user->email,
              'b2b/b2c' => $status,
              'organization' => $user->isOrganizationUser() ? $user->userToken->token->organization()->withTrashed()->first()->name : 'Individual',
              'no_of_plans_bought' => $user->bundleStatus->count(),
              'plans' => implode(" || ", $plans_array),
           );
        }


        return collect($result);

    }

    public function headings(): array
    {
       return [
         '#',
         'Username',
         'Email',
         'B2B / B2C',
         'Organization.',
         'No. of Plans Bought',
         'Plans',
       ];
    }


}
