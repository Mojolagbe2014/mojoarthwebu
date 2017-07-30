$(document).ready(function(){
    $( "#startDate" ).datepicker({ 
        dateFormat: "yy-mm-dd",appendText: "(yyyy-mm-dd)", changeMonth: true, changeYear: true,
        onClose: function(){ $('#endDate').datepicker( "option", "minDate", new Date($(this).datepicker( "getDate" )) ); }
    });
    $( "#endDate" ).datepicker({ dateFormat: "yy-mm-dd",appendText: "(yyyy-mm-dd)", changeMonth: true, changeYear: true });
   
    //Fetch all the categories
    $.ajax({
        url: "../REST/fetch-categories.php",
        type: 'POST',
        cache: false,
        success : function(data, status) {
            $('#category').empty();
            if(data.status === 0 ){ 
                $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Category loading error. '+data.msg+'</div>');
            }
            if(data.status === 2 ){ 
                $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>No category available</div>');
                 $('#category').append('<option value="">-- No category available --</option>');
            }
            else if(data.status ===1 && data.info.length > 0){
                $('#category').append('<option value="">-- Select a category.. --</option>');
                $.each(data.info, function(i, item) {
                    $('#category').append('<option value="'+item.id+'">'+item.name+'</option>');
                });
            } 

        }
    });
    
    //Fetch all currencies
    $.ajax({
        url: "common-currencies.json",
        type: 'POST',
        cache: false,
        success : function(data, status) {
            $.each(data, function(i, item) {
                $('#currency').append('<option value="'+item.code+'" title="'+item.name+'">'+item.code+' ('+item.symbol+')</option>');
            });
        }
    });
    
    $("form#CreateCourse").submit(function(e){ 
        e.stopPropagation();
        e.preventDefault();
        $(document).scrollTo('div.panel h3');
        var formData = new FormData($(this)[0]);
        formData.append('description', CKEDITOR.instances['description'].getData());
        var alertType = ["danger", "success", "danger", "error"];
        $.ajax({
            url: $(this).attr("action"),
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            async: false,
            success : function(data, status) {
                if(data.status != null)  $("#messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                else $("#messageBox, .messageBox").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
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