<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Product usage tables had no index on user_id, so "users of product X"
 * filters and per-user usage counts scanned the whole table once per user.
 */
return new class extends Migration
{
    const TABLES = [
        'happiself_users_last_visit_sub_course_and_contents',
        'happiguide_sessions',
        'group_chats',
        'happitalk_sessions',
        'recently_viewed_happi_learn_contents',
        'scores',
    ];

    public function up()
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->index('user_id');
            });
        }
    }

    public function down()
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropIndex(['user_id']);
            });
        }
    }
};
