var dataTable;
$(document).ready(function(){
    $("form#CreateTestimonial").submit(function(e){ 
        e.stopPropagation();
        e.preventDefault();
        $(document).scrollTo('div.panel h3');
        var formDatas = new FormData($(this)[0]);
        var alertType = ["danger", "success", "danger", "error"];
        $.ajax({
            url: $(this).attr("action"),
            type: 'POST',
            data: formDatas,
            cache: false,
            contentType: false,
            async: false,
            success : function(data, status) {
                if(data.status != null && data.status !=1) { 
                    $("#messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' </div>');
                }
                else if(data.status != null && data.status == 1) { 
                    $("#messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'  </div>'); 
                    $("form#CreateTestimonial")[0].reset();
                    $('form#CreateTestimonial #addNewTestimonial').val('addNewTestimonial');
                    $('form#CreateTestimonial #multi-action-catAddEdit').text('Add Testimonial');
                    $('form#CreateTestimonial #oldFile').val(''); $('form#CreateTestimonial #oldFileComment').html('');
                }
                else $("#messageBox, .messageBox").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
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
            },
            processData: false
        });
        return false;
    });
    
    loadAllTestimonials();
    function loadAllTestimonials(){
        dataTable = $('#testimoniallist').DataTable( {
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   [0, 5]
            } ],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            order: [[ 1, 'asc' ]],
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "ajax":{
                url :"../REST/manage-testimonials.php", //employee-grid-data.php",// json datasource
                type: "post",  // method  , by default get
                data: {fetchTestimonials:'true'},
                error: function(){  // error handling
                        $("#testimoniallist-error").html("");
                        $("#testimoniallist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#testimoniallist_processing").css("display","none");

                }
            }
        } );
    }
    
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
    $('.multi-delete-testimonial').click(function(){
        if(confirm("Are you sure you want to delete selected testimonial(s)?")) {
            if($('#multi-action-box').prop("checked") || $('#testimoniallist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#testimoniallist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#testimoniallist :checkbox:checked').each(function(){
                        deleteTestimonial($(this).attr('data-id'),$(this).attr('data-image'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    
    $(document).on('click', '.delete-testimonial', function() {
        if(confirm("Are you sure you want to delete this testimonial? Testimonial: '"+$(this).find('span').html()+"'")) deleteTestimonial($(this).attr('data-id'),$(this).attr('data-image'));
    });
    $(document).on('click', '.edit-testimonial', function() {
        if(confirm("Are you sure you want to edit this testimonial? Testimonial: '"+$(this).find('span').html()+"'")) editTestimonial($(this).attr('data-id'), $(this).find('span').html(), $(this).attr('data-author'), $(this).attr('data-image'));
    });
    
    function deleteTestimonial(id,image){
        $.ajax({
            url: "../REST/manage-testimonials.php",
            type: 'POST',
            data: {deleteThisTestimonial: 'true', id:id, image:image},
            cache: false,
            success : function(data, status) {
                if(data.status === 1){
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' </div>');
                }
                else {
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
    
    function editTestimonial(id, content, author, image){//,
        $('form #addNewTestimonial').val('editTestimonial');
        $('form #multi-action-catAddEdit').text('Update Testimonial');
        $(document).scrollTo('div#hiddenUpdateForm');
        var formVar = {id:id, content:content, author:author, image:image};
        $.each(formVar, function(key, value) { 
            if(key == 'image') { $('form #oldFile').val(value); $('form #oldFileComment').html('<img src="../media/testimonial/'+value+'" style="width:50px;height:50px; margin:5px">');} 
            $('form #'+key).val(value); 
        });
        
    }
});