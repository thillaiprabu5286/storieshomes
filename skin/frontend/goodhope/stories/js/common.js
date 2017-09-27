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
      jQuery(".dropdown").hover(
          function() {
              jQuery('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).show();
              jQuery(this).toggleClass('open');
          },
          function() {
              jQuery('.dropdown-menu', this).not('.in .dropdown-menu').stop(true,true).hide();
              jQuery(this).toggleClass('open');
          }
      );

      /*
      var trigger = jQuery('.hamburger'),
            overlay = jQuery('.overlay'),
            isClosed = false;

        trigger.click(function () {
            hamburger_cross();
        });

        function hamburger_cross() {

            if (isClosed == true) {
                overlay.hide();
                trigger.removeClass('is-open');
                trigger.addClass('is-closed');
                isClosed = false;
            } else {
                overlay.show();
                trigger.removeClass('is-closed');
                trigger.addClass('is-open');
                isClosed = true;
            }
        }

        jQuery('[data-toggle="offcanvas"]').click(function () {
            jQuery('#wrapper').toggleClass('toggled');
        });
        */
    });




