<?php

namespace App\Models\Empatia\Cbs;

use App\Models\Backend\CMS\Content;
use App\Models\User;
use App\Objects\Empatia\CbParameter;
use App\Traits\Auditable;
use App\Traits\HasSushiModels;
use App\Traits\Versionable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Cb extends Model
{
    use HasFactory, SoftDeletes, Auditable, Versionable, HasSushiModels;

    protected $sushiModels = [
        OperationSchedule::class => 'data.configurations.operation_schedules',
        TechnicalAnalysisQuestion::class => 'data.configurations.topic.technical_analysis.questions'
    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empatia_cbs';

    protected $effectiveVersions = false;

    protected $guarded = [
        'site',
        'id',
        'updated_at',
        'updated_by',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $casts = [
        'versions' => 'object',
        'title' => 'object',
        'content' => 'object',
        'slug' => 'object',
        'parameters' => 'object',
        'data' => 'object',
    ];

    protected $fillable = ['data'];

    public function topics() {
        return $this->hasMany(Topic::class);
    }

    public function getTableTopics(string $code = null){
        if( empty($code) ){
            return collect();
        }

        $tables = getField($this->data, 'configurations.tables', []);
        $table = getField($tables, $code, []);

        return Topic::whereIn('id', getField($table, 'topics', []))->get();
    }

    /**
     * Finds whether the cb is ongoing
     *
     * @return bool
     */
    public function isOngoing() : bool
    {
        $now = now();
        $startDate  = !empty($startDate = data_get($this, 'start_date')) ? carbon($startDate) : null;
        $endDate    = !empty($endDate = data_get($this, 'end_date')) ? carbon($endDate) : null;

        // if not started yet
        if( empty($startDate) || $startDate > $now ){
            return false;
        }

        // if already ended
        if( !empty($endDate) && $endDate < $now ){
            return false;
        }

        // at this point, it's certain the cb is ongoing

        return true;
    }

    /**
     * Finds whether the cb is has already ended
     *
     * @return bool
     */
    public function hasEnded() : bool
    {
        $now = now();
        $startDate  = !empty($startDate = data_get($this, 'start_date')) ? carbon($startDate) : null;
        $endDate    = !empty($endDate = data_get($this, 'end_date')) ? carbon($endDate) : null;

        // if not started yet
//        if( empty($startDate) || $startDate > $now ){
//            return false;
//        }

        // if already ended
        if( !empty($endDate) && $endDate < $now ){
            return true;
        }

        return false;
    }

    /**
     * Finds whether the topic create phase is ongoing
     *
     * @return bool
     */
    public function isTopicCreateOngoing() : bool
    {
        $settings = data_get($this, 'data.configurations.topic.create');
        if( empty($settings) ){
            return false;
        }

        $now = now();
        $startDate  = !empty($startDate = data_get($settings, 'start_date')) ? carbon($startDate) : $startDate;
        $endDate    = !empty($endDate = data_get($settings, 'end_date')) ? carbon($endDate) : $endDate;

        // if not started yet
        if( empty($startDate) || $startDate > $now ){
            return false;
        }

        // if already ended
        if( !empty($endDate) && $endDate < $now ){
            return false;
        }

        // at this point, it's certain the phase is ongoing

        return true;
    }

    public function canShowInFrontend() : bool
    {
        // TODO: develop this function

        if( $this->isOngoing() ){
            return true;
        }
        if( true ){
            return true;
        }

        return false;
    }

    public function parameter(string $code) : mixed
    {
        return $this->getParameterByCode($code);
    }

    public function getParameterByCode(string $code) : mixed
    {
        $parameter = $this->getParameterByProperty('code', $code);
        return new CbParameter($parameter);
    }

    public function getParameterByProperty(string $key, string $value) : mixed
    {
        foreach ($this->parameters ?? [] as $parameter) {
            if ($parameter->{$key} == $value) {
                return $parameter;
            }
        }

        return null;
    }

    public function getParameters(array $properties = null, bool $preserveKeys = true) : array
    {
        $parameters = [];

        foreach ($this->parameters ?? [] as $key => $parameter) {
            $skip = false;

            foreach ($properties ?? [] as $propertyKey => $value){
                if( data_get($parameter, $propertyKey) != $value ){
                    $skip = true;
                    break;
                }
            }

            if( $skip ) continue;

            if( $preserveKeys ){
                $parameters[$key] = new CbParameter($parameter);
            } else {
                $parameters[] = new CbParameter($parameter);
            }
        }

        return $parameters;
    }

    // Sorts the topics collection for winners to be first
    public function topicsByWinningStatus(string $order = 'asc') : mixed {
        $order = in_array(strtolower($order), ['asc', 'desc']) ? $order : 'asc';

        $method = \Str::camel('sort_by'.($order == 'asc' ? '' : "_$order"));

        return $this->topics->{$method}(function ($topic) {
            return !data_get($topic, 'data.winner', false);
        });
    }

    // TODO: implement and use phases instead
    public function getShowContent() : ?Content
    {
        $contentCode = data_get($this, 'data.configurations.cms.cb_show.code');

        if( !empty($contentCode) ){
            return Content::whereCode($contentCode)->first();
        }

        return null;
    }


    public static function byCode(string $code): ?Cb
    {
        try {
            if (is_string($code) && $code != "") {
                return Cb::whereCode($code)->first();
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError('cb not found: ' . json_encode($e->getMessage()));
        }
        return null;
    }

    public function previousTopic(int $topicId, bool $loop = false): ?Topic
    {
        $topicIndex = $this->topics->search(function ($item) use ($topicId) {
            return $item->id === $topicId;
        });
        $previewTopicIndex = null;
        if (is_numeric($topicIndex)) {
            if (($topicIndex - 1) >= 0) {
                $previewTopicIndex = $topicIndex - 1;
            } elseif ($loop) {
                $previewTopicIndex = $this->topics->count() - 1;
            }
            return $this->topics->get($previewTopicIndex);
        }
        return null;
    }

    public function nextTopic(int $topicId, bool $loop = false): ?Topic
    {
        $topicIndex = $this->topics->search(function ($item) use ($topicId) {
            return $item->id === $topicId;
        });
        $nextTopicIndex = null;
        if (is_numeric($topicIndex)) {
            if (($topicIndex + 1) < $this->topics->count()) {
                $nextTopicIndex = $topicIndex + 1;
            } elseif($loop) {
                $nextTopicIndex = 0;
            }
            return $this->topics->get($nextTopicIndex);
        }
        return null;
//        return route('page', [\App\Helpers\Empatia\Cbs\HCb::getCbTypeSlug( getField($this, 'type') ) , getField($this, 'slug.'.getLang(), '') . '/' . getField($topics[$nextTopicIndex], 'slug.'.getLang(), $this->type)]);
    }


    /**   ACTIONS   **/
    public function featureAction(string $feature, string $code) : ?object
    {
        return data_get($this, "data.configurations.$feature.actions.$code");
    }

    public function topicActions() : ?object
    {
        return data_get($this, 'data.configurations.topic.actions');
    }

    public function topicAction(string $code) : ?object
    {
        if( empty($code) ) return null;

        return data_get($this, "data.configurations.topic.actions.$code");
    }

    public function voteActions() : ?object
    {
        return data_get($this, 'data.configurations.vote.actions');
    }

    public function voteAction(string $code) : ?object
    {
        if( empty($code) ) return null;

        return data_get($this, "data.configurations.vote.actions.$code");
    }

    /**
     * Checks whether an action is enabled.
     * @param string $feature   The Cb feature the action belongs to
     * @param string $code      The action code
     * @return bool
     */
    public function isActionEnabled(string $feature, string $code) : bool
    {
        $action = $this->featureAction($feature, $code);
        if( empty($action) ) return false;

        return data_get($action, 'enabled') === true;
    }

    /**
     * Checks whether an action is ongoing.
     * @param string $feature   The Cb feature the action belongs to
     * @param string $code      The action code
     * @return bool
     */
    public function isActionOngoing(string $feature, string $code) : bool
    {
        $action = $this->featureAction($feature, $code);
        if( empty($action) ) return false;

        return $this->isOperationScheduleOngoing(data_get($action, 'operation_schedule', ''));
    }

    /**
     * Checks whether an action is enabled, ongoing and has its schedule active.
     * @param string $feature   The Cb feature the action belongs to
     * @param string $code      The action code
     * @return bool
     */
    public function isActionActive(string $feature, string $code) : bool
    {
        $action = $this->featureAction($feature, $code);
        if( empty($action) ) return false;

        return $this->isActionEnabled($feature, $code) &&
            $this->isActionOngoing($feature, $code) &&
            $this->isOperationScheduleActive(data_get($action, 'operation_schedule', ''));
    }

    /**   AUTHORIZATION   **/

    public function requiresAuthenticationForTopicAction(string $action) : bool
    {
        $actionSetting = data_get($this, "data.configurations.topic.actions.$action.authorization.authentication_required");

        return $actionSetting !== false;
    }

    /**
     * Checks whether a topic action is authorized.
     * @param string $action
     * @param User|null $user
     * @return bool
     */
    public function isTopicActionAuthorized(string $action, User $user = null) : bool
    {
        $user = $user ?? User::find(Auth::user()?->id ?? 0);

        $topicActions = data_get($this, 'data.configurations.topic.actions', []);
        $targetAction = data_get($topicActions, $action);

        // validate action
        if( empty($targetAction) ){
            return false;
        }

        // validate authentication
        // WARNING: overrides login levels and roles
        if( $this->requiresAuthenticationForTopicAction($action) && empty($user) ){
            return false;
        }

        // cb ongoing
        if( !$this->isOngoing() && !data_get($targetAction, 'authorization.override_cb_schedule', false) ){
            return false;
        }
        // action active
        if( !$this->isActionActive('topic', $action) ){
            return false;
        }

        // phase enabled & ongoing?

        // validate login levels
        $actionLoginLevels = data_get($targetAction, 'authorization.login_levels', []);
        if( !empty($user) && !$user->hasAllLLs($actionLoginLevels) ){
            session()->flash('login-levels-required', ['missing-details']);
            // TODO: refactor this; maybe a dynamic redirect based on config?
            app('redirect')->setIntendedUrl( route('profile.edit', ['tab' => 'details']) );
            return false;
        }

        // TODO: validate roles

        return true;
    }

    /**   OPERATION SCHEDULES   **/

    /**
     * Gets the operation schedules object from the Cb configurations.
     */
    public function operationSchedules() : ?object
    {
        return data_get($this, 'data.configurations.operation_schedules');
    }

    /**
     * Gets an operation schedule from the Cb phases.
     */
    public function operationSchedule(string $code) : ?object
    {
        if( empty($code) ){
            return null;
        }

        return data_get($this->operationSchedules(), $code);
    }

    /**
     * Checks whether an operation schedule is enabled.
     * @param string $code  The operation schedule code
     * @return bool
     */
    public function isOperationScheduleEnabled(string $code) : bool
    {
        $schedule = $this->operationSchedule($code);
        if( empty($schedule) ) return false;

        return data_get($schedule, 'enabled') === true;
    }

    /**
     * Checks whether an operation schedule is ongoing.
     * @param string $code  The operation schedule code
     * @return bool
     */
    public function isOperationScheduleOngoing(string $code) : bool
    {
        $schedule = $this->operationSchedule( $code );
        if( empty($schedule) ) return false;

        $now = now();

        $startDate  = !empty($startDate = data_get($schedule, 'start_date')) ? carbon($startDate) : null;
        $endDate    = !empty($endDate = data_get($schedule, 'end_date')) ? carbon($endDate) : null;

        // if not started yet
        if( empty($startDate) || $startDate > $now ){
            return false;
        }

        // if already ended
        if( !empty($endDate) && $endDate < $now ){
            return false;
        }

        // at this point, it's certain it's ongoing

        return true;
    }

    /**
     * Checks whether an operation schedule is enabled and ongoing.
     * @param string $code  The operation schedule code
     * @return bool
     */
    public function isOperationScheduleActive(string $code) : bool
    {
        return $this->isOperationScheduleEnabled($code) && $this->isOperationScheduleOngoing($code);
    }

    /**
     * Checks whether an operation schedule has started.
     * @param string $code  The operation schedule code
     * @return bool
     */
    public function hasOperationScheduleStarted(string $code) : bool
    {
        $schedule = $this->operationSchedule( $code );
        if( empty($schedule) ) return false;

        $now = now();
        $startDate = !empty($startDate = data_get($schedule, 'start_date')) ? carbon($startDate) : null;

        return !empty($startDate) && $now >= $startDate;
    }

    /**
     * Checks whether an operation schedule has ended.
     * @param string $code  The operation schedule code
     * @return bool
     */
    public function hasOperationScheduleEnded(string $code) : bool
    {
        $schedule = $this->operationSchedule( $code );
        if( empty($schedule) ) return false;

        $now = now();
        $endDate = !empty($endDate = data_get($schedule, 'end_date')) ? carbon($endDate) : null;

        return !empty($endDate) && $now > $endDate;
    }

    /**   CMS   **/

    /**
     * Gets the content object from the Cb configurations.
     */
    public function contents() : ?object
    {
        return data_get($this, 'data.configurations.cms');
    }

    /**
     * Gets an content from the Cb phases.
     */
    public function content(string $code) : ?Content
    {
        if( empty($code) ){
            return null;
        }

        $cmsCode = data_get($this->contents(), $code);
        if( empty($cmsCode) || empty($cmsCode->code) ){
            return null;
        }

        return Content::whereCode($cmsCode->code)->first();
    }

    public function defaultContent() : ?Content
    {
        return $this->content( data_get($this, 'data.configurations.default_content', '') );
    }

    public function activeContent() : ?Content
    {
        $phaseContent = $this->phaseContent($this->activePhaseCode() ?? '');

        return $phaseContent ?? $this->defaultContent();
    }

    /**   PHASES   **/

    /**
     * Gets the phases object from the Cb configurations.
     */
    public function phases() : ?object
    {
        return data_get($this, 'data.configurations.phases');
    }

    /**
     * Gets a phase from the Cb phases.
     */
    public function phase(string $code) : ?object
    {
        if( empty($code) ){
            return null;
        }

        return data_get($this->phases(), $code);
    }

    /**
     * Gets the operation schedule from a given phase.
     */
    public function phaseOperationSchedule(string $code) : ?object
    {
        if( empty($code) ){
            return null;
        }

        $operationScheduleCode = data_get($this->phase($code), "operation_schedule");

        return $this->operationSchedule($operationScheduleCode);
    }

    /**
     * Gets the content from a given phase.
     */
    public function phaseContent(string $code) : ?object
    {
        if( empty($code) ){
            return null;
        }

        $contentCode = data_get($this->phase($code) ?? $this->activePhase(), "cms");

        return $this->content($contentCode);
    }

    /**
     * Gets the Cb active phase.
     */
    public function activePhase() : ?object
    {
        $phases = $this->phases() ?? [];
        $activePhase = null;

        foreach ($phases as $phase) {
            if( $this->isPhaseActive($phase->code ?? '') ){
                $activePhase = $phase;
            }
        }

        return $activePhase;
    }

    /**
     * Gets the Cb active phase's code.
     */
    public function activePhaseCode() : ?string
    {
        return data_get($this->activePhase(), 'code');
    }

    /**
     * Checks whether a phase is enabled.
     * @param string $code  The phase code
     * @return bool
     */
    public function isPhaseEnabled(string $code) : bool
    {
        $phase = $this->phase($code);
        if( empty($phase) ) return false;

        return data_get($phase, 'enabled') === true;
    }

    /**
     * Checks whether a phase is ongoing.
     * @param string $code  The phase code
     * @return bool
     */
    public function isPhaseOngoing(string $code) : bool
    {
        $phase = $this->phase($code);
        if( empty($phase) ) return false;

        return $this->isOperationScheduleOngoing(data_get($phase, 'operation_schedule', '') );
    }

    /**
     * Checks whether a phase is active (enabled & ongoing).
     * @param string $code  The phase code
     * @return bool
     */
    public function isPhaseActive(string $code) : bool
    {
        return $this->isPhaseEnabled($code) && $this->isPhaseOngoing($code);
    }

    /**
     * Checks whether a phase has started.
     * @param string $code  The phase code
     * @return bool
     */
    public function hasPhaseStarted(string $code) : bool
    {
        $phase = $this->phase($code);
        if( empty($phase) ) return false;

        return $this->hasOperationScheduleStarted(data_get($phase, 'operation_schedule', '') );
    }

    /**
     * Checks whether a phase has ended.
     * @param string $code  The phase code
     * @return bool
     */
    public function hasPhaseEnded(string $code) : bool
    {
        $phase = $this->phase($code);
        if( empty($phase) ) return false;

        return $this->hasOperationScheduleEnded(data_get($phase, 'operation_schedule', '') );
    }

    /**   State FUNCTIONS   **/

    /**
     * Returns the default state of the topic
     * @return object
     */
    public function defaultState(): ?object
    {
        try {
            return (object)['code' => getField($this,'data.configurations.topic.default_state', null)];
        } catch (\Exception $e) {
            logError($e->getMessage());
        }
        return null;
    }

    /**
     * Returns the states that make the topic visible in FE
     * @return array
     */
    public function visibleStates(): array{
        try {
            return getField($this,'data.configurations.topic.visible_states', []);
        } catch (\Exception $e) {
            logError($e->getMessage());
        }
        return [];
    }

    /**
     * Returns the status object
     * @param string $code  The state code
     * @return object
     */
    public function state(string $code): ?object{
        try {
            return getField($this,'data.configurations.topic.status.'.$code, (object)[]);
        } catch (\Exception $e) {
            logError($e->getMessage());
        }
        return null;
    }

    /**
     * Returns the status object
     * @param string $code  The state code
     * @return object
     */
    public function stateLabel(string $code, string $lang = null): ?string{
        try {
                $state = $this->state($code);

            return getField($state,'title.'.$lang, '-');
        } catch (\Exception $e) {
            logError($e->getMessage());
        }
        return null;
    }

    /**   TECHNICAL ANALYSIS FUNCTIONS   **/

    /**
     * Returns technical analysis question types as object or array
     *
     * @param bool $convertToArray
     * @return array|mixed
     */
    public function technicalAnalysisQuestionTypes(bool $convertToArray = false) : mixed{
        $questionTypes = data_get($this, 'data.configurations.topic.technical_analysis.question_types', []);
        if(!$convertToArray)
            return $questionTypes;

        $questionTypesArray = [];
        foreach ($questionTypes ?? [] as $questionType) {
            $questionTypesArray[data_get($questionType, 'code')] = getFieldLang($questionType, 'title');
        }
        return $questionTypesArray;
    }
}
