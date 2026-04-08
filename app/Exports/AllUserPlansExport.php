<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMapping;

use App\Models\User;


class AllUserPlansExport implements FromQuery, WithHeadings, ShouldAutoSize, WithChunkReading, WithMapping
{
    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        return User::with('profileType', 'bundleStatus.plans.package')
            ->latest();
    }
    
    /**
    * @param User $user
    * @return array
    */
    public function map($user): array
    {
        static $i = 0;
        
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

        // Get the purchase date (created_at) from the most recent bundle status
        $purchase_date = $user->bundleStatus->count() > 0 
            ? $user->bundleStatus->sortByDesc('created_at')->first()->created_at->format('M d, Y') 
            : '-';

        return [
            'id'=> ++$i,
            'username' => $user->username .'(User id: '. $user->id .')',
            'email' => $user->email,
            'b2b/b2c' => $status,
            'organization' => $user->isOrganizationUser() ? $user->userToken->token->organization()->withTrashed()->first()->name : 'Individual',
            'no_of_plans_bought' => $user->bundleStatus->count(),
            'purchase_date' => $purchase_date,
            'plans' => implode(" || ", $plans_array),
        ];
    }
    
    /**
    * @return int
    */
    public function chunkSize(): int
    {
        return 500; // Adjust this value based on your server's memory capacity
    }

    public function headings(): array
    {
       return [
         '#',
         'Username',
         'Email',
         'B2B / B2C',
         'Organization',
         'No. of Plans Bought',
         'Purchase Date',
         'Plans',
       ];
    }
}