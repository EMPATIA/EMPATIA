<div>
    <div class="form-group files">
        <label>{{$label}} </label>
        <div class="" style="">
            <livewire:file-preview
                    :name="$name"
                    :type="$type"
                    :files="$files"
                    :single="($maxFiles ?? 0) == 1"
                    containerClass="d-flex justify-content-center"
                    containerStyle="max-height: 400px; overflow-y: auto; overflow-x: hidden"
            />
        </div>
        <input type="hidden" id="{{$name}}" name="{{$name}}" wire:model="value">
        @if(!\App\Helpers\HForm::isShow())
            @livewire('file-upload', [
                'view'      => 'livewire.backend.cms.file.file-upload',
                'type'      => $type,
                'maxSize'   => $maxSize,
                'maxFiles'  => 1,
                'misc'      => [
                    'dropzone' => false,
                    'jsOptions' => [
                        'clearMessages' => true,
                        'disableProgress' => false
                    ],
                    'button-container' => [
                        'class' => 'd-flex justify-content-center'
                        ]
                ],
            ] + ( $maxFiles > 0 ? ['maxFiles' => $maxFiles] : []))
        @endif
    </div>
</div>
