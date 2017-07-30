var dataTable;
$(document).ready(function(){
    $.ajax({
        url: "../REST/fetch-news.php",
        type: 'POST',
        cache: false,
        success : function(data, status) {
            $('#newsType').empty();
            if(data.status === 0 ){ 
                $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>News loading error. '+data.msg ? data.msg : data+'</div>');
            }
            if(data.status === 2 ){ 
                $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>No news available</div>');
                $('#newsType').append('<option value="custom">-- No news available --</option>');
            }
            else if(data.status ===1 && data.info.length > 0){
                $('#newsType').append('<option value="custom">-- Custom Message --</option>');
                $.each(data.info, function(i, item) {
                    $('#newsType').append('<option value="'+item.id+'">'+item.title.substr(0,50)  +' ---------------- '+ item.dateAdded + '</option>');
                });
            } 

        }
    });
    
    loadAllSubscribers();
    function loadAllSubscribers(){
        dataTable = $('#subscriberlist').DataTable( {
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
                url :"../REST/manage-subscribers.php", //employee-grid-data.php",// json datasource
                type: "post",  // method  , by default get
                data: {fetchUsers:'true'},
                error: function(){  // error handling
                        $("#subscriberlist-error").html("");
                        $("#subscriberlist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#subscriberlist_processing").css("display","none");

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
    $('.multi-delete-user').click(function(){
        if(confirm("Are you sure you want to delete selected subscribers?")) {
            if($('#multi-action-box').prop("checked") || $('#subscriberlist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#subscriberlist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#subscriberlist :checkbox:checked').each(function(){
                        deleteSubscriber($(this).attr('data-id'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    $('form#emailSenderForm').submit(function(e){
        e.stopPropagation();
        e.preventDefault();
        $(document).scrollTo('div.panel h3');
        if(confirm("Are you sure you want to email selected subscribers?")) {
            if($('#multi-action-box').prop("checked") || $('#subscriberlist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#subscriberlist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#subscriberlist :checkbox:checked').each(function(){
                        emailSubscriber($(this).attr('data-email'), $(this).attr('data-name'), $('form#emailSenderForm #subject').val(), CKEDITOR.instances['message'].getData(), $('form#emailSenderForm #newsType').val());
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    
    $(document).on('click', '.delete-user', function() {
        if(confirm("Are you sure you want to delete this subscriber? Subscriber Name: '"+$(this).attr('data-name')+"'")) deleteSubscriber($(this).attr('data-id'));
    });
    
    function deleteSubscriber(id){
        $.ajax({
            url: "../REST/manage-subscribers.php",
            type: 'POST',
            data: {deleteThisUser: 'true', id:id},
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
    
    function emailSubscriber(email, name, subject, message, newsType){
        $.ajax({
            url: "../REST/manage-subscribers.php",
            type: 'POST',
            data: {sendEmails: 'true', email:email, name:name, subject:subject, message:message, newsType:newsType},
            cache: false,
            success : function(data, status) {
                if(data.status === 1){
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' </div>');
                }
                else {
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                }
                //dataTable.ajax.reload();
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