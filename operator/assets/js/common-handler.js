function logout(){
    if (typeof localStorage !== "undefined") {
        $('.container').animate({ scrollTop:0 }, 800, 'easeInOutQuad');
        $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button> <img src="images/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Login out .. ! Please wait ... </div>');
        $(".messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button> <img src="images/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Login out .. ! Please wait ... </div>');
        $.ajax({
            url: "../REST/admin-logout.php",
            success : function(data, status) {
                if(data.status == "1"){
                    sessionStorage.clear();
                    $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><img src="images/cycling.GIF" width="30" height="30" alt="Ajax Loading"> '+data.msg+' Redirecting...</div>');
                    setInterval(function(){ window.location = 'login'; }, 2000);
                }
                else {
                    $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                }
            }
        });
    }
}

$(document).ready(function(){
    //Login Verification
    if (typeof localStorage !== "undefined") {
        if(sessionStorage.ITCadminId == "" || sessionStorage.ITCadminId == null || sessionStorage.ITCadminEmail == "" || sessionStorage.ITCadminEmail == null)
        window.location = "login";
    }
    //Logout Handler
    $(".logout").click(function(){
        if (typeof localStorage !== "undefined") {
            $('.container').animate({ scrollTop:0 }, 800, 'easeInOutQuad');
            $("#messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button> <img src="images/cycling.GIF" width="30" height="30" alt="Ajax Loading"> Login out .. ! Please wait ... </div>');
            $.ajax({
                url: "../REST/admin-logout.php",
                success : function(data, status) {
                    if(data.status == "1"){
                        sessionStorage.clear();
                        $("#messageBox, .messageBox").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><img src="images/cycling.GIF" width="30" height="30" alt="Ajax Loading"> '+data.msg+' Redirecting...</div>');
                        setInterval(function(){ window.location = 'login'; }, 2000);
                    }
                    else {
                        $("#messageBox, .messageBox").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+data.msg+'</div>');
                    }
                }
            });
        }
    });
    
    $('.adminName').text(sessionStorage.ITCAdminName);
    $('.logout').click(function(){ logout(); });
    var loading = $(".messageBox, #messageBox");
    $(document).ajaxStart(function () {
        loading.html("<img src='images/please-wait-animation.gif' class='loading-image' height='80' width='200' alt='Loading..'/>").css('text-align', 'center');
    });

    $(document).ajaxStop(function () {
        $('img.loading-image').hide();
    });
    
});


