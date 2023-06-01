<?php

namespace App\Traits;

use App\Models\LoginLevel;
use Illuminate\Support\Collection;

trait HasLoginLevels
{
    public function loginLevels() : array
    {
        return (array)data_get($this, 'data.login_levels', []);
    }

    public function loginLevelCodes() : array
    {
        return array_keys($this->loginLevels());
    }

    public function setLoginLevels(array|object $loginLevels) : mixed
    {
        $data = $this->data;
        return $this->data = data_set($data, 'login_levels', (object)$loginLevels);
    }

    /**
     * Assign the given login level to the user.
     *
     * @param array|string|Collection|LoginLevel $loginLevels
     * @param bool $manualOverride
     * @return $this
     */
    public function assignLL(array|string|Collection|LoginLevel $loginLevels, bool $manualOverride = false)
    {
        if (is_string($loginLevels) && false !== strpos($loginLevels, '|')) {
            $loginLevels = $this->convertPipeToArray($loginLevels);
        }

        if (is_string($loginLevels)) {
            $loginLevels = [
                $loginLevels => (object)[
                    'code' => $loginLevels,
                    'manual_override' => $manualOverride,
                ]
            ];
        }

        if ($loginLevels instanceof LoginLevel) {
            $loginLevels = [
                $loginLevels->code => (object)[
                    'code' => $loginLevels->code,
                    'manual_override' => $manualOverride,
                ]
            ];
        }

        if (is_array($loginLevels)) {
            $sourceLoginLevels = $loginLevels;

            $loginLevels = [];
            foreach ($sourceLoginLevels as $sourceLoginLevel) {
                if( !is_string($sourceLoginLevel) ) continue;

                $loginLevels[$sourceLoginLevel] = (object)[
                    'code' => $loginLevels->code,
                    'manual_override' => $manualOverride,
                ];
            }
        }

        $allLLs = LoginLevel::all();
        $model = $this;

        $loginLevels = collect()->make($loginLevels)->reduce(function ($array, $loginLevel) use ($allLLs, $model, $manualOverride) {
            if (empty($loginLevel)) {
                return $array;
            }

            $loginLevel = $loginLevel instanceof LoginLevel ? ((object)[
                'code' => $loginLevel->code,
                'manual_override' => $manualOverride,
            ]) : $loginLevel;

            if (! $allLLs->where('code', '=', $loginLevel)->first() ) {
                return $array;
            }

            if( !in_array($loginLevel, $this->loginLevelCodes()) ){
                array_push($array, $loginLevel);
            }

            return $array;
        }, $this->loginLevels() ?? []);

        if( count( array_diff($loginLevels, $this->loginLevelCodes()) ) ){
            $this->setLoginLevels($loginLevels);
            $this->save();
        }

        return $this;
    }

    /**
     * Revoke the given login level from the user.
     *
     * @param string|LoginLevel $loginLevel
     * @return $this
     */
    public function removeLL(string|LoginLevel $loginLevel){
        if ($loginLevel instanceof LoginLevel) {
            $loginLevel = $loginLevel->code;
        }

        $loginLevels = [];

        foreach ($this->loginLevels() ?? [] as $code => $userLoginLevel){
            if ( $code == $loginLevel ) {
                continue;
            }

            $loginLevels[$code] = $userLoginLevel;
        }

        if( count($loginLevels) != count($this->loginLevels() ?? []) ){
            $this->setLoginLevels($loginLevels);
            $this->save();
        }

        return $this;
    }

    /**
     * Determine if the user has (one of) the given login level(s).
     *
     * @param string|array|LoginLevel|Collection $loginLevels
     * @return bool
     */
    public function hasLL($loginLevels): bool
    {
        if (is_string($loginLevels) && false !== strpos($loginLevels, '|')) {
            $loginLevels = $this->convertPipeToArray($loginLevels);
        }

        if (is_string($loginLevels)) {
            return in_array($loginLevels, $this->loginLevelCodes());
        }

        if ($loginLevels instanceof LoginLevel) {
            return in_array($loginLevels->code, $this->loginLevelCodes());
        }

        if (is_array($loginLevels)) {
            foreach ($loginLevels as $loginLevel) {
                if ($this->hasLL($loginLevel)) {
                    return true;
                }
            }

            return false;
        }

        return !empty( array_intersect($loginLevels->pluck('code')->toArray(), $this->loginLevelCodes()) );
    }

    /**
     * Determine if the user has any of the given login levels(s).
     *
     * Alias to hasLL()
     *
     * @param string|array|LoginLevel|Collection $loginLevels
     *
     * @return bool
     */
    public function hasAnyLL(...$loginLevels): bool
    {
        return $this->hasLL($loginLevels);
    }

    /**
     * Determine if the user has all of the given login level(s).
     *
     * @param string|array|LoginLevel|Collection $loginLevels
     * @return bool
     */
    public function hasAllLLs(...$loginLevels): bool
    {
        if (is_string($loginLevels) && false !== strpos($loginLevels, '|')) {
            $loginLevels = $this->convertPipeToArray($loginLevels);
        }

        if (is_string($loginLevels)) {
            return in_array($loginLevels, $this->loginLevelCodes());
        }

        if ($loginLevels instanceof LoginLevel) {
            return in_array($loginLevels->code, $this->loginLevelCodes());
        }

        $loginLevels = collect()->make($loginLevels)->flatten()->map(function ($loginLevel) {
            return $loginLevel instanceof LoginLevel ? $loginLevel->code : $loginLevel;
        });

        return $loginLevels->intersect( collect($this->loginLevelCodes()) ) == $loginLevels;
    }

    /**
     * Determine if the user has exactly all of the given login level(s).
     *
     * @param string|array|LoginLevel|Collection $loginLevels
     * @return bool
     */
    public function hasExactLLs(...$loginLevels): bool
    {
        if (is_string($loginLevels) && false !== strpos($loginLevels, '|')) {
            $loginLevels = $this->convertPipeToArray($loginLevels);
        }

        if (is_string($loginLevels)) {
            $loginLevels = [$loginLevels];
        }

        if ($loginLevels instanceof LoginLevel) {
            $loginLevels = [$loginLevels->code];
        }

        $loginLevels = collect()->make($loginLevels)->flatten()->map(function ($loginLevel) {
            return $loginLevel instanceof LoginLevel ? $loginLevel->code : $loginLevel;
        });

        return count($this->loginLevelCodes()) == $loginLevels->count() && $this->hasAllLLs($loginLevels);
    }

    protected function convertPipeToArray(string $pipeString)
    {
        $pipeString = trim($pipeString);

        if (strlen($pipeString) <= 2) {
            return $pipeString;
        }

        $quoteCharacter = substr($pipeString, 0, 1);
        $endCharacter = substr($quoteCharacter, -1, 1);

        if ($quoteCharacter !== $endCharacter) {
            return explode('|', $pipeString);
        }

        if (! in_array($quoteCharacter, ["'", '"'])) {
            return explode('|', $pipeString);
        }

        return explode('|', trim($pipeString, $quoteCharacter));
    }

    /**
     * Check if the given login level is applicable to the user.
     *
     * @param string|LoginLevel $loginLevel
     * @return bool
     */
    public function checkLL(string|LoginLevel $loginLevel): bool
    {
        if ($loginLevel instanceof LoginLevel) {
            $loginLevel = $loginLevel->code;
        }

        $method = \Str::camel('checkLL_'.\Str::snake($loginLevel));

        if( !method_exists(self::class, $method) ){
            return false;
        }

        return $this->{$method}();
    }

    /**
     * Check if the given login level is applicable to the user and assign it if it is.
     *
     * @param string|LoginLevel $loginLevel
     * @return bool
     */
    public function checkAndAssignLL(string|LoginLevel $loginLevel): bool
    {
        $state = $this->checkLL($loginLevel);

        if( $state ){
            $this->assignLL($loginLevel);
        }

        return $state;
    }

    /**
     * Update user login levels
     */
    public function updateLoginLevels(bool $manualOverride = false): bool
    {
        try {
            logDebug('init');

            $loginLevels = LoginLevel::all();
            $applicableLoginLevels = [];
            $dependentLoginLevels = [];

            foreach ($loginLevels as $loginLevel) {
                if( $this->checkLL($loginLevel) ){
                    $applicableLoginLevels[$loginLevel->code] = (object)[
                        'code' => $loginLevel->code,
                        'manual_override' => $manualOverride,
                    ];
                }
                if( !empty( getField($loginLevel, 'data.dependencies') ) ) {
                    array_push($dependentLoginLevels, $loginLevel);
                }
            }

            $ll = $this->loginLevels() ?? [];
            ksort($ll);
            $this->setLoginLevels($ll);
            ksort($applicableLoginLevels);

            if( $applicableLoginLevels != ($this->loginLevelCodes()) ){
                $this->setLoginLevels($applicableLoginLevels);
                $this->save();
            }

            foreach ($dependentLoginLevels as $dependentLoginLevel){
                if( $this->recursiveUpdateLL($dependentLoginLevel) ){
                    $applicableLoginLevels[$dependentLoginLevel->code] = (object)[
                        'code' => $dependentLoginLevel->code,
                        'manual_override' => $manualOverride,
                    ];
                }
            }

//            $applicableLoginLevels = array_unique($applicableLoginLevels);

            $ll = $this->loginLevels() ?? [];
            ksort($ll);
            $this->setLoginLevels($ll);
            ksort($applicableLoginLevels);

            if( $applicableLoginLevels != ($this->loginLevels() ?? []) ){
                $updatedLoginLevels = $applicableLoginLevels;

                if( !$manualOverride ){
                    foreach ($updatedLoginLevels as $code => $updatedLoginLevel) {
                        $llManualOverride = data_get($this->loginLevels(), "$updatedLoginLevel.manual_override") === true;
                        $updatedLoginLevels[$code] = data_set($updatedLoginLevels[$code], 'manual_override', $llManualOverride);
                    }
                }
                $this->setLoginLevels($updatedLoginLevels);
                $this->save();
            }

//            logInfo('user login levels updated('.implode('|', $this->loginLevelCodes()).')', json_encode($this->loginLevelCodes()));
            logDebug('finish');

            return true;
        } catch (\Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line ' . $e->getLine());

            return false;
        }
    }

    /**
     * Check if the given login level is applicable to the user.
     *
     * @param LoginLevel $loginLevel
     * @param int $depth
     * @param array $stack
     * @return bool
     */
    public function recursiveUpdateLL($loginLevel, int $depth = 0, array $stack = []): bool
    {
        if( $depth > 10 || in_array($loginLevel->code, $stack)){
            return false;
        }

        $requiredLoginLevels = getField($loginLevel, 'data.dependencies');

        if( empty($requiredLoginLevels) || !is_array($requiredLoginLevels) ){
            $requiredLoginLevels = [];
        }

        foreach ( $requiredLoginLevels ?? [] as $requiredLoginLevel ){
            $loginLevelModel = LoginLevel::whereCode($requiredLoginLevel)->first();

            if( !empty($loginLevelModel) ){
                array_push($stack, $requiredLoginLevel);
                $this->recursiveUpdateLL($loginLevelModel, $depth+1, $stack);
            }
        }

        if( $result = $this->checkLL($loginLevel->code) ){
            $this->assignLL($loginLevel);
        }

        return $result;
    }
}
