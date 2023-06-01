<div>
    @php
        use App\Helpers\HFrontend;

        $projectPath = HFrontend::getProjectPath(true);
    @endphp

    <div class="form-group files">
        <x-form-label class="mb-0 text-muted small" :label="$label" :for="$name" />
        <div class="" style="">
            @livewire('file-preview', [
                'view'      => "livewire.backend.files.file-preview",
                'name'      => $name,
                'type'      => $type,
                'files'     => $files,
                'single'    => ($maxFiles ?? 0) == 1,
                'containerClass'    => "",
                'containerStyle'    => "max-height: 400px; overflow-y: auto; overflow-x: hidden",
            ])
        </div>
        <input type="hidden" id="{{$name}}" name="{{$name}}" wire:model="value">
        @if( $action != 'show' )
            @livewire('file-upload', [
                'view'      => "livewire.backend.files.file-upload",
                'type'      => $type,
                'maxSize'   => $maxSize,
                'misc'      => [
                    'dropzone' => false,
                    'jsOptions' => [
                        'clearMessages' => true,
                        'disableProgress' => true
                    ],
                    'button-container' => [
                    'class' => 'd-flex justify-content-center'
                    ]
                ],
            ] + ( $maxFiles > 0 ? ['maxFiles' => $maxFiles] : []))
        @endif
    </div>
</div>
