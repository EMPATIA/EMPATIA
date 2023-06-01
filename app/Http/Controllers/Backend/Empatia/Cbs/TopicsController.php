<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs;

use App\Helpers\Empatia\Cbs\HCb;
use App\Helpers\Empatia\Cbs\TopicHelpers;
use App\Helpers\HBackend;
use App\Helpers\HFrontend;
use App\Http\Controllers\Backend\Controller;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Topic;
use Cache;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Backend\Entities\Configuration;
use Modules\Backend\Helpers\HForm;
use Modules\Cbs\Helpers\CVHelper;
use Modules\CMS\Entities\Content;
use Modules\Files\Entities\File;
use Modules\Sites\Entities\Site;

class TopicsController extends Controller
{
    // TODO: needs cleanup and probably refactoring (improve code and add validations)

    // Controller prefix
    private $prefix = "backend.empatia.cbs.topics.";
    private $paramPrefix = "parameter_";

    // Data
    public $cbId;
    public $cb;
    public $cbVersion;
    public $cbType; //Used to personalize index table
    public $topicId;

    private $guarded = [
        'site',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $listeners = [
        'reorder',
        'deleteTopic' => 'destroy',
        'restoreTopic' => 'restore',
        'update',
        'showTopic' => 'show',
        'destroy',
        'restore',
    ];

    public array $validateRules = [];
    public array $validateMessages = [];

    function __construct($id = null)
    {
//        parent::__construct($id);

        $param_rules = [];
        $cbId = \Route::current()->parameter('cbId');
        if (!empty($cbId)) {
            try {
                $cb = Cb::findOrFail($cbId);
                $this->cb = $cb;
                $parameters = CbsController::getCbParametersByVersion($cb, request()->get('cbVersion') ?? $cb->version);

                $this->cbType = $cb->type;

                foreach ($parameters ?? [] as $key => $parameter) {
                    if ( HCb::isParameterMultilang($parameter) ) {
                        foreach (getLanguagesBackend() ?? [] as $langKey => $language) {
                            $param_rules["parameter_" . $parameter->code] = [
                                'rules' => isset($parameter->rules) ? explode('|', $parameter->rules) : [],
                                'locale' => true
                            ];
                        }
                    } else {
                        $param_rules["parameter_" . $parameter->code] = [
                            'rules' => isset($parameter->rules) ? explode('|', $parameter->rules) : [],
                            'locale' => false
                        ];
                    }

                    if (($parameter->mandatory ?? false) && (!partial_in_array('required', $param_rules["parameter_" . $parameter->code]['rules']) && !partial_in_array('required_if', $param_rules["parameter_" . $parameter->code]['rules']))) {
                        $param_rules["parameter_" . $parameter->code]['rules'][] = 'required';
                    }
                }
            } catch (\Exception $e) {
                logError('rules: ' . $e->getMessage());
            }

            $base_rules = [
                'title' => [
                    'rules' => ['required', 'max:255'],
                    'locale' => true,
                ],
                'content' => [
                    'rules' => ['nullable'],
                    'locale' => true,
                ],
                'number' => [
                    'rules' => ['required',
//                        function ($attribute, $value, $fail) {
//                            $topic_number = Topic::whereCbId($this->cb->id)->withTrashed()->where('number', $value)->first();
//                            if (!empty($topic_number) && !empty($this->topicId != $topic_number->id)) {
//                                $fail(__('cbs.form.error.' . $attribute . '.exists'));
//                            }
//                        },
                    ]
                ],
            ];

            [$this->validateRules, $this->validateMessages] = HBackend::createControllerValidate($base_rules + $param_rules, $this->prefix . 'form.error');


        } else {
            logError('cbId is null');
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

        $view = "backend.empatia.cbs.$type.topics.index";
        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.topics.index";
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

        $view = "backend.empatia.cbs.$type.topics.topic";
        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.topics.topic";
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
    public function store(Request $request, $type, $cbId, $public = false)
    {
//        dd($request, $request->all());
        $this->cb = Cb::whereId($cbId)->firstOrFail();
        $this->cbType = $type;
        $this->cbId = $cbId;
        // check if topic can be created
        // TODO: implement error alert to show this message
        if (!TopicHelpers::canCreate($this->cb)) {
            return redirect()->back()->withInput()->withErrors(['conditions' => __($this->prefix . "{$this->cb->type}.store.conditions.fail")]);
        }

        // remove validations if request is public
        if (HFrontend::isPublicRequest()) {
            $this->updatePublicRequestRules($this->cb);
        }

        $request->validate($this->validateRules, $this->validateMessages);

//        $validator = Validator::make($request->all(), $this->validateRules, $this->validateMessages);
//        if ($validator->fails()) {
//            return redirect()->back()->withInput()->withErrors($validator->errors());
//        }

        try {


            $data = $this->setTopic($request, $this->cb);

            DB::beginTransaction();
            $topic = Topic::create($data);
            DB::commit();
            $topic->assignState();
            $topic->addToTable(\Auth::user()->getCbTable($this->cb), $this->cb);

            logDebug(json_encode($topic));
            session()->flash('success', __($this->prefix . 'store.ok'));

            return $this->redirectStore();

        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . 'at line ' . $e->getLine());
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

        $view = "backend.empatia.cbs.$type.topics.topic";
        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.topics.topic";
        }

        $cb->dates = Carbon::parse($cb->start_date)->isoFormat('Y-MM-DD') . ' | ' . Carbon::parse($cb->end_date)->isoFormat('Y-MM-DD');
        $topic = Topic::whereId($id)->withTrashed()->firstOrFail();
        return view($view, [
            'title' => __($this->prefix.'title.show'),
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

        $view = "backend.empatia.cbs.$type.topics.topic";
        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.topics.topic";
        }

        $topic = Topic::whereId($id)->firstOrFail();

        return view($view, [
            'title' => __($this->prefix.'title.show'),
            'cb' => $cb,
            'topic' => $topic
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(string $cbType, string $cbId, string $id)
    {
        try {
            if (Topic::findOrFail($id)->delete()) {
                session()->flash('success', __($this->prefix . 'delete.ok'));
                return response()->json(['success' => 'success'], 200);
            }

            logError('delete');
        } catch (QueryException|Exception|\Throwable $e) {
            logError('delete: ' . json_encode($e->getMessage()));
            return redirect()->back()->withFail(__($this->prefix . 'delete.error'));
        }

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
//            if( !TopicHelpers::canEdit($topic) ){
//                return redirect()->back()->withInput()->withErrors(['conditions' => __($this->prefix . "$cb->type.update.conditions.fail")]);
//            }

            // remove validations if request is public
            if( HFrontend::isPublicRequest() ){
                $this->updatePublicRequestRules($this->cb);
            }

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

    public function restore(string $cbType, string $cbId, string $id)
    {
        try {
            if (Topic::withTrashed()->findOrFail($id)->restore()) {
                session()->flash('success', __($this->prefix . 'restore.ok'));
                return response()->json(['success' => 'success'], 200);

//                return redirect()->action([AttendancesController::class, 'index']);
            } else {
                logError('error in restore');
                return redirect()->back()->withFail(__($this->prefix . 'restore.error'))->withInput();
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError('restore: ' . json_encode($e->getMessage()));
            return redirect()->back()->withFail(__($this->prefix . 'restore.error'))->withInput();
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
                foreach (getLanguagesBackend() as $language) {
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
            foreach (getLanguages() as $language) {
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
            foreach (getLanguagesBackend() as $language) {
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
            foreach ($cb->getParameters() ?? [] as $cbParam) {
                if( !($code = getField($cbParam, 'code')) ){
                    continue;
                }

                $value = [];
                $name = 'parameter_' . $code;

                if( getField($cbParam, 'multilang') == true ){
                    foreach (getLanguagesBackend() as $lang) {
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
                            $file = \App\Models\Backend\File::whereName($request[$name])->first();
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
                                    $file = \App\Models\Backend\File::whereName($item)->first();
                                    if (!empty($file)) {
                                        $value[] = $file->name;
                                    }
                                }
                            } else {
                                $value = $request[$name];
                            }
                        }
                    } else {
                        $value = $request[$name] ?? null;
                    }
                }

                $parameters[$code] = is_array($value) ? $value : $value;
            }
            return $parameters;

        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage());
        }
    }

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
        foreach(getLanguages() ?? [] as $language){
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
        if ( HFrontend::isPublicRequest() ){
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

        return redirect()->action([CbsController::class, 'show'], ['type' => $this->cbType, 'id' => $this->cb->id]);
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

