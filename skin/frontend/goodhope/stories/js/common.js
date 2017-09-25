    jQuery('#slider').owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        dots:false,
        autoplay:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    })
    jQuery('#carousel').owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        dots:false,
        autoplay:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:3
            },
            1000:{
                items:4
            }
        }
    })

    /*mega menu*/
    jQuery(document).ready(function(){
      jQuery(".dropdown").click(
          function() {
              jQuery('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).slideDown("400");
              jQuery(this).toggleClass('open');
          },
          function() {
              jQuery('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).slideUp("400");
              jQuery(this).toggleClass('open');
          }
      );

    });




