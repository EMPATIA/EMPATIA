@if(session()->has('success') || session()->has('fail') || $errors->any())
    <div style="position: fixed; bottom: 0; right: 0; margin: 10px 20px">
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-white @if(session()->has('success')) bg-success @else bg-danger @endif">
                <strong class="mr-auto">
                    @if(session()->has('success'))
                        {{ __('backend.cms.contents.success.title') }}
                    @else
                        {{ __('backend.cms.contents.fail.title') }}
                    @endif
                </strong>
                <button type="button" class="ms-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>

            <div class="toast-body">
                @if(session()->has('success'))
                    {{ session('success') }}
                @elseif(session()->has('fail'))
                    {{ session('fail') }}
                @elseif($errors->any())
                    @foreach($errors->getBags() as $bag)
                        @foreach($bag->toArray() as $key => $value)
                            @error($key)
                            {{ $key.": ".$message }}<br>
                            @enderror
                        @endforeach
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endif
