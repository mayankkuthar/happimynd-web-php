<?php

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProfileTypeInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_profile_id')->after('username')->nullable(true)->constrained();
        });
        if (Schema::hasColumn('users', 'user_profile_id')) {
            $profiles = UserProfile::all()->pluck('id', 'name');
            $users = User::all();
            if ($users) {
                foreach ($users as $user) {
                    if ($user->getRawOriginal('profession'))
                        $user->user_profile_id = $profiles[$user->getRawOriginal('profession')];
                    $user->save();
                }
            }
            if (Schema::hasColumn('users', 'profession')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn(['profession']);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_profile_id');
        });
    }
}
