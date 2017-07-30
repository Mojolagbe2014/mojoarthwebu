$(document).ready(function(){
    disableUpdateForm();
    
    function disableUpdateForm(){
        $('form#UpdateProfile input').attr('disabled', 'disabled');
        $('form#UpdateProfile select').attr('disabled', 'disabled');
        $('form#UpdateProfile #enableProfileUpdate').removeClass('hidden');
        $('form#UpdateProfile #mainUpdateButton').addClass('hidden');
        $('form#UpdateProfile #cancelProfileUpdate').addClass('hidden');
    }
    $('form#UpdateProfile #enableProfileUpdate').click(function(){
        $('form#UpdateProfile #mainUpdateButton').toggleClass('hidden');
        $('form#UpdateProfile #cancelProfileUpdate').toggleClass('hidden');
        $(this).addClass('hidden'); $('form#UpdateProfile input').prop("disabled", false);
        $('form#UpdateProfile select').prop("disabled", false);
    });
    
    $('form#UpdateProfile #cancelProfileUpdate').click(function(){disableUpdateForm();});
    
    
    var formVar = {id:sessionStorage.ITCadminId, name:sessionStorage.ITCAdminFullName, email:sessionStorage.ITCadminEmail, userName:sessionStorage.ITCAdminName, role:sessionStorage.ITCAdminRole};
    $.each(formVar, function(key, value) { $('form #'+key).val(value); });
    
    
    $("form#UpdateProfile").submit(function(e){ 
        
        e.stopPropagation();
        e.preventDefault();
        var formDatas = $(this).serialize();
        var alertType = ["danger", "success", "danger", "warning"];
        $.ajax({
            url: $(this).attr("action"),
            type: 'POST',
            data: formDatas,
            cache: false,
            success : function(data, status) {
                if(data.status === 1) { 
                    disableUpdateForm();
                    $("#messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'. Please wait ... </div>');
                    setInterval(function(){ logout(); }, 2500);
                }
                else if(data.status === 2 || data.status === 3 || data.status === 0 || data.status === 4) { 
                    $("#messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'. </div>');
                }
                else $("#messageBox, .messageBox").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');
            }
        });
        return false;
    });
    
    $("form#changeAdminPassword").submit(function(e){ 
        e.stopPropagation();
        e.preventDefault();
        var formDatas = $(this).serialize();
        var alertType = ["danger", "success", "danger", "warning"];
        $.ajax({
            url: $(this).attr("action"),
            type: 'POST',
            data: formDatas,
            cache: false,
            success : function(data, status) {
                if(data.status === 1) {  
                    $(".messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+' Please wait ... Login out..</div>');
                    setInterval(function(){ logout(); }, 2500);
                }
                else if(data.status === 2 || data.status === 3 || data.status === 0 || data.status === 4) { 
                    $(".messageBox, .messageBox").html('<div class="alert alert-'+alertType[data.status]+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'. </div>');
                }
                else $(".messageBox, .messageBox").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data+'</div>');            }
        });
        return false;
    });
});