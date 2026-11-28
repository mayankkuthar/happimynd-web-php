<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddHappimyndSsoToHubUsers extends Migration
{
    /**
     * Players now sign in with HappiMynd (OTP) through SSO: link each hub
     * user to its HappiMynd user, and allow no email (mobile-only accounts).
     * Applied on prod by database/sql/2026_11_28_sso.sql.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('hub')->statement('ALTER TABLE users
            ADD COLUMN happimynd_user_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER id,
            ADD COLUMN mobile VARCHAR(20) NULL DEFAULT NULL AFTER happimynd_user_id,
            MODIFY email VARCHAR(255) NULL DEFAULT NULL,
            ADD UNIQUE KEY uq_users_happimynd_user (happimynd_user_id)');
    }

    public function down()
    {
        DB::connection('hub')->statement('ALTER TABLE users
            DROP INDEX uq_users_happimynd_user,
            DROP COLUMN mobile,
            DROP COLUMN happimynd_user_id');
    }
}
