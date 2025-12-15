<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop columns that exist
            if (Schema::hasColumn('users', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('users', 'region')) {
                $table->dropColumn('region');
            }
            if (Schema::hasColumn('users', 'stripe_id')) {
                $table->dropColumn('stripe_id');
            }
            if (Schema::hasColumn('users', 'pm_type')) {
                $table->dropColumn('pm_type');
            }
            if (Schema::hasColumn('users', 'pm_last_four')) {
                $table->dropColumn('pm_last_four');
            }
            if (Schema::hasColumn('users', 'trial_ends_at')) {
                $table->dropColumn('trial_ends_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD');
            $table->string('region', 2)->nullable();
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
        });
    }
};
