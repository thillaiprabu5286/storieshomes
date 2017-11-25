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
    });
    jQuery('#carousel').owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        dots:false,
        autoplay:true,
        responsive:{
            0:{
                items:2
            },
            600:{
                items:3
            },
            1000:{
                items:4
            }
        }
    });
    jQuery('#tabs-nav-carousel').owlCarousel({
        loop:true,
        margin:10,
        nav:false,
        dots:false,
        autoplay:false,
        responsive:{
            0:{
                items:4
            },
            600:{
                items:2
            },
            1000:{
                items:4
            }
        }
    });
    jQuery('#tabs-carousel').owlCarousel({
        loop:true,
        margin:10,
        nav:false,
        dots:false,
        autoplay:false,
        responsive:{
            0:{
                items:2
            },
            600:{
                items:2
            },
            1000:{
                items:4
            }
        }
    });

    var checkWidth = jQuery(document).width();
    if(checkWidth <=600){
        jQuery('#carousel2').owlCarousel({
            loop:true,
            margin:10,
            nav:false,
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
                    items:3
                }
            }
        });
        jQuery('#best-sellers-carousel').owlCarousel({
            loop:true,
            margin:10,
            nav:false,
            dots:false,
            autoplay:true,
            responsive:{
                0:{
                    items:2
                },
                600:{
                    items:2
                },
                1000:{
                    items:4
                }
            }
        });
    }

    jQuery(window).scroll(function(){
        var sticky = jQuery('.header-bottom'),
            scroll = jQuery(window).scrollTop();

        if (scroll >= 200) {
            sticky.addClass('header-fixed');
            jQuery('.fixed-header-logo').css('display', 'block');
        } else {
            sticky.removeClass('header-fixed');
            jQuery('.fixed-header-logo').css('display', 'none');
        }
    });




