@if($floating)
    <div class="form-floating"> @endif

        @if(!$floating)
            <x-form-label class="mb-0 text-muted small" :label="$label" :for="$attributes->get('id') ?: $id()"/>
        @endif

        @if(!empty($attributes->get('action')) && \App\Helpers\HForm::isShow($attributes->get('action') ?? null))
            <div class="form-control-plaintext pt-0">
                @forelse($options as $key => $option)
                    @if($isSelected($key)) {{ $option }} @endif
                @empty
                    {!! $slot !!}
                @endforelse
            </div>
        @else
            <select
                @if($isWired())
                    wire:model{!! $wireModifier() !!}="{{ $name }}"
                @endif

                name="{{ $name }}"

                @if($multiple)
                    multiple
                @endif

                @if($placeholder)
                    placeholder="{{ $placeholder }}"
                @endif

                @if($label && !$attributes->get('id'))
                    id="{{ $id() }}"
                @endif

                {!! $attributes->merge(['class' => 'form-select' . ($hasError($name) ? ' is-invalid' : '')]) !!}
            >


            @if($placeholder)
                <option value=""  @if($nothingSelected()) selected="selected" @endif>
                    {{ $placeholder }}
                </option>
            @endif

            @forelse($options as $key => $option)
                <option value="{{ $key }}" @if($isSelected($key)) selected="selected" @endif>
                    {{ $option }}
                </option>
            @empty
                {!! $slot !!}
            @endforelse
        </select>
        @endif

        @if($floating)
            <x-form-label :label="$label" :for="$attributes->get('id') ?: $id()"/>
        @endif

        @if($floating) </div>
@endif

{!! $help ?? null !!}

@if($hasErrorAndShow($name))
    <x-form-errors :name="$name"/>
@endif
