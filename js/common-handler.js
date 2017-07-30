$(document).ready(function(){
        $("div.toggle-content").css('display', 'none').first().show();
        $("a.toggle").on("click",function(e){
            $("div.toggle-content").hide();
            menuContentId = $(this).attr('data-content');
            $('#'+menuContentId).slideToggle('slow');
        });

        $("a.toggle-read").on("click",function(e) {
            e.preventDefault();
            if($("div.toggle-read-more").css("display") == "none")
                $("div.toggle-read-more").slideDown(500).show();
            else
                $("div.toggle-read-more").fadeOut(500).hide();
          });
    });
