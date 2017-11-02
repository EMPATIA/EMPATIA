<?php
/**
 * Copyright (C) 2016 OneSource - Consultoria Informatica Lda <geral@onesource.pt>
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by the Free
 * Software Foundation; either version 3 of the License, or (at your option) any
 * later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along
 * with this program; if not, see <http://www.gnu.org/licenses>.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned()->default(0);
            $table->string('user_key', 32)->unique();
            $table->string('password');
            $table->string('recover_password_token')->nullable();
			$table->string('sms_token')->nullable();
            $table->string('name');
			$table->boolean('public_name')->default(0);
			$table->string('surname')->nullable();
			$table->boolean('public_surname')->default(0);
            $table->string('email');
			$table->boolean('public_email')->default(0);
			$table->string('timeout')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->string('nationality')->nullable();
            $table->string('identity_card')->nullable();
            $table->integer('identity_type')->nullable()->unsigned();
            $table->string('vat_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('homepage')->nullable();
            $table->date('birthday')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('job')->nullable();
            $table->string('job_status')->nullable();
            $table->string('photo_id')->nullable();
            $table->string('photo_code')->nullable();
            $table->boolean('confirmed')->default(0);
            $table->string('confirmation_code')->nullable();
            $table->string('rfid')->nullable();
            $table->string('alphanumeric_code')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        $defaultAdmin = ['name' => 'Admin EMPATIA', 'password' => bcrypt('empatia2016'), 'user_key' => 'defaultUSERprojectEMPATIA2016JAN', 'email' => 'admin@empatia-project.eu', 'confirmed' => true];

        DB::table('users')->insert([$defaultAdmin]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
