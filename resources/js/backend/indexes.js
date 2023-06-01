function modelIndexScripts() {
    let cancelButton = indexesTranslations['buttonActions']['cancel'];
    let confirmButton = indexesTranslations['buttonActions']['confirm'];

    $('.delete-entry').unbind('click').click(function () {
        let action = $(this).attr('data-action');           // If using controller actions
        
        let identifier = $(this).attr('data-identifier');   // If using component actions
        let component = $(this).attr('data-component');     // If using component actions
        
        let deleteTitle = indexesTranslations['delete']['title'];
        let deleteMessage = indexesTranslations['delete']['message']

        Swal.fire({
            title: deleteTitle,
            text: deleteMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmButton,
            cancelButtonText: cancelButton,
            focusConfirm: false,
        }).then((result) => {
            if (result.isConfirmed) {
                if(component.trim().length === 0){
                    $.ajax({
                        url: action,
                        type: "delete",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function () {
                            Livewire.emit('refreshDatatable');
                        },
                        error: function () {
                            Livewire.emit('refreshDatatable');
                        },
                    });
                } else {
                    Livewire.emitTo(component, 'destroy', identifier);
                }
            }
        })
    });

    $('.restore-entry').unbind('click').click(function () {
        let action = $(this).attr('data-action');           // If using controller actions

        let identifier = $(this).attr('data-identifier');   // If using component actions
        let component = $(this).attr('data-component');     // If using component actions

        let restoreTitle = indexesTranslations['restore']['title'];
        let restoreMessage = indexesTranslations['restore']['message'];
        
        Swal.fire({
            title: restoreTitle,
            text: restoreMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButton,
            cancelButtonText: cancelButton
        }).then((result) => {
            if (result.isConfirmed) {
                if(component.trim().length === 0){
                    $.ajax({
                        url: action,
                        type: "patch",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function () {
                            Livewire.emit('refreshDatatable');
                        },
                        error: function () {
                            Livewire.emit('refreshDatatable');
                        },
                    });
                }else{
                    Livewire.emitTo(component, 'restore', identifier);
                }
            }
        })
    });
}
