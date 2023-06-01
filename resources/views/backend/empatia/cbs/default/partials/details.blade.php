@php
    use App\Helpers\HForm;
    use \Illuminate\Support\Facades\Auth;
@endphp
<x-backend.card container="col-12">
        <x-backend.card-header>
            {{__('backend.empatia.cbs.header')}}
        </x-backend.card-header>

        <x-backend.card-body>
            <x-backend.form
                :store="route('cbs.store', ['type' => $type])"
                :update="route('cbs.update', ['type' => $type, 'id' => $modelId])"
            >
                @bind($model ?? null)
                <div class="row">
                    @if( Auth::user()->hasAnyRole(['admin','laravel-admin']) )
                        <div class="col-sm-6">
                            <x-backend.form.input
                                :action="HForm::getAction()"
                                name="template"
                                :label="__('backend.empatia.cbs.form.template.label')"
                                :placeholder="__('backend.empatia.cbs.form.template.placeholder')"
                            >
                            </x-backend.form.input>
                            <x-backend.form.input
                                :action="HForm::getAction()"
                                name="code"
                                :label="__('backend.empatia.cbs.form.code.label')"
                                :placeholder="__('backend.empatia.cbs.form.code.placeholder')"
                            >
                            </x-backend.form.input>
                        </div>
                    @endif
                    <div class="col-sm-6">
                        <x-backend.form.input
                            action="show"
                            name="dates"
                            :label="__('backend.empatia.cbs.form.dates.label')"
                            :placeholder="__('backend.empatia.cbs.form.dates.placeholder')"
                        >
                        </x-backend.form.input>
                        <x-backend.form.input-lang
                            :action="HForm::getAction()"
                            name="slug"
                            mandatory="true"
                            :value="getField($model, 'slug')"
                            :label="__('backend.empatia.cbs.form.slug.label')"
                            :placeholder="__('backend.empatia.cbs.form.slug.placeholder')"
                        />
                    </div>
                </div>
            </x-backend.form>
        </x-backend.card-body>
</x-backend.card>
