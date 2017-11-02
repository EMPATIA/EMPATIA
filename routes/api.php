<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Orchestrator module routes
 */
Route::group(['middleware' => ['authOne']], function () {

    /**
     * Route for the requests of Kiosk Model
     */
    Route::post('kiosk/{kiosk_key}/proposals/store', 'KiosksController@storeProposals');
    Route::put('kiosk/{kiosk_key}/proposalsReorder/', 'KiosksController@proposalsReorder');
    Route::get('kiosk/{kiosk_key}/proposals', 'KiosksController@getProposals');
    Route::delete('kiosk/{kiosk_key}/destroyProposal/{id}', 'KiosksController@destroyProposal');
    Route::post('kiosk/addProposal', 'KiosksController@addProposal');
    Route::get('kiosk/list', 'KiosksController@index');
    Route::resource('kiosk', 'KiosksController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /**
     * Route for the requests of Co-Construction Model
     */
    Route::get('coConstruction/list', 'CoConstructionsController@index');
    Route::resource('coConstruction', 'CoConstructionsController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /**
     * Route for the requests of Kiosk Type Model
     */
    Route::get('kiosktype/list', 'KioskTypesController@index');
    Route::resource('kiosktype', 'KioskTypesController', ['only' => ['show']]);

    /**
     * Route for the requests of Entity Model
     */

    /** -----------------------------------------------------------
     *  {BEGIN} Routes to deal with the entity registration values
     * ------------------------------------------------------------
     */
    Route::put('entity/{entityKey}/manualUpdateTopicVotesInfo', 'EntitiesController@manualUpdateTopicVotesInfo');
    Route::get('entity/getListOfAvailableUsersToSendEmails', 'EntitiesController@getListOfAvailableUsersToSendEmails');
    Route::get('entity/getEntityRegistrationValues', 'EntitiesController@getEntityRegistrationValues');
    Route::get('entity/getEntityStatistics', 'EntitiesController@getEntityStatistics');
    Route::post('entity/importRegistrationFields', 'EntitiesController@importRegistrationFields');
    Route::post('entity/deleteRegistrationValues', 'EntitiesController@deleteRegistrationValues');
    /** -----------------------------------------------------------
     *  {END} Routes to deal with the entity registration values
     * ------------------------------------------------------------
     */
    Route::get('entity/managersList', 'EntitiesController@getEntityManagers');

    /** -----------------------------------------------------------
     *  {BEGIN} Routes to deal with the entity notifications
     * ------------------------------------------------------------
     */
    Route::get('entity/getNotificationTypes', 'EntitiesController@getNotificationTypes');
    Route::get('entity/getEntityNotifications', 'EntitiesController@getEntityNotifications');
    Route::post('entity/setEntityNotifications', 'EntitiesController@setEntityNotifications');

    Route::post('entity/setEntityNotificationTemplate', 'EntitiesController@setEntityNotificationTemplate');
    Route::get('entity/getEntityNotificationTemplate/{template_key}', 'EntitiesController@getEntityNotificationTemplate');
    Route::post('entity/updateEntityNotificationTemplate/{template_key}', 'EntitiesController@updateEntityNotificationTemplate');
    /** -----------------------------------------------------------
     *  {END} Routes to deal with the entity registration values
     * ------------------------------------------------------------
     */

    Route::post('validateVatNumber', 'EntitiesController@validateVatNumber');
    Route::post('validateDomainName', 'EntitiesController@validateDomainName');

    Route::get('entity/{entity_key}/publicEntityForNotify', 'EntitiesController@getPublicEntityForNotify');
    Route::post('entity/addAuthMethod', 'EntitiesController@addAuthMethod');
    Route::get('entity/userList', 'EntitiesController@getEntityUserList');
    Route::delete('entity/{entity_key}/AuthMethod/{authMethod_key}', 'EntitiesController@removeAuthMethod');
    Route::delete('entity/{entity_key}/Language/{lang_id}', 'EntitiesController@removeLanguage');
    Route::delete('entity/{entity_key}/Layout/{layout_key}', 'EntitiesController@removeLayout');
    Route::get('entity/list', 'EntitiesController@index');
    Route::get('entity/totalUsers', 'EntitiesController@totalEntityUsers');
    Route::get('entity/parameters', 'EntitiesController@entityParameters');
    Route::post('entity/addLanguage', 'EntitiesController@addLanguage');
    Route::post('entity/addLayout', 'EntitiesController@addLayout');
    Route::get('entity/addManager/{user_key}', 'EntitiesController@addManager');
    Route::put('entity/defaultLanguage', 'EntitiesController@defaultLanguage');
    Route::get('entity/validateUsers', 'EntitiesController@getUsersAwaitingValidation');
    Route::resource('entity', 'EntitiesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of EntityGroup Model
     */

    Route::put('entityGroup/{entityGroupKey}/user/{userKey}', 'EntityGroupsController@addEntityGroupUsers');
    Route::delete('entityGroup/{entityGroupKey}/removeUser/{userKey}', 'EntityGroupsController@removeEntityGroupUsers');
    Route::get('entityGroup/{entityGroupKey}/listUsers', 'EntityGroupsController@listUsers');
    Route::put('entityGroup/reorder/{entityGroupKey}', 'EntityGroupsController@reorder');
    Route::get('entityGroup/listByType/{key}', 'EntityGroupsController@listByType');
    Route::get('entityGroup/list/', 'EntityGroupsController@index');
    Route::resource('entityGroup', 'EntityGroupsController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /**
     * Route for the requests of GroupType Model
     */
    Route::get('groupType/list', 'GroupTypesController@index');
    Route::resource('groupType', 'GroupTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Currency Model
     */
    Route::get('currency/list', 'CurrenciesController@index');
    Route::resource('currency', 'CurrenciesController', ['only' => ['show', 'store', 'update', 'destroy']]);
    /**
     * Route for the requests of Country Model
     */
    Route::get('country/list', 'CountriesController@index');
    Route::resource('country', 'CountriesController', ['only' => ['show', 'store', 'update', 'destroy']]);
    /**
     * Route for the requests of Language Model
     */
    Route::post('lang/languages', 'LanguagesController@getLanguages');
    Route::get('lang/list', 'LanguagesController@index');
    Route::get('lang/listAll', 'LanguagesController@listAll');
    Route::resource('lang', 'LanguagesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Timezone Model
     */
    Route::get('tz/list', 'TimezonesController@index');
    Route::resource('tz', 'TimezonesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of User Model
     */
    Route::get('user/userEmail', 'OrchUsersController@getUserEmail');
    Route::post('user/managersList', 'OrchUsersController@listAllManagers');
    Route::get('user/listrole', 'OrchUsersController@listRole');
    Route::get('user/listWithStatus/{state}', 'OrchUsersController@indexWithStatus');
//    Route::get('user/{userKey}/listEntityRoles', 'UsersController@listEntityRoles');
    Route::post('user/list', 'OrchUsersController@index');
    Route::get('user/count', 'OrchUsersController@countEntityUsers');
    Route::post('user/updateStatus', 'OrchUsersController@updateStatus');
    Route::post('user/migrateUserToEntity', 'OrchUsersController@migrateUserToEntity');
    Route::post('user/{user_key}/updateEntityUserRole', 'OrchUsersController@updateEntityUserRole');
    Route::put('user/{user_key}/updateLevel', 'OrchUsersController@updateUserLevel');
    Route::delete('user/{user_key}/updateLevel', 'OrchUsersController@deleteUserLevel');
    Route::get('user/{user_key}/site/{site_key}', 'OrchUsersController@manualUpdateUserLevel');
    Route::get('user/smsUpdateLevel', 'OrchUsersController@SmsUpdateUserLevel');
    Route::get('user/{user_key}/level/{level_key?}', 'OrchUsersController@setUserLevel');
    Route::post('user/checkAndUpdateLevel', 'OrchUsersController@checkAndUpdateLevel');
    Route::post('user/{user_key}/getUserLevel', 'OrchUsersController@getUserLevel');
    Route::get('user/{user_key}/getUserEntities', 'OrchUsersController@getUserEntities');
    Route::resource('user', 'OrchUsersController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Auth Model
     */
    Route::get('orchAuth/validate', 'AuthsController@index');
    Route::get('auth/role/{userKey}', 'AuthsController@checkRole');

    /**
     * Route for the requests of Authentication Methods
     */
    Route::get('authmethod/list', 'AuthMethodsController@index');
    Route::get('authmethod/listEntityAuthMethods', 'AuthMethodsController@listEntityAuthMethods');
    Route::get('authmethod/listAvailableAuthMethods', 'AuthMethodsController@listAvailableAuthMethods');
    Route::resource('authmethod', 'AuthMethodsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Vote Methods
     */
    Route::get('votemethod/list', 'VoteMethodsController@index');
    Route::resource('votemethod', 'VoteMethodsController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /**
     * Route for the requests of Access Types
     */
    Route::get('accesstype/list', 'AccessTypesController@index');

    /**
     * Route for the requests of Access Menus
     */
    Route::get('accessmenu/{id}/activate', 'AccessMenusController@activate');
    Route::get('accessmenu/list', 'AccessMenusController@index');
    Route::get('accessmenu/info', 'AccessMenusController@menuConstructor');
    Route::resource('accessmenu', 'AccessMenusController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Pages
     */
    Route::get('page/list', 'PagesController@index');
    Route::get('page/listByType/{page_type}', 'PagesController@listByType');
    Route::resource('page', 'PagesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Geographic Area
     */
    Route::get('geoarea/list', 'GeographicAreasController@index');
    Route::resource('geoarea', 'GeographicAreasController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Category
     */
    Route::post('category/createCategories', 'CategoriesController@createCategories');
    Route::get('category/list', 'CategoriesController@index');
    Route::resource('category', 'CategoriesController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /**
     * Route for the requests of Module
     */
    Route::get('module/list', 'ModulesController@index');
    Route::get('module/types', 'ModulesController@moduleWithTypes');
    Route::get('module/checkToken', 'ModulesController@checkToken');
//    Route::post('module', 'ModulesController@registerModule');
    Route::resource('module', 'ModulesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Sites
     */
    Route::get('site/ethics/{site_ethic_key}', 'SitesController@getSiteEthics');
    Route::get('site/useTerms', 'SitesController@getUseTerms') ;
    Route::get('site/siteEntityKey', 'SitesController@SiteEntityKey') ;
    Route::get('site/list', 'SitesController@index') ;
    Route::get('site/{site_key}/additionalUrls', 'SitesController@getSiteAdditionalUrls') ;
    Route::get('site/{site_key}/useTerms', 'SitesController@SiteUseTerms') ;
    Route::get('site/{url_id}/getAdditionalUrl', 'SitesController@getAdditionalUrl') ;
    Route::get('site/{site_id}/getSiteById', 'SitesController@getSiteById') ;
    Route::delete('site/{url_id}/deleteAdditionalUrl', 'SitesController@deleteAdditionalUrl') ;
    Route::post('site/{site_key}/siteEthic', 'SitesController@siteEthic');
    Route::post('site/entity', 'SitesController@siteEntity') ;
    Route::post('site/storeSiteAdditionalLink', 'SitesController@addAdditionalLink');
    Route::put('site/updateSiteAdditionalLink', 'SitesController@updateAdditionalLink');
    Route::get('site/getEntitiesSites', 'SitesController@entitiesSites');
    Route::resource('site', 'SitesController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /**
     * Route for the requests of Components
     */
    Route::get('components', 'ComponentsController@index') ;

    /**
     * Route for the requests of ParameterTypes
     */
    Route::get('orchParameterTypes/list', 'OrchParameterTypesController@index');
    Route::get('orchParameterTypes/getParameterTypesVoteConfig/{voteConfigKey}', 'OrchParameterTypesController@getVoteConfigParameterTypes');
    Route::resource('orchParameterTypes', 'OrchParameterTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of SiteConfGroup
     */
    Route::get('siteConfGroup/list', 'SiteConfGroupController@index');
    Route::resource('siteConfGroup', 'SiteConfGroupController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of SiteConfGroupTranslation
     */
    Route::get('siteConfGroupTranslation/{siteKey}/list', 'SiteConfGroupTranslationController@index');
    Route::resource('siteConfGroupTranslation', 'SiteConfGroupTranslationController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of SiteConfValues
     */
    Route::get('siteConfValues/list', 'SiteConfValuesController@index');
    Route::put('siteConfValues/updateValues', 'SiteConfValuesController@updateValues');

    /**
     * Route for the requests of SiteSiteConfs
     */
    Route::get('SiteSiteConfs/list', 'SiteSiteConfsController@index');
    Route::resource('SiteSiteConfs', 'SiteSiteConfsController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /**
     * Route for the requests of SiteConf
     */
    Route::get('SiteConf/list', 'SiteConfController@index');
    Route::get('SiteConf/list/{groupKey}', 'SiteConfController@listByGroupKey');
    Route::get('SiteConf/{siteKey}/edit', 'SiteConfController@edit');
    Route::resource('SiteConf', 'SiteConfController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of SiteConfGroupTranslation
     */
    Route::get('siteConfTranslation/list', 'SiteConfTranslationController@index');
    Route::resource('siteConfTranslation', 'SiteConfTranslationController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Roles
     */
    Route::get('role/list', 'RolesController@index');
    Route::resource('role', 'RolesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Permissions
     */
    Route::get('entityPermissions/list', 'EntityPermissionController@index');
    Route::Post('entityPermissions', 'EntityPermissionController@store');
    Route::Delete('entityPermissions', 'EntityPermissionController@destroy');




    /**
     * Route for the requests of Permissions
     */
    Route::get('permissions/list', 'PermissionsController@index');
    Route::post('permissions/addPermission', 'PermissionsController@addPermission');
    Route::resource('permissions', 'PermissionsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Layouts
     */
    Route::get('layout/list', 'LayoutsController@index');
    Route::resource('layout', 'LayoutsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of cbParameterTemplate
     */
    Route::get('cbParameterTemplate/list', 'CbParameterTemplatesController@index');
    Route::resource('cbParameterTemplate', 'CbParameterTemplatesController', ['only' => ['show', 'store', 'destroy']]);

    /**
     * Route for the requests of Entity Cbs
     */
    Route::get('entityCb/list', 'EntityCbsController@index');
    Route::resource('entityCb', 'EntityCbsController', ['only' => ['store','destroy']]);

    /**
     * Route for the requests of Entity Cb Templates
     */
    Route::get('entityCbTemplate/list', 'EntityCbTemplatesController@index');
    Route::resource('entityCbTemplate', 'EntityCbTemplatesController', ['only' => ['store','destroy']]);

    /**
     * Route for the requests of  Cb Types
     */
    Route::get('cbTypes/list', 'CbTypesController@index');
    Route::get('cbTypes/cb/{cb_key}', 'CbTypesController@getTypeByCb');
    Route::post('cbTypes/cb', 'CbTypesController@getTypesByCbKeys');


    /**
     * Route for the requests of Home Page Types
     */

    //Route::get('homePageType/list', 'HomePageTypesController@index');
    Route::get('homePageType/groupsList', 'HomePageTypesController@groupsList');

    Route::post('homePageType/groupTypesList', 'HomePageTypesController@groupTypesList');


    Route::post('homePageType/parentsList', 'HomePageTypesController@parentsList');
    Route::resource('homePageType', 'HomePageTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Home Page Configurations
     */

    Route::get('homePageConfiguration/list', 'HomePageConfigurationsController@index');
    Route::get('homePageConfiguration/sitePages/{siteKey}', 'HomePageConfigurationsController@sitePages');
    Route::get('homePageConfiguration/{home_page_configuration_key}/edit', 'HomePageConfigurationsController@edit');
    Route::get('homePageConfiguration/listGroups/{site_key}', 'HomePageConfigurationsController@listGroups');
    Route::get('homePageConfiguration/showGroup/{group_key}', 'HomePageConfigurationsController@showGroup');
    Route::post('homePageConfiguration/storeConfigurations', 'HomePageConfigurationsController@storeGroup');
    Route::get('homePageConfiguration/editGroup/{group_key}', 'HomePageConfigurationsController@editGroup');
    Route::put('homePageConfiguration/updateGroup/{group_key}', 'HomePageConfigurationsController@updateGroup');
    Route::delete('homePageConfiguration/destroyGroup/{group_key}', 'HomePageConfigurationsController@destroyGroup');
    Route::get('homePageConfiguration/siteConfigurations/', 'HomePageConfigurationsController@siteConfigurations');
    Route::resource('homePageConfiguration', 'HomePageConfigurationsController', ['only' => ['show', 'update', 'destroy']]);

    /**
     * Route for the requests of Topic Form Reply
     */
    Route::get('topicQuestionaryReply/getReplyByTopic/{topic_key}', 'TopicQuestionaryReplyController@getReplyByTopic');
    Route::get('topicQuestionaryReply/getTopicByReply/{reply_key}', 'TopicQuestionaryReplyController@getTopicByReply');
    Route::resource('topicQuestionaryReply', 'TopicQuestionaryReplyController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Newsletter Subscriptions
     */
    Route::get('newsletterSubscription/list', 'NewsletterSubscriptionsController@index');
    Route::get('newsletterSubscription/export', 'NewsletterSubscriptionsController@export');
    Route::resource('newsletterSubscription', 'NewsletterSubscriptionsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Entity Modules
     */
    Route::get('entityModule/moduleCode', 'EntityModulesController@getSidebarMenu');
    Route::get('entityModule/{entity_key}', 'EntityModulesController@getActiveEntityModules');
    Route::post('entityModule/setModuleTypeForCurrentEntity', 'EntityModulesController@setModuleTypeForCurrentEntity');
    Route::resource('entityModule', 'EntityModulesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Module Types
     */
    Route::post('moduleType/types', 'ModuleTypesController@getTypes');
    Route::post('moduleType/list', 'ModuleTypesController@index');
    Route::resource('moduleType', 'ModuleTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /**
     * Route for the requests of Parameter User Types
     */
    Route::post('parameterUserType/verifyUniqueParameterUserTypes', 'ParameterUserTypesController@verifyUniqueParameterUserTypes');
    Route::get('parameterUserType/list', 'ParameterUserTypesController@index');
    Route::post('parameterUserType/getParameterUserTypesList', 'ParameterUserTypesController@getParameterUserTypesList');
    Route::get('parameterUserType/{parameter_user_type_key}/edit', 'ParameterUserTypesController@edit');
    Route::resource('parameterUserType', 'ParameterUserTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Parameter User Options
     */
    Route::get('parameterUserOption/list', 'ParameterUserOptionsController@index');
    Route::get('parameterUserOption/{parameter_user_option_key}/edit', 'ParameterUserOptionsController@edit');
    Route::resource('parameterUserOption', 'ParameterUserOptionsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Social Networks
     */
    Route::resource('socialNetwork', 'SocialNetworksController');

    /**
     * Route for the requests of Status Logins
     */
    Route::get('statusLogin/list', 'StatusLoginsController@index');
    Route::resource('statusLogin', 'ParameterUserTypesController', ['only' => ['store']]);

    /**
     * Route for the requests of Login Levels
     */
    Route::post('level/{level_parameter_key}/updateParameters', 'LevelParametersController@updateLoginLevelParameters');
    Route::put('level/reorder', 'LevelParametersController@updateLoginLevelPositions');
    Route::get('level/list', 'LevelParametersController@index');
//    Route::get('level/siteLevels', 'LevelParametersController@siteLoginLevels');
    Route::get('level/{level_parameter_key}/levelParameters', 'LevelParametersController@loginLevelParameters');
    Route::get('level/usersToModerate', 'LevelParametersController@listUsersToModerate');
    Route::get('level/siteUsersToModerate', 'LevelParametersController@siteUsersToModerate');
    Route::resource('level', 'LevelParametersController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /**
     * Route for the requests of Site Ethics
     */
    Route::post('siteEthic/{site_ethic_key}/activateVersion', 'SiteEthicsController@activateVersion');
    Route::post('siteEthic/{site_ethic_key}', 'SiteEthicsController@show');
    Route::resource('siteEthic', 'SiteEthicsController', ['only' => ['store','update','destroy']]);

    /**
     * Route for the requests of Messages
     */
    Route::post('message/markAsUnseen', 'MessageController@markAsUnseen');
    Route::get('message/list', 'MessageController@index');
    Route::get('message/messagesFrom/{user_key}', 'MessageController@messagesFrom');
    Route::get('message/{message_key}/viewed', 'MessageController@viewed');
    Route::get('message/usersWithMessages2', 'MessageController@usersWithMessages2');
    Route::get('message/usersWithMessages', 'MessageController@usersWithMessages');
    Route::post('message/markAsSeen', 'MessageController@markAsSeen');
    Route::post('message/sendToAll','MessageController@sendToAll');

    Route::resource('message', 'MessageController', ['only' => ['show', 'store', 'destroy']]);
});

/**
 * Auth module routes
 */

Route::group(['middleware' => ['authOne']], function () {
    Route::post('auth/verifyUniqueParameter', 'UsersController@verifyUniqueParameter');
    Route::post('auth/setPublicParameter', 'UsersController@setPublicParameter');
    Route::post('auth/storeId', 'UsersController@storeByID');
    Route::post('auth/recover', 'UsersController@recoverPassword');
    Route::post('auth/updatePassword', 'UsersController@updatePassword');
    Route::post('auth/storeSocial', 'UsersController@storeSocial');
    Route::post('auth/authenticateSocial', 'UsersController@authenticateSocial');
    Route::post('auth/registerFacebookAccount', 'UsersController@registerFacebookAccount');
    Route::get('auth/removeFacebookAccount', 'UsersController@removeFacebookAccount');
    Route::post('auth/authenticate', 'UsersController@authenticateUser');
    Route::post('auth/authenticateRFID', 'UsersController@authenticateRFID');
    Route::post('auth/authenticateAlphanumeric', 'UsersController@authenticateAlphanumeric');
    Route::post('auth/getUserList', 'UsersController@getUserList');
    Route::post('auth/usersToModerate2', 'UsersController@getUsersToModerate2');
    Route::post('auth/usersToModerate', 'UsersController@getUsersToModerate');
    Route::post('auth/authenticateUserKey', 'UsersController@authenticateUserkey');
    Route::get('auth/validate', 'UsersController@validateUser');
    Route::get('auth/getUser', 'UsersController@getUser');
    Route::get('auth/searchEmail', 'UsersController@searchEmail');
    Route::get('auth/emailExists', 'UsersController@emailExists');
    Route::get('auth/getSmsToken', 'UsersController@getSmsToken');
    Route::post('auth/setSmsAttempt', 'UsersController@setSmsAttempt');
    Route::post('auth/resetNumberSms', 'UsersController@resetNumberSms');
    Route::post('auth/deleteUserParameters', 'UsersController@deleteUserParameters');
    Route::post('auth/validateSmsToken', 'UsersController@validateSmsToken');
    Route::post('auth/getUserAccordingToFields','UsersController@getUserAccordingToFields');
    Route::get('auth/list', 'UsersController@index');
    Route::post('auth/listUserConfirmed', 'UsersController@getListUsersConfirmed');
    Route::post('auth/listUser', 'UsersController@getListUsers');
    Route::post('auth/listNames', 'UsersController@getListNames');
    Route::post('auth/publicListNames', 'UsersController@getPublicListNames');
    Route::post('auth/analyticsListNames', 'UsersController@getAnalyticsListNames');
    Route::get('auth/logout', 'UsersController@logoutUser');
    Route::get('auth/confirm/{code}', 'UsersController@confirmEmail');
    Route::get('auth/manuallyConfirmUserEmail', 'UsersController@manuallyConfirmUserEmail');
    Route::get('auth/manuallyConfirmUserSms', 'UsersController@manuallyConfirmUserSms');
    Route::post('user/generateUniqueKey', 'UsersController@generateUniqueKey');
    Route::post('user/verifyUniqueKey', 'UsersController@verifyUniqueKey');
    Route::resource('auth', 'UsersController', ['only' => ['show', 'store', 'update', 'destroy']]);

    Route::get('getUserParameters/{userKey}', 'UsersController@getUserParameters');
});


/**
 * CM module routes
 */

Route::group(['middleware' => ['authOne']], function () {
    /*
    |--------------------------------------------------------------------------
    | Contents Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Contents» group to every route
    | it contains.
    |
    */
    Route::delete('content/{content_key}/file/{file_id}', 'ContentsController@deleteFile');
    Route::post('content/listContent', 'ContentsController@listContent');
    Route::post('content/{content_key}/edit/', 'ContentsController@edit');
    Route::post('content/{content_key}/file/', 'ContentsController@addFile');
    Route::put('content/{content_key}/orderfile/{file_id}', 'ContentsController@orderFile');
    Route::put('content/{content_key}/file/{file_id}', 'ContentsController@updateFileDetails');
    Route::put('content/{content_key}/publish/', 'ContentsController@publish');
    Route::put('content/{content_key}/unpublish/', 'ContentsController@unpublish');
    Route::put('content/{content_id}/version/{version}/enable/', 'ContentsController@enable');
    Route::get('content/{content_key}/file/{file_id}', 'ContentsController@getFileDetails');
    Route::get('content/{content_key}/files/{type_id?}', 'ContentsController@getFiles');
    Route::get('content/{content_key}/firstfiles/{type_id?}', 'ContentsController@getFirstFiles');
    Route::get('content/list/{type_id}', 'ContentsController@index');
    Route::get('content/newslist/', 'ContentsController@getNewsList');
    Route::get('content/newsids/', 'ContentsController@getNewsIds');
    Route::get('content/presentnews/', 'ContentsController@getPresentNews');
    Route::post('content/contentsByKeyWithType/','ContentsController@getContentsByKeyWithType');
    Route::post('content/contentsByKey/', 'ContentsController@getContentsByKey');
    Route::post('content/activeContentsByKey/', 'ContentsController@getActiveContentKeys');
    Route::get('content/lastnews/', 'ContentsController@getLastNews');
    Route::get('content/eventslist/', 'ContentsController@getEventsList');
    Route::get('content/eventsids/', 'ContentsController@getEventsIds');
    Route::get('content/lastevents/', 'ContentsController@getLastEvents');
    Route::get('content/{content_key}/version/{version?}', 'ContentsController@showVersion');
    Route::get('content/{content_key}/showVersions/', 'ContentsController@showVersions');
    Route::resource('content', 'ContentsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    Route::get('contentType', 'ContentTypesController@all');
    Route::get('contentType/linkable', 'ContentTypesController@linkable');


    Route::get('contentTypeTypes/listByType/{content_type_code}', 'ContentTypeTypesController@getAllByType');
    Route::get('contentTypeTypes/listByEntity', 'ContentTypeTypesController@listByEntity');
    Route::get('contentTypeTypes/list', 'ContentTypeTypesController@index');
    Route::get('contentTypeTypes/{content_type_type_key}/edit', 'ContentTypeTypesController@edit');
    Route::resource('contentTypeTypes', 'ContentTypeTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /*
    |--------------------------------------------------------------------------
    | Menus Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Menus» group to every route
    | it contains.
    |
    */

    Route::get('menu/get', 'MenusController@get');
    Route::get('menu/list/{access_id?}', 'MenusController@index');
    Route::get('menu/listByAccessId/{access_id?}', 'MenusController@listByAccessId');
    Route::put('menu/reorder/{menu_key}', 'MenusController@reorder');
    Route::get('menu/{menu_key}/sonsList', 'MenusController@sonsList');
    Route::get('menu/{menu_key}/edit/', 'MenusController@edit');
    Route::resource('menu', 'MenusController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /*
    |--------------------------------------------------------------------------
    | Menu Types Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «MenuTypes» group to every route
    | it contains.
    |
    */

    Route::get('menutype/list', 'MenuTypesController@index');
    Route::resource('menutype', 'MenuTypesController', ['only' => ['show']]);


    /*
    |--------------------------------------------------------------------------
    | Texts Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Texts» group to every route
    | it contains.
    |
    */

    Route::post('text/listTexts', 'TextsController@listTexts');
    Route::get('text/list', 'TextsController@index');
    Route::resource('text', 'TextsController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /*
    |--------------------------------------------------------------------------
    | Mails Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Mails» group to every route
    | it contains.
    |
    */

    Route::post('mail/listMails', 'MailsController@listMails');
    Route::get('mail/list', 'MailsController@index');
    Route::resource('mail', 'MailsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Section Type Parameters Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Section Type Parameters» group to every route
    | it contains.
    |
    */

    Route::get('sectionTypeParameter/list', 'SectionTypeParameterController@index');
    Route::resource('sectionTypeParameter', 'SectionTypeParameterController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Section Type Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Section Type» group to every route
    | it contains.
    |
    */

    Route::get('sectionType/list', 'SectionTypeController@index');
    Route::resource('sectionType', 'SectionTypeController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | New Content Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «New Content» group to every route
    | it contains.
    |
    */

    Route::post('newContent/list', 'NewContentController@index');
    Route::get('newContent/getLastOf/{contentType}/{count}','NewContentController@getLastOf');
    Route::get('newContent/{contentKey}/{version?}', 'NewContentController@show');
    Route::post('newContent/{contentKey}/{version}/status', 'NewContentController@toggleActiveStatusOfVersion');
    Route::get('publicNewContent/{contentType}', 'NewContentController@publicIndex');
    Route::get('publicNewContent/{contentType}/{contentKey}', 'NewContentController@publicShow');
    Route::post('publicNewContentCode', 'NewContentController@codeShow');
    Route::get('publicNewContentPreview/{contentCode}/{contentVersion}', 'NewContentController@previewShow');
    Route::resource('newContent', 'NewContentController', ['only' => ['store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Application Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the "web" middleware group to every route
    | it contains. The "web" middleware group is defined in your HTTP
    | kernel and includes session state, CSRF protection, and more.
    |
    */
});


/**
 * CB module routes
 */

Route::group(['middleware' => ['authOne']], function () {

    Route::post('cb/{cb_key}/getTopicsbyVotes', 'CbsController@getTopicsbyVotes');
    Route::post('cb/{cb_key}/publishTechnicalAnalysisResults', 'CbsController@publishTechnicalAnalysisResults');
    Route::get('cb/verifyTemplate', 'CbsController@verifyTemplate');
    Route::post('cb/cbTemplate', 'CbsController@setCbTemplate');
    Route::get('cb/getCbTemplates', 'CbsController@getCbTemplates');
    Route::post('cb/getParticipationInformationForDataTable', 'CbsController@getParticipationInformationForDataTable');
    Route::post('cb/getParticipationInformation', 'CbsController@getParticipationInformation');

    Route::post('cb/{cb_key}/exportTopics', 'CbsController@exportTopics');
    Route::get('cb/{cb_key}/getDataToExport/', 'CbsController@getDataToExport');
    Route::get('cb/{cb_key}/getCbTopicAuthors/', 'CbsController@getCbTopicAuthors');
    Route::get('cb/{cb_key}/getTopicsByCbKey', 'CbsController@getTopicsByCbKey');
    Route::get('cb/exportProposalsToProjects','CbsController@exportProposalsToProjects');
    Route::get('cb/{cb_key}/getCbWithFlags', 'CbsController@getCbWithFlags');
    Route::get('cb/abuses/', 'CbsController@listAllAbuses');
    Route::get('cb/list/', 'CbsController@index');

    Route::get('cb/{cb_key}/eventsList/', 'CbsController@getEventsKeyList');

    Route::post('cb/{cb_key}/addCbNews', 'CbsController@addCbNews');
    Route::get('cb/{cb_key}/getCbNews', 'CbsController@getCbNews');
    Route::get('cb/{cb_key}/getCbChildren', 'CbsController@getCbChildren');
    Route::post('cb/{cb_key}/getCbTopicsByTag', 'CbsController@getCbTopicsByTag');
    Route::delete('cb/{cb_key}/deleteCbNews/{news_key}', 'CbsController@deleteCbNews');
    Route::post('cb/getTopicsByNumber', 'CbsController@getTopicsByNumber');
    Route::post('cb/listCbsWithStatistics/', 'CbsController@listCbsWithStatistics');
    Route::post('cb/listCBs/', 'CbsController@listCBs');
    Route::post('cb/create/', 'CbsController@storeAdvanced');
    Route::put('cb/update/{cb_key}', 'CbsController@updateAdvanced');
    Route::get('cb/{cb_key}/statistics/', 'CbsController@statistics');
    Route::get('cb/{cb_key}/analytics/', 'CbsController@getCbAnalytics');
    Route::post('cb/{cb_key}/moderators/', 'CbsController@addModerator');
    Route::get('cb/{cb_key}/moderators/{user_key}', 'CbsController@isModerator');
    Route::delete('cb/{cb_key}/moderators/{user_key}', 'CbsController@removeModerator');
    Route::get('cb/{cb_key}/moderators/', 'CbsController@moderatorList');
    Route::post('cb/{cb_key}/configurations/', 'CbsController@setConfigurations');
    Route::get('cb/{cb_key}/configurations/', 'CbsController@configurations');
    Route::get('cb/{cb_key}/abuses/', 'CbsController@listAbuses');
    Route::match(['PUT', 'PATCH'], 'cb/{cb_key}/status/', 'CbsController@changeStatus');
    Route::match(['PUT', 'PATCH'], 'cb/{cb_key}/block/', 'CbsController@block');
    Route::match(['PUT', 'PATCH'], 'cb/{cb_key}/unblock/', 'CbsController@unblock');
    Route::get('cb/{cb_key}/topicsWithFirstPost/', 'CbsController@topicsWithFirstPost');
    Route::get('cb/{cb_key}/topicsWithLastPost/', 'CbsController@topicsWithLastPost');
    Route::get('cb/{cb_key}/getAllTopics/', 'CbsController@getAllTopics');
    Route::get('cb/{cb_key}/topicsWithParameters/', 'CbsController@topicsWithParameters');
    Route::get('cb/{cb_key}/topicsKey/', 'CbsController@topicsKey');
    Route::get('cb/{cb_key}/topicsWithBasicData', 'CbsController@getTopicsWithBasicData');
    Route::get('cb/{cb_key}/votes/{vote_key}', 'CbsController@showVote');
    Route::put('cb/{cb_key}/votes/{vote_key}', 'CbsController@updateVote');
    Route::post('cb/{cb_key}/votes/', 'CbsController@addVote');
    Route::get('cb/{cb_key}/votes/', 'CbsController@listVotes');
    Route::delete('cb/{cb_key}/votes/{vote_key}', 'CbsController@removeVote');
    Route::get('cb/{cb_key}/parameters', 'CbsController@parameters');
    Route::post('cb/{cb_key}/parameters', 'CbsController@setParameters');
    Route::delete('cb/{cb_key}/parameters/{parameter_id}', 'CbsController@removeParameters');
    Route::post('cb/{cb_key}/options', 'CbsController@setOptions');
    Route::get('cb/{cb_key}/options', 'CbsController@options');
    Route::get('cb/{cb_key}/getAllUserTopics', 'CbsController@getAllUserTopics');   //TODO - work in progress
    Route::get('cb/{cb_key}/getAllUserFollowingTopics', 'CbsController@getAllUserFollowingTopics');   //TODO - work in progress
    Route::post('cb/{cb_key}/getWithPagination', 'CbsController@getWithPagination');
    Route::post('cb/{cb_key}/getPublicPadInformation', 'CbsController@getPublicPadInformation'); //if this method is changed make the same change in the getMultiplePublicPadsInformation, if it applies there
    Route::post('cb/getMultiplePublicPadsInformation', 'CbsController@getMultiplePublicPadsInformation'); //if this method is changed make the same change in the getPublicPadInformation, if it applies there

    Route::get('cb/{cb_key}/getAllInformation', 'CbsController@getAllInformation');
    Route::post('cb/{cb_key}/getWithTopicKeys', 'CbsController@getWithTopicKeys');
    Route::get('cb/{cb_id}/getCbById', 'CbsController@getCbById');

    Route::get('cb/{cbKey}/switchToNewParameter', 'CbsController@switchToNewParameter');
    Route::get('cb/{cbKey}/finishPhase/{topicCheckpointNewId}', 'CbsController@finishPhase');
    Route::get('cb/{cbKey}/finishPhase2/{topicCheckpointNewId}', 'CbsController@finishPhase2');
    Route::resource('cb', 'CbsController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /* Topics All routes */
    /** ------------------ Routes for topic news ------------------ */

    Route::post('topic/{topic_key}/addTopicNews', 'TopicsController@addTopicNews');
    Route::get('topic/{topic_key}/getTopicNews', 'TopicsController@getTopicNews');
    Route::delete('topic/{topic_key}/deleteTopicNews/{news_key}', 'TopicsController@deleteTopicNews');

    /** ------------------ End routes for topic news ------------------ */


    /** ------------------ Routes for topic cbs ------------------ */
    Route::post('topic/{topic_key}/addTopicCb', 'TopicsController@addTopicCb');
    Route::get('topic/{topic_key}/getTopicCbs', 'TopicsController@getTopicCbs');
    Route::delete('topic/{topic_key}/deleteTopicCb/{cb_key}', 'TopicsController@deleteTopicCb');
    Route::get('topic/{topic_key}/publishUserTopic','TopicsController@publishUserTopic');
    Route::put('topic/{topic_key}/updateTopicVotesInfo','TopicsController@updateTopicVotesInfo');

    /** ------------------ End routes for topic cbs ------------------ */

    Route::post('topic/{topic_key}/activeVersionStatus', 'TopicsController@changeActiveVersionStatus');
    Route::get('topic/{topic_key}/topicVersions', 'TopicsController@getTopicVersions');
    Route::post('topic/ally/respond/{ally_key}', 'TopicsController@allyRequestResponse');
    Route::post('topic/ally/create/{topic_first_key}/{topic_second_key}', 'TopicsController@allyTopics');
    Route::get('topic/ally/show/{ally_key}', 'TopicsController@getAlly');
    Route::get('topic/ally/all','TopicsController@getAllies');

    Route::get('topic/user/{user_key}','TopicsController@getUserTopics');
    Route::get('topic/user/{user_key}/timeline','TopicsController@getUserTopicsTimeline');
    Route::post('topic/user/{user_key}/paginated','TopicsController@getUserTopicsPaginated');

    Route::get('topic/{topic_key}/topicFollowers', 'TopicsController@getTopicFollowers');
    Route::get('topic/{topic_key}/permissions', 'TopicsController@cooperatorPermissions');
    Route::get('topic/cooperators', 'TopicsController@getCooperators');
    Route::get('topic/cooperators/list', 'TopicsController@getCooperatorsList');
    Route::post('topic/topicsWithModeration/', 'TopicsController@topicsWithModeration');
    Route::post('topic/topicsWithModeration/', 'TopicsController@topicsWithModeration');
    Route::post('topic/topicsWithTechnicalEvaluation/', 'TopicsController@topicsWithTechnicalEvaluation');
    Route::post('topic/topicsWithFirstPost/', 'TopicsController@topicsWithFirstPost');
    Route::get('topic/list/{cb_id}', 'TopicsController@index');
    Route::get('topic/listWithFirst/{cb_key}', 'TopicsController@indexWithFirst');
    Route::get('topic/{topic_key}/data', 'TopicsController@data');
    Route::get('topic/{topic_key}/privateDataWithChildsForModal', 'TopicsController@privateDataWithChildsForModal');

    Route::get('topic/{topic_key}/privateDataWithChilds', 'TopicsController@privateDataWithChilds');
    Route::get('topic/{topic_key}/getTopicPostsWithPagination', 'TopicsController@getTopicPostsWithPagination');
    Route::get('topic/{topic_key}/dataWithChilds', 'TopicsController@dataWithChilds');
    Route::post('topic/{topic_key}/cooperators', 'TopicsController@addCooperator');
    Route::delete('topic/{topic_key}/cooperators', 'TopicsController@removeCooperator');
    Route::put('topic/{topic_key}/cooperators', 'TopicsController@updateCooperatorPermission');
    Route::get('topic/{topic_key}/listAbuses/','TopicsController@listAbuses');
    Route::get('topic/{topic_key}/posts', 'TopicsController@posts');
    Route::match(['PUT', 'PATCH'], 'topic/{topic_id}/status/', 'TopicsController@updateStatus');
    Route::get('topic/{topic_key}/statistics/', 'TopicsController@statistics');
    Route::get('topic/{topic_key}/firstPost/', 'TopicsController@getFirstPost');
    Route::get('topic/{topic_key}/block/', 'TopicsController@block');
    Route::get('topic/{topic_key}/unblock/', 'TopicsController@unblock');
    Route::post('topic/{topic_key}/parameters', 'TopicsController@setParameters');
    Route::get('topic/{topic_key}/parameters', 'TopicsController@parameters');
    Route::get('topic/{topic_key}/kioskParameters', 'TopicsController@kioskParameters');
    Route::get('topic/{topic_key}/getTopicUserEmail', 'TopicsController@getTopicUserEmail');
    Route::get('topic/{topic_key}/getTopicStatus', 'TopicsController@getTopicStatus');
    Route::get('topic/{topic_key}/forTimeline', 'TopicsController@getTopicForTimeline');
    Route::post('topic/getTopics', 'TopicsController@getTopics');
    Route::post('topic/getTopicsByParent', 'TopicsController@getTopicsByParent');
    Route::resource('topic', 'TopicsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /* New TopicsController routes */
    Route::resource('padTopic', 'PadTopicsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    Route::post('post/postManagerList/', 'PostsController@postManagerList');
    Route::post('post/postManagerListLastly/', 'PostsController@postManagerListLastly');
    Route::get('post/list/', 'PostsController@index');
    Route::post('post/listThatNeedsApproval', 'PostsController@listThatNeedsApproval');
    Route::get('post/postTimeline', 'PostsController@postTimeline');
    Route::match(['PUT', 'PATCH'], 'post/changeStatus/{post_key}', 'PostsController@changeStatus');
    Route::match(['PUT', 'PATCH'], 'post/{post_key}/blocked/', 'PostsController@storeBlocked');
    Route::put('post/{post_key}/active/', 'PostsController@storeActive');
    Route::get('post/{post_key}/files/list/{type_id?}', 'PostsController@files');
    Route::get('post/{post_key}/filesByType/list/{type_id?}', 'PostsController@filesByType');
    Route::post('post/getTopicsFiles', 'PostsController@getTopicsFiles');
    Route::get('post/{post_key}/files/{file_id}', 'PostsController@getFile');
    Route::post('post/{post_key}/files', 'PostsController@addFile');
    Route::match(['PUT', 'PATCH'], 'post/{post_id}/files/{file_id}', 'PostsController@updateFile');
    Route::delete('post/{post_key}/files/{file_id}', 'PostsController@deleteFile');
    Route::match(['PUT', 'PATCH'], 'post/{post_key}/orderFile/{file_id}', 'PostsController@orderFile');
    Route::get('post/{post_key}/history', 'PostsController@postHistory');
    Route::get('post/{post_key}/listAbuses/', 'PostsController@listAbuses');
    Route::get('post/{post_key}/revertPost/{version}', 'PostsController@revertPost');
    Route::get('post/{post_key}/acceptPost/{version}', 'PostsController@acceptPost');
    Route::resource('post', 'PostsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    Route::post('postlike/like/', 'PostLikesController@like');
    Route::post('postlike/dislike/', 'PostLikesController@dislike');
    Route::get('postlike/list/', 'PostLikesController@index');
    Route::get('postlike/info/{post_id}', 'PostLikesController@info');
    Route::resource('postlike', 'PostLikesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    Route::get('postabuse/listByCb/{cb_id}', 'PostAbusesController@listByCb');
    Route::get('postabuse/listByTopic/{topic_id}', 'PostAbusesController@listByTopic');
    Route::get('postabuse/list/{post_id}', 'PostAbusesController@index');
    Route::resource('postabuse', 'PostAbusesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    Route::get('topicmoderator/list/{topic_id}', 'TopicModeratorsController@index');
    Route::resource('topicmoderator', 'TopicModeratorsController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /* ParameterTypes */
    Route::get('parameterTypes/list', 'ParameterTypesController@index');
    Route::resource('parameterTypes', 'ParameterTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /* UserAnalysis */
    Route::post('userAnalysis/getUserAnalysisStats', 'UserAnalysisController@getUserAnalysisStats');
    Route::post('userAnalysis/getUserAnalysis', 'UserAnalysisController@getUserAnalysis');
    Route::resource('userAnalysis', 'UserAnalysisController', ['only' => [ 'store']]);

    /* Parameters */
    Route::get('parameters/{parameter_id}/options', 'ParametersController@parameterOptions');
    Route::get('parameters/{parameter_id}/edit', 'ParametersController@edit');
    Route::post('parameters/{parameter_id}/options', 'ParametersController@addOption');
    Route::post('parameters/{parameter_id}/optionsMulti', 'ParametersController@addOptions');
    Route::match(['PUT', 'PATCH'],'parameters/{parameter_id}/options/{option_id}', 'ParametersController@editOption');
    Route::delete('parameters/{parameter_id}/options/{option_id}', 'ParametersController@removeOption');
    Route::get('parameters/list', 'ParametersController@index');
    Route::get('parameters/listWithOptions', 'ParametersController@parameters');
    Route::resource('parameters', 'ParametersController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /* PadPermissions */
    Route::get('padPermissions/list', 'PadPermissionsController@index');
    Route::get('padPermissions/getOptionsPermissions', 'PadPermissionsController@getOptionsPermissions');
    Route::resource('padPermissions', 'PadPermissionsController', ['only' => ['show', 'store', 'update', 'destroy']]);


    /* ParameterTemplates */
    Route::get('parameterTemplates/{parameter_template_id}/options', 'ParameterTemplatesController@parameterOptions');
    Route::post('parameterTemplates/{parameter_template_id}/options', 'ParameterTemplatesController@addOption');
    Route::post('parameterTemplates/{parameter_template_id}/optionsMulti', 'ParameterTemplatesController@addOptions');
    Route::match(['PUT', 'PATCH'],'parameters/{parameter_template_id}/options/{template_option_id}', 'ParameterTemplatesController@editOption');
    Route::delete('parameterTemplates/{parameter_template_id}/options/{template_option_id}', 'ParameterTemplatesController@removeOption');
    Route::get('parameterTemplates/list', 'ParameterTemplatesController@index');
    Route::post('parameterTemplates/listWithOptions', 'ParameterTemplatesController@parameters');
    Route::resource('parameterTemplates', 'ParameterTemplatesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /* Topic Reviews */
    Route::get('topicReviews/list/{topicKey}', 'TopicReviewsController@index');
    Route::get('topicReviews/listByType/{topicKey}', 'TopicReviewsController@indexByReviewerType');
    Route::resource('topicReviews', 'TopicReviewsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /* Topic Reviews Replies*/
    Route::get('topicReviewReplies/list/{topicReviewKey}', 'TopicReviewRepliesController@index');
    Route::resource('topicReviewReplies', 'TopicReviewRepliesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /* Topic Reviews Status*/
    Route::get('topicReviewStatus/list', 'TopicReviewStatusController@index');
    Route::resource('topicReviewStatus', 'TopicReviewStatusController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /* Topic Reviews Status Types*/
    Route::get('topicReviewStatusTypes/list', 'TopicReviewStatusTypesController@index');
    Route::get('topicReviewStatusTypes/{statusKey}/edit', 'TopicReviewStatusTypesController@edit');
    Route::resource('topicReviewStatusTypes', 'TopicReviewStatusTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /* Topic Followers*/
    Route::get('topicFollowers/list', 'TopicFollowersController@index');
    Route::get('topicFollowers/{topic_key}/getFollowers', 'TopicFollowersController@getFollowers');
    Route::resource('topicFollowers', 'TopicFollowersController', ['only' => [ 'store', 'destroy']]);

    /* Field Types */
    Route::get('fieldTypes/list', 'FieldTypesController@index');
    Route::resource('fieldTypes', 'FieldTypesController');

    /* Param Add Fields */
    Route::get('paramAddFields/list', 'ParamAddFieldsController@index');
    Route::resource('paramAddFields', 'ParamAddFieldsController');

    /*
    |--------------------------------------------------------------------------
    | Configurations Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Configurations» group to every route
    | it contains.
    |
    */
    //Route::post('configuration/addType', 'ConfigurationsController@addType');
    //Route::delete('configuration/removeType/{id}', 'ConfigurationsController@removeType');
    //Route::post('configuration/addOption/{configuration_id}', 'ConfigurationsController@addOption');
    //Route::delete('configuration/removeOption/{id}', 'ConfigurationsController@removeOption');
    Route::get('configuration/getConfigurationOptions', 'ConfigurationsController@getConfigurationOptions');
    Route::get('configuration/list', 'ConfigurationsController@index');
    Route::get('configuration/{id}/edit', 'ConfigurationsController@edit');
    Route::resource('configuration', 'ConfigurationsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Configuration Types Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Configuration Types» group to every route
    | it contains.
    |
    */


    Route::get('configurationType/list', 'ConfigurationTypesController@index');
    Route::get('configurationType/{id}/showTypeConfigurations', 'ConfigurationTypesController@showTypeConfigurations');
    Route::get('configurationType/{id}/edit', 'ConfigurationTypesController@edit');
    Route::resource('configurationType', 'ConfigurationTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Configuration Options Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Configuration Types» group to every route
    | it contains.
    |
    */

    Route::get('configurationOption/list', 'ConfigurationOptionsController@index');
    Route::get('configurationOption/{id}/edit', 'ConfigurationOptionsController@edit');
    Route::resource('configurationOption', 'ConfigurationOptionsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Vote Configurations Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Vote Configurations» group to every route
    | it contains.
    |
    */

    Route::get('voteConfigurations/list', 'VoteConfigurationsController@index');
    Route::get('voteConfigurations/{voteConfigurationKey}/edit', 'VoteConfigurationsController@edit');
    Route::resource('voteConfigurations', 'VoteConfigurationsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Status Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Status» group to every route
    | it contains.
    |
    */

    Route::get('status/list', 'StatusController@index');
    Route::get('status/{topicKey}/history', 'StatusController@history');
    Route::get('status/{statusKey}/edit', 'StatusController@edit');
    Route::resource('status', 'StatusController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Status Types Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Status Types» group to every route
    | it contains.
    |
    */

    Route::get('statusTypes/list', 'StatusTypesController@index');
    Route::get('statusTypes/{statusKey}/edit', 'StatusTypesController@edit');
    Route::resource('statusTypes', 'StatusTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Comments Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Comments» group to every route
    | it contains.
    |
    */

    Route::get('comments/list', 'CommentsController@index');
    Route::get('comments/{commentKey}/edit', 'CommentsController@edit');
    Route::resource('comments', 'CommentsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    | Annotations Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Annotations» group to every route
    | it contains.
    |
    */

    Route::get('annotation/tags', 'AnnotationsController@getTags');
    Route::resource('annotation', 'AnnotationsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    |  Technical Analysis Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Technical Analysis Routes» group to every route
    | it contains.
    |
    */

    Route::post('technicalAnalysis/{technicalAnalysisKey}/sendNotification', 'TechnicalAnalysesController@sendNotification');
    Route::get('technicalAnalysis/topic/{topic_key}', 'TechnicalAnalysesController@show');
    Route::get('technicalAnalysis', 'TechnicalAnalysesController@index');
    Route::put('technicalAnalysis/activate/{topic_key}','TechnicalAnalysesController@activate');
    Route::get('technicalAnalysis/TopicTechnicalAnalysis/{topic_key}', 'TechnicalAnalysesController@getTechnicalAnalysis');
    Route::resource('technicalAnalysis', 'TechnicalAnalysesController', ['only' => ['store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    |  Technical Analysis Question Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Technical Analysis Question Routes» group to every route
    | it contains.
    |
    */
    Route::get('technicalAnalysisQuestions/questionsAndExistenceOfTechnicalAnalysis/{cb_key}', 'TechnicalAnalysisQuestionsController@questionsAndExistenceOfTechnicalAnalysis');
    Route::get('technicalAnalysisQuestions/list/{cb_key}', 'TechnicalAnalysisQuestionsController@index');
    Route::resource('technicalAnalysisQuestions', 'TechnicalAnalysisQuestionsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    |  Post Comment Type Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Post Comment Type Routes» group to every route
    | it contains.
    |
    */
    Route::resource('postCommentTypes', 'PostCommentTypesController', ['only' => ['show', 'store', 'update', 'destroy']]);


    Route::get('cb/{cb_key}/configurationPermissions', 'ConfigurationPermissionsController@configurationPermissions');
    Route::get('configurationPermission/list', 'ConfigurationPermissionsController@index');
    Route::get('configurationPermission/insertConfigurationPermission', 'ConfigurationPermissionsController@insertConfigurationPermission');
    Route::get('configurationPermission/updateConfigurationPermission', 'ConfigurationPermissionsController@updateConfigurationPermission');


    Route::get('configurationPermissionType/list', 'ConfigurationPermissionTypesController@index');


    Route::post('loginLevel/{login_level_key}/updateLoginLevelParameters', 'LoginLevelsController@updateLoginLevelParameters');
    Route::get('loginLevel/manualListUsers', 'LoginLevelsController@manualListUsers');
    Route::get('loginLevel/list', 'LoginLevelsController@index');
    Route::get('loginLevel/{login_level_key}/getLoginLevelParameters', 'LoginLevelsController@getLoginLevelParameters');
    Route::resource('loginLevel', 'LoginLevelsController', ['only' => ['show','store','update','destroy']]);

    /*
    |--------------------------------------------------------------------------
    |  User Login Levels Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «User Login Levels Routes» group to every route
    | it contains.
    |
    */

    Route::get('user/{user_key}/loginLevels', 'OrchUsersController@loginLevels');
    Route::get('user/{user_key}/userLoginLevels', 'OrchUsersController@userLoginLevels');
    Route::get('user/{user_key}/userLoginLevelsVotes', 'OrchUsersController@userLoginLevelsVotes');
    Route::post('user/{user_key}/smsCheckLoginLevel', 'OrchUsersController@smsCheckLoginLevel');
    Route::post('user/{user_key}/manualCheckLoginLevel', 'OrchUsersController@manualCheckLoginLevel');
    Route::post('user/autoCheckLoginLevel', 'OrchUsersController@autoCheckLoginLevel');
    Route::post('user/updateUserLoginLevels','OrchUsersController@updateUserLoginLevels');
    Route::post('user/autoUpdateEntityUsersLoginLevels','OrchUsersController@autoUpdateEntityUsersLoginLevels');
    Route::post('user/{user_key}/manualGrantLoginLevel','OrchUsersController@manualGrantLoginLevel');
    Route::post('user/{user_key}/manualRemoveLoginLevel','OrchUsersController@manualRemoveLoginLevel');

    /*
    |--------------------------------------------------------------------------
    |  Technical Analysis Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Technical Analysis Routes» group to every route
    | it contains.
    |
    */

    Route::get('technicalAnalysisQuestions/list/{cb_key}', 'TechnicalAnalysisQuestionsController@index');
    Route::get('technicalAnalysisQuestions/{technical_analysis_question_key}/edit', 'TechnicalAnalysisQuestionsController@edit');
    Route::get('technicalAnalysisQuestions/questionsAndExistenceOfTechnicalAnalysis/{cb_key}', 'TechnicalAnalysisQuestionsController@questionsAndExistenceOfTechnicalAnalysis');
    Route::resource('technicalAnalysisQuestions', 'TechnicalAnalysisQuestionsController', ['only' => ['show', 'store', 'update', 'destroy']]);

    /*
    |--------------------------------------------------------------------------
    |  Flag Type Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Flag Types Routes» group to every route
    | it contains.
    |
    */
    /*Route::Delete('flagTypes/{flagTypeId}', 'FlagTypesController@destroy');*/
    Route::get('flagTypes/list', 'FlagTypesController@index');
    Route::resource('flagTypes', 'FlagTypesController',['only' => ['show', 'store', 'update','destroy']]);


    /*
    |--------------------------------------------------------------------------
    |  Flags Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «Flags Routes» group to every route
    | it contains.
    |
    */
    Route::post('flags/getElementFlagHistory', 'FlagsController@getElementFlagHistory');
    Route::post('flags/attachFlag', 'FlagsController@attachFlag');
    Route::get('flags/{cbKey}/getFlagsFromCb', 'FlagsController@getFlagsFromCb');
    Route::resource('flags', 'FlagsController',['only' => ['show', 'store', 'update', 'destroy']]);

    Route::get('cbQuestionnaires/list', 'CbQuestionnariesController@index');
    Route::get('cbQuestionnaire/getCbQuestionnaire', 'CbQuestionnariesController@getCbQuestionnaire');
    Route::put('cbQuestionnaire/setCbQuestionnaire', 'CbQuestionnariesController@setCbQuestionnaire');
    Route::get('cbQuestionnaire/setQuestionnaireTemplate', 'CbQuestionnariesController@setQuestionnaireTemplate');
    Route::get('cbQuestionnaire/getCbQuestionnaireTemplate', 'CbQuestionnariesController@getCbQuestionnaireTemplate');

    Route::get('cbQuestionnaireUser/getUserIgnoredQuestionnaires', 'CbQuestionnariesController@getUserIgnoredQuestionnaires');
    Route::post('cbQuestionnaireUser/getCbQuestionnaireUser', 'CbQuestionnariesController@getCbQuestionnaireUser');
    Route::get('cbQuestionnaireUser/setCbQuestionnaireUser', 'CbQuestionnariesController@setCbQuestionnaireUser');

    Route::get('cbQuestionnaireUser/getUserIgnoredQuestionnaires', 'CbQuestionnariesController@getUserIgnoredQuestionnaires');



/**************************************************************************************** New Component Routes ****************************************************************************************/
    Route::get('accountRecovery/isActive','AccountRecoveryController@isActive');
    Route::get('accountRecovery/getParametersForForm','AccountRecoveryController@getParametersForForm');
    Route::post('accountRecovery/validate','AccountRecoveryController@validateRecoveryRequest');
    Route::post('accountRecovery/recover','AccountRecoveryController@recoverAccount');
    Route::resource('accountRecovery','AccountRecoveryController');

    /*
    |--------------------------------------------------------------------------
    |  DashBoard Elements Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «DashBoard Elements Routes» group to every route
    | it contains.
    |
    */
    Route::put('dashBoardElements/reorderUserDashBoardElements', 'DashBoardElementsController@reorderUserDashBoardElements');
    Route::get('dashBoardElements/list', 'DashBoardElementsController@index');
    Route::get('dashBoardElements/getEntityDashBoardElements', 'DashBoardElementsController@getEntityDashBoardElements');
    Route::get('dashBoardElements/getAvailableDashBoardElementsWithConfigurations', 'DashBoardElementsController@getAvailableDashBoardElementsWithConfigurations');
    Route::delete('dashBoardElements/unsetUserDashBoardElement', 'DashBoardElementsController@unsetUserDashBoardElement');
    Route::put('dashBoardElements/updateUserDashBoardElement/{id}', 'DashBoardElementsController@updateUserDashBoardElement');
    Route::get('dashBoardElements/setUserDashBoardElement', 'DashBoardElementsController@setUserDashBoardElement');
    Route::get('dashBoardElements/{dashBoardElementId}/updateEntityDashBoardElements', 'DashBoardElementsController@updateEntityDashBoardElements');
    Route::resource('dashBoardElements', 'DashBoardElementsController',['only' => ['show', 'store', 'update','destroy']]);

    /*
    |--------------------------------------------------------------------------
    |  DashBoard Element Configurations Routes
    |--------------------------------------------------------------------------
    |
    | This route group applies the «DashBoard Element Configurations Routes» group to every route
    | it contains.
    |
    */
    Route::get('dashBoardElementConfigurations/list', 'DashBoardElementConfigurationsController@index');
    Route::resource('dashBoardElementConfigurations', 'DashBoardElementConfigurationsController',['only' => ['show', 'store', 'update','destroy']]);
    /* ----- CB Menu Translations ----- */
    Route::post('cbMenuTranslation/storeOrUpdate', 'CbMenuTranslationsController@storeOrUpdate');
    Route::post('cbMenuTranslation/getEntityCbsWithTranslations', 'CbMenuTranslationsController@getEntityCbsWithTranslations');
    Route::get('cbMenuTranslation/{cbKey}/list', 'CbMenuTranslationsController@index');
    Route::get('cbMenuTranslation/{cbKey}/isCodeUsed/{code}', 'CbMenuTranslationsController@isCodeUsed');
    Route::delete('cbMenuTranslation/{cbKey}/{code}', 'CbMenuTranslationsController@delete');
    Route::post('cbMenuTranslation/{cbKey}/copy', 'CbMenuTranslationsController@copyFromAnotherCB');

    Route::get('cbTranslation/getCbTranslations', 'CbTranslationsController@index');
    Route::post('cbTranslation/storeOrUpdate', 'CbTranslationsController@storeOrUpdate');
    Route::post('cbTranslation/delete', 'CbTranslationsController@delete');
    Route::post('cbTranslation/getCbEntity', 'CbTranslationsController@getCbEntity');
    Route::post('cbTranslation/storeCodeAdminOrManager', 'CbTranslationsController@storeCodeAdminOrManager');
    Route::get('cbTranslation/translation', 'CbTranslationsController@translation');
    Route::get('cbTranslation/getCode', 'CbTranslationsController@getCode');
    Route::get('cbTranslation/getStatusTranslations', 'CbTranslationsController@getStatusTranslations');

    /***** Entity Messages ******/
    Route::get('entityMessages/getEntityMessages', 'EntityMessagesController@getEntityMessages');
    Route::resource('entityMessages', 'EntityMessagesController',['only' => ['index']]);


    /*
    |--------------------------------------------------------------------------
    |  Dynamic BackOffice Menu Routes
    |--------------------------------------------------------------------------
    */
    Route::get('beMenuElementParameters/list', 'BEMenuElementParametersController@index');
    Route::resource('beMenuElementParameters', 'BEMenuElementParametersController',['only' => ['show', 'store', 'update', 'destroy']]);

    Route::get('beMenuElements/list', 'BEMenuElementsController@index');
    Route::resource('beMenuElements', 'BEMenuElementsController',['only' => ['show', 'store', 'update', 'destroy']]);

    Route::get('beMenu/list', 'BEMenuController@index');
    Route::get('beMenu/import', 'BEMenuController@import');
    Route::get('beMenu/renderData', 'BEMenuController@menuRenderData');
    Route::put('beMenu/reorder/{key}', 'BEMenuController@reorder');
    Route::resource('beMenu', 'BEMenuController',['only' => ['show', 'store', 'update', 'destroy']]);

    Route::get('actions/list', 'ActionController@index');
    Route::resource('actions', 'ActionController',['only'=>['show']]);


    /* Operation Type routes */
    Route::resource('operationTypes', 'OperationTypeController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    /* Operation Action routes */
    Route::resource('operationActions', 'OperationActionController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

    /* Cb Operation Schedule routes */
    Route::get('cbOperationSchedules/getCbSchedules/{cbKey}', 'CbOperationScheduleController@getCbSchedules');

    Route::post('cbOperationSchedules/verifyScheduleExternal', 'CbOperationScheduleController@verifyScheduleExternal');

    Route::resource('cbOperationSchedules', 'CbOperationScheduleController', ['only' => ['show', 'store', 'update', 'destroy']]);
    
    
    /* Short Links */
    Route::get('shortLinks/list', 'ShortLinksController@index');
    Route::get('shortLinks/resolve/{shortLinkCode}', 'ShortLinksController@resolve');
    Route::resource('shortLinks', 'ShortLinksController', ['only' => ['show', 'store', 'update', 'destroy']]);
});
