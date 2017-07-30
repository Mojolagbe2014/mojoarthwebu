var dataTable, currentStatus;
$(document).ready(function(){ 
    loadAllRegisteredSliders();
    function loadAllRegisteredSliders(){
        dataTable = $('#sliderslist').DataTable( {
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   [0, 2]
            } ],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            order: [[ 3, 'asc' ]],
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "ajax":{
                url :"../REST/manage-sliders.php", //employee-grid-data.php",// json datasource
                type: "post",  // method  , by default get
                data: {fetchSliders:'true'},
                error: function(){  // error handling
                        $("#sliderslist-error").html("");
                        $("#sliderslist").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#sliderslist_processing").css("display","none");

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
    $('.multi-activate-slider').click(function(){
        if(confirm("Are you sure you want to change slider status for selected slider(s)?")) {
            if($('#multi-action-box').prop("checked") || $('#sliderslist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#sliderslist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#sliderslist :checkbox:checked').each(function(){
                        currentStatus = 'Activate'; if(parseInt($(this).attr('data-status')) === 1) currentStatus = "De-activate";
                        activateSlider($(this).attr('data-id'),$(this).attr('data-status'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    $('.multi-delete-slider').click(function(){
        if(confirm("Are you sure you want to delete selected sliders?")) {
            if($('#multi-action-box').prop("checked") || $('#sliderslist :checkbox:checked').length > 0) {
                var atLeastOneIsChecked = $('#sliderslist :checkbox:checked').length > 0;
                if (atLeastOneIsChecked !== false) {
                    $('#sliderslist :checkbox:checked').each(function(){
                        deleteSlider($(this).attr('data-id'),$(this).attr('data-image'));
                    });
                }
                else alert("No row selected. You must select atleast a row.");
            }
            else alert("No row selected. You must select atleast a row.");
        }
    });
    
    $(document).on('click', '.activate-slider', function() {
        currentStatus = 'Activate'; if(parseInt($(this).attr('data-status')) === 1) currentStatus = "De-activate";
        if(confirm("Are you sure you want to "+currentStatus+" this slider? Slider Name: '"+$(this).attr('data-title')+"'")) activateSlider($(this).attr('data-id'),$(this).attr('data-status'));
    });
    $(document).on('click', '.delete-slider', function() {
        if(confirm("Are you sure you want to delete this slider ["+$(this).attr('data-title')+"]? Slider image ['"+$(this).attr('data-image')+"'] will be deleted too.")) deleteSlider($(this).attr('data-id'),$(this).attr('data-image'));
    });
    $(document).on('click', '.edit-slider', function() {
        if(confirm("Are you sure you want to edit this slider ["+$(this).attr('data-title')+"] details?")) editSlider($(this).attr('data-id'), $(this).attr('data-title'), $(this).find('span#JQDTcontentholder').html(), $(this).attr('data-image'), $(this).attr('data-orders'));
    });
    
    function deleteSlider(id, image){
        $.ajax({
            url: "../REST/manage-sliders.php",
            type: 'POST',
            data: {deleteThisSlider: 'true', id:id, image: image},
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
    
    function activateSlider(id, status){
        $.ajax({
            url: "../REST/manage-sliders.php",
            type: 'GET',
            data: {activateSlider: 'true', id:id, status:status},
            cache: false,
            success : function(data, status) {
                if(data.status === 1){
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Slider Successfully '+currentStatus+'d! </div>');
                }
                else if(data.status === 0 || data.status === 2 || data.status === 3 || data.status === 4){
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Slider Activation Failed. '+data.msg+'</div>');
                }
                else {
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Slider Activation Failed. '+data+'</div>');
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
    
    function editSlider(id, title, content, image, orders){//,
        var formVar = {id:id, title:title, content:content, image:image, orders:orders};
        $.each(formVar, function(key, value) { 
            if(key == 'image') { $('form #oldImage').val(value); $('form #oldImageComment').text(value).css('color','red');$('form #oldImageSource').html('<img src="../media/slider/'+value+'" style="width:80px;height:60px;" />');} 
            else $('form #'+key).val(value);  
        });
        $('#hiddenUpdateForm').removeClass('hidden');
        $(document).scrollTo('div#hiddenUpdateForm');
        
        $('#cancelEdit').click(function(){ $("#hiddenUpdateForm").addClass('hidden'); });
    }
    $("form#UpdateSlider").submit(function(e){ 
        e.stopPropagation(); 
        e.preventDefault();
        $(document).scrollTo('div.panel h3');
        var formData = new FormData($(this)[0]);
        var alertType = ["danger", "success", "danger", "error"];
        $.ajax({
            url: $(this).attr("action"),
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            async: false,
            success : function(data, status) {
                $("#hiddenUpdateForm").addClass('hidden');
                if(data.status === 1) {
                    $("#messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' </div>');
                }
                else if(data.status === 2 || data.status === 3 || data.status ===0 ) $("#messageBox").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
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
});