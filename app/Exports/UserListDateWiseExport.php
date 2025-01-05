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


class UserListDateWiseExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        //
        
        $records = User::latest()->with('profileType' , 'usersRating') ->whereBetween('created_at', [$this->start, $this->end])->get();


        $result = array();

        $i =0 ;

        foreach($records as $user){
           $result[] = array(
              'id'=> ++$i,
              'username' => $user->username ?? '-' ,
              'nickname' => $user->nickname ?? '-',
              'coupen_used' => $user->getUsedCouponCodes()??'-',
              'organization' => ($user->isOrganizationUser())? $user->userToken->token->organization()->withTrashed()->first()->name : '-' ,
              'token' => $user->isOrganizationUser() ? $user->userToken->token->token: '-',
              'email' => $user->email ?? '-' ,
              'mobile' => $user->mobile ?? '-' ,
              'profession' => $user->profileType->name ?? '-',
              'account_status' => $user->account_status ?? '-',
              'age' => $user->age ?? '-' ,
              'gender' => $user->gender ?? '-',
              'date' => $user->created_at->format('M d,Y h:i a') ?? '-',
           );
        }



        return collect($result);

    }

    public function headings(): array
    {
       return [
         '#',
         'Username',
         'Nickname',
         'Coupon Used',
         'Organization.',
         'Token',
         'E-mail',
         'Mobile',
         'Profession',
         'Account Status',
         'Age',
         'Gender',
         'Date',

       ];
    }


}
