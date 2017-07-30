$(document).ready(function(){

  		$('a#toggle-requirements').on('click',function(e){
  				if($("a#basic-requirements").css("display") != "none"){
  					return;
  				}
  				else if($("a#phases-of-course").css("display") != "none"){
  					$("a#phases-of-course").fadeOut(500).css("display","none");
  					$("a#basic-requirements").fadeIn(500).css("display","block");
  				}
  				else if($("a#outline-of-course").css("display") != "none"){
  					$("a#outline-of-course").fadeOut(500).css("display","none");
  					$("a#basic-requirements").fadeIn(500).css("display","block");
  				}
  				e.preventDefault();
  		});
  		$('a#toggle-phases').on('click',function(e){
  			if($("a#phases-of-course").css("display") != "none"){
  				return;
  			}
  			else if($("a#basic-requirements").css("display") != "none"){
  				$("a#basic-requirements").fadeOut(500).css("display","none");
  				$("a#phases-of-course").fadeIn(500).css("display","block");
  			}
  			else if($("a#outline-of-course").css("display") != "none"){
  				$("a#outline-of-course").fadeOut(500).css("display","none");
  				$("a#phases-of-course").fadeIn(500).css("display","block");
  			}
  			e.preventDefault();
  		});
  		$('a#toggle-outline').on('click',function(e){
  			if($("a#outline-of-course").css("display") != "none"){
  				return;
  			}
  			else if($("a#phases-of-course").css("display") != "none"){
  				$("a#phases-of-course").fadeOut(500).css("display","none");
  				$("a#outline-of-course").fadeIn(500).css("display","block");
  			}
  			else if($("a#basic-requirements").css("display") != "none"){
  				$("a#basic-requirements").fadeOut(500).css("display","none");
  				$("a#outline-of-course").fadeIn(500).css("display","block");
  			}
  			e.preventDefault();
  		});
});
