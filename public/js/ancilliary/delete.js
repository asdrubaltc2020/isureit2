$(function(){

    $('#myGrid').on('click','.btn-delete',function(e){
        e.preventDefault();

        var id=$(this).data('id');
        var form = $('#form');
        var url = form.attr('action').replace('ANCILLARY_ID', id);
        var data = {'id': id};

        console.log(url)

        bootbox.dialog({
            message: 'Are you sure?',
            title: 'Delete',
            buttons: {
                success: {
                    label: '<i class="fas fa-ban"></i> Cancel',
                    className: 'btn-success',
                    callback: function() { }
                },
                danger: {
                    label: '<i class="far fa-trash-alt"></i> Delete',
                    className: 'btn-danger',
                    callback: function() {
                        $.post(url, data, function (result) {

                            var removed=result.removed;
                            var message=result.message;

                            if(removed==1){
                              var selectedData = gridOptions.api.getSelectedRows();
                              var res = gridOptions.api.updateRowData({remove: selectedData});

                                new PNotify({
                                    title: 'Success',
                                    text: message,
                                    icon: 'icon-checkmark3',
                                    addclass: 'bg-success border-success'
                                })

                            }else{
                                $('.cont-alert-removed-false').fadeIn(300);
                                new PNotify({
                                    title: 'Error',
                                    text: message,
                                    icon: 'icon-checkmark3',
                                    addclass: 'bg-danger border-danger'
                                })
                            }
                        })
                    }
                }
            }
        })
    })
})