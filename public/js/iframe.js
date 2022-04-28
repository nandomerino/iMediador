var oldStyle;
jQuery( document ).ready(function() {

    jQuery('.load-PMwidget').click(function(){

        jQuery('.PMwidget').hide();

        jQuery('#scroll-section').attr("style","display:none !important");

        if( jQuery(this).hasClass("basic") ){
            jQuery('.PMwidget.basic').attr("src","https://imediador.com/widget?p=D4XTP9L1JHMGMM2VDRDJ"); // D4XTP9L1JHMGMM2VDRDJ
            jQuery('.PMwidget.basic').fadeIn();
        }
        if( jQuery(this).hasClass("medium") ){
            jQuery('.PMwidget.medium').attr("src","https://imediador.com/widget?p=ZT3OJ0AAOXVN3Q8HMRK2"); // 58448VDL2O9C3DG5DBY1
            jQuery('.PMwidget.medium').fadeIn();
        }
        if( jQuery(this).hasClass("protect") ){
            jQuery('.PMwidget.protect').attr("src","https://imediador.com/widget?p=BOIL1SEH9JL19TF085Z1"); // FC4Z57XC439P6192J2D2
            jQuery('.PMwidget.protect').fadeIn();
        }
        jQuery('#bg-landing-hospitalizacion, #descubre, #ventajas, #como, .como.mobile').fadeOut(3000);

        if( jQuery('body').attr("style") ) {
            window.oldStyle = jQuery('body').attr("style");
        }else{
            window.oldStyle = "";
        }
        jQuery('body').attr("style", "overflow: hidden; " + window.oldStyle);

        jQuery('#PMmodalWidget').fadeIn();

        jQuery('#PMmodalWidget .close-button').fadeOut();
        jQuery('#PMmodalWidget .close-button').fadeIn();
        jQuery('#PMmodalWidget .close-button').fadeOut();
        jQuery('#PMmodalWidget .close-button').fadeIn();
        jQuery('#PMmodalWidget .close-button').fadeOut();
        jQuery('#PMmodalWidget .close-button').fadeIn();
        /*
        $([document.documentElement, document.body]).animate({
            scrollTop: jQuery(".PMwidget:visible").offset().top - 50
        }, 2000);
        imageTop = jQuery('#scroll-img').offset().top;
        startScroll = imageTop - startManualAdjustment;
        */
    });

    jQuery('#PMmodalWidget .close-button').click(function(){
        jQuery('#PMmodalWidget').hide();
        jQuery('.PMwidget').removeAttr("src");
        jQuery('#bg-landing-hospitalizacion').show();
        jQuery('#descubre').show();
        jQuery('#ventajas').show();
        jQuery('#como').show();
        jQuery('.como.mobile').show();
        jQuery('#scroll-section').removeAttr("style");
        jQuery('body').attr("style", window.oldStyle);
    });

});
