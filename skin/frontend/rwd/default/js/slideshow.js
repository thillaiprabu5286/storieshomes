//=====================new slider==================//
                $j(window).load(function(){
                  
      $j('#carousel').flexslider({

        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        itemWidth: 90,

        itemMargin: 0,
        asNavFor: '#slider',
        
        end: function(){
        
        }
      });

      $j('#slider').flexslider({
      
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        sync: "#carousel",
        start: function(slider) {
            $j('.product-image-gallery').click( function() {
                
                
                 var img_sel =$j('.visible').attr('id');
                        if(img_sel == "image-main"){
                            img_sel ="image-0"; 
                        }
                       
                        var img = $j('.visible').attr('src'); 
                           var img_n = img_sel.split("-");
                           var img_no =  img_n[1];
                           
                           slider.currentSlide =Number(img_no);
            });
             
         }
      });
    });
   $j(function(){
     // SyntaxHighlighter.all();
    });
    //============new slider==========//

$j(document).ready(function(){$j('.slideshow-container .slideshow').cycle({slides:'> li',pager:'.slideshow-pager',pagerTemplate:'<span class="pager-box"></span>',speed:600,pauseOnHover:true,swipe:true,prev:'.slideshow-prev',next:'.slideshow-next',fx:'scrollHorz'});});
$j(document).ready(function(){$j('.slideshow-testimonial .slideshow').cycle({slides:'> li',pager:'.tslideshow-pager',pagerTemplate:'<span class="pager-box"></span>',timeout:15000,speed:600,pauseOnHover:true,swipe:true,prev:'.slideshow-prev',next:'.slideshow-next',fx:'scrollHorz'});});
$j(document).ready( function() {
  
  $j('.grid').isotope({
    itemSelector: '.grid-item',
    masonry: {
      columnWidth: 0,
      gutter: 34
    }
  });

});

//========easy===zoom===============//
$j(document).ready(function(){
	
	
   $j('.ezlink').on("click", function (e) {
        e.preventDefault();
    });
  
   
		// Instantiate EasyZoom instances
		var $easyzoom = $j('.easyzoom').easyZoom();
               
  

		// Setup thumbnails example
		var api1 = $easyzoom.filter('.easyzoom--with-thumbnails').data('easyZoom');

		$j('.easy-zoom-container .thumbnails').on('click', 'a', function(e) {
                    //alert("onclick");
			var $this = $j(this);

			e.preventDefault();
                          //=======style li=========
                        
                        $j('.easy-zoom-container .thumbnails li').removeClass('thumb_selected_zoom');
            
                   $this.parent().addClass(
                    "thumb_selected_zoom"
                    );
                          //=======style li==========
			// Use EasyZoom's `swap` method
                        //alert("stan"+$this.data('standard')+"href"+$this.attr('href'));
			api1.swap($this.data('standard'), $this.attr('href'));
                        var owl = $j(".owl-carousel").data('owlCarousel');
                          owl.jumpTo(0);
		});
                
                //=============zoom popup click===============//
             //   loadPopupBox();
                  // $j(window).trigger('resize');
                    $j('.easy-zoom-popup-close').click( function() {            
                        unloadPopupBox();
                    });

                    $j('.main-container').click( function() {
                       
                        //unloadPopupBox();
                    });

                      $j('.product-image-gallery').click( function(event) {
                           

                         
                          setTimeout(function() {
                    $j(".easy-zoom-container").trigger('resize');
                }, 0);

                setTimeout(function() {
                    $j(".easy-zoom-container").trigger('resize');
                }, 2)
                 setTimeout(function() {
                    $j(".easy-zoom-container").trigger('resize');
                }, 5)
                        //alert("selected_"+$j('.visible').attr('id')); 
                        var img_sel =$j('.visible').attr('id');
                        if(img_sel == "image-main"){
                            img_sel ="image-0"; 
                        }
                       // alert("sel--"+img_sel);
                        var img = $j('.visible').attr('src'); 
                           var img_n = img_sel.split("-");
                           var img_no =  img_n[1];
                          
                         
                        loadPopupBox(img_no,img);
                         
                    });
                   
                    function unloadPopupBox() {    // TO Unload the Popupbox
                        $j('#popup_box').fadeOut("slow");
                        $j("#container").css({ // this is just for style        
                            "opacity": "1"  
                        }); 
                        $j("#popup_overlay").css({ // this is just for style
                            "display": "none"  
                        });
                        $j(".easy-zoom-container").css({ 
                            "display": "none"  
                        }); 
                    }    

                    function loadPopupBox(imgno,img) {    // To Load the Popupbox
                      
                         
                        if(imgno !== "0"){
                            //alert("inside");
                          var temo_0_img = $j('.temp_0').attr('href');
                         
//                       
                        }
                        $j('.easy-zoom-container .thumbnails .themp_'+imgno).addClass(
                    "thumb_selected_zoom"
                    );
                        $j('#popup_box').fadeIn("slow");
                         $j('.easy-zoom-container').fadeIn("slow");
                        
                         $j("#popup_overlay").css({ // this is just for style
                            "display": "block"  
                        }); 
                        $j("#container").css({ // this is just for style
                            "opacity": "0.3"  
                        });     
                        $j(".easy-zoom-container").css({ 
                            "display": "block"  
                        });
                        
                        //unloadPopupBox();
                        
                    }        
                //==============zoom popup click===============//
	
	
});	//==============end of easy zoom=========//