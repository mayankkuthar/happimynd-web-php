<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Availability;
use App\Models\AvailableDate;
use App\Models\AssessmentApprove;
use App\Models\AvailabilityDates;

class AppointmentService
{
    public static function getBookedAppointmentDates()
    {
        // $slots = AssessmentApprove::whereNotNull('available_date')->get();
        $unavailable = Availability::where('date', '>=', Carbon::now()->format('m/d/yy'))->get();
        $slotsBooked = [];
        // foreach ($slots as $slot) {
        //     if (isset($slotsBooked[$slot->available_date])) {
        //         $slotsBooked[$slot->available_date] = array_merge($slotsBooked[$slot->available_date], [$slot->slot]);
        //     } else {
        //         $slotsBooked[$slot->available_date] = [$slot->slot];
        //     }
        // }
        foreach ($unavailable as $dateTime) {
            if (isset($slotsBooked[$dateTime->date])) {
                $slotsBooked[$dateTime->date] = array_merge($slotsBooked[$dateTime->date], [$dateTime->time]);
            } else {
                $slotsBooked[$dateTime->date] = [$dateTime->time];
            }
        }
        return $slotsBooked;
    }

    public static function getAvailableAppointmentDates(){
        $available = AvailableDate::where('date', '<=', Carbon::now())->get();
        // return $available;
        $slotsBooked = [];
        foreach ($available as $dateTime) {
            if (isset($slotsBooked[$dateTime->date])) {
                $slotsBooked[$dateTime->date] = array_merge($slotsBooked[$dateTime->date], [date("g:i a", strtotime($dateTime->from)).'-'.date("g:i a", strtotime($dateTime->to)).'*'.$dateTime->consultant]);
            } else {
                $slotsBooked[$dateTime->date] = [date("g:i a", strtotime($dateTime->from)).'-'.date("g:i a", strtotime($dateTime->to)).'*'.$dateTime->consultant];
            }
        }
        return $slotsBooked;
    }

    public static function getAllAppointmentDates()
    {
        $available = AvailabilityDates::where('date', '>=', Carbon::now())->get();
        $slotsBooked = [];
            foreach ($available as $dateTime) {
            if (isset($slotsBooked[$dateTime->date])) {
                $slotsBooked[$dateTime->date] = array_merge($slotsBooked[$dateTime->date], [$dateTime->time.'-'. $dateTime->consultant]);
            } else {
                $slotsBooked[$dateTime->date] = [$dateTime->time.'-'. $dateTime->consultant];
            }
        }
        return $slotsBooked;
    }
}
