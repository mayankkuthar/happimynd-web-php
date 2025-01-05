<?php

use App\Models\Duration;
use App\Models\DurationType;
use App\Models\Package;
use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('duration_type_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->float('price')->default(0);
            $table->tinyInteger('active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
        $packages = Package::with('duration')->get();
        $durationType = DurationType::create(
            [
                'type' => DurationType::TYPES['year'],
                'value' => 1,
                'frequency' => 1
            ]
        );
        $durationType = DurationType::create(
            [
                'type' => DurationType::TYPES['session'],
                'value' => 45,
                'frequency' => 1
            ]
        );
        $durationType = DurationType::create(
            [
                'type' => DurationType::TYPES['session'],
                'value' => 45,
                'frequency' => 2
            ]
        );
        $durationType = DurationType::create(
            [
                'type' => DurationType::TYPES['session'],
                'value' => 45,
                'frequency' => 4
            ]
        );
        $durationType = DurationType::create(
            [
                'type' => DurationType::TYPES['onetime'],
                'value' => 1,
                'frequency' => 1
            ]
        );
        foreach ($packages as $package) {
            if (strcasecmp($package->duration->name, '1 year access') == 0) {
                $durationType = DurationType::ofTypeYear()->first();
                Plan::create(
                    [
                        'package_id' => $package->id,
                        'duration_type_id' => $durationType->id,
                        'price' => $package->regular_price
                    ]
                );
            } elseif (strcasecmp($package->duration->name, '1 session') == 0) {
                $durationType = DurationType::ofTypeSession(1)->first();
                Plan::create(
                    [
                        'package_id' => $package->id,
                        'duration_type_id' => $durationType->id,
                        'price' => $package->regular_price
                    ]
                );
            } elseif (strcasecmp($package->duration->name, '2 session') == 0) {
                $durationType = DurationType::ofTypeSession(2)->first();
                Plan::create(
                    [
                        'package_id' => $package->id,
                        'duration_type_id' => $durationType->id,
                        'price' => $package->regular_price
                    ]
                );
            } elseif (strcasecmp($package->duration->name, '4 session') == 0) {
                $durationType = DurationType::ofTypeSession(4)->first();
                Plan::create(
                    [
                        'package_id' => $package->id,
                        'duration_type_id' => $durationType->id,
                        'price' => $package->regular_price
                    ]
                );
            } elseif (strcasecmp($package->duration->name, 'onetime pay') == 0) {
                $durationType = DurationType::ofTypeOnetimePay()->first();
                Plan::create(
                    [
                        'package_id' => $package->id,
                        'duration_type_id' => $durationType->id,
                        'price' => $package->regular_price
                    ]
                );
            }
        }



        // Schema::table('packages', function (Blueprint $table) {
        //     $table->dropColumn(['price']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('packages', function (Blueprint $table) {
        //     $table->addColumn('float', 'price');
        // });
        Schema::dropIfExists('plans');
    }
}
