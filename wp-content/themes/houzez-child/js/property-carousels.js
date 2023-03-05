jQuery(document).ready( function($){

    $('.houzez-properties-carousel-js[id^="houzez-properties-carousel-"]').each(function(){
        var $div = jQuery(this);
        var token = $div.data('token');
        var obj = window['houzez_prop_caoursel_' + token];

        var slides_to_show = parseInt(obj.slides_to_show),
            slides_to_scroll = parseInt(obj.slides_to_scroll),
            navigation = parseBool(obj.navigation),
            auto_play = parseBool(obj.slide_auto),
            auto_play_speed = parseInt(obj.auto_speed),
            slide_infinite = parseBool(obj.slide_infinite),
            dots = parseBool( obj.slide_dots );

        var houzez_rtl = houzez_vars.houzez_rtl;

        if( houzez_rtl == 'yes' ) {
            houzez_rtl = true;
        } else {
            houzez_rtl = false;
        }

        function parseBool(str) {
            if( str == 'true' ) { return true; } else { return false; }
        }

        var houzezCarousel = $('#houzez-properties-carousel-'+token);

        var slick_args = {
            rtl: houzez_rtl,
            lazyLoad: 'ondemand',
            infinite: slide_infinite,
            autoplay: auto_play,
            autoplaySpeed: auto_play_speed,
            speed: 300,
            slidesToShow: slides_to_show,
            slidesToScroll: slides_to_scroll,
            arrows: navigation,
            adaptiveHeight: true,
            dots: dots,
            appendArrows: '.houzez-carousel-arrows',
            prevArrow: $('.slick-prev-js-'+token),
            nextArrow: $('.slick-next-js-'+token),
            responsive: [{
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 769,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        };

        if( houzezCarousel.parents('.property-carousel-module').next('.pd-slick-dot-container').length > 0 ){
            var dot_container = houzezCarousel.parents('.property-carousel-module').next('.pd-slick-dot-container');
            Object.assign(slick_args, {appendDots: dot_container.find('.pd-slick-dot-lists')});
        }
        
        houzezCarousel.slick(slick_args);

        houzezCarousel.parents('.property-carousel-module').next('.pd-slick-dot-container').find('.pd-slick-dot-prev').click(function () {
            houzezCarousel.slick("slickPrev");
        });
    
        houzezCarousel.parents('.property-carousel-module').next('.pd-slick-dot-container').find('.pd-slick-dot-next').click(function () {
            houzezCarousel.slick("slickNext");
        });

    });

});