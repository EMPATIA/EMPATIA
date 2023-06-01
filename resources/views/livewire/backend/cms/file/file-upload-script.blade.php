<script>
    if( typeof fileUpload == 'undefined' ){
        fileUpload = {
            instances: {},

            addInstance: function(form, id, component, options = {}){
                console.log(`Starting FileUpload Instance [${id}]`, form);

                this.instances[id] = {
                    form, id, component, options
                };

                this.instances[id].dropzone         = $(form).find('.file-upload-drag-drop');
                this.instances[id].progress         = $(form).find('.file-upload-progress');
                this.instances[id].progressbar      = $(form).find('.file-upload-progress .progress-bar');
                this.instances[id].container        = $(form).find('.file-upload-container');
                this.instances[id].input            = $(form).find("input");

                this.bindDropzone(id);

                $(this.instances[id].input).on('change', (e) => {
                    this.inputChanged(id, e);
                }).on('livewire-upload-start', () => {
                    this.start(id);
                }).on('livewire-upload-progress', (event) => {
                    this.progress(id, event);
                }).on('livewire-upload-finish', () => {
                    this.success(id);
                }).on('livewire-upload-error', () => {
                    this.error(id);
                });
            },
            bindDropzone: function(id){
                let dropzone    = this.instances[id].dropzone;
                let form        = this.instances[id].form;

                if( typeof dropzone == 'undefined'){
                    return false;
                }

                dropzone.click( e => {
                    $(this.instances[id].input).trigger('click');
                });

                dropzone.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                })

                dropzone.on('dragover dragenter', () => {
                    $(form).addClass('is-dragover border-primary text-primary');
                    $(form).removeClass('is-dragover border-secondary text-secondary');
                })
                dropzone.on('dragleave dragend drop', () => {
                    $(form).addClass('border-secondary text-secondary');
                    $(form).removeClass('is-dragover border-primary text-primary');
                })

                dropzone.on('drop', e => {
                    let files = e.originalEvent.dataTransfer.files;
                    this.start(id);
                    this.instances[id].component.uploadMultiple(
                        'files',
                        files,
                        () => { this.success(id) },
                        () => { this.error(id) },
                        (event) => { this.progress(id, event) }
                    )
                });
            },

            inputChanged: function (id, event){
                // const files = Object.assign({}, this.instances[id].input[0].files);
                const files = this.instances[id].input[0].files;

                this.instances[id].component.validateFilesQuantity(files)
                    .then(
                        (value) => {
                            console.log('validateFilesQuantity request sucess');
                            if( !this.instances[id].component.filesValidationError ){
                                this.start(id);
                                this.instances[id].component.uploadMultiple(
                                    'files',
                                    files,
                                    () => { this.success(id) },
                                    () => { this.error(id) },
                                    (event) => { this.progress(id, event) }
                                )
                            }
                        },
                        () => { console.log('validateFilesQuantity request fail'); }
                    );
            },

            start: function(id){
                if(typeof this.instances[id].dropzone != 'undefined'){
                    $(this.instances[id].dropzone).off();
                }
                this.instances[id].component.uploadProgress = 0;
                this.instances[id].component.uploadUploading = true;
                this.instances[id].component.uploadSuccess = false;
            },
            progress: function(id, event){
                if( this.getOption(id, 'disableProgress') === true ){
                    return;
                }

                event = typeof event.originalEvent != 'undefined' ? event.originalEvent : event;
                this.instances[id].component.uploadProgress = event.detail.progress;
            },
            success: function(id){
                let component = this.instances[id].component;

                component.uploadUploading = false;

                if( component.uploadValidated )
                    component.saveFiles()
                else
                    component.uploadError = true;

                this.instances[id].input[0].value = '';

                this.bindDropzone(id);

                if( this.getOption(id, 'clearMessages') === true ){
                    setTimeout(() => {
                        component.uploadUploading = false;
                        component.uploadSuccess = false;
                        component.uploadError = false;
                        component.uploadProgress = 0;
                    }, this.getOption(id, 'messagesTimeout') ? this.getOption(id, 'messagesTimeout') : 7000);
                }
            },
            error: function(id){
                let component = this.instances[id].component;

                component.uploadUploading = false;
                component.uploadSuccess = false;
                component.uploadError = true;

                this.instances[id].input[0].value = '';

                this.bindDropzone(id);

                if( this.getOption(id, 'clearMessages') === true ){
                    setTimeout(() => {
                        component.uploadUploading = false;
                        component.uploadSuccess = false;
                        component.uploadError = false;
                    }, this.getOption(id, 'messagesTimeout') ? this.getOption(id, 'messagesTimeout') : 7000);
                }
            },

            getOption: function (id, name){
                let instance = this.instances[id];
                if( typeof instance == 'undefined' ){
                    return undefined;
                }

                let options = this.instances[id].options;
                if( typeof options == 'undefined' ){
                    return undefined;
                }

                let value = options[name];
                if( typeof value == 'undefined' ){
                    return undefined;
                }

                return value;
            }
        };
    }

    document.addEventListener('livewire:load', function() {
        fileUpload.addInstance($('.file-upload #{{ $uploadId }}'), '{{ $uploadId }}', @this, {{ Illuminate\Support\Js::from(getField($misc, 'jsOptions', [])) }});
    })
</script>