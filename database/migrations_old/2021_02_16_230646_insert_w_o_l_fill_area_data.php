<?php

use App\Models\Category;
use App\Models\ReportCharacteristic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertWOLFillAreaData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ReportCharacteristic::where('meter_scale_level_name', 'VeryLow')->update(['meter_scale_level_name' => 'Very Low']);
        $reports = ReportCharacteristic::whereHas('category')->get();
        $c = 0;

        foreach ($reports as $report) {
            if ($report->category->acronymn == 'Happiness') {
                if ($report->meter_scale_level_name == 'Extremely Happy') {
                    $report->WOL_fill_area = 10;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Happy') {
                    $report->WOL_fill_area = 7;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Moderately Happy') {
                    $report->WOL_fill_area = 4;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Unhappy') {
                    $report->WOL_fill_area = 2;
                    $report->save();
                    $c++;
                }
            } elseif ($report->category->acronymn == 'Internet Addiction') {
                if ($report->meter_scale_level_name == 'Normal') {
                    $report->WOL_fill_area = 10;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Mild') {
                    $report->WOL_fill_area = 7;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Moderate') {
                    $report->WOL_fill_area = 4;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Severe') {
                    $report->WOL_fill_area = 2;
                    $report->save();
                    $c++;
                }
            } elseif ($report->category->acronymn == 'Resilience') {
                if ($report->meter_scale_level_name == 'High Resilience') {
                    $report->WOL_fill_area = 10;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Normal Resilience') {
                    $report->WOL_fill_area = 6;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Low Resilience') {
                    $report->WOL_fill_area = 2;
                    $report->save();
                    $c++;
                }
            } elseif ($report->category->acronymn == 'Job Satisfaction' || $report->category->acronymn == 'Self Esteem') {
                if ($report->meter_scale_level_name == 'Very High') {
                    $report->WOL_fill_area = 10;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'High') {
                    $report->WOL_fill_area = 8;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Average') {
                    $report->WOL_fill_area = 6;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Low') {
                    $report->WOL_fill_area = 4;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Very Low') {
                    $report->WOL_fill_area = 2;
                    $report->save();
                    $c++;
                }
            } elseif ($report->category->acronymn == 'Burn out' || $report->category->acronymn == 'Stress' || $report->category->acronymn == 'Anxiety' || $report->category->acronymn == 'Depression') {
                if ($report->meter_scale_level_name == 'Normal') {
                    $report->WOL_fill_area = 10;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Mild') {
                    $report->WOL_fill_area = 8;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Moderate') {
                    $report->WOL_fill_area = 6;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Severe') {
                    $report->WOL_fill_area = 4;
                    $report->save();
                    $c++;
                }
                if ($report->meter_scale_level_name == 'Extremely Severe') {
                    $report->WOL_fill_area = 2;
                    $report->save();
                    $c++;
                }
            }
        }
        dump($c);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
