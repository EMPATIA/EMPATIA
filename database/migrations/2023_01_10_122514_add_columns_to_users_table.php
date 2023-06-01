<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TODO: review this migration to check for improvements and to make sure it's not causing issues

        // Needs to be separated for the dropColumns process finishes,
        // because later columns will be created with the same names (created_at and updated_at)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('password');
            $table->dropColumn('remember_token');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('uuid')->unique()->after('id');
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->json('parameters')->nullable();
            $table->json('data')->nullable();
            $table->versionable();
            $table->blamestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Needs to be separated for the same reason above, but now it's for doing the rollback
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid', 'two_factor_secret', 'two_factor_recovery_codes', 'parameters', 'data');
            $table->dropVersionable();
            $table->dropBlamestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('name');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
};
