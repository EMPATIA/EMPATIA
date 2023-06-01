<?php

namespace App\Traits;

use Exception;
use \Illuminate\Validation\ValidationException;
use \Illuminate\Http\Request;
use Throwable;

trait LivewireForm
{
    /**
     * Default parameters property name.
     */
    public static string $PARAMETERS_PROPERTY = 'parameters';
    /**
     * Default request parameter name prefix.
     */
    public static string $PARAMETER_PREFIX = 'parameter_';
    
    /**
     * The controller class for the form.
     * @var $controllerClass
     */
    /**
     * The controller validation rules property.
     * @var string $controllerRulesProperty
     */
    /**
     * Array to map inputs to specific component properties (TODO).
     * @var array $inputMap
     */
    /**
     * The controller validation messages property.
     * @var string $controllerMessagesProperty
     */
    /**
     * The parameter prefix used in the controller.
     * @var string $parameterPrefix
     */
    /**
     * The component's property name containing the parameters.
     * @var string $parametersPropertyName
     */
    /**
     * Whether to validate when calling controller method.
     * @var bool $validateBeforeRequest
     */
    /**
     * Whether to catch the controller error messages when calling its methods.
     * @var bool $catchValidatorErrors
     */
    /**
     * Whether to add all the component's public properties to the request.
     * @var bool $addAllPropertiesToRequest
     */
    /**
     * The controller instance.
     */
    private $controller;
    
    /**
     * Runs before instantiating the controller.
     * @function bootController()
     */
    
    /**
     * Override the component 'booted' method.
     */
    public function booted()
    {
        if( method_exists(parent::class, 'booted') ) parent::booted();
        if( method_exists($this, 'bootController') ) $this->bootController();
        
        $this->controller = resolve($this->controllerClass ?? null);
    }
    
    /**
     * @return ?string
     */
    public function getRulesProperty() : ?string
    {
        return (!empty($this->controllerRulesProperty) && is_string($this->controllerRulesProperty))
            ? $this->controllerRulesProperty : null;
    }
    
    /**
     * @return ?string
     */
    public function getMessagesProperty() : ?string
    {
        return (!empty($this->controllerMessagesProperty) && is_string($this->controllerMessagesProperty))
            ? $this->controllerMessagesProperty : null;
    }
    
    /**
     * Override the component's rule() method.
     *
     * @return null|array
     */
    public function rules() : ?array
    {
        $rules = (method_exists(parent::class, 'rules') ? parent::rules() : null)
            ?? $this->rules
            ?? data_get($this->controller, $this->getRulesProperty() ?? 'validateRules')
            ?? [];
        
        return $this->mapNamesToProperties($rules);
    }
    
    /**
     * Override the component's message() method.
     *
     * @return null|array
     */
    public function messages() : ?array
    {
        $messages = (method_exists(parent::class, 'messages') ? parent::messages() : null)
            ?? $this->messages
            ?? data_get($this->controller, $this->getMessagesProperty() ?? 'validateMessages')
            ?? [];
        
        return $this->mapNamesToProperties($messages);
    }
    
    /**
     * Get input names from rules.
     *
     * @return null|array
     */
    public function getNamesFromRules() : ?array
    {
        $rules = (method_exists(parent::class, 'rules') ? parent::rules() : null)
            ?? $this->rules
            ?? data_get($this->controller, $this->getRulesProperty() ?? 'validateRules')
            ?? [];
        
        return array_keys($rules);
    }
    
    /**
     * Calls a method on the controller instance.
     *
     * @param string $method    Name of the method to call
     * @param mixed ...$args    Arguments to pass to the method
     * @return mixed
     * @throws ValidationException|Exception|Throwable
     */
    public function controller(string $method, ...$args) : mixed
    {
        try {
            return $this->controller->{$method}(...$args);
            
        } catch (ValidationException|Exception|Throwable $e) {
            if ( $this->shouldCatchValidatorErrors() && $validator = ($e->validator ?? null) ) {
                $this->setErrorBag( $this->mapNamesToProperties($validator->errors()?->messages() ?? []) );
            } else {
                throw $e;
            }
            
            return null;
        }
    }
    
    /**
     * Calls a method on the controller instance passing a request object.
     *
     * @param string $method    Name of the method to call
     * @param mixed ...$args    Arguments to pass to the method
     * @return mixed
     * @throws ValidationException|Exception|Throwable
     */
    public function makeRequest(string $method, ...$args) : mixed
    {
        if( !$this->hasConditionsToMakeRequest() ){
            return null;
        };
        
        if( $this->shouldValidateBeforeRequest() ){
            $this->validate();
        }
        
        $this->overrideRouteActionController($method);
        
        // add request to the beginning of the arguments array
        array_unshift($args, $this->buildControllerRequest());
        
        return $this->controller($method, ...$args);
    }
    
    /**
     * Format controller keys to component properties.
     *
     * @param array $keys
     * @return array
     */
    public function formatToComponentProperties(array $keys) : array
    {
        $parameterPrefix        = $this->parameterPrefix();
        $parametersPropertyName = $this->parametersPropertyName();
        
        return array_map(function($key) use ($parameterPrefix, $parametersPropertyName){
            if( stripos($key, $parameterPrefix) === 0 ){
                $key = "$parametersPropertyName." . substr($key, strlen($parameterPrefix));
            }
            return str_replace('->', '.', $key);
        }, $keys);
    }
    
    /**
     * Format component properties to controller keys.
     *
     * @param array $keys
     * @return array
     */
    public function formatToControllerKeys(array $keys) : array
    {
        $parametersPropertyName = $this->parametersPropertyName();
        $parameterPrefix        = $this->parameterPrefix();
        
        return array_map(function($key) use ($parametersPropertyName, $parameterPrefix){
            if( stripos($key, $parametersPropertyName) === 0 ){
                $key = "$parameterPrefix" . substr($key, strlen($parametersPropertyName)+1);
            }
            return str_replace('.', '->', $key);
        }, $keys);
    }
    
    /**
     * Map the controller names to the component properties.
     *
     * @param array $array
     * @return array
     */
    public function mapNamesToProperties(array $array) : array
    {
        return array_combine($this->formatToComponentProperties(array_keys($array)), array_values($array));
    }
    
    /**
     * Map the component properties to the controller names.
     *
     * @param array $array
     * @return array
     */
    public function mapPropertiesToNames(array $array) : array
    {
        return array_combine($this->formatToControllerKeys(array_keys($array)), array_values($array));
    }
    
    /**
     * Builds the request object to be sent to the controller.
     *
     * @return Request
     */
    public function buildControllerRequest() : Request
    {
        $request = new Request;
        $request->merge( $this->getInputForRequest() );
        
        return $request;
    }
    
    /**
     * Overrides the route action controller.
     *
     * @param string $method
     * @return void
     */
    public function overrideRouteActionController(string $method) : void
    {
        tap(\Route::current(), function($route) use ($method){
            $route->setAction(
                array_merge($route->getAction() ?? [], ['controller' => "$this->controllerClass@$method"])
            );
        });
    }
    
    /**
     * Get the formatted input array to merge with the request.
     * @return array
     */
    public function getInputForRequest() : array
    {
        $input = [];
        
        $inputMap = is_array($this->inputMap ?? null) ? $this->inputMap : [];
        $publicProperties = $this->getPublicPropertiesDefinedBySubClass();
        
        $inputNames         = $this->getNamesFromRules();                           // returns things like "title->en"
        $propertykeys       = $this->formatToComponentProperties($inputNames); // returns things like "title.en"
        $parameterPrefix    = $this->parameterPrefix();
        
        // fill input array with controller input name as key and the corresponding property as value
        for ($i = 0; $i < count($inputNames); ++$i) {
            $name           = $inputNames[$i];
            $propertyKey    = $propertykeys[$i];
            $propertyValue  = data_get($publicProperties, $propertyKey);
            
            // process parameters
            if( stripos($name, $parameterPrefix) === 0 && is_array($propertyValue) ){
                foreach ($propertyValue as $code => $parameter){
                    $input[ $parameterPrefix . $code ] = $parameter;
                }
                continue;
            }
            
            $input[ $name ] = $propertyValue;
        }
        
        if( $this->shouldAddAllPropertiesToRequest() ){
            foreach ($publicProperties as $name => $property){
                if( !isset($input[$name]) ){
                    $input[$name] = $property;
                }
            }
        }
        
        return $input;
    }
    
    /**
     * Get the parameters property name.
     * @return string
     */
    public function parametersPropertyName() : string
    {
        return $this->parametersPropertyName ?? self::$PARAMETERS_PROPERTY;
    }
    
    /**
     * Get the parameter name prefix.
     * @return string
     */
    public function parameterPrefix() : string
    {
        return $this->parameterPrefix ?? self::$PARAMETER_PREFIX;
    }
    
    /**
     * Whether validate is called before calling the controller method
     *
     * @return bool
     */
    public function shouldValidateBeforeRequest() : bool
    {
        return ($this->validateBeforeRequest ?? true) === true;
    }
    
    /**
     * Whether validation errors should be catched when calling controller method.
     *
     * @return bool
     */
    public function shouldCatchValidatorErrors() : bool
    {
        return $this->catchValidatorErrors ?? true;
    }
    
    public function hasConditionsToMakeRequest() : bool
    {
        return !empty($this->controllerClass) && !empty($this->controller);
    }
    
    public function shouldAddAllPropertiesToRequest() : bool
    {
        return ($this->addAllPropertiesToRequest ?? false) === true;
    }
}