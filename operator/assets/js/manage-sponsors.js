var dataTable, currentStatus;
$(document).ready(function(){
    $("form#CreateSponsor").submit(function(e){ 
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
                    $("form#CreateSponsor")[0].reset();
                    $('form #addNewSponsor').val('addNewSponsor');
                    $('form #multi-action-sponsorAddEdit').text('Add Sponsor');
                    $('#multiHeader').html('Add Sponsor/Partner');
                    $('form#CreateSponsor #oldLogo').val(''); $('form#CreateSponsor #oldLogoSource').html(''); $('form#CreateSponsor #oldLogoComment').text('');
                    $('form#CreateSponsor #oldImage').val(''); $('form#CreateSponsor #oldImageSource').html(''); $('form#CreateSponsor #oldImageComment').text('');
                    CKEDITOR.instances['description'].setData('');  $('form #oldLogoLabel').addClass('hidden');
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
    
    loadAllSponsors();
    function loadAllSponsors(){
        dataTable = $('#sponsorlist').DataTable( {
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
                url :"../REST/manage-sponsors.php", 
                type: "post",  
                data: {fetchSponsors:'true'},
                error: function(){  // error handling
                        $("#sponsorlist-error").html("");
                        $("#sponsorlist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#sponsorlist_processing").css("display","none");

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
    $('.multi-activate-sponsor').click(function(){
        if(confirm("Are you sure you want to change sponsor status for selected events?")) {
            if($('#multi-action-box').prop("checked") || $('#sponsorlist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#sponsorlist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#sponsorlist :checkbox:checked').each(function(){
                        currentStatus = 'Activate'; if(parseInt($(this).attr('data-status')) === 1) currentStatus = "De-activate";
                        activateSponsor($(this).attr('data-id'),$(this).attr('data-status'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    $('.multi-delete-sponsor').click(function(){
        if(confirm("Are you sure you want to delete selected events?")) {
            if($('#multi-action-box').prop("checked") || $('#sponsorlist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#sponsorlist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#sponsorlist :checkbox:checked').each(function(){
                        deleteSponsor($(this).attr('data-id'), $(this).attr('data-logo'), $(this).attr('data-image'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    
    $(document).on('click', '.activate-sponsor', function() {
        currentStatus = 'Activate'; if(parseInt($(this).attr('data-status')) === 1) currentStatus = "De-activate";
        if(confirm("Are you sure you want to "+currentStatus+" this sponsor? Sponsor Name: '"+$(this).attr('data-name')+"'")) activateSponsor($(this).attr('data-id'),$(this).attr('data-status'));
    });
    $(document).on('click', '.delete-sponsor', function() {
        if(confirm("Are you sure you want to delete this sponsor? ("+$(this).attr('data-name')+")")) deleteSponsor($(this).attr('data-id'), $(this).attr('data-logo'), $(this).attr('data-image'));
    });
    $(document).on('click', '.edit-sponsor', function() {
        if(confirm("Are you sure you want to edit this sponsor?")) editSponsor($(this).attr('data-id'), $(this).attr('data-name'), $(this).attr('data-logo'), $(this).attr('data-website'), $(this).attr('data-product'), $(this).find('span#JQDTdescriptionholder').html(), $(this).attr('data-image'));
    });
    
    function activateSponsor(id, status){
        $.ajax({
            url: "../REST/manage-sponsors.php",
            type: 'GET',
            data: {activateSponsor: 'true', id:id, status:status},
            cache: false,
            success : function(data, status) {
                if(data.status === 1){
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Sponsor Successfully '+currentStatus+'d! </div>');
                }
                else if(data.status === 0 || data.status === 2 || data.status === 3 || data.status === 4){
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Sponsor Activation Failed. '+data.msg+'</div>');
                }
                else {
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Sponsor Activation Failed. '+data+'</div>');
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
    
    function deleteSponsor(id, logo, image){
        $.ajax({
            url: "../REST/manage-sponsors.php",
            type: 'POST',
            data: {deleteThisSponsor: 'true', id:id, logo:logo, image:image},
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
    
    function editSponsor(id, name, logo, website, product, description, image){//,
        $('form #addNewSponsor').val('editSponsor');
        $('form #multi-action-sponsorAddEdit').text('Update Sponsor');
        $('#multiHeader').html('Edit "<i style="font-weight:normal;">'+name+'</i>"');
        $('form #oldLogoLabel').removeClass('hidden');
        var formVar = {id:id, name:name, logo:logo, website:website, product:product, image:image};
        $.each(formVar, function(key, value) { 
            if(key === 'logo') { $('form #oldLogo').val(value); $('form #oldLogoSource').html('<img src="../media/sponsor/'+value+'" style="width:80px;height:60px;" />'); $('form #oldLogoComment').text(value).css('color','red');} 
            else if(key === 'image') { $('form #oldImage').val(value); $('form #oldImageSource').html('<img src="../media/sponsor-image/'+value+'" style="width:80px;height:60px;" />'); $('form #oldImageComment').text(value).css('color','red');} 
            else $('form #'+key).val(value);   
        });
        CKEDITOR.instances['description'].setData(description);
        $(document).scrollTo('div#hiddenUpdateForm');
    }
});