$(function(){

    $('#myGrid').on('click','.btn-delete',function(e){
        e.preventDefault();

        var id=$(this).data('id');
        var form = $('#form');
        var url = form.attr('action').replace('AGENT_ID', id);
        var data = {'id': id};

        bootbox.dialog({
            message: 'Are you sure?',
            title: 'Delete Agent',
            buttons: {
                success: {
                    label: '<i class="fas fa-ban"></i> Cancel',
                    className: 'btn-success',
                    callback: function() {

                    }
                },
                danger: {
                    label: '<i class="far fa-trash-alt"></i> Delete',
                    className: 'btn-danger',
                    callback: function() {
                        $.post(url, data, function (result) {

                            var removed=result.removed;
                            var message=result.message;

                            $('#message-removed').html(message);

                            if(removed==1){

                              var selectedData = gridOptions.api.getSelectedRows();
                              var res = gridOptions.api.updateRowData({remove: selectedData});

                              $('.cont-alert-removed-true').fadeIn(300);

                              setTimeout(function(){
                                    $('.cont-alert-removed-true').fadeOut(500);
                              }, 3000);

                            }else{
                                $('.cont-alert-removed-false').fadeIn(300);

                                setTimeout(function(){
                                    $('.cont-alert-removed-false').fadeOut(500);
                                }, 3000);
                            }

                        })
                    }
                }
            }

        });

    });

});