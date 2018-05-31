<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_key',
        'country_id',
        'currency_id',
        'timezone_id',
        'name',
        'designation',
        'description',
        'url',
        'created_by'
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
    protected $hidden = ['deleted_at'];



    /**
     * An Entity has many Language
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages(){
        return $this->belongsToMany('App\Language')
            ->withPivot('default')
            ->withTimestamps();
    }

    /**
     * An Entity has one Currency
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(){
        return $this->belongsTo('App\Currency');
    }

    /**
     * An Entity has one Country
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(){
        return $this->belongsTo('App\Country');
    }

    /**
     * An Entity has one Currency
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function timezone(){
        return $this->belongsTo('App\Timezone');
    }

    /**
     * An Entity can have many Users
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(){
        return $this->belongsToMany('App\OrchUser','entity_user','entity_id','user_id')
        ->withTimestamps()->withPivot('role', 'status');
    }

    /**
     * An Entity can have many Authentication methods
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authMethodEntities(){
        return $this->belongsToMany('App\AuthMethod')
        ->withTimestamps();
    }

    /**
     * An Entity can have many Forums
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forumEntity(){
        return $this->hasMany('App\Forum');
    }

    /**
     * An Entity can have many Discussions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discussionEntity(){
        return $this->hasMany('App\Discussion');
    }

    /**
     * An Entity can have many Access Menus
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessMenuEntity(){
        return $this->hasMany('App\AccessMenu');
    }

    /**
     * An Entity can have many Access Pages
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessPageEntity(){
        return $this->hasMany('App\AccessPage');
    }

    /**
     * An Entity can have many Geographic Area
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function geoAreaEntity(){
        return $this->hasMany('App\GeographicArea');
    }

    /**
     * An Entity can have many Vote Methods
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function voteMethods(){
        return $this->hasMany('App\VoteMethod');
    }

    /**
     * An Entity can have many Sites
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sites(){
        return $this->hasMany('App\Site');
    }

    /**
     * An Entity can have many Kiosks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kiosks(){
        return $this->hasMany('App\Kiosk');
    }

    /**
     * An Entity can have many users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ideas(){
        return $this->hasMany('App\Idea');
    }

    /**
     * An Entity can have many Parameters
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameters(){
        return $this->hasMany('App\Parameter');
    }

    /**
     * An Entity can have many Tematic Consultations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tematicConsultations(){
        return $this->hasMany('App\TematicConsultation');
    }

    /**
     * An Entity can have many Proposals
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposals(){
        return $this->hasMany('App\Proposal');
    }

    /**
     * An Entity can have many Co Constructions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coConstructions(){
        return $this->hasMany('App\CoConstruction');
    }

    /**
     * An Entity can have many Public Consultations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publicConsultations(){
        return $this->hasMany('App\PublicConsultation');
    }


    /**
     * An Entity can have many Roles
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles(){
        return $this->hasMany('App\Role');
    }


    /**
     * An Entity can have many Categories
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories(){
        return $this->hasMany('App\Category');
    }

    /**
     * An Entity can have many Layouts
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function layouts(){
        return $this->belongsToMany('App\Layout');
    }

    /**
     * An Entity can have many Parameters
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parametersTemplates(){
        return $this->hasMany('App\CbParameterTemplate');
    }

    /**
     * An Entity can have many Home Page Types.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function homePageTypes(){
        return $this->hasMany('App\HomePageType');
    }

    /**
     * An Entity can have many Entity Modules.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityModules(){
        return $this->hasMany('App\EntityModule');
    }

    /**
     * An Entity can have many Parameter User Types.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameterUserTypes(){
        return $this->hasMany('App\ParameterUserType');
    }

    /**
     * An Entity  can have many EntityGroups
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityGroups()
    {
        return $this->hasMany('App\EntityGroup');
    }


    /**
     * An Entity can have many Entity Permissions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityPermissions(){
        return $this->hasMany('App\EntityPermission');
    }


    /**
     * An Entity can have many vat numbers
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vatNumbers(){
        return $this->hasMany('App\EntityVatNumber');
    }


    /**
     * An Entity can have many domain names
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domainNames(){
        return $this->hasMany('App\EntityDomainName');
    }

    /**
     * An Entity can have many Messages
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(){
        return $this->hasMany('App\Message');
    }


    public function loginLevels(){
        return $this->hasMany('App\LoginLevel');
    }

    public function accountRecoveryParameters(){
        return $this->hasMany('App\AccountRecoveryParameter','entity_key','entity_key');
    }

    /**
     * An Entity can have many Entity Cbs
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityCbs(){
        return $this->hasMany('App\EntityCb');
    }

    /**
     * An Entity has many EntityNotifications
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityNotifications(){
        return $this->hasMany('App\EntityNotification');
    }

    public function openData() {
        return $this->belongsTo('App\Modules\OpenData\Models\OpenDataEntity','entity_key','entity_key');
    }
}
