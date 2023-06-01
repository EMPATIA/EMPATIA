<?php

namespace App\Traits;

trait LivewireWithSushi
{
    /**
     * Use the components' 'boot' lifecycle hook.
     *
     * @return void
     */
    public function bootLivewireWithSushi(): void
    {
        // Link the Sushi models on every request to support Livewire properties of Model type

        $request = request();

        foreach ($this->sushiLinks ?? [] as $sushiProperty => $linkedProperty){
            $this->resolveLink($sushiProperty, $linkedProperty, $request);
        }
    }

    public function resolveLink(string $sushiProperty, string $linkedProperty, mixed $request = null): void
    {
        $request = $request ?? request();

        // Gets the Livewire's Model identifiers
        $linkedModelIdentifier =
            data_get($request, "serverMemo.dataMeta.models.$linkedProperty") ??
            data_get($request, "serverMemo.dataMeta.modelCollections.$linkedProperty");
        $sushiModelIdentifier =
            data_get($request, "serverMemo.dataMeta.models.$sushiProperty") ??
            data_get($request, "serverMemo.dataMeta.modelCollections.$sushiProperty");

        // Ignore if one of them is missing
        if( empty($linkedModelIdentifier) || empty($sushiModelIdentifier) ){
            return;
        }

        // Attempt to instantiate the Models' classes statically
        // Laravel's own exceptions will be thrown if something is misused
        $linkedStaticModel  = app(data_get($linkedModelIdentifier, 'class'));
        $sushiStaticModel   = app(data_get($sushiModelIdentifier, 'class'));

        // Attempt to get the model instance to be linked
        $linkedModel = $linkedStaticModel::find(data_get($linkedModelIdentifier, 'id', 0));

        // Attempt to link the model to the Sushi model static
        $sushiStaticModel::linkModel($linkedModel);
    }

    /**
     * Use the components' 'booted' lifecycle hook.
     *
     * @return void
     */
    public function bootedLivewireWithSushi(): void
    {
        // Link the Sushi models automatically every time the component is booted
        // TODO: Might not be usefull

        // Allows to disable automatic linkage
        if( !$this->shouldLinkModels() ){
            return;
        }

        foreach ($this->sushiLinks ?? [] as $sushiProperty => $linkedProperty){
            // Gets the Livewire's property values
            $linkedPropertyValue  = $this->{$sushiProperty} ?? null;
            $sushiPropertyValue   = $this->{$linkedProperty} ?? null;

            // Ignore if one of them is missing
            if( empty($linkedPropertyValue) || empty($sushiPropertyValue) ){
                continue;
            }

            // Attempt to instantiate the Sushi model class statically
            // Laravel's own exceptions will be thrown if something is misused
            $sushiStaticModel = app(get_class($sushiPropertyValue));

            // Attempt to link the model to the sushi model static
            $sushiStaticModel::linkModel($linkedPropertyValue);
        }
    }


    /**
     * Whether Sushi models should be linked automatically.
     *
     * @return bool
     */
    public function shouldLinkModels(): bool
    {
        return ($this->linkSushiModelsAutomatically ?? false) === true;
    }
}
