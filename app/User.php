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

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package App
 */
class User extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = ['recover_password_token', 'confirmed','role_id', 'name','public_name','surname','public_surname', 'email','public_email', 'timeout', 'street', 'city', 'zip_code', 'country', 'nationality', 'identity_card', 'identity_type', 'vat_number', 'phone_number', 'mobile_number', 'homepage', 'birthday', 'gender', 'marital_status', 'job', 'job_status', 'photo_id', 'photo_code'];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $hidden = ['deleted_at', 'id', 'password', 'rfid', 'alphanumeric_code'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialNetworks() {
        return $this->hasMany('App\SocialNetwork');
    }

    /**
     * Each User may has many User Parameters .
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userParameters() {
        return $this->hasMany('App\UserParameter');
    }


    public function cb_questionnaire()
    {
      return $this->belongsToMany('App\cbQuestionnaries');
    }

    public function orchUser() {
        return $this->hasOne('App\OrchUser','user_key','user_key');
    }

    public function accountRecoveryTokens() {
        return $this->hasMany('App\AccountRecoveryToken','user_key','user_key');
    }

    public function sms() {
        return $this->hasMany('App\Sms');
    }

    public function UserQuestionnaireUniqueKey() {
        return $this->hasMany('App\UserQuestionnaireUniqueKey');
    }

    public function anonymization() {
        return $this->hasOne('App\UserAnonymization','user_key','user_key');
    }
    /**
     * An User  belongs to many AllPermissions     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userPermissions()
    {
        return $this->belongsToMany('App\AllPermission','all_permission_users','user_id','all_permission_code');
    }

    /**
     * An User  belongs to many CbPermissions     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userCbPermissions()
    {
        return $this->belongsToMany('App\CbPermission','cb_permission_users','user_id','cb_permission_code');
    }
}
