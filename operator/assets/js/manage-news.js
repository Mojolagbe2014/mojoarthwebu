var dataTable, currentStatus;
$(document).ready(function(){
    $('#dateTime').datetimepicker({ dayOfWeekStart : 1, lang:'en' });
    
    $("form#CreateNews").submit(function(e){ 
        e.stopPropagation();
        e.preventDefault();
        $(document).scrollTo('div.panel h3');
        var formDatas = new FormData($(this)[0]);
        formDatas.append('description', CKEDITOR.instances['description'].getData());
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
                    $("form#CreateNews")[0].reset();
                    $('form #addNewNews').val('addNewNews');
                    $('form #multi-action-newsAddEdit').text('Add News');
                    $('#multiHeader').html('Add Upcoming News');
                    $('form #oldImage').val(''); $('form #oldImageSource').html('');
                    CKEDITOR.instances['description'].setData(''); $('form #oldImageComment').text('');
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
    
    loadAllNews();
    function loadAllNews(){
        dataTable = $('#newslist').DataTable( {
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   [0, 2]
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
                url :"../REST/manage-news.php", 
                type: "post",  
                data: {fetchNews:'true'},
                error: function(){  // error handling
                        $("#newslist-error").html("");
                        $("#newslist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#newslist_processing").css("display","none");

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
    $('.multi-activate-news').click(function(){
        if(confirm("Are you sure you want to change news status for selected news?")) {
            if($('#multi-action-box').prop("checked") || $('#newslist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#newslist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#newslist :checkbox:checked').each(function(){
                        currentStatus = 'Activate'; if(parseInt($(this).attr('data-status')) === 1) currentStatus = "De-activate";
                        activateNews($(this).attr('data-id'),$(this).attr('data-status'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    $('.multi-delete-news').click(function(){
        if(confirm("Are you sure you want to delete selected news?")) {
            if($('#multi-action-box').prop("checked") || $('#newslist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#newslist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#newslist :checkbox:checked').each(function(){
                        deleteNews($(this).attr('data-id'), $(this).attr('data-image'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    
    $(document).on('click', '.activate-news', function() {
        currentStatus = 'Activate'; if(parseInt($(this).attr('data-status')) === 1) currentStatus = "De-activate";
        if(confirm("Are you sure you want to "+currentStatus+" this news? News Name: '"+$(this).attr('data-title')+"'")) activateNews($(this).attr('data-id'),$(this).attr('data-status'));
    });
    $(document).on('click', '.delete-news', function() {
        if(confirm("Are you sure you want to delete this news? ("+$(this).attr('data-title')+")")) deleteNews($(this).attr('data-id'), $(this).attr('data-image'));
    });
    $(document).on('click', '.edit-news', function() {
        if(confirm("Are you sure you want to edit this news?")) editNews($(this).attr('data-id'), $(this).attr('data-title'), $(this).find('span#JQDTdescriptionholder').html(), $(this).attr('data-image'));
    });
    
    function activateNews(id, status){
        $.ajax({
            url: "../REST/manage-news.php",
            type: 'GET',
            data: {activateNews: 'true', id:id, status:status},
            cache: false,
            success : function(data, status) {
                if(data.status === 1){
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>News Successfully '+currentStatus+'d! ');
                }
                else if(data.status === 0 || data.status === 2 || data.status === 3 || data.status === 4){
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>News Activation Failed. '+data.msg+'</div>');
                }
                else {
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>News Activation Failed. '+data+'</div>');
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
    
    function deleteNews(id, image){
        $.ajax({
            url: "../REST/manage-news.php",
            type: 'POST',
            data: {deleteThisNews: 'true', id:id, image:image},
            cache: false,
            success : function(data, status) {
                if(data.status === 1){
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' </div>');
                }
                else if(data.status === 0 || data.status === 2 || data.status === 3 || data.status === 4){
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                }
                else {
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
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
    
    function editNews(id, title, description, image){//,
        $('form #addNewNews').val('editNews');
        $('form #multi-action-newsAddEdit').text('Update News');
        $('#multiHeader').html('Edit "<i style="font-weight:normal;">'+name+'</i>"');
        var formVar = {id:id, title:title, image:image};
        $.each(formVar, function(key, value) { 
            if(key == 'image') { $('form #oldImage').val(value); $('form #oldImageSource').html('<img src="../media/news/'+value+'" style="width:80px;height:60px;" />'); $('form #oldImageComment').text(value).css('color','red');} 
            else $('form #'+key).val(value);   
        });
        CKEDITOR.instances['description'].setData(description);
        $(document).scrollTo('div#hiddenUpdateForm');
    }
});