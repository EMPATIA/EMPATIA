@extends('backend::layouts.master')

@section('header')
    @if(HForm::isEdit($action ?? null))
        {{ __('cbs::cbs.form.edit.header') }}
    @elseif(HForm::isCreate($action ?? null))
        {{ __('cbs::cbs.form.create.header') }}
    @else
        {{ __('cbs::cbs.form.show.header') }}
    @endif
@endsection

@section('content')
    @if(HForm::isShow())
        <div class="card-body pb-0">
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    @include('empatia::cbs.cbs.partials._cb-details')
                    @if(\Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
                        <livewire:cb-parameters :cb="$cb"/>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>

        window.addEventListener('reloadScripts', event => {
            loadScripts();
        });


        loadScripts();
        function loadScripts() {

            // $('.menu-sort-container').sortable({
            //     handle: '.drag_handle',
            //     group: 'nested',
            //     animation: 600,
            //     ghostClass: 'drag_drop_class',
            //     fallbackOnBody: true,
            //     swapThreshold: 0.65,
            //
            //     onEnd: function (evt) {
            //         var lista = $('.menu-sort-container').sortable('toArray');
            //         let cbid = $(evt.item).data('cbid');
            //         Livewire.emit('CbsMoved', lista, cbid);
            //     }
            //     // onEnd: function (evt) {
            //     //     let id = $(evt.item).data('id');
            //     //     let cbid = $(evt.item).data('cbid');
            //     //     let newIndex = evt.newIndex;
            //     //     // var item = evt.oldIndex;
            //     //     Livewire.emit('CbsMoved', id, cbid, newIndex);
            //     // }
            //
            // });

            $(".parameter-delete").unbind('click').click(function (e) {
                e.preventDefault();
                let id = $(this).attr('data-content-id');
                bootbox.confirm({
                    title: "{{ __('cbs::cbs.parameter.delete.title') }}",
                    message: "{{ __('cbs::cbs.parameter.delete.message') }}",
                    buttons: {
                        cancel: {
                            label: '{{ __('cbs::cbs.parameter.delete.cancel') }}'
                        },
                        confirm: {
                            label: '{{ __('cbs::cbs.parameter.delete.confirm') }}',
                            className: 'btn-danger',
                        }
                    },
                    callback: function (result) {
                        if (result) {
                            Livewire.emitTo('cb-parameters', 'destroy', id);
                        }
                    }
                });
            });
        }

        //_tab-content toggle parameters details modal
        window.addEventListener('closeParametersModals', event => {
            $('#parameters-modal').modal('hide');

        });

        window.addEventListener('openParametersModal', event => {
            $('#parameters-modal').modal('show');
        });

        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('component.initialized', (component) => {
                loadScripts();
            })
            Livewire.hook('message.processed', (message, component) => {
                loadScripts();
            })
        });
    </script>
@endsection
