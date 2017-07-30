var dataTable;
$(document).ready(function(){
    loadAllRegisteredAdmins();
    function loadAllRegisteredAdmins(){
        dataTable = $('#adminlist').DataTable( {
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   [0, 7]
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
                url :"../REST/manage-admins.php", //employee-grid-data.php",// json datasource
                type: "post",  // method  , by default get
                error: function(){  // error handling
                        $("#adminlist-error").html("");
                        $("#adminlist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#adminlist_processing").css("display","none");

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
    $('.multi-upgrade-admin').click(function(){
        if(confirm("Are you sure you want to change admin type for selected admins?")) {
            if($('#multi-action-box').prop("checked") || $('#adminlist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#adminlist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#adminlist :checkbox:checked').each(function(){
                        changeAdminStatus($(this).attr('data-id'), $(this).attr('data-role'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    $('.multi-delete-admin').click(function(){
        if(confirm("Are you sure you want to delete selected admins?")) {
            if($('#multi-action-box').prop("checked") || $('#adminlist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#adminlist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#adminlist :checkbox:checked').each(function(){
                        deleteAdmin($(this).attr('data-id'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    
    var currentStatus;
    $(document).on('click', '.upgrade-admin', function() {
        if($(this).attr('data-role')=="Admin"){ currentStatus = "degrade"; }else currentStatus = "upgrade";
        if(confirm("Are you sure you want to "+currentStatus+" this admin? Admin Name: '"+$(this).attr('data-name')+"'")) changeAdminStatus($(this).attr('data-id'),$(this).attr('data-role'));
    });
    $(document).on('click', '.delete-admin', function() {
        if(confirm("Are you sure you want to delete this admin? Admin Name: '"+$(this).attr('data-name')+"'")) deleteAdmin($(this).attr('data-id'),$(this).attr('data-role'));
    });
    $(document).on('click', '.edit-admin', function() {
        if(confirm("Are you sure you want to edit this admin details? Admin Name: '"+$(this).attr('data-name')+"'")) editAdminDetails($(this).attr('data-id'), $(this).attr('data-name'), $(this).attr('data-email'), $(this).attr('data-username'), $(this).attr('data-role'));
    });
    function deleteAdmin(adminId){
        $.ajax({
            url: "../REST/delete-admin.php",
            type: 'POST',
            data: {deleteThisAdmin: 'true', id:adminId},
            cache: false,
            success : function(data, status) {
                if(data.status === 1){
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                    
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
    function changeAdminStatus(adminId, adminRole){
        $.ajax({
            url: "../REST/change-admin-status.php",
            type: 'GET',
            data: {changeAdminStatus: 'true', id:adminId, role:adminRole},
            cache: false,
            success : function(data, status) {
                if(data.status === 1){
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                    dataTable.ajax.reload();
                }
                else {
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                }
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
    function editAdminDetails(id, name, email, userName, role){//,
        var formVar = {id:id, name:name, email:email, userName:userName, role:role};
        $('#hiddenUpdateForm').removeClass('hidden');
        $(document).scrollTo('div#hiddenUpdateForm');
        $.each(formVar, function(key, value) { $('form #'+key).val(value); });
        $("form#CreateAdmin").submit(function(e){ 
            e.stopPropagation(); 
            e.preventDefault();
            $(document).scrollTo('div.panel h3');
            var formDatas = $(this).serialize();
            //formDatas.append('updateThisAdmin', 'true');
            var alertType = ["danger", "success", "danger", "error"];
            $.ajax({
                url: $(this).attr("action"),
                type: 'POST',
                data: formDatas,
                cache: false,
                success : function(data, status) {
                    $('#hiddenUpdateForm').addClass('hidden');
                    if(data.status !== null)  $("#messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>'); 
                    else $("#messageBox, .messageBox").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button> '+data+'</div>');
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
            return false;
        });
        $('#cancelEdit').click(function(){ $("#hiddenUpdateForm").addClass('hidden'); });
    }
});