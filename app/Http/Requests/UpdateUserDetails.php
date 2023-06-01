<?php

namespace App\Http\Requests;

use App\Helpers\Empatia\Cbs\HCb;
use App\Helpers\HFrontend;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateUserDetails extends FormRequest
{
    protected $rulePrefix = 'parameters_';
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth()->user()->hasAnyRole(['laravel-user', 'laravel-admin'])) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $user = User::find( auth()?->user()?->id ?? 0 );
        $parameters = HFrontend::getConfigurationByCode('user_parameters');
        foreach ($parameters ?? [] as $key => $parameter) {
            if (HCb::isParameterMultilang($parameter)) {
                foreach (getLanguages() as $langKey => $language) {
                    if (getField($parameter, 'rules')) {
                        $rules[$this->rulePrefix . $parameter->code] = getField($parameter, 'rules') ?? '';
                    }
                }
            } else {
                if (getField($parameter, 'rules')) {
                    $rules[$this->rulePrefix . $parameter->code] = getField($parameter, 'rules');
                }
            }

            $individualRules = explode('|', $rules[$this->rulePrefix . $parameter->code] ?? '');

            if (($parameter->mandatory ?? false) && (!partial_in_array('required', $individualRules) && !partial_in_array('required_if', $individualRules))) {
                $individualRules[] = 'required';
                $rules[$this->rulePrefix . $parameter->code] = implode('|', $individualRules);
            }

            // when unique rule exists, ignore the current user
            if( partial_in_array('unique', $individualRules) && $user ){
                foreach ($individualRules as $key => $rule){
                    if( Str::startsWith($rule, 'unique:') ){
                        $parts = explode(':', $rule);
                        $argsString = $parts[1] ?? '';
                        $args = array_filter(explode(',', $argsString));
                        $individualRules[$key] = Rule::unique(...$args)->ignore($user);
                        $rules[$this->rulePrefix . $parameter->code] = $individualRules;
                        break;
                    }
                }
            }
        }
        return $rules;
    }
}
