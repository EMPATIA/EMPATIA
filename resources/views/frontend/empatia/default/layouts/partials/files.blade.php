@php
    use App\Http\Controllers\Backend\FilesController;
@endphp
<div class="row">
    @foreach($filesCodes ?? [] as $fileCode)
        @php
            if(is_string($fileCode)){
                $file = FilesController::getFileByName($fileCode);
                $fileURL = FilesController::getFileUrlByName($fileCode);
                $fileSize = bytesToHuman($file->size);
                $fileName = data_get($file, 'original');
            }
        @endphp

        @if(!empty($fileURL))
            <div class="row mb-2">
                <div class="col">
                     <span class="float-start">
                <a href="{{ $fileURL }}">
                    <i class="fa fa-download me-2"></i>{{ $fileName ?? '' }}
                </a>
                </span>
                </div>
               <div class="col-auto">
                   <span class="float-end">{{ $fileSize }}</span>
               </div>
            </div>
        @endif
    @endforeach
</div>
