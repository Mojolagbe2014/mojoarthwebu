//var fileextension = Array('.jpg','.gif','.bmp','.png');
//    $.ajax({
//        url: "../REST/fetch-gallery-images.php",
//        dataType: "json",
//        success: function (data) {
//            $.each(data, function(i,filename) {
//                $.each(fileextension, function(j, ext){
//                    if(filename.indexOf(ext)>=0)
//                        $('table').append('<tr><td><img width="40px" height="40px" src="'+ filename +'"></td><td>'+ filename.replace('../media/','') +'</td><td class="td-actions"><a href="delete-images?file='+ filename.replace('../media/gallery/','') +'" class="btn btn-danger btn-small"><i class="btn-icon-only icon-trash"> </i></a></td></tr>');
//                });
//            });
//        }
//    });
var dataTable;
$(document).ready(function(){
    dataTable = $('#gallerylist').DataTable( {
        columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   [0, 3]
            } ],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            order: [[ 1, 'asc' ]],
            "processing": true,
            "scrollX": true,
        "ajax":{
            url :"../REST/fetch-gallery-images.php",
            type: "post",  
            error: function(){  // error handling
                    $("#gallerylist-error").html("");
                    $("#gallerylist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#gallerylist_processing").css("display","none");

            }
        }
    } );

    //Select Multiple Values
    $("#multi-action-box").click(function () {
        var checkAll = $("#multi-action-box").prop('checked');
        if (checkAll) {
            $(".multi-action-box").prop("checked", true);
        } else {
            $(".multi-action-box").prop("checked", false);
        }
    });
    //Handler for multiple selection
    $('.multi-delete-image').click(function(){
        if(confirm("Are you sure you want to delete selected image(s)?")) {
            if($('#multi-action-box').prop("checked") || $('#gallerylist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#gallerylist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#gallerylist :checkbox:checked').each(function(){
                        deleteImage($(this).attr('data-image'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });

    $(document).on('click', '.delete-image', function() {
        if(confirm("Are you sure you want to delete this image ["+$(this).attr('data-image')+"]? ")) deleteImage($(this).attr('data-image'));
    });
    function deleteImage(image){
        $.ajax({
            url: "../REST/delete-gallery-image.php",
            type: 'POST',
            data: {image: image},
            cache: false,
            success : function(data, status) {
                if(data.status === 1){
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' </div>');
                }
                else if(data.status === 0 || data.status === 2 || data.status === 3 || data.status === 4){
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                }
                dataTable.ajax.reload();
                $.gritter.add({
                    title: 'Notification!',
                    text: data.msg ? data.msg : data
                });
            },
            error : function(xhr, status) {
                erroMsg = '';
                if(xhr.status===0){ erroMsg = 'There is a problem connecting to internet. Please review your internet connection.'; }
                else if(xhr.status===404){ erroMsg = 'Requested page not found.'; }
                else if(xhr.status===500){ erroMsg = 'Internal Server Error.';}
                else if(status==='parsererror'){ erroMsg = 'Error. Parsing JSON Request failed.'; }
                else if(status==='timeout'){  erroMsg = 'Request Time out.';}
                else { erroMsg = 'Unknow Error.\n'+xhr.responseText;}          
                $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Admin details update failed. '+erroMsg+'</div>');

                $.gritter.add({
                    title: 'Notification!',
                    text: erroMsg
                });
            }
        });
    }

});