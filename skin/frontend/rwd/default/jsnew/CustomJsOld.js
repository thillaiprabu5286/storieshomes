(function($) {
  $.expr[":"].onScreen = function(elem) {
    var $window = $(window)
    var viewport_top = $window.scrollTop()
    var viewport_height = $window.height()
    var viewport_bottom = viewport_top + viewport_height
    var $elem = $(elem)
    var top = $elem.offset().top
    var height = $elem.height()
    var bottom = top + height

    return (top >= viewport_top && top < viewport_bottom) ||
           (bottom > viewport_top && bottom <= viewport_bottom) ||
           (height > viewport_height && top <= viewport_top && bottom >= viewport_bottom)
  }
})(jQuery);
function animation_1(){
		$('.s1 .ani_active, .s2 .ani_active, .s3 .ani_active, .s4 .ani_active, .s5 .ani_active, .s6 .ani_active, .s7 .ani_active, .s8 .ani_active, .s9 .ani_active').filter(":onScreen").removeClass('ani');
	}
$(window).on('load', function(){
		$('.loadScreen').addClass('LoadScreenRemove');
		animation_1();
	});

$(window).scroll(function() { 
	animation_1();
});



$(window).scroll(function(){
		var scrollValue = $(window).scrollTop();
		if (scrollValue > 200) {
			$(".TopBtn").addClass("active");
		}
		else {
			$(".TopBtn").removeClass("active");
		}
});

$(document).ready(function(){
	
	$(".searchbtn").click(function(){
		$('.searchpanel').addClass('active');
	});
	$(".SearchClose").click(function(){
		$('.searchpanel').removeClass('active');
	});
	
	/*Slider Jquery*/
	
	 $("#owl-demo1, #owl-demo2").owlCarousel({
        navigation:true,
        afterInit : function(elem){
          var that = this
          that.owlControls.prependTo(elem)
        }
      });
	  
	  $("#owl-demo3").owlCarousel({
        navigation:true,
		 singleItem : true,
		 stopOnHover : true,
        afterInit : function(elem){
          var that = this
          that.owlControls.prependTo(elem)
        }
      });
	  
	  $("#owl-demo4").owlCarousel({

      navigation : true,
      singleItem : true,
	  autoPlay: 3000,
      });
	  
	  
	
	$('.pagescroll').on('click',function (e) {
			e.preventDefault();

			var target = this.hash;
			var $target = $(target);
			
			$('html, body').stop().animate({
				'scrollTop': $target.offset().top
				}, 1000, 'swing', function () {
				window.location.hash = target;
			});
			
			
		});
	function setHeight(){
		var ImageHeight = $(".imgDiv").innerHeight();
		$('.DesDiv').css("height", ImageHeight);
		//console.log(setHeight);
	}
	
	function BlogsetHeight(){
		var BlogImageHeight = $(".blogImg").innerHeight();
		$('.blogCnt').css("height", BlogImageHeight);
		//console.log(BlogImageHeight);
	}
	setHeight();
	BlogsetHeight();
	$(window).resize(function(){
			setHeight();
			BlogsetHeight();
		});
		
		
});

$(document).ready(function(){
	
	$(".R-menu").click(function(){
		$('.ResponsiveMenuContent').slideToggle()
	});
	
	/*Load Responsive Menu*/
	function loadMenuData (){
		$(".ResponsiveMenuContent").html("");
		var MenuList = $(".MenuFull").html();
		//console.log(MenuList);
		$(".ResponsiveMenuContent").append(MenuList);
	}
	
	loadMenuData ();
	
	/*adding Class*/
	$('.SubheadDiv a').addClass('subHeadmenu');
	$('.level1 a').addClass('level1Menu');
	
	
	
	$(".SubheadDiv").click(function(){
			if( $(window).width() < 1250)
				{
					$(".level1Ul").slideUp();
					var show_cnt = $(this).next('.level1Ul');
					//console.log(show_cnt);
					if(show_cnt.css('display') == 'block')
						{
							show_cnt.slideUp();
							}
					else{
							$('.level1Ul').slideUp();
							show_cnt.slideDown();
						}
					
				}
			});
			
			/*Responsive Menu First level Click*/
			$(".mainlink").click(function(){
			if( $(window).width() < 1250)
				{
					$(".MegaMenuDiv").slideUp();
					var show_cnt1 = $(this).next('.MegaMenuDiv');
					//console.log(show_cnt);
					if(show_cnt1.css('display') == 'block')
						{
							show_cnt1.slideUp();
							}
					else{
							$('.level1Ul').slideUp();
							show_cnt1.slideDown();
						}
					
				}
			});
			
			
	  
	
});

