if( typeof(jQuery) != 'undefined' ){
    if( jQuery("#pm-widget-wrapper").length ){
        token = jQuery("#pm-widget-wrapper").data("token");
        jQuery.ajax({
            type: 'get',
            url: 'https://imediador.wldev.es/embed?p=' + token,
            success: function(result) {
                jQuery("#pm-widget-wrapper").append(result);
            }
        });
    }else{
        console.error("pm-widget-wrapper not found");
    }
}else{
    console.error("jQuery not found");
}
