<?php

namespace App\Models\Empatia\Cbs;

use App\Events\Empatia\Frontend\NewTopicState;
use App\Helpers\HBackend;
use App\Helpers\HForm;
use App\Http\Controllers\Backend\FilesController;
use App\Models\User;
use App\Traits\Auditable;
use App\Traits\Versionable;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class Topic extends Model
{
    use HasFactory, SoftDeletes, HasTimestamps, Auditable, Versionable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empatia_topics';

    protected $guarded = [];

    protected $casts = [
        'title' => 'object',
        'content' => 'object',
        'slug' => 'object',
        'versions' => 'object',
        'parameters' => 'object',
        'proponents' => 'object',
        'status' => 'object',
        'data' => 'object'
    ];

    public function cb()
    {
        return $this->belongsTo(Cb::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get the topic title corresponding to the locale
     * @param string|null $locale The language locale
     * @param bool $firstAvailable Whether to return the first locale available, when current locale is empty
     * @return string|null
     */
    public function getTitle(string $locale = null, bool $firstAvailable = true): ?string
    {
        $locale = $locale ?? getLang();

        return data_get($this->title, $locale, ($firstAvailable ? first_element(array_filter(array_values((array)$this->title))) : null));
    }

    public function getCoverImage(): ?string
    {
        $coverImage = null;
        $coverImageCode = $this->parameter('cover_image');

        if (!empty($coverImageCode) && (is_string($coverImageCode) || is_array($coverImageCode))) {
            if (is_array($coverImageCode) && is_string(first_element($coverImageCode))) {
                $coverImageCode = first_element($coverImageCode);
            }
            return FilesController::getFileUrlByName($coverImageCode);
        }
        return null;

    }


    // FIXME: Retornar o código da tabela ou o objeto? [código da mesa a que pertence, está no gitlab!]

    /**
     * Searches the topic's cb tables for it and returns the first code if successful
     *
     * @return string|null
     */
    public function getCbTable(): ?string
    {
        if (empty($this->id)) {
            return null;
        }

        $tables = data_get($this, 'cb.data.configurations.tables');
        if (empty($tables)) {
            return null;
        }

        foreach ($tables as $code => $table) {
            $topics = data_get($table, 'topics', null);
            if (empty($topics) || !is_array($topics)) {
                continue;
            }

            if (in_array($this->id, $topics)) {
                return $code;
            }
        }

        return null;
    }

    public function addToTable($tableCode, $cb)
    {
        if (empty($tableCode))
            return '';
        $code = Str::slug($tableCode);
        $table = first_element(findObjectsByProperty('code', $code, getField($cb->data, 'configurations.tables', [])));

        $tableData =
            ['code' => $code,
                'name' => $tableCode,
                'participants' => [],
                'moderators' => [],
                'topics' => []];

        if (isset($table->topics)) {
            // FIXME: ou camelCase ou snake_case, consistência no código da mesma função!
            $table_topics = getField($table, 'topics', []);
            array_push($table_topics, $this->id);
            $cbData = data_set($cb->data, 'configurations.tables.' . $code . '.topics', $table_topics);
        } else {
            $cbData = data_set($cb->data, 'configurations.tables.' . $code, $tableData);
            $table_topics = getField($cbData, 'configurations.tables.' . $code . '.topics', []);
            array_push($table_topics, $this->id);
            $cbData = data_set($cbData, 'configurations.tables.' . $code . '.topics', $table_topics);
        }
        $cb->update(['data' => $cbData]);

    }

    /**
     * Finds whether the currently authenticated user is the owner of the topic
     * @return bool
     */
    public function isOwn(): bool
    {
        $user = auth()->user();

        if (empty($user) || empty($this->created_by)) {
            return false;
        }

        return $user->id == $this->created_by;
    }

    public function hasTechnicalAnalysisPublished(): bool
    {
        if (!empty($this->cb) && $this->cb->hasEnded()) {
            return true;
        }

        return false;
    }

    public function isTechnicalAnalysisApproved(): bool
    {
        if (!empty($this->parameter('budget')) && !empty($this->parameter('technical_analysis'))) {
            return true;
        }

        return false;
    }

    /**   PARAMETER FUNCTIONS   **/

    /**
     * Returns the Cb parameter
     *
     * @param string $code
     * @return null
     */
    public function cbParameter(string $code)
    {
        $parameter = null;

        try {
            $parameter = $this->cb?->parameter($code) ?? null;
        } catch (\Exception $e) {
            logError($e->getMessage());
        }

        return $parameter;
    }

    /**
     * Returns the parameter value from the current topic
     *
     * @param string $code
     * @param mixed|null $default
     * @return null
     */
    public function parameterValue(string $code, mixed $default = null): mixed
    {
        return data_get($this, "parameters.$code", $default);
    }

    /**
     * Alias for parameterValue()
     *
     * @param string $code
     * @param mixed|null $default
     * @return null
     */
    public function parameter(string $code, mixed $default = null): mixed
    {
        return $this->parameterValue($code, $default);
    }

    /**
     * Returns the parameter selected options
     *
     * @param string $code
     * @param mixed|null $default
     * @return null
     */
    public function parameterSelectedOptions(string $code, bool $preserveKeys = true): array
    {
        $selectedOptions = [];

        $parameter = $this->cbParameter($code);
        if (empty($parameter) || empty($parameter->options))
            return $selectedOptions;

        $parameterValue = $this->parameterValue($code);
        if (!is_array($parameterValue))
            return $selectedOptions;

        foreach ($parameterValue as $value) {
            $option = findObjectByProperty('code', $value, $parameter->options);

            if (!empty($option)) {
                if ($preserveKeys)
                    $selectedOptions[$value] = $option;
                else
                    $selectedOptions[] = $option;
            }
        }

        return $selectedOptions;
    }

    /**
     * Returns the parameter selected option labels
     *
     * @param string $code
     * @param string|null $lang
     * @param string|null $default
     * @param bool $preserveKeys
     * @return array
     */
    public function parameterSelectedOptionLabels(string $code, string $lang = null, string $default = null, bool $preserveKeys = true): array
    {
        $selectedOptionLabels = [];
        $lang = $lang ?? getLang();

        $selectedOptions = $this->parameterSelectedOptions($code);

        foreach ($selectedOptions as $option) {
            if ($preserveKeys)
                $selectedOptionLabels[$option->code] = data_get($option, "label.$lang", $default);
            else
                $selectedOptionLabels[] = data_get($option, "label.$lang", $default);
        }

        return $selectedOptionLabels;
    }

    /**
     * Returns a topic url
     * @return route
     */
    public function getTopicUrl()
    {
        return route('page', [\App\Helpers\Empatia\Cbs\HCb::getCbTypeSlug(getField($this->cb, 'type')), getField($this->cb, 'slug.' . getLang(), '') . '/' . $this->slug->{getLang()}]);
    }

    /**   State FUNCTIONS   **/

    /**
     * Assign a State and Status to topic
     * @param string $code  The state code
     */
    public function assignState(string $code = null)
    {
        try {
            if (getField($this, 'cb_id')) {
                $configurations = getField($this->cb, 'data.configurations', []);
                $status = getField($configurations, 'topic.status', []);

                if (HForm::getAction() == 'store') {
                    $date = 'created_at';
                    $by = 'created_by';
                } else {
                    $date = 'updated_at';
                    $by = 'updated_by';
                }
                $statusList = $this->status ?? [];
                $state = empty($code) ? getField($this->cb->defaultState(), 'code') : $code;
                $newStatus = [
                    'id' => !empty($this->status)  ? count($this->status) + 1 : 1,
                    'code' => $state,
                    'title' => (array)getField($status, $code . '.title', []),
                    'description' => (array)getField($status, $code . '.description', []),
                    $date => Carbon::now()->format('d-m-Y H:i'),
                    $by => Auth::user()->id,
                ];
                array_push($statusList, (object)$newStatus);

                $this->status = $statusList;
                $this->state = empty($code) ? getField($configurations, 'topic.default_state', '') : $code;
                 $this->save();
                flash()->addSuccess(__('backend.generic.save.ok'));
                NewTopicState::dispatch($this, $state);
            }
        } catch (\Exception $e) {
            logError($e->getMessage());
        }
    }
}
