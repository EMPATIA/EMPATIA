function modelDeleteScripts() {
    let cancelButton = indexesTranslations['buttonActions']['cancel'];
    let confirmButton = indexesTranslations['buttonActions']['confirm'];
    
    $('.delete-entry-show').unbind('click').click(function () {
        let id = $(this).data('id');
        let route = $(this).data('delete');
        let index = $(this).data('index');
    
        // Text
        let deleteTitle = indexesTranslations['delete']['title'];
        let deleteMessage = indexesTranslations['delete']['message']

        Swal.fire({
            title: deleteTitle,
            text: deleteMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButton,
            cancelButtonText: cancelButton
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(route);
                $.ajax({
                    url:route,
                    type:"delete",
                    data:{
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function () {
                        location.href = index;
                    },
                    error: function () {
                        location.reload();
                    },
                })
            }
        })

    });
}