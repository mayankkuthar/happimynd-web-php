<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Single sign-on for Quizzard and the Games Hub (HappiPAUSE).
 *
 * sso_codes          one-time codes the website hands to an app after login
 * sso_admin_numbers  mobile numbers that get admin rights in those apps
 *
 * Created on prod with database/sql/2026_11_28_sso.sql (artisan can't run there).
 */
class CreateSsoTables extends Migration
{
    public function up()
    {
        Schema::create('sso_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('code_hash', 64)->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('app', 20);
            $table->timestamp('expires_at')->index();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sso_admin_numbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('mobile', 15)->unique();
            $table->enum('quizzard_role', ['none', 'admin', 'super_admin'])->default('none');
            $table->string('quizzard_org_id', 255)->nullable();
            $table->boolean('hub_admin')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('sso_admin_numbers')->insert([
            ['name' => 'Mayank Kuthar', 'mobile' => '9318458907', 'quizzard_role' => 'super_admin', 'hub_admin' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ashish', 'mobile' => '8104247997', 'quizzard_role' => 'super_admin', 'hub_admin' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('sso_admin_numbers');
        Schema::dropIfExists('sso_codes');
    }
}
