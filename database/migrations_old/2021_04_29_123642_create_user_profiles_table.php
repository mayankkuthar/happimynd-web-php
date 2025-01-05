<?php

use App\Models\UserProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true)->comment('1 -> active 0->inactive');
            $table->timestamps();
        });
        if (Schema::hasTable('user_profiles')) {
            $profiles = [
                ['name' => 'salaried'],
                ['name' => 'self employed'],
                ['name' => 'home maker'],
                ['name' => 'senior citizen'],
                ['name' => 'student(school)'],
                ['name' => 'student(college/university)'],
                ['name' => 'entrepreneur'],
                ['name' => 'jobseeker'],
                ['name' => 'frontline warrior'],
            ];
            UserProfile::insert($profiles);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}
