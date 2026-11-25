<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // Default validity for HappiMynd codes / coupons created without an expiry date
        $now = now();
        foreach (['token_default_validity_days', 'coupon_default_validity_days'] as $key) {
            if (!DB::table('settings')->where('key', $key)->exists()) {
                DB::table('settings')->insert(['key' => $key, 'value' => '60', 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
