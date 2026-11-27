<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What users can redeem their reward points for. Edited in the admin panel
 * (Engagement > Redeem Options) and listed to the app by
 * GET /api/v1/reward-redeem-options. Redeeming itself comes later with the app.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('reward_redeem_options', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 32)->default('other');
            $table->unsignedInteger('points_required');
            $table->string('reward_value')->nullable();      // e.g. "Rs 100 off", "1 free HappiTALK session"
            $table->string('image')->nullable();             // public URL
            $table->unsignedInteger('stock')->nullable();    // null = unlimited
            $table->unsignedInteger('max_per_user')->nullable(); // null = no limit
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('terms')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reward_redeem_options');
    }
};
