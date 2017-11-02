<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrchUser extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orch_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_key',
        'admin',
        'geographic_area_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Verifies if the User as the role
     * @param $userKey
     * @param $role
     * @param $entityId
     * @return bool
     */
    public static function verifyRole($userKey, $role, $entityId = 0){
        if($role === 'admin'){
            if(OrchUser::whereUserKey($userKey)->whereAdmin(1)->exists())
                return true;
        }
        else {
            $user = OrchUser::with(['entities' => function ($query) use ($entityId, $role) {
                $query->whereEntityId($entityId)->whereRole($role);
            }])->where("user_key", "=", $userKey)->first();

            if (count($user->entities ?? []) > 0)
                return true;
        }
        return false;
    }

    /**
     * Get user role
     * @param $entityId
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getRole($entityId){

        $role =  $this->belongsToMany('App\Entity', 'entity_user','user_id','entity_id')->withPivot('role')->wherePivot('entity_id',$entityId)->get();
        if(sizeof($role)>0){
            $this->setAttribute('role',$role[0]->pivot->role);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return $this
     */
    public function entities(){
        return $this->belongsToMany('App\Entity','entity_user','user_id','entity_id')
            ->withTimestamps()
            ->withPivot('role', 'status');
    }

    /**
     * A User belongs to a Geographic Area
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function geographicArea(){
        return $this->belongsTo('App\GeographicArea');
    }

    /**
     * An User can have  many Roles
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(){
        return $this->belongsToMany('App\Role','role_user','user_id','role_id')
            ->withTimestamps();
    }

    /**
     * An User can have many EntityGroups
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entityGroups(){
        return $this->belongsToMany('App\EntityGroup','entity_group_user','user_id','entity_group_id')
            ->withTimestamps();
    }

    /**
     * A Users belongs to many Level Parameters
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function levelParameters(){
        return $this->belongsToMany('App\LevelParameter','level_parameter_user','user_id','level_parameter_id');
    }

    /**
     * An User can have many Entity Permissions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityPermissions(){
        return $this->hasMany('App\EntityPermission','user_id');
    }

    /**
     * A User can have many Messages
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messagesTo(){
        return $this->hasMany('App\Message','to','user_key');
    }

    /**
     * A User can have many Messages
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messagesFrom(){
        return $this->hasMany('App\Message','from','user_key');
    }

    /**
     * A User can have many Messages
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(){
        return $this->hasMany('App\Message', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userLoginLevels(){
      return $this->hasMany('App\UserLoginLevel', 'user_id', 'login_level_id');
  }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cbQuestionnaires(){
        return $this->belongsToMany('App\CbQuestionnaries', 'cb_questionnaries_user', 'user_id', 'cb_questionnarie_id')
            ->withPivot('date_ignore')
            ->withTimestamps();
    }
}
