(function($j) {
  $j.expr[":"].onScreen = function(elem) {
    var $jwindow = $j(window)
    var viewport_top = $jwindow.scrollTop()
    var viewport_height = $jwindow.height()
    var viewport_bottom = viewport_top + viewport_height
    var $jelem = $j(elem)
    var top = $jelem.offset().top
    var height = $jelem.height()
    var bottom = top + height

    return (top >= viewport_top && top < viewport_bottom) ||
           (bottom > viewport_top && bottom <= viewport_bottom) ||
           (height > viewport_height && top <= viewport_top && bottom >= viewport_bottom)
  }
})(jQuery);
function animation_1(){
		$j('.s1 .ani_active, .s2 .ani_active, .s3 .ani_active, .s4 .ani_active, .s5 .ani_active, .s6 .ani_active, .s7 .ani_active, .s8 .ani_active, .s9 .ani_active, .InnerPage .ani_active').filter(":onScreen").removeClass('ani');
	}
 function setHeight(){
		var ImageHeight = $j(".imgDiv").innerHeight();
		$j('.DesDiv').css("height", ImageHeight);
		//console.log("seth"+ImageHeight);
	}
	
	function BlogsetHeight(){
//		var BlogImageHeight = $j(".blogImg").innerHeight();
//		$j('.blogCnt').css("height", BlogImageHeight);
                
                var BlogImageHeight1 = $j("#blogImg1").innerHeight();
		$j('#blogCnt1').css("height", BlogImageHeight1);
                
                var BlogImageHeight2 = $j("#blogImg2").innerHeight();
		$j('#blogCnt2').css("height", BlogImageHeight2);
                
                
		
                
                
	}        
$j(window).on('load', function(){
		$j('.loadScreen').addClass('LoadScreenRemove');
		animation_1();
                
               
	setHeight();
	BlogsetHeight();
        
        
	});

$j(window).scroll(function() { 
	animation_1();
});



$j(window).scroll(function(){
		var scrollValue = $j(window).scrollTop();
		if (scrollValue > 200) {
			$j(".TopBtn").addClass("active");
		}
		else {
			$j(".TopBtn").removeClass("active");
		}
});

$j(document).ready(function(){

	$j(".Account").click(function(){
		$j(".Account").toggleClass('active');
		$j('.AccountInfoDropDown').slideToggle();
	});

	$j(".cartBtn").click(function(){
		$j('.CartDropDown_Full').slideToggle();
	});
	$j(".CartClose").click(function(){
		$j('.CartDropDown_Full').slideUp();
	});
	
	$j(".searchbtn").click(function(){
		$j('.searchpanel').addClass('active');
		setTimeout(function(){
                   $j("#SearchInput").focus();
                 },1000);
	});
	$j(".SearchClose").click(function(){
		$j('.searchpanel').removeClass('active');
	});

	$j( ".CartDropDownInput" ).focus(function() { 
		$j( this ).siblings('.QtyBtn').css('display', 'block');
	}).blur(function() {
		/*$j('.QtySubmtBtn').css('display', 'none');*/
		});
	
// OUT SIDE CLICK
	$j(document).mouseup(function (e){
		var container = $j(".Account");
		if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
		{
			$j('.AccountInfoDropDown').slideUp();
			$j('.Account').removeClass('active');
			//$j('.CartDropDown_Full').slideUp();
		}
	});


	/*Slider Jquery*/
	
	 $j("#owl-demo1, #owl-demo2").owlCarousel({
        navigation:true,      
        afterInit : function(elem){
          var that = this
          that.owlControls.prependTo(elem)
        }
      });
	  
	  $j("#owl-demo3").owlCarousel({
        navigation:true,
		 singleItem : true,
		 stopOnHover : true,
        afterInit : function(elem){
          var that = this
          that.owlControls.prependTo(elem)
        }
      });
	  
	  $j("#owl-demo4").owlCarousel({

      navigation : true,
      singleItem : true,
	  autoPlay: 3000,
      });
	  
	$j("#owl-demo5").owlCarousel({

      navigation:true,
		 singleItem : true,
		 stopOnHover : true,
        afterInit : function(elem){
          var that = this
          that.owlControls.prependTo(elem)
        }
      });
	  
	
	$j('.pagescroll').on('click',function (e) {
			e.preventDefault();

			var target = this.hash;
			var $jtarget = $j(target);
			
			$j('html, body').stop().animate({
				'scrollTop': $jtarget.offset().top
				}, 1000, 'swing', function () {
				window.location.hash = target;
			});
			
			
		});
	
	$j(window).resize(function(){
			setHeight();
			BlogsetHeight();
		});
		
		
});

$j(document).ready(function(){
     $j("#video-popup").css("display", "none");
     $j(".video-popup-close").click(function(){        
       $j("#video-popup").css("display", "none");  

     });
    $j("#video-link").click(function(){

        $j("#video-popup").css("display", "block");
        console.log("alerttt-"+$j("#video-link").attr('title'));
        var link_video = $j("#video-link").attr('title');
         $j(".inner-video").html('<iframe width="560" height="376" src="http://www.youtube.com/embed/'+link_video+'" frameborder="0" allowfullscreen></iframe>');  
    });
    
    $j(".thumb-link").click(function(){
        if($j('#video-popup').css('display') == 'block'){ 
            $j("#video-popup").css("display", "none");
        }else{
            return;
        }
        
    });
	if( $j(window).width() < 1250)
        {
            
            $j("header .logo").html("");
            
            var urrl = 'http://'+window.location.host+'/';
            //console.log("url-"+urrl);
            $j('header .logo').html('<a href="'+urrl+'"><img src="'+urrl+'logo_small.png"></a>');
            /*Set blog image height */
            var ImageHeight = $j(".MiddleCnt").innerHeight();
           
		$j('.blogImg').css("height", ImageHeight);
             var ImageHeight2 = $j(".blogcont2").innerHeight();
           
		$j('.BlogImg2nd').css("height", ImageHeight2);    
               
            var Imagewidthsmall = $j(".blogimg-small").innerWidth();
            $j('.blogimg-small').css("height", Imagewidthsmall);
          
            
        }
	$j(".R-menu").click(function(){
            //console.log("clicked menuu");
		$j('.ResponsiveMenuContent').slideToggle()
	});
	
	/*Load Responsive Menu*/
	function loadMenuData (){
		$j(".ResponsiveMenuContent").html("");
		var MenuList = $j(".MenuFull").html();
		//console.log(MenuList);
		$j(".ResponsiveMenuContent").append(MenuList);
	}
	
	loadMenuData ();
	
	/*adding Class*/
	$j('.SubheadDiv a').addClass('subHeadmenu');
	$j('.level1 a').addClass('level1Menu');
	
	
	
	$j(".SubheadDiv").click(function(){
			if( $j(window).width() < 1250)
				{
					$j(".level1Ul").slideUp();
					var show_cnt = $j(this).next('.level1Ul');
					//console.log(show_cnt);
					if(show_cnt.css('display') == 'block')
						{
							show_cnt.slideUp();
							}
					else{
							$j('.level1Ul').slideUp();
							show_cnt.slideDown();
						}
					
				}
			});
			
			/*Responsive Menu First level Click*/
			$j(".level1open").click(function(){
			if( $j(window).width() < 1250)
				{
					$j(".MegaMenuDiv").slideUp();
					var show_cnt1 = $j(this).next('.MegaMenuDiv');
					//console.log(show_cnt);
					if(show_cnt1.css('display') == 'block')
						{
							show_cnt1.slideUp();
							}
					else{
							$j('.level1Ul').slideUp();
							show_cnt1.slideDown();
						}
					
				}
                                
                                
                                
			});
			$j(".level2open").click(function(){
			if( $j(window).width() < 1250)
				{	

					//$(".MegaMenuDiv").slideUp();
					var show_cnt1 = $j(this).next('.MegaMenuDiv');
					//console.log(show_cnt);
					if(show_cnt1.css('display') == 'block')
						{
							show_cnt1.slideUp();
							}
					else{
							$j('.level1Ul').slideUp();
							show_cnt1.slideDown();
						}
					
				}
			});
			
			
	  
	
});

$j(document).ready(function(){
    
   
	$j(".popupBtn").click(function(){
		var PopupTitle = $j(this).attr("title");
		console.log(PopupTitle);
		$j("#"+PopupTitle).addClass("active");
                //======ajax call==================================//
                
               var p_id = $j(this).attr("id");
                 $j.ajax({
                 //   url: "http://stories.local/index.php/mobileconnect/Product/ajaxview/id/"+p_id,
				  url: "http://www.storieshomes.com/index.php/mindster/Product/ajaxview/id/"+p_id,
                 //url: "http://119.226.240.222:900/Storieshomes_magento/index.php/mobileconnect/Product/ajaxview/id/"+p_id,
                 //url: "http://192.168.100.116/Storieshomes_meganto/index.php/mobileconnect/Product/ajaxview/id/"+p_id,
                    type: "POST",
                    dataType: "json",
                    data: {},
                    success: function(data) {
                    //console.log(JSON.stringify(data));    
                   // $j('#results').html(data);
                   //PopupBg
                     $j('#popup1').html(data.data);//===this is using =====
                    
                      var sync1 = $j("#sync1");
                        var sync2 = $j("#sync2");

                        sync1.owlCarousel({
                          singleItem : true,
                          slideSpeed : 1000,
                          navigation: false,
                          pagination:false,
                          afterAction : syncPosition,
                          responsiveRefreshRate : 200,
                        });

                        sync2.owlCarousel({
                          items : 5,
                          itemsDesktop      : [1199,5],
                          itemsDesktopSmall     : [979,5],
                          itemsTablet       : [768,5],
                          itemsMobile       : [479,2],
                          pagination:false,
                          responsiveRefreshRate : 100,
                          afterInit : function(el){
                            el.find(".owl-item").eq(0).addClass("synced");
                          }
                        });
                        function syncPosition(el){
                          var current = this.currentItem;
                          $j("#sync2")
                            .find(".owl-item")
                            .removeClass("synced")
                            .eq(current)
                            .addClass("synced")
                          if($j("#sync2").data("owlCarousel") !== undefined){
                            center(current)
                          }
                        }
                        
                        $j("#sync2").on("click", ".owl-item", function(e){
                        e.preventDefault();
                        var number = $j(this).data("owlItem");
                        sync1.trigger("owl.goTo",number);
                      });

                      function center(number){
                        var sync2visible = sync2.data("owlCarousel").owl.visibleItems;
                        var num = number;
                        var found = false;
                        for(var i in sync2visible){
                          if(num === sync2visible[i]){
                            var found = true;
                          }
                        }

                        if(found===false){
                          if(num>sync2visible[sync2visible.length-1]){
                            sync2.trigger("owl.goTo", num - sync2visible.length+2)
                          }else{
                            if(num - 1 === -1){
                              num = 0;
                            }
                            sync2.trigger("owl.goTo", num);
                          }
                        } else if(num === sync2visible[sync2visible.length-1]){
                          sync2.trigger("owl.goTo", sync2visible[1])
                        } else if(num === sync2visible[0]){
                          sync2.trigger("owl.goTo", num-1)
                        }

                      }
                      
                      $j(".PopupClose").click(function(){
                             $j('.PopupFullBg').removeClass("active");
                        });

                    }//=====end of ajax success
                   });
                //======end of ajax call============================//
                
	});
	$j(".PopupClose").click(function(){
		$j('.PopupFullBg').removeClass("active");
	});
	
	$j("#webform-client-form-19").submit(function(){
		alert('Hello');
		/*
		var name = ;
		$j.ajax({
			url: 'submit.php',
			data: {'name': name),
			success(data){
				
			}
		});
		*/
		return false;
	});
});
/*popup slider*/
$j(document).ready(function() {
 
  var sync1 = $j("#sync1");
  var sync2 = $j("#sync2");
 
  sync1.owlCarousel({
    singleItem : true,
    slideSpeed : 1000,
    navigation: false,
    pagination:false,
    afterAction : syncPosition,
    responsiveRefreshRate : 200,
  });
 
  sync2.owlCarousel({
    items : 5,
    itemsDesktop      : [1199,5],
    itemsDesktopSmall     : [979,5],
    itemsTablet       : [768,5],
    itemsMobile       : [479,2],
    pagination:false,
    responsiveRefreshRate : 100,
    afterInit : function(el){
      el.find(".owl-item").eq(0).addClass("synced");
    }
  });
 
  function syncPosition(el){
    var current = this.currentItem;
    $j("#sync2")
      .find(".owl-item")
      .removeClass("synced")
      .eq(current)
      .addClass("synced")
    if($j("#sync2").data("owlCarousel") !== undefined){
      center(current)
    }
  }
 
  $j("#sync2").on("click", ".owl-item", function(e){
    e.preventDefault();
    var number = $j(this).data("owlItem");
    sync1.trigger("owl.goTo",number);
  });
 
  function center(number){
    var sync2visible = sync2.data("owlCarousel").owl.visibleItems;
    var num = number;
    var found = false;
    for(var i in sync2visible){
      if(num === sync2visible[i]){
        var found = true;
      }
    }
 
    if(found===false){
      if(num>sync2visible[sync2visible.length-1]){
        sync2.trigger("owl.goTo", num - sync2visible.length+2)
      }else{
        if(num - 1 === -1){
          num = 0;
        }
        sync2.trigger("owl.goTo", num);
      }
    } else if(num === sync2visible[sync2visible.length-1]){
      sync2.trigger("owl.goTo", sync2visible[1])
    } else if(num === sync2visible[0]){
      sync2.trigger("owl.goTo", num-1)
    }
    
  }
  
    if( $j(window).width() < 900)
        {
            $j(".categoryFull h6").click(function(){
                    $j(".innerLeftcategory").slideToggle();
            });
        }
  //=======ajax call===============//
  
  
  
  //=======ajax call==============//
  
 
});

