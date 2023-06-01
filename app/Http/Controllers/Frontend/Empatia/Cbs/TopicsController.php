<?php

namespace App\Http\Controllers\Frontend\Empatia\Cbs;

use App\Events\Empatia\Frontend\TopicCreated;
use App\Helpers\Empatia\Cbs\HCb;
use App\Helpers\Empatia\Cbs\TopicHelpers;
use App\Helpers\HBackend;
use App\Helpers\HFrontend;
use App\Helpers\HKeycloak;
use App\Http\Controllers\Backend\Empatia\Cbs\CbsController;
use App\Http\Controllers\Backend\Notifications\NotificationsController;
use App\Models\Backend\File;
use App\Models\Backend\Notifications\Template;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Topic;
use App\Models\User;
use Cache;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TopicsController extends Controller
{
    // Controller prefix
    private $prefix = "cbs.topics.";
    private $parameterPrefix = "parameter_";

    // Data
    public int  $cbId;
    public Cb   $cb;
    public int  $topicId;

    private $guarded = [
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    private array $baseRules = [
        'title' => [
            'rules' => ['required', 'max:255'],     // depends on Cb configurations
            'locale' => false,
        ],
        'content' => [
            'rules' => ['required'],                // depends on Cb configurations
            'locale' => false,
        ],
    ];
    private array $parameterRules = [];
    private array $validateRules = [];
    private array $validateMessages = [];


    private function setRules()
    {
        $this->buildParameterRules();
        [$this->validateRules, $this->validateMessages] =
            HBackend::createControllerValidate($this->baseRules + $this->parameterRules, $this->prefix.'form.error');
    }

    private function buildParameterRules()
    {
        if( empty($this->cb) ){
            return;
        }

        try {
            $rules = [];

            foreach ($this->cb->parameters ?? [] as $key => $parameter) {
                $parameterName = $this->parameterPrefix . $parameter->code;

                $rules[$parameterName] = [
                    'rules' => isset($parameter->rules) ? explode('|', $parameter->rules) : [],
                    'locale' => HCb::isParameterMultilang($parameter)
                ];

                if (($parameter->mandatory ?? false) &&
                    !partial_in_array('required', $rules[$parameterName]['rules']) &&
                    !partial_in_array('required_if', $rules[$parameterName]['rules']))
                {
                    $rules[$parameterName]['rules'][] = 'required';
                }
            }

            $this->parameterRules = $rules;

        } catch (\Exception $e) {
            logError($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($type, $cbId)
    {
        if ($type != 'all')
            $this->cbType = HCb::validateType($type);

        $cb = Cb::findOrFail($cbId);

        $view = "empatia::cbs.topics.$type.index";
        if (!view()->exists($view)) {
            $view = "empatia::cbs.topics.default.index";
        }

        $indexFilters = HBackend::initializeIndexFilters();

        return view($view, [
            'cb' => $cb,
            'cbTypes' => HCb::getCbTypes(),
            'deletionStatusFilter' => $indexFilters['deletionStatusFilter'],
            'deletionStatusFilterOptions' => $indexFilters['deletionStatusFilterOptions'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($type, $cbId)
    {
        if ($type != 'all')
            $this->cbType = HCb::validateType($type);


        $cb = Cb::findOrFail($cbId);

        $view = "empatia::cbs.topics.$type.topic";
        if (!view()->exists($view)) {
            $view = "empatia::cbs.topics.default.topic";
        }

        return view($view, [
            'cb' => $cb,
            'topic' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable|Redirector|RedirectResponse
     */
    public function store(Request $request, $type, $cbId)
    {
        $this->cb   = Cb::whereId($cbId)->firstOrFail();
        $this->cbId = $cbId;

        $this->setRules();

        // check if topic can be created
        if ( !$this->cb->isTopicActionAuthorized('create') ) {
            return redirect()->back()->withInput()->withErrors(['conditions' => __($this->prefix . "{$this->cb->type}.store.conditions.fail")]);
        }

        $request->validate($this->validateRules, $this->validateMessages);
        try {
            data_set($request, 'title->' . getLang(), $request->input('title'));
            data_set($request, 'content->' . getLang(), $request->input('content'));

            $data = $this->setTopic($request, $this->cb);

            DB::beginTransaction();
            $topic = Topic::create($data);
            DB::commit();
            $topic->assignState();
            logDebug(json_encode($topic));
            //session()->flash('success', __($this->prefix . 'store.ok'));

            $authUser = Auth::user();
            logInfo("[ID:{$authUser->id}, Name:{$authUser->name}] Topic created with id: {$topic->id}.");

            TopicCreated::dispatch($topic);

            return $this->redirectStore();

        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return redirect()->back()->withFail(__($this->prefix . 'store.error'))->withInput();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($type, $cbId, $id)
    {
        if ($type != 'all')
            $this->cbType = HCb::validateType($type);

        $cb = Cb::findOrFail($cbId);

        $view = "empatia::cbs.topics.$type.topic";
        if (!view()->exists($view)) {
            $view = "empatia::cbs.topics.default.topic";
        }
        $cb->dates = Carbon::parse($cb->start_date)->isoFormat('Y-MM-DD') . ' | ' . Carbon::parse($cb->end_date)->isoFormat('Y-MM-DD');
        $topic = Topic::whereId($id)->withTrashed()->firstOrFail();
        return view($view, [
            'cb' => $cb,
            'topic' => $topic
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($type, $cbId, $id)
    {
        if ($type != 'all')
            $this->cbType = HCb::validateType($type);

        $cb = Cb::findOrFail($cbId);

        $view = "empatia::cbs.topics.$type.topic";
        if (!view()->exists($view)) {
            $view = "empatia::cbs.topics.default.topic";
        }

        $topic = Topic::whereId($id)->firstOrFail();

        return view($view, [
            'cb' => $cb,
            'topic' => $topic
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable|Redirector|RedirectResponse
     */
    public function update(Request $request, $type, $cbId, $id, $public = false)
    {
        try {
            $this->topicId = $id;
            $cb = Cb::whereId($cbId)->firstOrFail();
            $this->cb = $cb;
            $this->cbType = $type;
            $topic = Topic::findOrFail($id);

            // check if topic can be edited
            if( !TopicHelpers::canEdit($topic) ){
                return redirect()->back()->withInput()->withErrors(['conditions' => __($this->prefix . "$cb->type.update.conditions.fail")]);
            }

            // remove validations if request is public
            $this->updatePublicRequestRules($this->cb);

            $validator = Validator::make($request->all(), $this->validateRules, $this->validateMessages);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            $data = $this->setTopic($request, $cb, $topic);
            DB::beginTransaction();
            if ($topic->update($data)) {
                session()->flash('success', __($this->prefix . 'update.ok'));
                DB::commit();
                // Identify what cache keys must be cleared due to this update
                Cache::forget('cache_topic_cover_image_' . $topic->cb_id . '_' . $topic->id);

                logDebug(json_encode($topic));

                return $this->redirectUpdate();

            } else {
                DB::rollback();
                logError('error in update');
                return redirect()->back()->withFail(__($this->prefix . 'update.error'))->withInput();
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError('topic update: ' . json_encode($e->getMessage()));
            return redirect()->back()->withFail(__($this->prefix . 'update.error'))->withInput();
        }
    }

    private function setTopic($request, $cb, $topic = null)
    {
        try {
            $data = [];
            if ( HFrontend::action() == 'store' ) {
                $data['number'] = (Topic::whereCbId($cb->id)->withTrashed()->max('number') ?? 0) + 1;
            } else if( !HFrontend::isPublicRequest() ) {
                $data['number'] = $request->exists('number') ? $request->input('number') : data_get($topic, 'number');
            } else {
                $data['number'] = data_get($topic, 'number');
            }

            $data['cb_id']      = $cb->id;
            $data['cb_version'] = $request->input('cbVersion') ?? getField($topic, 'cb_version')  ?? $cb->version;
            $data['title']      = $this->setTopicTitle($request->except($this->guarded), $cb, $topic);
            $data['content']    = $this->setTopicContent($request->except($this->guarded));

            if ( HFrontend::action() == 'store' ) {
                $data['proponents'][] = (object)[
                    'user_id'       => \Auth::id(),
                    'primary'       => true,
                    'created_by'    => \Auth::id(),
                    'data'          => $request->input('proponent_data')
                ];
                $data['slug']       = $this->setTopicSlugs($data);
                $data['data']       = self::setTopicData();
                $data['position']   = (Topic::withTrashed()->max('position') + 1);
            } else {
                $data['proponents'] = getField($topic, 'proponents');
            }

            $data['parameters'] = $this->setTopicParameters($cb, $request->except($this->guarded), $topic);

            return $data;

        } catch (\Exception $e) {
            logError($e->getMessage());
        }
    }

    public function reorder($id, $prevId, $nextId)
    {
        //THIS CODE IS PREPARED FOR WHEN THERE IS A POSITION FIELD ON THE DATABASE FOR THE COLUMN QUERIED.

        $posCurrent = Topic::whereId($id)->withTrashed()->firstOrFail()->position;
        if ($prevId)
            $posPrev = Topic::whereId($prevId)->withTrashed()->firstOrFail()->position;
        if ($nextId)
            $posNext = Topic::whereId($nextId)->withTrashed()->firstOrFail()->position;


        $arrayPositions = [];
        foreach (TopicHelpers::getTopicsFromCbByPos($this->cbId, true) as $obj) { //This list needs to be ordered by position!!
            $arrayPositions[] = $obj->id;
        }


        $out = array_splice($arrayPositions, $posCurrent, 1);

        if (!$nextId) { //If there is no next item
            array_splice($arrayPositions, $posPrev, 0, $out);
        } else if (!$prevId) { //If there is no previous item
            array_splice($arrayPositions, $posNext, 0, $out);
        } else {
            if ($this->sortDirection == 'asc') {

                if ($posPrev > $posCurrent && $posNext > $posCurrent) { //If moved to a bigger position
                    array_splice($arrayPositions, $posPrev, 0, $out);
                } else if ($posPrev < $posCurrent && $posNext < $posCurrent) { //If moved to a smaller position
                    array_splice($arrayPositions, $posNext, 0, $out);
                }
            } else if ($this->sortDirection == 'desc') {
                if ($posPrev > $posCurrent && $posNext > $posCurrent) { //If moved to a bigger position
                    array_splice($arrayPositions, $posNext, 0, $out);
                } else if ($posPrev < $posCurrent && $posNext < $posCurrent) { //If moved to a smaller position
                    array_splice($arrayPositions, $posPrev, 0, $out);
                }
            }
        }


        foreach ($arrayPositions as $p => $id) {
            $topic = Topic::whereId($id)->withTrashed()->firstOrFail();
            $topic->position = $p;
            $topic->update();
        }
    }

    public static function setTopicTitle($data, Cb $cb, $topic = null): object
    {
        $titles = (array)getField($topic, 'title', []);
        try {
            if( HFrontend::isPublicRequest() && data_get($cb, 'data.configurations.topic.create.fields.title.required') === false ){
                $titles = data_get($cb, 'data.configurations.topic.create.fields.title.default', [getLang() => __('backend::generic.topic.title')]);
            } else {
                foreach (getLanguagesFrontend() as $language) {
                    if (!empty($data["title->" . $language['locale']])) {
                        $title = $data["title->" . $language['locale']];
                        $titles[$language['locale']] = $title;
                    }
                }
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError( $e->getMessage() );
        }

        return (object)$titles;
    }

    public function setTopicSlugs($data): object
    {
        $slugs = [];
        try {
            $topics = Topic::withTrashed()->get();
            foreach (getLanguagesFrontend() as $language) {
                $locale = $language['locale'];
                $title = $data["title->" . $locale] ?? (getField($data, "title.$locale") ? getField($data, "title.$locale") : null);
                if (!empty($title)) {
                    $baseSlug = Str::slug($title);
                    $slug = $baseSlug;

                    for ($i = 0; $topics->where("slug.{$locale}", $slug)->first(); $i++)
                        $slug = "{$baseSlug}-" . ($i + 1);

                    $slugs[$language['locale']] = $slug;
                }
            }

        } catch (\Illuminate\Database\QueryException|Exception|\Throwable $e) {
            logError('slug: ' . json_encode($e->getMessage()));
        }
        return (object)$slugs;
    }

    public static function setTopicContent($data, $topic = null): object
    {
        $contents = (array)getField($topic, 'content', []);

        try {
            foreach (getLanguagesFrontend() as $language) {
                if (!empty($data["content->" . $language['locale']])) {
                    $contents[$language['locale']] = $data["content->" . $language['locale']];
                }
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError('content: ' . json_encode($e->getMessage()));
        }

        return (object)$contents;
    }

    public static function setTopicParameters(Cb $cb, $request, Topic &$topic = null)
    {
        try {
            $parameters = [];
            foreach ($cb->getParameters(['flags.is_in_frontend_form' => true]) ?? [] as $cbParam) {
                if( !($code = getField($cbParam, 'code')) ){
                    continue;
                }

                $value = [];
                $name = 'parameter_' . $code;

                if( getField($cbParam, 'multilang') == true ){
                    foreach (getLanguagesFrontend() as $lang) {
                        $locale = $lang['locale'];

                        if (isset($request["$name->$locale"])) {
                            $value[$locale] = $request["$name->$locale"];

                        } else if (isset($request["{$name}_$locale"])) {
                            $value[$locale] = $request["{$name}_$locale"];

                        } else {
                            $value = null;
                        }
                    }
                } else {
                    if ( in_array(getField($cbParam, 'type'), ['file', 'image','files', 'images']) && !empty($request[$name])) {
                        if( in_array(getField($cbParam, 'type'), ['file', 'image']) ){
                            $file = File::whereName($request[$name])->first();
                            if (!empty($file)) {
                                $value = $file->name;
                            } else {
                                $value = $request[$name];
                            }
                        } else {
                            $decodedInput = json_decode($request[$name], true);

                            if( is_array($decodedInput) ){
                                $value = [];
                                foreach ($decodedInput as $item) {
                                    $file = File::whereName($item)->first();
                                    if (!empty($file)) {
                                        $value[] = $file->name;
                                    }
                                }
                            } else {
                                $value = $request[$name];
                            }
                        }
                    } else {
                        $value = $request[$name];
                    }
                }
                $parameters[$code] = is_array($value) ? $value : $value;
            }
            return $parameters;

        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage());
        }
    }

//    public static function setTopicDefaultState($cb): array
//    {
//        try {
//            $state = TopicHelpers::getStateById(1, $cb);
//            if (!$state)
//                return [];
//
//            $userId = \Auth::id();
//            $status[] = (object)[
//                'id' => $state->id,
//                'code' => $state->code,
//                'title' => $state->title,
//                'description' => $state->description,
//                'created_at' => date('d-m-Y H:i'),
//                'created_by' => auth()->user()->id ?? null,
//                'updated_at' => date('d-m-Y H:i'),
//                'updated_by' => auth()->user()->id ?? null,
//            ];
//
//            return $status;
//
//        } catch (\Exception $e) {
//            logError( $e->getMessage() );
//        }
//
//        return [];
//    }

    public static function setTopicData(): array
    {
        $data = [];

        // load default topic data here (either from configs or from cb settings)

        return $data + [
                "hierarchy" =>
                    [
                        "parents" => null,
                        "children" => null,
                    ]
            ];
    }

    private function updatePublicRequestRules(Cb $cb = null)
    {
        if( !HFrontend::isPublicRequest() ){
            return;
        }

        // override fields rules (title and content)
        $cbTopicCreateFields = data_get($cb, 'data.configurations.topic.create.fields');
        $topicCreateRules = [];
        foreach ($cbTopicCreateFields ?? [] as $field => $properties){
            $rules = data_get($properties, 'rules');
            if( !$rules ){
                continue;
            }

            $topicCreateRules[$field] = [
                'rules' => explode('|', $rules),
                'locale' => true
            ];
        }

        [$newValidateRules, $newValidateMessages] = \HBackend::createControllerValidate($topicCreateRules, $this->prefix . 'form.error');
        $this->validateRules = array_merge($this->validateRules, $newValidateRules);
        $this->validateMessages = array_merge($this->validateMessages, $newValidateMessages);

        unset($this->validateRules['number']);

        // if title not required
        if( data_get($cb, 'data.configurations.topic.create.fields.title.required') === false ){
            foreach($this->validateRules ?? [] as $key => $rules){
                if( stripos($key,'title->',0) !== false ){
                    unset($this->validateRules[$key]);
                }
            }
        }
        // if content not required
        if( data_get($cb, 'data.configurations.topic.create.fields.content.required') === false ){
            foreach($this->validateRules ?? [] as $key => $rules){
                if( stripos($key,'content->',0) !== false ){
                    unset($this->validateRules[$key]);
                }
            }
        }

        // remove other languages validations
        foreach(getLanguagesFrontend() ?? [] as $language){
            if( $language['locale'] == getLang() ){
                continue;
            }

            unset($this->validateRules['title->'.$language['locale']]);
            unset($this->validateRules['content->'.$language['locale']]);

            // parameters
            if( !$cb ){
                continue;
            }
            foreach ($cb->parameters ?? [] as $key => $parameter) {
                if( HCb::isParameterMultilang($parameter) ) {
                    unset($this->validateRules["parameter_{$parameter->code}->{$language['locale']}"]);
                }
            }

        }
    }

    private function redirectStore() : RedirectResponse
    {
        $thanksContentCode = data_get($this->cb, 'data.configurations.cms.topic_create_thanks.code');
        $thanksContent = is_string($thanksContentCode) ? HFrontend::getContentByCode($thanksContentCode) : null;
        $thanksSlug = data_get($thanksContent, 'slug.'.getLang(), '');

        if( !empty($thanksSlug) ){
            return redirect()->route('page', [ $thanksSlug ]);
        }

        return redirect()->route('page', [
            HCb::getCbTypeSlug( data_get($this->cb, 'type') ),
            data_get($this->cb, 'slug.'.getLang(), '').'/thank-you-topic'
        ]);
    }

    private function redirectUpdate() : RedirectResponse
    {
        if ( HFrontend::isPublicRequest() ){
            $thanksContentCode = data_get($this->cb, 'data.configurations.cms.topic_edit_thanks');
            $thanksContent = is_string($thanksContentCode) ? HFrontend::getContentByCode($thanksContentCode) : null;
            $thanksSlug = data_get($thanksContent, 'slug.'.getLang(), '');

            if( !empty($thanksSlug) ){
                return redirect()->route('page', [ $thanksSlug ]);
            }

            return redirect()->route('page', [
                HCb::getCbTypeSlug( data_get($this->cb, 'type') ),
                data_get($this->cb, 'slug.'.getLang(), '')
            ]);
        }
        return redirect()->action([CbsController::class, 'show'], ['type' => $this->cbType, 'id' => $this->cb->id]);
    }
}

