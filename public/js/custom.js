var companyAddressTypeOthers = "O";
var healthFormRequired = false;

jQuery( document ).ready(function() {

    // GENERAL - Display modal
    if (jQuery('#PMmodal').length) {

        // sets focus on modal
        /*
        jQuery('#PMmodal').on('shown.bs.modal', function () {
            jQuery('#PMmodal').trigger('focus')
        })
        */

        // Shows modal with specified content (accepts HTML)
        function displayModal (customClass, title, body, primaryButton, secondaryButton = ""){
            if( typeof customClass !== 'undefined' &&
                typeof title !== 'undefined' &&
                typeof body !== 'undefined' &&
                ( typeof primaryButton !== 'undefined' || typeof secondaryButton !== 'undefined' ) ){

                jQuery('#PMmodal').removeClass (function (index, className) {
                    return (className.match (/(^|\s)custom-\S+/g) || []).join(' ');
                });
                jQuery('#PMmodal').addClass("custom-" + customClass);
                jQuery('#PMmodal .modal-title').html(title);
                jQuery('#PMmodal .modal-body').html(body);
                if( primaryButton.length > 1 ){
                    jQuery('#PMmodal .btn-primary').html(primaryButton);
                    jQuery('#PMmodal .btn-primary').removeClass("d-none");
                }
                if( secondaryButton.length > 1 ) {
                    jQuery('#PMmodal .btn-secondary').html(secondaryButton);
                    jQuery('#PMmodal .btn-secondary').removeClass("d-none");
                }
                jQuery('#PMmodal').modal('show');
                jQuery('.modal-backdrop').toggleClass("show");
            }else{
                console.error("Not enough parameters for the modal");
            }

        }
    }

    // GENERAL - Logout
    /*    if( clearCookies ) {

            var cookies = document.cookie.split(";");

            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i];
                var eqPos = cookie.indexOf("=");
                var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
            }

        }*/

    // Updates post links
    if (jQuery('.app-core a[href]').length) {
        jQuery('.app-core a[href]').each(function() {
            var link = jQuery(this).attr("href");
            newLink = link.replace("WP/blog","app/novedades");
            jQuery(this).attr("href",newLink);
        });
    }
    if (jQuery('.public-core a[href]').length) {
        jQuery('.public-core a[href]').each(function() {
            var link = jQuery(this).attr("href");
            newLink = link.replace("WP/blog","blog");
            jQuery(this).attr("href",newLink);
        });
    }

    // MENU MOBILE - display menu
    if (jQuery('.mobile-menu').length) {
        jQuery('.mobile-menu .icon').click(function() {
            jQuery('.mobile-menu .navbar-nav').toggleClass("hidden");
        });
    }

    // PUBLIC - Sends contact form as JSON and gets response
    if (jQuery('#contactForm').length){

        jQuery('#contactForm input[type="submit"]').on('click', function() {
            jQuery('#contactForm input[type="text"]:invalid,' +
                '#contactForm input[type="email"]:invalid,' +
                '#contactForm textarea:invalid').css('border-color','RGBA(255,0,0,0.5)');
            jQuery('#contactForm input[type="text"]:valid,' +
                '#contactForm input[type="email"]:valid,' +
                '#contactForm textarea:valid').css('border-color','RGBA(210,210,210,1)');
        });
        jQuery('#contactForm').on('submit', function(e) {
            e.preventDefault(); // prevent native submit
            jQuery('#contactForm .loadingIcon').show();

            var name = jQuery("#contactForm input[name=name]").val();
            var surname = jQuery("#contactForm input[name=surname]").val();
            var phone = jQuery("#contactForm input[name=phone]").val();
            var email = jQuery("#contactForm input[name=email]").val();
            var company = jQuery("#contactForm input[name=company]").val();
            var position = jQuery("#contactForm input[name=position]").val();
            var cif = jQuery("#contactForm input[name=cif]").val();
            var message = jQuery("#contactForm textarea").val();
            var rgpd = jQuery("#contactForm input[name=rgpd]").val();
            var action = jQuery("#contactForm input[name=action]").val();

            jQuery.ajax({
                type: "POST",
                url: "/send-mail-request-data",
                data: {
                    type: "contact",
                    name:name,
                    surname:surname,
                    phone:phone,
                    email:email,
                    company:company,
                    position:position,
                    cif:cif,
                    message:message,
                    rgpd:rgpd,
                    action:action
                },
                success: function(response) {
                    displayModal(response['customClass'], response['title'], response['body'], response['button']);
                    jQuery('#contactForm .loadingIcon').hide();
                    if(response['error']){
                        console.error(response['e']);
                    }
                },
                error: function(response){
                    displayModal(response['customClass'], response['title'], response['body'], response['button']);
                    jQuery('#contactForm .loadingIcon').hide();
                    console.error(response['e']);
                }
            });
        });
    }

    // GENERAL - Marks current active menu depending on current url
    if (jQuery('#top-menu').length){
        var path = window.location.href;
        jQuery('#top-menu a').each(function() {
            if (this.href === path) {
                jQuery(this).addClass('active');
            }
        });
    }

    // PUBLIC - Sends public login form as JSON and gets response
    if (jQuery('#loginForm').length){

        jQuery('#loginForm input[type="submit"]').on('click', function() {
            jQuery('#loginForm input[type="text"]:invalid,' +
                '#loginForm input[type="password"]:invalid,' +
                '#loginForm textarea:invalid').css('border-color','RGBA(255,0,0,0.5)');
            jQuery('#loginForm input[type="text"]:valid,' +
                '#loginForm input[type="password"]:valid,' +
                '#loginForm textarea:valid').css('border-color','RGBA(210,210,210,1)');
        });
        jQuery('#loginForm').on('submit', function(e) {
            e.preventDefault(); // prevent native submit
            jQuery('#loginForm .loadingIcon').show();

            var user = jQuery("#loginForm input[name='user']").val();
            var pass = jQuery("#loginForm input[name='password']").val();
            var gestor = jQuery("#loginForm input[name='gestor']").val();
            var loginType = jQuery("#loginForm input[name='login-type']").val();
            var action = jQuery("#loginForm input[name='action']").val();

            jQuery.ajax({
                type: "POST",
                url: "/send-login-form",
                data: {
                    user: user,
                    pass: pass,
                    gestor: gestor,
                    loginType: loginType,
                    action: action
                },
                success: function(response){
                    if(response['success'] == true){
                        window.location.href = response['redirect'];
                    }else{
                        jQuery('#loginForm .loadingIcon').hide();
                        jQuery('.public-core #loginForm .error-message').html( response['e']);
                        jQuery('.public-core #loginForm .error-message').hide();
                        jQuery('.public-core #loginForm .error-message').fadeIn(1000);
                    }
                },
                error: function(response){
                    jQuery('#loginForm .loadingIcon').hide();
                    jQuery('.public-core #loginForm .error-message').html( lang["error.login"] );
                    jQuery('.public-core #loginForm .error-message').hide();
                    jQuery('.public-core #loginForm .error-message').fadeIn(1000);
                    console.error( lang["login.form.error"] );
                }
            });
        });
    }

    // APP - Sends document by mail
    if (jQuery('#sendDocumentByMail').length){

        jQuery('#sendDocumentByMail').on('submit', function(e) {
            e.preventDefault(); // prevent native submit
            jQuery('#sendDocumentByMail .loadingIcon').show();

            var mail = jQuery('#sendDocumentByMail').data("mail");
            var name = jQuery('#sendDocumentByMail').data("name");
            var file = jQuery('#sendDocumentByMail').data("file");

            jQuery.ajax({
                type: "POST",
                url: "/send-mail-request-data",
                data: {
                    type: "document",
                    name : name,
                    file: file,
                    mail: mail
                },
                success: function(response) {
                    displayModal(response['customClass'], response['title'], response['body'], response['button']);
                    jQuery('#sendDocumentByMail .loadingIcon').hide();
                    if(response['error']){
                        console.error(response['e']);
                    }
                },
                error: function(response){
                    displayModal(response['customClass'], response['title'], response['body'], response['button']);
                    jQuery('#sendDocumentByMail .loadingIcon').hide();
                    console.error(response['e']);
                }
            });
        });
    }

    // QUOTE - Loads content of selected quote type
    if (jQuery('#quote .toggles button').length) {
        jQuery('#quote .toggles button').click(function() {

            jQuery('#quote .toggles button').removeClass("active");
            jQuery('#quote #step-1 .productsList input').prop("checked", false);
            resetProductVariations();
            jQuery('#quote #step-1 .subsidio .content').hide();
            jQuery('#quote #step-1 .subsidio.content').hide();
            jQuery('#quote #step-1 .recomendador.row').hide();
            jQuery(this).addClass("active");
            // display proper content
            if( jQuery(this).hasClass("subsidio") ){
                jQuery('#quote #step-1 .quote-price-wrapper').hide();
                jQuery('#quote #step-1 .get-rates button.price').hide();
                jQuery('#quote #step-1 .get-rates button.benefit').show();
                jQuery('#quote #step-1 .subsidio.content').fadeIn();

                jQuery('#quote #step-1 .fields-wrapper').removeClass("hide-impersonator-fields");
            };
            if( jQuery(this).hasClass("precio") ){
                jQuery('#quote #step-1 .quote-price-wrapper').show();
                jQuery('#quote #step-1 .get-rates button.benefit').hide();
                jQuery('#quote #step-1 .get-rates button.price').show();
                jQuery('#quote #step-1 .subsidio.content').fadeIn();

                // this will hide impersonator fields since there is no way to send
                jQuery('#quote #step-1 .fields-wrapper').addClass("hide-impersonator-fields");
            };
            if( jQuery(this).hasClass("recomendador") ){
                jQuery('#quote #step-1 .recomendador .content').hide();
                advisor_load_fields();
                resetAdvisor();
                jQuery('#quote #step-1 .recomendador').fadeIn();

                jQuery('#quote #step-1 .fields-wrapper').removeClass("hide-impersonator-fields");

            };

        });
    }

    // QUOTE - Loads next content when productor is selected
    if (jQuery('#quote-productor').length) {
        jQuery('#quote .select-user').fadeIn();
        jQuery('#quote-productor').change(function() {
            // load content
            if( jQuery(this).children("option:selected").val() != "" ){
                jQuery('#quote .dynamic-block').hide();
                jQuery('#quote .toggles').fadeIn();

            }else{
                jQuery('#quote .dynamic-block').hide();
            }
        });
    }

    // QUOTE - products list
    jQuery("#quote .productsList").on('click', "input[type='radio']", function (e) {

        // Hides next blocks and displays loader
        resetProductVariations();

        jQuery('#quote #step-1 .loader-wrapper').fadeIn();

        var url = "/get-data";
        var ws = "getProductVariations";
        var productor = jQuery("#quote-productor").val();
        var product = jQuery("#quote input[name='quote-product']:checked").val();

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                ws: ws,
                productor: productor,
                product: product
            },
            success: function (response) {
                if (response['success'] == true) {
                    quote_load_ProductVariations(response.data);
                } else {
                    console.error( response.e);
                }
            },
            error: function (response) {
                console.error( lang["WS.error"] );
            }
        });
    });

    // QUOTE - Loads product variations dynamically from WS
    function quote_load_ProductVariations( data ){
        // Stores this info in a global array to access it later on
        window.PMproductVariations = data;

        //console.log( window.PMproductVariations );
        var output = "";


        Object.keys(data).forEach(function(key) {
            output += "<div class='checkboxWithLabel col-6 pb-2'>";
            output += "<label>";
            output += "<input type='radio' class='form-control' name='quote-product-variation' value='" + key + "'>";
            output += "<div>" + data[key].name + "</div>";
            output += "</label>";
            output += "</div>";
        });

        // Displays the new block with content
        jQuery('#quote .product-variations .dynamic-content .row').html(output);
        jQuery('#quote #step-1 .loader-wrapper').hide();
        jQuery('#quote .product-variations').fadeIn();
    }

    // QUOTE - Gets product configuration and displays new block to fill
    jQuery("#quote .product-variations").on('click', "input[type='radio']",  function (e) {

        // hides next Block and resets fields
        resetProductExtraInfo();

        jQuery('#quote #step-1 .loader-wrapper').fadeIn();

        // Then retrieves extra info of selected product variation in the background
        var url = "/get-data";
        var ws = "getProductsList";
        var productor = jQuery("#quote-productor").val();
        var product = jQuery("#quote input[name='quote-product']:checked").val();
        var productVariation = jQuery("#quote input[name='quote-product-variation']:checked").val();

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                ws: ws,
                productor: productor,
                product: product,
                productVariation: productVariation
            },
            success: function (response) {
                if (response['success'] == true) {
                    quote_load_ProductModalities(response.data);
                } else {
                    console.error( response.e);
                }
            },
            error: function (response) {
                console.error( lang["WS.error"] );
            }
        });

    });
    /*
        // QUOTE - Gets product configuration and displays new block to fill
        jQuery("#quote .product-variations").on('click', "input[type='radio']",  function (e) {

            // hides next Block and resets fields
            resetProductExtraInfo();

            jQuery('#quote #step-1 .loader-wrapper').fadeIn();

            // Then retrieves extra info of selected product variation in the background
            var url = "/get-data";
            var ws = "getProductConfiguration";
            var productor = jQuery("#quote-productor").val();
            var product = jQuery("#quote input[name='quote-product']:checked").val();
            var productVariation = jQuery("#quote input[name='quote-product-variation']:checked").val();

            jQuery.ajax({
                type: "POST",
                url: url,
                data: {
                    ws: ws,
                    productor: productor,
                    product: product,
                    productVariation: productVariation
                },
                success: function (response) {
                    if (response['success'] == true) {
                        quote_load_ProductConfiguration(response.data);
                    } else {
                        console.error( response.e);
                    }
                },
                error: function (response) {
                    console.error( lang["WS.error"] );
                }
            });

        });



    */
    function quote_load_ProductModalities ( data ){

        // Stores this info in a global array to access it later on
        window.PMproductModalities = data;

        productVariation = jQuery("#quote input[name='quote-product-variation']:checked").val();

        //console.log( data );
        //console.log("Producto:  ")
        //console.log( productVariation );
        var output = "";

        Object.keys(data).forEach(function(key) {
            if (key == productVariation) {
                Object.keys(data[key].modalityList).forEach(function(key2) {
                    output += "<div class='checkboxWithLabel col-6 pb-2'>";
                    output += "<label>";
                    output += "<input type='radio' class='form-control' name='quote-product-modality' value='" + data[key].modalityList[key2].modalityId + "'>";
                    output += "<div>" + data[key].modalityList[key2].modalityName + "</div>";
                    output += "</label>";
                    output += "</div>";
                });
            }
        });

        // Displays the new block with content
        jQuery('#quote .product-modalities .dynamic-content .row').html(output);
        jQuery('#quote #step-1 .loader-wrapper').hide();
        jQuery('#quote .product-modalities').fadeIn();

    }

    // QUOTE - Gets product configuration and displays new block to fill
    jQuery("#quote .product-modalities").on('click', "input[type='radio']",  function (e) {

        // hides next Block and resets fields
        resetProductExtraInfo();

        jQuery('#quote #step-1 .loader-wrapper').fadeIn();

        // Then retrieves extra info of selected product variation in the background
        var url = "/get-data";
        var ws = "getProductConfiguration";
        var productor = jQuery("#quote-productor").val();
        var product = jQuery("#quote input[name='quote-product']:checked").val();
        //var productVariation = jQuery("#quote input[name='quote-product-variation']:checked").val();
        var productModality = jQuery("#quote input[name='quote-product-modality']:checked").val();
        window.PMSelectedProductModality = productModality;
        //console.log(productModality);

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                ws: ws,
                productor: productor,
                product: product,
                // productVariation: productVariation,
                productModality: productModality
            },
            success: function (response) {

                if (response['success'] == true) {
                    quote_load_ProductConfiguration(response.data);
                } else {
                    console.error( response.e);
                }
            },
            error: function (response) {
                console.error( lang["WS.error"] );
            }
        });

    });

    // QUOTE - Loads extra info dynamically from WS
    function quote_load_ProductConfiguration( data ){
        // Stores this info in a global array to access it later on
        window.PMproductConfig = data;
        //console.log(data);

        // Signing method Logalty/handwriting
        if( typeof data.P_ES_EMISION_LOGALTY !== 'undefined' ) {
            window.PMsigningMode = data.P_ES_EMISION_LOGALTY;
        }else{
            window.PMsigningMode = null;
        }

        //var timestamp = window.PMproductConfig.coberturas;//'{{ Session::get("quote")}}';


        // Loads jobs
        var jobsArray = data.P_PROFESION_CLIENTE.values;
        var jobPicker = [];
        var jobSelect = "";
        Object.keys(jobsArray).forEach(function(key) {
            jobPicker.push(jobsArray[key]);
            jobSelect += "<option value='" + key + "'>" + jobsArray[key] + "</option>";
        });
        window.PMjobPicker = jobPicker.sort();
        window.PMjobSelect = jobSelect;

        // Loads Gender
        var genderArray = data.P_SEXO.values;
        var genderSelect = "<option value=''></option>";
        Object.keys(genderArray).forEach(function(key) {
            genderSelect += "<option value='" + key + "'>" + genderArray[key] + "</option>";
        });

        // Loads Height
        var hiddenHeight = "";
        //console.log(data.P_TALLA);
        helpHeight = '';
        if (data.P_TALLA.help != null){
            helpHeight = '<i class="fas fa-info-circle" title="' + data.P_TALLA.help + '"></i>';
        }
        if(data.P_TALLA.hidden == "S"){
            var heightProduct = "<input type='hidden' class='form-control w-100 quote-height valid' name='quote-height' " + data.P_TALLA.attributes + ">";
        } else {
            var heightProduct = "<label className='quote-height-label mb-1 control-label' htmlFor='quote-height'>"+data.P_TALLA.name+" "+helpHeight+"</label>";
            heightProduct += "<input type='number' class='form-control w-100 quote-height valid' name='quote-height' id='quote-height'  " + data.P_TALLA.attributes + " min='"+data.P_TALLA.min+"' max='"+data.P_TALLA.max+"'>";
        }

        // Loads Weight
        var hiddenWeight = "";
        //console.log(data.P_PESO);
        helpWeight = '';
        if (data.P_PESO.help != null){
            helpWeight = '<i class="fas fa-info-circle" title="' + data.P_PESO.help + '"></i>';
        }
        if(data.P_PESO.hidden == "S"){
            var weightProduct = "<input type='hidden' class='form-control w-100 quote-weight valid' name='quote-weight' " + data.P_PESO.attributes + ">";
        } else {
            var weightProduct = "<label className='quote-weight-label mb-1 control-label' htmlFor='quote-weight'>"+data.P_PESO.name+" "+helpWeight+"</label>";
            weightProduct += "<input type='number' class='form-control w-100 quote-weight valid' name='quote-weight' id='quote-weight' " + data.P_PESO.attributes + " min='"+data.P_PESO.min+"' max='"+data.P_PESO.max+"'>";
        }


        // Loads Job type
        var hidden = "";
        if(data.P_REGIMEN_SEG_SOCIAL.hidden == "S"){
            hidden = " type='hidden' ";
        }
        var jobTypeField = "<div class='col-6'>";
        jobTypeField += "<label class='quote-job-type-label mb-1' for='quote-job-type'>" + data.P_REGIMEN_SEG_SOCIAL.name + "</label>";
        jobTypeField += "<" + data.P_REGIMEN_SEG_SOCIAL.fieldType + hidden + " class='form-control w-100 quote-job-type valid' name='quote-job-type' " + data.P_REGIMEN_SEG_SOCIAL.attributes + ">\n";

        if( data.P_REGIMEN_SEG_SOCIAL.fieldType == "select"){

            var jobTypeArray = data.P_REGIMEN_SEG_SOCIAL.values;
            var jobTypeSelect ="";
            jobTypeSelect +="<option value=\"\"> </option>";
            Object.keys(jobTypeArray).forEach(function(key) {
                jobTypeSelect += "<option value='" + key + "'>" + jobTypeArray[key] + "</option>";
            });
            jobTypeField += jobTypeSelect;

            jobTypeField += "</select>";
        }
        jobTypeField += "</div>";


        // Loads durations
        // There are no field names coming from the WS so we have to set them manually
        var duration = "";
        if( typeof data.duracion !== 'undefined' ) {
            var durationArray = data.duracion;
            window.PMduracion = data.duracion;

            Object.keys(durationArray).forEach(function (key) {
                FieldName = key;
                duration += "<input type='hidden' class='form-control w-100 quote-duration valid quote-duration-" + FieldName + "' name='quote-duration-" + FieldName + "' data-name='" + durationArray[key].name + "' " + durationArray[key].attributes + ">\n";
                i++;
            });
        }

        // Load commercial key
        window.PMcommercialKey = data.P_CLAVE_COMERCIAL;
        //console.log(data.P_CLAVE_COMERCIAL);
        var hidden = "";
        if(data.P_CLAVE_COMERCIAL.hidden == "S"){
            hidden = " type='hidden' ";
        }
        var commercialKey = "<div class='col-12'>";
        commercialKey += "<label class='mb-1 quote-commercial-key-label' for='quote-commercial-key'>" + data.P_CLAVE_COMERCIAL.name + "</label>";
        commercialKey += "<" + data.P_CLAVE_COMERCIAL.fieldType + hidden + " class='form-control w-100 quote-commercial-key valid' name='quote-commercial-key' " + data.P_CLAVE_COMERCIAL.attributes + ">\n";

        if( data.P_CLAVE_COMERCIAL.fieldType == "select"){

            var claveCommercialArray = data.P_CLAVE_COMERCIAL.values;
            var claveCommercialSelect = "";
            claveCommercialSelect += "<option value=\"\"></option>";
            Object.keys(claveCommercialArray).forEach(function(key) {
                claveCommercialSelect += "<option value='" + key + "'>" + claveCommercialArray[key] + "</option>";
            });
            commercialKey += claveCommercialSelect;

            commercialKey += "</select>";
        }
        commercialKey += "</div>";

        var franchiseField = "";
        if (data.P_FRANQUICIA ){//data.P_NOMBRE_PRODUCTO == "ENFERMEDADES GRAVES"){
            // Load franchise
            window.PMfranchise = data.P_FRANQUICIA;
            //window.PMEnfGraves = true;
            //console.log(data.P_FRANQUICIA);
            var hidden = "";
            if(data.P_FRANQUICIA.hidden == "S"){
                hidden = " type='hidden' ";
            }
            franchiseField += "<div class='col-6'>";
            franchiseField += "<label class='mb-1 quote-franchise-label' for='quote-franchise'>" + data.P_FRANQUICIA.name + "</label>";
            franchiseField += "<" + data.P_FRANQUICIA.fieldType + hidden + " class='form-control w-100 quote-franchise valid' name='quote-franchise' " + data.P_FRANQUICIA.attributes + ">\n";

            if( data.P_FRANQUICIA.fieldType == "select"){

                var franchiseArray = data.P_FRANQUICIA.values;
                var franchiseSelect = "<option value=''>Todas las Franquicias</option>";

                Object.keys(franchiseArray).forEach(function(key) {
                    franchiseSelect += "<option value='" + key + "'>" + franchiseArray[key] + "</option>";
                });
                franchiseField += franchiseSelect;

                franchiseField += "</select>";
            }
            franchiseField += "</div>";
        } else {
            window.PMfranchise = null;
            window.PMEnfGraves = false;
        }

        window.PMduration = null;
        var durationField = "";
        if (data.P_PERIODO_COBERTURA){
            // Load commercial key
            window.PMduration = data.P_PERIODO_COBERTURA;
            var hidden = "";
            var durationField = "";
            if(data.P_PERIODO_COBERTURA.hidden == "S"){
                hidden = " type='hidden' ";
            }
            var durationField = "<div class='col-6'>";
            durationField += "<label class='mb-1 quote-duration-label' for='quote-duration'>" + data.P_PERIODO_COBERTURA.name + "</label>";
            durationField += "<" + data.P_PERIODO_COBERTURA.fieldType + hidden + " class='form-control w-100 quote-duration valid' name='quote-duration' " + data.P_PERIODO_COBERTURA.attributes + ">\n";

            if( data.P_PERIODO_COBERTURA.fieldType == "select"){

                var durationArray = data.P_PERIODO_COBERTURA.values;
                var durationSelect = "";

                Object.keys(durationArray).forEach(function(key) {
                    durationSelect += "<option value='" + key + "'>" + durationArray[key] + "</option>";
                });
                durationField += durationSelect;

                durationField += "</select>";
            }
            durationField += "</div>";
        }

        // Loads coverages if "subsidio" is selected
        var benefits = "";
        if( jQuery(".toggles .subsidio.active").length == 1 ) {
            // There are no field names coming from the WS so we have to set them manually
            var benefitsArray = data.coberturas;
            console.log(data.coberturas);
            var cols;

            var i = 1;
            cols = Math.floor(12 / Object.keys(benefitsArray).length);
            if (cols < 4) {
                cols = 4;
            }
            Object.keys(benefitsArray).forEach(function (key) {
                switch (i) {
                    case 1:
                        //FieldDescription = lang["quote.sickness"];
                        FieldName = lang["quote.sicknessFieldName"];
                        break;
                    case 2:
                        //FieldDescription = lang["quote.accident"];
                        FieldName = lang["quote.accidentFieldName"];
                        break;
                    case 3:
                        //FieldDescription = lang["quote.hospitalization"];
                        FieldName = lang["quote.hospitalizationFieldName"];
                        break;
                        case 4:
                        //FieldDescription = lang["quote.hospitalization"];
                        FieldName = "covidPrestacion";
                        break;
                    case 5:
                        //FieldDescription = lang["quote.hospitalization"];
                        FieldName = "covidHospitalizacion";
                        break;
                    case 6:
                        //FieldDescription = lang["quote.hospitalization"];
                        FieldName = "covidUCI";
                        break;
                }
                hiddenBenefits = benefitsArray[key].hidden;
                FieldDescription = benefitsArray[key].label;
                FieldType = benefitsArray[key].fieldType;
                helpBenefits = '';
                if (benefitsArray[key].helpField != null){
                    helpBenefits = '<i class="fas fa-info-circle" title="' + benefitsArray[key].helpField + '"></i>';
                }
                if (hiddenBenefits == "S") {
                    benefits += "<div class='col-" + cols + "' align-self-end >";
                    benefits += "<input type='hidden' id='"+benefitsArray[key].name+"' class='form-control w-100 quote-benefit quote-benefit-" + FieldName + "' name='quote-benefit-" + FieldName + "' min='" + benefitsArray[key].min + "' max='" + benefitsArray[key].max + "' step='1' autocomplete='off' " + benefitsArray[key].attributes + ">";
                    benefits += "<script type='text/javascript'>jQuery(document).ready(function (){jQuery('#"+benefitsArray[key].valueCopy+"').keyup(function (){var value = jQuery(this).val();jQuery('#"+benefitsArray[key].name+"').val(value);});});</script>";
                    benefits += "</div>";
                } else {
                    benefits += "<div class='col-" + cols + "' align-self-end >";
                    benefits += "<label class='mb-1 quote-benefit-label' for='quote-benefit-" + FieldName + "'>" + benefitsArray[key].label + ""+ helpBenefits +"</label>";
                    /*benefits += "<input type='number' class='form-control w-100 quote-benefit quote-benefit-" + FieldName + "' name='quote-benefit-" + FieldName + "' min='" + benefitsArray[key].min + "' max='" + benefitsArray[key].max + "' step='1' autocomplete='off' placeholder='" + benefitsArray[key].min + " - " + benefitsArray[key].max + "' required>";*/
                    if (FieldType == 'select') {
                        benefits += "<select class='form-control w-100 quote-benefit quote-benefit-" + FieldName + "' name='quote-benefit-" + FieldName + "' " + benefitsArray[key].attributes + ">";
                        var valuesArray = benefitsArray[key].values;
                        var labelArray = benefitsArray[key].labelValue;
                        var durationSelect = "";
                        durationSelect += "<option value=''> </option>";
                        Object.keys(valuesArray).forEach(function(key) {
                            durationSelect += "<option value='" +  valuesArray[key]  + "'>" + labelArray[key] + "</option>";
                        });
                        benefits += durationSelect;
                        benefits += "</select>";

                    }else if (FieldType == 'checkbox') {
                        if (benefitsArray[key].valueCopy != null) {
                            benefits += "<input type='checkbox' id='"+benefitsArray[key].name+"' class='form-control 2 w-100 quote-benefit quote-benefit-" + FieldName + "' name='quote-benefit-" + FieldName + "' min='" + benefitsArray[key].min + "' max='" + benefitsArray[key].max + "' step='1' autocomplete='off' " + benefitsArray[key].attributes + " >";
                            benefits += "<script type='text/javascript'>jQuery(document).ready(function (){jQuery('#"+benefitsArray[key].valueCopy+"').keyup(function (){var value = jQuery(this).val();jQuery('#"+benefitsArray[key].name+"').val(value);});});</script>";
                        }else if (benefitsArray[key].dependsOn != null) {
                            benefits += "<input type='checkbox' id='"+benefitsArray[key].name+"' class='form-control 2 w-100 quote-benefit quote-benefit-" + FieldName + "' name='quote-benefit-" + FieldName + "' min='" + benefitsArray[key].min + "' max='" + benefitsArray[key].max + "' step='1' autocomplete='off' " + benefitsArray[key].attributes + " >";
                            benefits += "<script type='text/javascript'>jQuery('#"+benefitsArray[key].dependsOn+"').on('change', function(){jQuery('#"+benefitsArray[key].name+"').prop('checked',this.checked);});</script>";
                        }else{
                            benefits += "<input type='checkbox' id='"+benefitsArray[key].name+"' class='form-control 2 w-100 quote-benefit quote-benefit-" + FieldName + "' name='quote-benefit-" + FieldName + "' min='" + benefitsArray[key].min + "' max='" + benefitsArray[key].max + "' step='1' autocomplete='off' " + benefitsArray[key].attributes + ">";
                        }
                    }else{
                        benefits += "<input type='number' id='"+benefitsArray[key].name+"' class='form-control w-100 quote-benefit quote-benefit-" + FieldName + "' name='quote-benefit-" + FieldName + "' min='" + benefitsArray[key].min + "' max='" + benefitsArray[key].max + "' step='1' autocomplete='off' " + benefitsArray[key].attributes + ">";
                    }
                    benefits += "</div>";

                }
                i++;
                // TODO: min/max values received are not the ones that the WS is using,
                //  sometimes we get a WS response telling us it exceeds the max while it's within provided range.
            });
        }

        // load info button if there is text for it
        if( typeof data.P_PROFESION_CLIENTE.WS.textoAyuda !== 'undefined' && data.P_PROFESION_CLIENTE.WS.textoAyuda != null){
            jobLabel = data.P_PROFESION_CLIENTE.name + ' <i class="fas fa-info-circle" title="' + data.P_PROFESION_CLIENTE.WS.textoAyuda + '"></i>';
        }else{
            jobLabel = data.P_PROFESION_CLIENTE.name;
        }


        // Loads autocomplete input text.
        jQuery( function() {

            var accentMap = {
                "á": "a",
                "é": "e",
                "í": "i",
                "ó": "o",
                "ú": "u",
                "ü": "u"
            };

            // "Remove" accents
            var normalize = function(termOriginal) {
                var term = termOriginal.toLowerCase();
                var ret = "";
                for ( var i = 0; i < term.length; i++ ) {
                    ret += accentMap[ term.charAt(i) ] || term.charAt(i);
                }
                return ret;
            };

            jQuery( "#quote .quote-job-picker" ).autocomplete({
                minLength: 0,

                source: function( request, response ) {
                    var matcher = new RegExp( jQuery.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( jQuery.grep( jobPicker, function( value ) {
                        value = value.label || value.value || value;
                        return matcher.test( value ) || matcher.test( normalize( value ) );
                    }) );
                },

                select: function(event,ui) {
                    this.value=ui.item.value;
                    jQuery(this).trigger('change');
                    return false;
                }
            });
        });



        // Load dynamic data and displays updated block
        /*        jQuery('#quote .quote-job-picker').autocomplete({
                    source: jobPicker,
                    // triggers change event when selecting from the list, really important
                    select: function(event,ui) {
                        this.value=ui.item.value;
                        jQuery(this).trigger('change');
                        return false;
                    }
                });*/


        // load discount fields for impersonator users
        var discountFields = "";
        if( typeof data.P_DESCUENTO_06 !== 'undefined' ) {
            var hidden = "";
            if (data.P_DESCUENTO_06.hidden == "S") {
                hidden = " type='hidden' ";
            }
            discountFields += "<div class='col-6 impersonator-field'>";
            discountFields += "<label class='mb-1 quote-discount-label' for='quote-discount'>" + data.P_DESCUENTO_06.name + "</label>";
            discountFields += "<" + data.P_DESCUENTO_06.fieldType + hidden + " class='form-control w-100 quote-discount valid' name='quote-discount' " + data.P_DESCUENTO_06.attributes + ">\n";
            discountFields += "</div>";
        }

        if( typeof data.P_ANYOS_DTO_06 !== 'undefined' ) {
            var hidden = "";
            if (data.P_ANYOS_DTO_06.hidden == "S") {
                hidden = " type='hidden' ";
            }
            discountFields += "<div class='col-6 impersonator-field'>";
            discountFields += "<label class='mb-1 quote-discount-years-label' for='quote-discount-years'>" + data.P_ANYOS_DTO_06.name + "</label>";
            discountFields += "<" + data.P_ANYOS_DTO_06.fieldType + hidden + " class='form-control w-100 quote-discount-years valid' name='quote-discount-years' " + data.P_ANYOS_DTO_06.attributes + ">\n";
            discountFields += "</div>";
        }

        if( typeof data.P_SOBREPRIMA_DEL !== 'undefined' ) {
            var hidden = "";
            if (data.P_SOBREPRIMA_DEL.hidden == "S") {
                hidden = " type='hidden' ";
            }
            discountFields += "<div class='col-6 impersonator-field'>";
            discountFields += "<label class='mb-1 quote-discount-sobreprima-label' for='quote-discount-sobreprima'>" + data.P_SOBREPRIMA_DEL.name + "</label>";
            discountFields += "<" + data.P_SOBREPRIMA_DEL.fieldType + hidden + " class='form-control w-100 quote-discount-sobreprima valid' name='quote-discount-sobreprima' " + data.P_SOBREPRIMA_DEL.attributes + ">\n";
            discountFields += "</div>";
        }

        if( typeof data.P_DTO_COMISION_MED !== 'undefined' ) {
            var hidden = "";
            if (data.P_DTO_COMISION_MED.hidden == "S") {
                hidden = " type='hidden' ";
            }
            discountFields += "<div class='col-6 impersonator-field'>";
            discountFields += "<label class='mb-1 quote-discount-commision-med-label' for='quote-discount-commision-med'>" + data.P_DTO_COMISION_MED.name + "</label>";
            discountFields += "<" + data.P_DTO_COMISION_MED.fieldType + hidden + " class='form-control w-100 quote-discount-commision-med valid' name='quote-discount-commision-med' " + data.P_DTO_COMISION_MED.attributes + ">\n";
            discountFields += "</div>";
        }

        if( typeof data.P_DTO_COMISION_DEL !== 'undefined' ) {
            var hidden = "";
            discountFields +=  "";
            if(data.P_DTO_COMISION_DEL.hidden == "S"){
                hidden = " type='hidden' ";
            }
            discountFields += "<div class='col-6 impersonator-field'>";
            discountFields += "<label class='mb-1 quote-discount-commision-del-label' for='quote-discount-commision-del'>" + data.P_DTO_COMISION_DEL.name + "</label>";
            discountFields += "<" + data.P_DTO_COMISION_DEL.fieldType + hidden + " class='form-control w-100 quote-discount-commision-del valid' name='quote-discount-commision-del' " + data.P_DTO_COMISION_DEL.attributes + ">\n";
            discountFields += "</div>";
        }

        if( typeof data.P_RECARGO_FINANCIACION !== 'undefined' ) {
            var hidden = "";
            if(data.P_RECARGO_FINANCIACION.hidden == "S"){
                hidden = " type='hidden' ";
            }
            discountFields +=  "<div class='col-6 impersonator-field'>";
            discountFields += "<label class='quote-discount-recargo-financiacion-label mb-1' for='quote-discount-recargo-financiacion'>" + data.P_RECARGO_FINANCIACION.name + "</label>";
            discountFields +=  "<" + data.P_RECARGO_FINANCIACION.fieldType + hidden + " class='form-control w-100 quote-discount-recargo-financiacion valid' name='quote-discount-recargo-financiacion' " + data.P_RECARGO_FINANCIACION.attributes + ">\n";

            if (data.P_RECARGO_FINANCIACION.fieldType == "select") {

                var recargoArray = data.P_RECARGO_FINANCIACION.values;
                Object.keys(recargoArray).forEach(function (key) {
                    discountFields += "<option value='" + key + "'>" + recargoArray[key] + "</option>";
                });
                discountFields += "</select>";
            }
            discountFields += "</div>";
        }

        if( typeof data.P_CANAL_COBRO !== 'undefined' ) {
            var hidden = "";
            if (data.P_CANAL_COBRO.hidden == "S") {
                hidden = " type='hidden' ";
            }
            discountFields += "<div class='col-6 impersonator-field'>";
            discountFields += "<label class='quote-discount-cobro-label mb-1' for='quote-discount-cobro'>" + data.P_CANAL_COBRO.name + "</label>";
            discountFields += "<" + data.P_CANAL_COBRO.fieldType + hidden + " class='form-control w-100 quote-discount-cobro valid' name='quote-discount-cobro' " + data.P_CANAL_COBRO.attributes + ">\n";

            if (data.P_CANAL_COBRO.fieldType == "select") {
                var cobroArray = data.P_CANAL_COBRO.values;
                Object.keys(cobroArray).forEach(function (key) {
                    discountFields += "<option value='" + key + "'>" + cobroArray[key] + "</option>";
                });
                discountFields += "</select>";
            }
            discountFields += "</div>";
        }

        //console.log(data);

        jQuery('#quote .product-extra-info .quote-job').html(jobSelect);
        jQuery('#quote .quote-job-type-label').html(data.P_REGIMEN_SEG_SOCIAL.name);
        jQuery('#quote .product-extra-info .quote-job-type').html(jobTypeSelect);
        jQuery('#quote .product-extra-info .quote-commercialKey').html(commercialKey);
        jQuery('#quote .product-extra-info .quote-gender').html(genderSelect);
        jQuery('#quote .quote-birthdate-label').html(data.P_FECHA_NACIMIENTO_CLIENTE.name);
        jQuery('#quote .quote-gender-label').html(data.P_SEXO.name);
        //jQuery('#quote .quote-height').html(heightProduct);
        //jQuery('#quote .quote-weight').html(heightWeight);
        jQuery('#quote .quote-job-label').html(jobLabel);
        jQuery('#quote .product-extra-info .quote-height-wrapper').html(heightProduct);
        jQuery('#quote .product-extra-info .quote-weight-wrapper').html(weightProduct);
        //jQuery('#quote .product-extra-info .quote-weight').prop("min", data.P_PESO.min);
        //jQuery('#quote .product-extra-info .quote-weight').prop("max", data.P_PESO.max);
        //jQuery('#quote .product-extra-info .quote-weight-label').html(data.P_PESO.name);
        //jQuery('#quote .product-extra-info .quote-height').prop("min", data.P_TALLA.min);
        //jQuery('#quote .product-extra-info .quote-height').prop("max", data.P_TALLA.max);
        //jQuery('#quote .product-extra-info .quote-height-label').html(data.P_TALLA.name);

        durationGroup = franchiseField + duration + durationField;
        extraFields = benefits  + discountFields;
        jQuery('#quote .product-extra-info .quote-benefit-wrapper').html(extraFields);
        jQuery('#quote .product-extra-info .quote-duration-wrapper').html(durationGroup);


        //jQuery('#quote .product-extra-info .dynamic-content .row').html(output);
        jQuery('#quote #step-1 .loader-wrapper').hide();
        jQuery('#quote .product-extra-info').fadeIn();
        jQuery('#quote .get-rates').fadeIn();
    }

    // QUOTE - Date field behavior (only numbers and /)
    jQuery("#quote").on('keypress',
        ".quote-birthdate, .quote-another-insurance-ends, .datetimepickerHealth input, .quote-starting-date, .quote-another-insurance-ends, .date-input",
        function (evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }else{
                var inputLength = event.target.value.length;
                if (event.keyCode != 8){
                    if(inputLength === 2 || inputLength === 5){
                        var thisVal = event.target.value;
                        thisVal += '/';
                        jQuery(event.target).val(thisVal);
                    }
                }
            }
        });

    // QUOTE - Validates extra info fields
    if (jQuery('#quote .product-extra-info').length) {

        jQuery("#quote .product-extra-info").on('input change click keyup',
            ".quote-job-picker",
            function (e) {
                var i = 0;
                var found = false;
                jQuery(".product-extra-info .quote-job option").each(function () {
                    if (this.text == jQuery(".product-extra-info .quote-job-picker").val()) {
                        jQuery(".product-extra-info .quote-job").prop("selectedIndex", i);
                        found = true;
                    } else {
                        i++;
                    }
                });
                if (found) {
                    jQuery(this).addClass("valid");
                    jQuery(this).removeClass("invalid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            });

        jQuery("#quote .product-extra-info").on('input change click keyup',
            "input[required]:visible, select[required]:visible",
            function (e) {

                // basic field validation
                if( jQuery(this).attr("type") == "text" ||
                    jQuery(this).is("select") ){
                    if (jQuery(this).val() != "") {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                if( jQuery(this).attr("type") == "number"){
                    if (jQuery(this).val() > 0) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                // Specific field validation
                if (jQuery(this).hasClass("quote-height") ||
                    jQuery(this).hasClass("quote-weight")) {
                    if (jQuery(this).val() >= parseInt( jQuery(this).prop( "min" ) ) &&
                        jQuery(this).val() <= parseInt( jQuery(this).prop( "max" ) ) ) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                if (jQuery(this).hasClass("quote-birthdate") ||
                    jQuery(this).hasClass("quote-starting-date")) {
                    var valid;

                    if( jQuery(this).val().length == 10) {
                        var splitDate = jQuery(this).val().split("/");
                        if (splitDate[0] >= 1 && splitDate[0] <= 31) {
                            if (splitDate[1] >= 1 && splitDate[1] <= 12) {
                                if (splitDate[2] >= 1920 && splitDate[2] <= 2021) {
                                    valid = true;
                                } else {
                                    valid = false;
                                }
                            } else {
                                valid = false;
                            }
                        } else {
                            valid = false;
                        }
                    }else {
                        valid = false;
                    }

                    if (valid) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }


                //console.log( jQuery(this).attr("class") );
                enableQuoteButton();
            });

        // QUOTE - benefits only numbers
        jQuery("#quote .product-extra-info").on('keypress', ".quote-benefit[required]", function (evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }

            enableQuoteButton();
        });

        // QUOTE - benefits limits min/max provided by WS
        /*     jQuery("#quote .product-extra-info").on('input change click keyup', ".quote-benefit[required]", function (e) {
                 if (jQuery(this).val() >= parseInt( jQuery(this).prop( "min" ) ) &&
                     jQuery(this).val() <= parseInt( jQuery(this).prop( "max" ) ) ) {
                     jQuery(this).removeClass("invalid");
                     jQuery(this).addClass("valid");
                 } else {
                     jQuery(this).addClass("invalid");
                     jQuery(this).removeClass("valid");
                 }

                 enableQuoteButton();
             });*/
    }

    // QUOTE - Validates extra info and enables getRate button
    function enableQuoteButton( ) {
        allValid = true;
        //console.log( " --------------------  " );
        jQuery( '#quote .product-extra-info input[required]:visible, ' +
            '#quote .product-extra-info select[required]:visible' )
            .each(function() {
                if( !jQuery(this).hasClass("valid") ){
                    //console.log( " FALSE - " + jQuery(this).attr('class') );
                    allValid = false;
                }else{
                    //console.log( " TRUE - " + jQuery(this).attr('class') );
                }
            });

        if( allValid ){
            jQuery('.get-rates .quote-button').removeAttr("disabled");
            jQuery('.get-rates .quote-button').addClass("active");
        }else{
            jQuery('.get-rates .quote-button').attr("disabled", "disabled");
            jQuery('.get-rates .quote-button').removeClass("active");
        }

        return allValid;
    }

    // QUOTE - Checks fields again and gets rates from WS
    if (jQuery('#quote .get-rates .benefit').length) {
        jQuery('#quote .get-rates .benefit').click(function() {

            // Runs validation on all fields
            jQuery( '#quote .product-extra-info input:visible, ' +
                '#quote .product-extra-info select:visible' )
                .change();

            allValid = enableQuoteButton();

            // If all valid gets rates
            if( allValid ){
                //getRates
                resetRatesTable();
                jQuery('#quote .get-rates .loadingIcon').fadeIn();
                jQuery('#quote .form .loading-lock').fadeIn();
                jQuery('#quote .get-rates .quote-button').attr("disabled","disabled");
                jQuery('#quote .table-actions .action-minibutton').attr("disabled", "disabled").removeClass("active");

                var url = "/get-data";
                var ws = "getRates";

                var productor = jQuery("#quote-productor").val();
                var option = window.PMproductVariations[jQuery("#quote input[name='quote-product-variation']:checked").val()].option;
                var productId = jQuery("#quote input[name='quote-product-modality']:checked").val();//jQuery("#quote input[name='quote-product-variation']:checked").val();
                var startingDate = jQuery("#quote .quote-starting-date").val();
                var profession = jQuery("#quote .quote-job").val();
                var birthdate  = jQuery("#quote .quote-birthdate").val();
                var gender = jQuery("#quote .quote-gender").val();
                var height = jQuery("#quote #quote-height").val();
                var weight = jQuery("#quote #quote-weight").val();
                var commercialKey = jQuery('#quote .quote-commercial-key').val();
                var duration = null;
                if (window.PMduration != null)
                {
                    duration = jQuery('#quote .quote-duration').val();
                }
                var jobType = jQuery('#quote .quote-job-type').val();


                var discount  = jQuery('#quote .quote-discount').val();
                var discountYears  = jQuery('#quote .quote-discount-years').val();
                var discountSobreprima = jQuery('#quote .quote-discount-sobreprima').val();
                var discountCommisionMed = jQuery('#quote .quote-discount-commision-med').val();
                var discountCommisionDel = jQuery('#quote .quote-discount-commision-del').val();
                var discountRecargoFinanciacion = jQuery('#quote .quote-discount-recargo-financiacion').val();
                var discountCobro = jQuery('#quote .quote-discount-cobro').val();

                var enfCob = null;
                var enfSub  = null;
                var accCob = null;
                var accSub = null;
                var hospCob = null;
                var hospSub = null;
                var covidPrestacionCob = null;
                var covidPrestacionSub = null;
                var covidHospitalizacionCob = null;
                var covidHospitalizacionSub = null;
                var covidUCICob = null;
                var covidUCISub = null;
                var date = jQuery('#quote .quote-starting-date').val();


                var franchise = null;
                if (window.PMfranchise != null){
                    franchise = jQuery("#quote .quote-franchise").val();
                    if (franchise==''){
                        franchise = null;
                    }
                }

                var enfGraves = window.PMEnfGraves;

                // TODO: add new LENGTH parameter so the results are correct
                //  waiting for client response on increased development time

                var i = 1;
                benefitsArray = window.PMproductConfig.coberturas;
                Object.keys(benefitsArray).forEach(function(key) {
                    switch(i) {
                        case 1:
                            enfCob = window.PMproductConfig.coberturas[key].name
                            enfSub = jQuery("#quote .quote-benefit-sickness").val();
                            break;
                        case 2:
                            accCob = window.PMproductConfig.coberturas[key].name
                            accSub = jQuery("#quote .quote-benefit-accident").val();
                            break;
                        case 3:
                            hospCob = window.PMproductConfig.coberturas[key].name
                            hospSub  = jQuery("#quote .quote-benefit-hospitalization").val();
                            break;
                        case 4:
                            covidPrestacionCob = window.PMproductConfig.coberturas[key].name
                            if (jQuery("#quote .quote-benefit-covidPrestacion").is(':checkbox')) {
                                covidPrestacionSub = jQuery("#quote .quote-benefit-covidPrestacion:checked").val();
                            } else {
                                covidPrestacionSub = jQuery("#quote .quote-benefit-covidPrestacion").val();
                            }
                            break;
                        case 5:
                            covidHospitalizacionCob = window.PMproductConfig.coberturas[key].name
                            if (jQuery("#quote .quote-benefit-covidHospitalizacion").is(':checkbox')) {
                                covidHospitalizacionSub = jQuery("#quote .quote-benefit-covidHospitalizacion:checked").val();
                            } else {
                                covidHospitalizacionSub = jQuery("#quote .quote-benefit-covidHospitalizacion").val();
                            }
                            break;
                        case 6:
                            covidUCICob = window.PMproductConfig.coberturas[key].name
                            if (jQuery("#quote .quote-benefit-covidUCI").is(':checkbox')) {
                                covidUCISub  = jQuery("#quote .quote-benefit-covidUCI:checked").val();
                            } else {
                                covidUCISub  = jQuery("#quote .quote-benefit-covidUCI").val();
                            }
                            break;

                    }
                    i++;
                });

                // Stores it to use later
                window.PMgetRatesData = {
                    productor : productor,
                    option : option,
                    productId : productId,
                    startingDate : startingDate,
                    profession : profession,
                    birthdate : birthdate,
                    gender : gender,
                    height : height,
                    weight : weight,
                    commercialKey : commercialKey,
                    jobType : jobType,
                    duration : duration,
                    discount : discount,
                    discountYears : discountYears,
                    discountSobreprima : discountSobreprima,
                    discountCommisionMed : discountCommisionMed,
                    discountCommisionDel : discountCommisionDel,
                    discountRecargoFinanciacion : discountRecargoFinanciacion,
                    discountCobro : discountCobro,
                    enfCob : enfCob,
                    enfSub : enfSub,
                    accCob : accCob,
                    accSub : accSub,
                    hospCob : hospCob,
                    hospSub : hospSub,
                    hospSub : hospSub,
                    covidPrestacionCob : covidPrestacionCob,
                    covidPrestacionSub : covidPrestacionSub,
                    covidHospitalizacionCob : covidHospitalizacionCob,
                    covidHospitalizacionSub : covidHospitalizacionSub,
                    covidUCICob : covidUCICob,
                    covidUCISub : covidUCISub,
                    franchise : franchise,
                    date: date
                }


                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        ws: ws,
                        productor: productor,
                        option: option,
                        productId: productId,
                        profession: profession,
                        birthdate: birthdate,
                        gender: gender,
                        height: height,
                        weight: weight,
                        commercialKey : commercialKey,
                        jobType : jobType,
                        duration: duration,
                        discount : discount,
                        discountYears : discountYears,
                        discountSobreprima : discountSobreprima,
                        discountCommisionMed : discountCommisionMed,
                        discountCommisionDel : discountCommisionDel,
                        discountRecargoFinanciacion : discountRecargoFinanciacion,
                        discountCobro : discountCobro,
                        enfCob: enfCob,
                        enfSub: enfSub,
                        accCob: accCob,
                        accSub: accSub,
                        hospCob: hospCob,
                        hospSub: hospSub,
                        covidPrestacionCob : covidPrestacionCob,
                        covidPrestacionSub : covidPrestacionSub,
                        covidHospitalizacionCob : covidHospitalizacionCob,
                        covidHospitalizacionSub : covidHospitalizacionSub,
                        covidUCICob : covidUCICob,
                        covidUCISub : covidUCISub,
                        franchise: franchise,
                        enfGraves: enfGraves
                    },
                    success: function (response) {

                        if (response['success'] == true) {
                            quote_load_Rates(response.data);
                        } else {
                            console.error( response.e);
                            displayModal("health", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                            jQuery('#quote .get-rates .loadingIcon').hide();
                            jQuery('#quote .form .loading-lock').hide();
                            jQuery('#quote .get-rates .quote-button').removeAttr("disabled");
                        }
                    },
                    error: function (response) {
                        console.error( response.e);
                        displayModal("health", lang["quote.modal.error"], lang["WS.error"], lang["quote.modal.close"]);
                        jQuery('#quote .get-rates .loadingIcon').hide();
                        jQuery('#quote .form .loading-lock').hide();
                        jQuery('#quote .get-rates .quote-button').removeAttr("disabled");
                    }
                });
            }

        });
    }

    // QUOTE - Process rates and display the table
    function quote_load_Rates(data){

        window.PMrates = data;
        product = jQuery('#quote .quote-product:checked').next().html().trim().toUpperCase();
        productVariation = jQuery("#quote input[name='quote-product-modality']:checked").next().html().trim().toUpperCase();

        //console.log("Rates ");
        //console.log(data);
        var startIcon = "<i class='fas fa-info-circle'"; //comment-alt'";
        var endIcon = "></i>"
        var i;
        var j;
        var k;

        //Create headers using column names
        var headers = [];
        headers.push(lang["quote.text.exemption"]);
        Object.keys(data.messages).every(function(i) {
            Object.keys(data.messages[i]).forEach(function(j) {
                headers.push(j);
            });
            return false;
        });

        tableDescription = "<p>" + data.name + "</p>" + "<p>" + data.description + "</p>";
        tableFooter = "<p>" + data.foot + "</p>";

        head = "<thead>";
        head += "<tr>";
        head += "<th colspan='" + headers.length + "'>" + product + " - " + productVariation + "</th>";
        head += "</tr>";
        head += "<tr>";

        Object.keys(headers).forEach(function (j) {
            head += "<th scope='col'>" + headers[j] + "</th>";
        });


        head += "</tr>";
        head += "</thead>";

        rows = "<tbody>";

        var numFila = 0;

        // Render rows
        Object.keys(data.messages).forEach(function(i) {

            rows += "<tr class='PM-row row-" + numFila + "'><td>" + i + "</td>";
            numFila++;
            // Render columns
            Object.keys(data.messages[i]).forEach(function(j) {
                //console.log(data.messages[i][j])
                if (isNaN(data.messages[i][j])){
                    rows += "<td>" + startIcon  + "title='" + data.messages[i][j] + "'" + endIcon;
                } else {
                    let rawPrice = data.messages[i][j];
                    let splitPrice = rawPrice.split(".");

                    var adjustedDecimal;
                    if( typeof splitPrice[1] !== 'undefined' ) {
                        adjustedDecimal = splitPrice[1] + "00";
                    }else{
                        adjustedDecimal = "00";
                    }

                    let newDecimal = adjustedDecimal.slice(0,2);


                    amount = splitPrice[0] + "," + newDecimal + " &euro;";
                    rows += "<td class='product' ";

                    // Renders coverage (coberturas) data
                    for( k=0;k<data.table[i][j].coverages.length;k++ ) {
                        rows += " data-capital-" + k + "='" + data.table[i][j].coverages[k].capital + "'";
                        rows += " data-codigo-" + k + "='" + data.table[i][j].coverages[k].codigo + "'";
                        rows += " data-descripcion-" + k + "='" + data.table[i][j].coverages[k].descripcion + "'";
                        rows += " data-duracion-" + k + "='" + data.table[i][j].coverages[k].duracion + "'";
                        rows += " data-franquicia-" + k + "='" + data.table[i][j].coverages[k].franquicia + "'";
                        rows += " data-prima-neta-" + k + "='" + data.table[i][j].coverages[k].primaNeta + "'";

                    }

                    // Renders quotes (formas de pago) data
                    for( k=0;k<data.table[i][j].quotes.length;k++ ) {
                        switch(k){
                            case 0:
                                rows += " data-annual='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-annual-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-annual-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                            case 1:
                                rows += " data-biannual='" + data.table[i][j].quotes[k].primaNetaFraccionada + "' data-biannual-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-biannual-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                            case 2:
                                rows +=  " data-quarterly='" + data.table[i][j].quotes[k].primaNetaFraccionada + "' data-quarterly-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-quarterly-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                            case 3:
                                rows += " data-monthly='" + data.table[i][j].quotes[k].primaNetaFraccionada + "' data-monthly-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-monthly-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                        }
                    }

                    //description
                    rows += " data-info='" + data.info[i][j].quotes[4].valor + "'";
                    rows += " data-product-description='" + data.messages[i][j] + "' ";

                    rows += ">" + amount + "</td>";


                }
            });

            rows += "</tr>";

        });

        rows += "</tbody>";
        table = head + rows;

        // Billing cycles
        var billingCycles = "";
        cols = Math.floor( 12 / data.billingCycles.length );
        for( i=0;i<data.billingCycles.length;i++ ) {
            switch(i) {
                case 0:
                    description = lang["quote.annual.text"];
                    dataField = lang["quote.annual.field"];
                    break;
                case 1:
                    description = lang["quote.biannual.text"];
                    dataField = lang["quote.biannual.field"];
                    break;
                case 2:
                    description = lang["quote.quarterly.text"];
                    dataField = lang["quote.quarterly.field"];
                    break;
                case 3:
                    description = lang["quote.monthly.text"];
                    dataField = lang["quote.monthly.field"];
                    break;
            }

            billingCycles += "<div class='checkboxWithLabel col-" + cols + " pb-2'>";
            billingCycles +=    "<label>";
            billingCycles +=        "<input type='radio' class='form-control' name='quote-billing-cycle' value='" + data.billingCycles[i] + "' disabled>";
            billingCycles +=        "<div>" + description + "</div>";
            billingCycles +=    "</label>";
            billingCycles +=    "<div class='data-info " + dataField + " w-100'>";
            billingCycles +=        "<div class='quote-amount txt-navy-blue'></div>";
            billingCycles +=        "<div class='total-quote-amount txt-navy-blue'></div>";
            billingCycles +=    "</div>";
            billingCycles += "</div>";
        }


        window.PMquoteTable = table;
        jQuery('#quote .rates-table-description').html(tableDescription);
        jQuery('#quote .rates-table-footer').html(tableFooter);
        jQuery('#quote .rates-table-description').fadeIn();
        jQuery('#quote .rates-table table').html(table);
        jQuery('#quote .rates-table .billing-cycle').html(billingCycles);
        jQuery('#quote .rates-table').fadeIn();
        jQuery('#quote .get-rates .loadingIcon').hide();
        jQuery('#quote .form .loading-lock').hide();
        jQuery('#quote .get-rates .quote-button').removeAttr("disabled");

    }

    // QUOTE - Process rates and display the table (ENFERMEDADES GRAVES case)
    function quote_load_Rates_EG(data){

        window.PMrates = data;
        product = jQuery('#quote .quote-product:checked').next().html().trim().toUpperCase();
        productVariation = jQuery("#quote input[name='quote-product-modality']:checked").next().html().trim().toUpperCase();

        var startIcon = "<i class='fas fa-info-circle'"; //comment-alt'";
        var endIcon = "></i>"
        var i;
        var j;
        var k;

        //Create headers using column names
        var headers = [];
        headers.push(lang["quote.text.exemption"]);
        Object.keys(data.messages).every(function(i) {
            Object.keys(data.messages[i]).forEach(function(j) {
                headers.push(j);
            });
            return false;
        });

        tableDescription = "<p>" + data.name + "</p>" + "<p>" + data.description + "</p>";

        head = "<thead>";
        head += "<tr>";
        head += "<th colspan='" + headers.length + "'>" + product + " - " + productVariation + "</th>";
        head += "</tr>";
        head += "<tr>";

        Object.keys(headers).forEach(function (j) {
            head += "<th scope='col'>" + headers[j] + "</th>";
        });


        head += "</tr>";
        head += "</thead>";

        rows = "<tbody>";

        var numFila = 0;

        // Render rows
        Object.keys(data.messages).forEach(function(i) {

            rows += "<tr class='PM-row row-" + numFila + "'><td>" + i + "</td>";
            numFila++;
            // Render columns
            Object.keys(data.messages[i]).forEach(function(j) {

                if (isNaN(data.messages[i][j])){
                    rows += "<td>" + startIcon  + "title='" + data.messages[i][j] + "'" + endIcon;
                } else {
                    let rawPrice = data.messages[i][j];
                    let splitPrice = rawPrice.split(".");

                    var adjustedDecimal;
                    if( typeof splitPrice[1] !== 'undefined' ) {
                        adjustedDecimal = splitPrice[1] + "00";
                    }else{
                        adjustedDecimal = "00";
                    }

                    let newDecimal = adjustedDecimal.slice(0,2);


                    amount = splitPrice[0] + "," + newDecimal + " &euro;";
                    rows += "<td class='product' ";

                    // Renders coverage (coberturas) data
                    for( k=0;k<data.table[i][j].coverages.length;k++ ) {
                        rows += " data-capital-" + k + "='" + data.table[i][j].coverages[k].capital + "'";
                        rows += " data-codigo-" + k + "='" + data.table[i][j].coverages[k].codigo + "'";
                        rows += " data-descripcion-" + k + "='" + data.table[i][j].coverages[k].descripcion + "'";
                        rows += " data-duracion-" + k + "='" + data.table[i][j].coverages[k].duracion + "'";
                        rows += " data-franquicia-" + k + "='" + data.table[i][j].coverages[k].franquicia + "'";
                        rows += " data-prima-neta-" + k + "='" + data.table[i][j].coverages[k].primaNeta + "'";
                    }

                    // Renders quotes (formas de pago) data
                    for( k=0;k<data.table[i][j].quotes.length;k++ ) {
                        switch(k){
                            case 0:
                                rows += " data-annual='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-annual-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-annual-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                            case 1:
                                rows += " data-biannual='" + data.table[i][j].quotes[k].primaNetaFraccionada + "' data-biannual-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-biannual-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                            case 2:
                                rows +=  " data-quarterly='" + data.table[i][j].quotes[k].primaNetaFraccionada + "' data-quarterly-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-quarterly-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                            case 3:
                                rows += " data-monthly='" + data.table[i][j].quotes[k].primaNetaFraccionada + "' data-monthly-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-monthly-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                        }
                    }

                    //description
                    rows += " data-product-description='" + data.messages[i][j] + "' ";

                    rows += ">" + amount + "</td>";


                }
            });

            rows += "</tr>";

        });

        rows += "</tbody>";
        table = head + rows;

        // Billing cycles
        var billingCycles = "";
        cols = Math.floor( 12 / data.billingCycles.length );
        for( i=0;i<data.billingCycles.length;i++ ) {
            switch(i) {
                case 0:
                    description = lang["quote.annual.text"];
                    dataField = lang["quote.annual.field"];
                    break;
                case 1:
                    description = lang["quote.biannual.text"];
                    dataField = lang["quote.biannual.field"];
                    break;
                case 2:
                    description = lang["quote.quarterly.text"];
                    dataField = lang["quote.quarterly.field"];
                    break;
                case 3:
                    description = lang["quote.monthly.text"];
                    dataField = lang["quote.monthly.field"];
                    break;
            }

            billingCycles += "<div class='checkboxWithLabel col-" + cols + " pb-2'>";
            billingCycles +=    "<label>";
            billingCycles +=        "<input type='radio' class='form-control' name='quote-billing-cycle' value='" + data.billingCycles[i] + "' disabled>";
            billingCycles +=        "<div>" + description + "</div>";
            billingCycles +=    "</label>";
            billingCycles +=    "<div class='data-info " + dataField + " w-100'>";
            billingCycles +=        "<div class='quote-amount txt-navy-blue'></div>";
            billingCycles +=        "<div class='total-quote-amount txt-navy-blue'></div>";
            billingCycles +=    "</div>";
            billingCycles += "</div>";
        }


        window.PMquoteTable = table;
        jQuery('#quote .rates-table-description').html(tableDescription);
        jQuery('#quote .rates-table-description').fadeIn();
        jQuery('#quote .rates-table table').html(table);
        jQuery('#quote .rates-table .billing-cycle').html(billingCycles);
        jQuery('#quote .rates-table').fadeIn();
        jQuery('#quote .get-rates .loadingIcon').hide();
        jQuery('#quote .form .loading-lock').hide();
        jQuery('#quote .get-rates .quote-button').removeAttr("disabled");

    }


    // QUOTE - Checks fields again and gets rates by price from WS
    if (jQuery('#quote .get-rates .price').length) {
        jQuery('#quote .get-rates .price').click(function() {

            // Runs validation on all fields
            jQuery( '#quote .product-extra-info input:visible, ' +
                '#quote .product-extra-info select:visible' )
                .change();

            allValid = enableQuoteButton();

            // If all valid gets rates
            if( allValid ){
                //getRates
                resetRatesTable();
                jQuery('#quote .get-rates .loadingIcon').fadeIn();
                jQuery('#quote .form .loading-lock').fadeIn();
                jQuery('#quote .get-rates .quote-button').attr("disabled","disabled");
                jQuery('#quote .table-actions .action-minibutton').attr("disabled", "disabled").removeClass("active");

                var url = "/get-data";
                var ws = "getRatesByPrice";

                var productor = jQuery("#quote-productor").val();
                var option = window.PMproductVariations[jQuery("#quote input[name='quote-product-variation']:checked").val()].reverseQuote;
                //console.log(window.PMproductVariations);
                //console.log(window.PMproductVariations[jQuery("#quote input[name='quote-product-variation']:checked").val()]);
                var productCode = jQuery("#quote input[name='quote-product-modality']:checked").val();
                var productId = jQuery("#quote input[name='quote-product-variation']:checked").val();
                var startingDate = jQuery("#quote .quote-starting-date").val();
                var price = jQuery("#quote .quote-price").val();

                var franchise = null;
                if (window.PMfranchise != null){
                    franchise = jQuery("#quote .quote-franchise").val();
                    if (franchise==''){
                        franchise = null;
                    }
                }

                var jobType = jQuery('#quote .quote-job-type').val();
                var profession = jQuery("#quote .quote-job").val();
                var birthdate  = jQuery("#quote .quote-birthdate").val();
                var gender = jQuery("#quote .quote-gender").val();
                var height = jQuery("#quote #quote-height").val();
                var weight = jQuery("#quote #quote-weight").val();
                var commercialKey = jQuery('#quote .quote-commercial-key').val();

                var duration = null;
                if (window.PMduration != null)
                {
                    duration = jQuery('#quote .quote-duration').val();
                }


                // Stores it to use later
                window.PMgetRatesData = {
                    productor : productor,
                    option : option,
                    productCode : productCode,
                    productId : productId,
                    startingDate : startingDate,
                    price : price,
                    franchise : franchise,
                    jobType : jobType,
                    profession : profession,
                    birthdate : birthdate,
                    gender : gender,
                    height : height,
                    weight : weight,
                    commercialKey : commercialKey,
                    duration: duration
                }
                console.log(window.PMgetRatesData);


                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        ws: ws,
                        productor : productor,
                        option : option,
                        productCode : productCode,
                        productId : productId,
                        price : price,
                        franchise : franchise,
                        jobType : jobType,
                        profession : profession,
                        birthdate : birthdate,
                        gender : gender,
                        height : height,
                        weight : weight,
                        commercialKey : commercialKey,
                        duration: duration
                    },
                    success: function (response) {
                        if (response['success'] == true) {
                            // TODO: there are products that send information in a different structure and won't work
                            //console.log(response.data);
                            quote_load_RatesByPrice(response.data);
                        } else {
                            //console.error( response.e);
                            displayModal("health", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                            jQuery('#quote .get-rates .loadingIcon').hide();
                            jQuery('#quote .form .loading-lock').hide();
                            jQuery('#quote .get-rates .quote-button').removeAttr("disabled");
                        }
                    },
                    error: function (response) {
                        //console.error( response.e);
                        displayModal("health", lang["quote.modal.error"], lang["WS.error"], lang["quote.modal.close"]);
                        jQuery('#quote .get-rates .loadingIcon').hide();
                        jQuery('#quote .form .loading-lock').hide();
                        jQuery('#quote .get-rates .quote-button').removeAttr("disabled");
                    }
                });
            }

        });
    }

    // QUOTE - Process rates and display the table
    function quote_load_RatesByPrice(data){

        window.PMrates = data;
        product = jQuery('#quote .quote-product:checked').next().html().trim().toUpperCase();
        productVariation = jQuery("#quote input[name='quote-product-modality']:checked").next().html().trim().toUpperCase();
        console.log("Rates by price");
        console.log(data);
        var startIcon = "<i class='fas fa-info-circle'"; //comment-alt'";
        var endIcon = "></i>"
        var i;
        var j;
        var k;

        //Create headers using column names
        var headers = [];
        headers.push(lang["quote.text.exemption"]);
        Object.keys(data.messages).every(function(i) {
            Object.keys(data.messages[i]).forEach(function(j) {
                headers.push(j);
            });
            return false;
        });

        tableDescription = "<p>" + data.name + "</p>" + "<p>" + data.description + "</p>";
        tableFooter = "<p>" + data.optional + "</p>";
        head = "<thead>";
        head += "<tr>";
        head += "<th colspan='" + headers.length + "'>" + product + " - " + productVariation + "</th>";
        head += "</tr>";
        head += "<tr>";

        Object.keys(headers).forEach(function (j) {
            head += "<th scope='col'>" + headers[j] + "</th>";
        });


        head += "</tr>";
        head += "</thead>";

        rows = "<tbody>";

        var numFila = 0;

        // Render rows
        Object.keys(data.messages).forEach(function(i) {

            rows += "<tr class='PM-row row-" + numFila + "'><td>" + i + "</td>";
            numFila++;
            // Render columns
            Object.keys(data.messages[i]).forEach(function(j) {

                if (isNaN(data.messages[i][j])){
                    rows += "<td>" + startIcon  + "title='" + data.messages[i][j] + "'" + endIcon;
                } else {
                    let rawPrice = data.messages[i][j];
                    let splitPrice = rawPrice.split(".");

                    var adjustedDecimal;
                    if( typeof splitPrice[1] !== 'undefined' ) {
                        adjustedDecimal = splitPrice[1] + "00";
                    }else{
                        adjustedDecimal = "00";
                    }

                    let newDecimal = adjustedDecimal.slice(0,2);


                    amount = splitPrice[0] + "," + newDecimal + " &euro;";
                    rows += "<td class='product' ";

                    // Renders coverage (coberturas) data
                    for( k=0;k<data.table[i][j].coverages.length;k++ ) {
                        rows += " data-capital-" + k + "='" + data.table[i][j].coverages[k].capital + "'";
                        rows += " data-codigo-" + k + "='" + data.table[i][j].coverages[k].codigo + "'";
                        rows += " data-descripcion-" + k + "='" + data.table[i][j].coverages[k].descripcion + "'";
                        rows += " data-duracion-" + k + "='" + data.table[i][j].coverages[k].duracion + "'";
                        rows += " data-franquicia-" + k + "='" + data.table[i][j].coverages[k].franquicia + "'";
                        rows += " data-prima-neta-" + k + "='" + data.table[i][j].coverages[k].primaNeta + "'";
                    }

                    // Renders quotes (formas de pago) data
                    for( k=0;k<data.table[i][j].quotes.length;k++ ) {
                        switch(k){
                            case 0:
                                rows += " data-annual='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-annual-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-annual-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                            case 1:
                                rows += " data-biannual='" + data.table[i][j].quotes[k].primaNetaFraccionada + "' data-biannual-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-biannual-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                            case 2:
                                rows +=  " data-quarterly='" + data.table[i][j].quotes[k].primaNetaFraccionada + "' data-quarterly-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-quarterly-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                            case 3:
                                rows += " data-monthly='" + data.table[i][j].quotes[k].primaNetaFraccionada + "' data-monthly-total='" + data.table[i][j].quotes[k].primaTotalAnual + "' data-monthly-forma-pago='" + data.table[i][j].quotes[k].formaPago + "' ";
                                break;
                        }
                    }

                    //description
                    rows += " data-info='" + data.info[i][j].quotes[4].valor + "'";
                    rows += " data-product-description='" + data.messages[i][j] + "' ";

                    rows += ">" + amount + "</td>";


                }
            });

            rows += "</tr>";

        });

        rows += "</tbody>";
        table = head + rows;

        // Billing cycles
        var billingCycles = "";
        cols = Math.floor( 12 / data.billingCycles.length );
        for( i=0;i<data.billingCycles.length;i++ ) {
            switch(i) {
                case 0:
                    description = lang["quote.annual.text"];
                    dataField = lang["quote.annual.field"];
                    break;
                case 1:
                    description = lang["quote.biannual.text"];
                    dataField = lang["quote.biannual.field"];
                    break;
                case 2:
                    description = lang["quote.quarterly.text"];
                    dataField = lang["quote.quarterly.field"];
                    break;
                case 3:
                    description = lang["quote.monthly.text"];
                    dataField = lang["quote.monthly.field"];
                    break;
            }

            billingCycles += "<div class='checkboxWithLabel col-" + cols + " pb-2'>";
            billingCycles +=    "<label>";
            billingCycles +=        "<input type='radio' class='form-control' name='quote-billing-cycle' value='" + data.billingCycles[i] + "' disabled>";
            billingCycles +=        "<div>" + description + "</div>";
            billingCycles +=    "</label>";
            billingCycles +=    "<div class='data-info " + dataField + " w-100'>";
            billingCycles +=        "<div class='quote-amount txt-navy-blue'></div>";
            billingCycles +=        "<div class='total-quote-amount txt-navy-blue'></div>";
            billingCycles +=    "</div>";
            billingCycles += "</div>";
        }


        window.PMquoteTable = table;
        jQuery('#quote .rates-table-description').html(tableDescription);
        jQuery('#quote .rates-table-footer').html(tableFooter);
        jQuery('#quote .rates-table-description').fadeIn();
        jQuery('#quote .rates-table table').html(table);
        jQuery('#quote .rates-table .billing-cycle').html(billingCycles);
        jQuery('#quote .rates-table').fadeIn();
        jQuery('#quote .get-rates .loadingIcon').hide();
        jQuery('#quote .form .loading-lock').hide();
        jQuery('#quote .get-rates .quote-button').removeAttr("disabled");

    }


    // QUOTE - Send mail button
    jQuery('#quote .rates-table .action-minibutton.send-email').click(function(e){
        e.preventDefault(); // prevent native submit

        var form = "";
        form += "<form class='modal-send-email'>";
        form += "<label class='mt-4'>" + lang["modal.input.email"] + "</label>";
        form += "<input class='my-2' type='email' required>";
        form += "<button type='submit' class='my-2 bg-lime-yellow text-white mx-auto d-block rounded border-0 px-3 py-2' disabled>" + lang['modal.send'] + "</button>";
        form += "<div class='loader-wrapper w-100 pt-5 text-center' style='display:none;'>";
        form += "<i class='fas fa-circle-notch fa-spin fa-2x txt-navy-blue'></i>";
        form += "</div>";
        form += "<p class='result text-center txt-navy-blue mt-4'></p>";
        form += "</form>";

        displayModal("send-email", lang["modal.title.sendEmail"], form, lang['quote.modal.close']);

    });


    // MODAL - SEND EMAIL validation
    jQuery("#PMmodal").on('input change click keyup', '.modal-send-email input', function(e){

        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if ( regex.test( jQuery(this).val() ) ) {
            jQuery('.modal-send-email button').removeAttr("disabled");
        } else {
            jQuery('.modal-send-email button').attr("disabled", "disabled");
        }

    });




    // QUOTE - Print button
    jQuery('#quote .rates-table .action-minibutton.print').click(function(e){

        window.print();
    });

    // QUOTE - Selects row from rates table and enable billing cycle
    jQuery("#quote .rates-table table").on('click', "td.product", function (e) {

        // Updates selected element classes

        jQuery('#quote .rates-table table td').removeClass("selected");
        jQuery('#quote .rates-table table th').removeClass("selected");

        jQuery(this).parent().find("td:first-child").addClass("selected");
        jQuery(this).addClass("selected");
        selectedColumnIndex = jQuery(this).index() + 1;
        jQuery('#quote .rates-table table th:nth-child(' + selectedColumnIndex +')' ).addClass("selected");
        window.PMselectedFinalProduct = jQuery('#quote .rates-table table th:nth-child(' + selectedColumnIndex +')' ).html();

        // Enables action buttons
        resetRatesTableActionsBilling();
        jQuery('#quote .rates-table .billing-cycle input').removeAttr("disabled");


        // Loads selected payment methods
        if( jQuery(this).data("monthly") ){
            jQuery('.billing-cycle .data-info.monthly .quote-amount').html(jQuery(this).data("monthly") + " &euro; /mensual");
            jQuery('.billing-cycle .data-info.monthly .total-quote-amount').html("(" + jQuery(this).data("monthly-total") + " &euro;/año)");
        }else{
            jQuery('.billing-cycle .data-info.monthly').parent().hide();
        }
        if( jQuery(this).data("quarterly") ){
            jQuery('.billing-cycle .data-info.quarterly .quote-amount').html(jQuery(this).data("quarterly") + " &euro; /trimestral");
            jQuery('.billing-cycle .data-info.quarterly .total-quote-amount').html("(" + jQuery(this).data("quarterly-total") + " &euro;/año)");
        }else{
            jQuery('.billing-cycle .data-info.quarterly').parent().hide();
        }
        if( jQuery(this).data("biannual") ){
            jQuery('.billing-cycle .data-info.biannual .quote-amount').html(jQuery(this).data("biannual") + " &euro; /semestral");
            jQuery('.billing-cycle .data-info.biannual .total-quote-amount').html("(" + jQuery(this).data("biannual-total") + " &euro;/año)");
        }else{
            jQuery('.billing-cycle .data-info.biannual').parent().hide();
        }
        if( jQuery(this).data("annual") ){
            jQuery('.billing-cycle .data-info.annual .quote-amount').html(jQuery(this).data("annual") + " &euro;");
        }else{
            jQuery('.billing-cycle .data-info.annual').parent().hide();
        }

        var coverages = [];
        // Loads coverage (cobertura) info
        var j = 0;
        for( i=0;i<10;i++ ) {

            // Stores exemption (franquicia)
            if( i ===0 ){
                window.PMselectedFinalProductExemption = jQuery(this).data("franquicia-" + i);
            }

            // Stores capital from first three elements
            if( j < 3){

                switch( j ){
                    case 0:
                        window.PMenfCob = "CAPITAL_" + jQuery(this).data("codigo-" + i);
                        window.PMenfSub = jQuery(this).data("capital-" + i);
                        break;

                    case 1:
                        if( typeof jQuery(this).data("capital-" + i) !== 'undefined' ){
                            window.PMaccCob = "CAPITAL_" + jQuery(this).data("codigo-" + i);
                            window.PMaccSub = jQuery(this).data("capital-" + i);
                        }else{
                            window.PMaccCob = null;
                            window.PMaccSub = null;
                        }
                        break;

                    case 2:
                        if( typeof jQuery(this).data("capital-" + i) !== 'undefined' ){
                            window.PMhospCob = "CAPITAL_" + jQuery(this).data("codigo-" + i);
                            window.PMhospSub = jQuery(this).data("capital-" + i);
                        }else{
                            window.PMhospCob = null;
                            window.PMhospSub = null;
                        }
                        break;
                }
                j++
            }


            // Stores all coverages (coberturas)
            if( typeof jQuery(this).data("capital-" + i) !== 'undefined' ){
                coverages[i] = {
                    capital : jQuery(this).data("capital-" + i),
                    codigo : jQuery(this).data("codigo-" + i),
                    descripcion : jQuery(this).data("descripcion-" + i),
                    duracion : jQuery(this).data("duracion-" + i),
                    franquicia : jQuery(this).data("franquicia-" + i),
                    primaNeta : jQuery(this).data("prima-neta-" + i)
                }
            }
        }

        window.PMselectedFinalProductCoverages = coverages;

        selectionDescription = "<p>" + jQuery(this).data("info") + "<p>";
        jQuery('#quote .rates-table-selection-description').html(selectionDescription);
        jQuery('#quote .rates-table-selection-description').fadeIn();

    });
    function getBudget() {
        var url = "/get-data";
        var ws = "getBudget";

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                ws: ws,
                productor: window.PMgetRatesData.productor,
                option: window.PMgetRatesData.option,
                productId: window.PMgetRatesData.productId,
                profession: window.PMgetRatesData.profession,
                birthdate: window.PMgetRatesData.birthdate,
                gender: window.PMgetRatesData.gender,
                height: window.PMgetRatesData.height,
                weight: window.PMgetRatesData.weight,
                commercialKey : window.PMgetRatesData.commercialKey,
                jobType : window.PMgetRatesData.jobType,
                duration: window.PMgetRatesData.duration,
                discount : window.PMgetRatesData.discount,
                date : window.PMgetRatesData.date,
                discountYears : window.PMgetRatesData.discountYears,
                discountSobreprima : window.PMgetRatesData.discountSobreprima,
                discountCommisionMed : window.PMgetRatesData.discountCommisionMed,
                discountCommisionDel : window.PMgetRatesData.discountCommisionDel,
                discountRecargoFinanciacion : window.PMgetRatesData.discountRecargoFinanciacion,
                discountCobro : window.PMgetRatesData.discountCobro,
                coverages : window.PMselectedFinalProductCoverages
            },
            success: function (response) {
                if (response['success'] == true) {
                    window.PMbudgetNumber = response.data.budgetNumber;
                    console.log(PMbudgetNumber);
                } else {
                    console.error( response.e);
                    displayModal("health", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                    jQuery('#quote .get-rates .loadingIcon').hide();
                    jQuery('#quote .form .loading-lock').hide();
                    jQuery('#quote .get-rates .quote-button').removeAttr("disabled");
                }
            },
            error: function (response) {
                console.error( response.e);
                displayModal("health", lang["quote.modal.error"], lang["WS.error"], lang["quote.modal.close"]);
                jQuery('#quote .get-rates .loadingIcon').hide();
                jQuery('#quote .form .loading-lock').hide();
                jQuery('#quote .get-rates .quote-button').removeAttr("disabled");
            }
        });
    }
    function getBudgetDocument() {
        var url = "/get-data";
        var ws = "getBudgetDocument";

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                ws: ws,
                productor: window.PMgetRatesData.productor,
                budgetNumber : window.PMbudgetNumber
            },

        });
    }
    // QUOTE - Stores selected billing cycle and displays Next step button
    jQuery("#quote .billing-cycle").on('click', "input", function (e) {
        getBudget();
        getBudgetDocument();


        jQuery('#quote .table-actions .action-minibutton').removeAttr("disabled").addClass("active");
        window.PMbillingCycle = jQuery(this).val(); // forma de pago

        window.PMselectedProduct = jQuery("#quote input[name='quote-product']:checked").next().html().trim().toUpperCase();
        window.PMselectedProductVariation = jQuery("#quote input[name='quote-product-variation']:checked").next().html().trim().toUpperCase();
        var billing = jQuery(this).parent().next().find(".quote-amount").html();
        var billingTotal = jQuery(this).parent().next().find(".total-quote-amount").html();
        var exemption = jQuery('.rates-table .PM-row td.selected:not(".product")').html();

        if( billingTotal ){
            // removes parenthesis from string
            billingTotal = billingTotal.substring( 1, billingTotal.indexOf(")") );
            jQuery('#quote #selected-product-info .billing .dynamic-content').html( billing );
            jQuery('#quote #selected-product-info .billing-total .dynamic-content').html( billingTotal );
            jQuery('#quote #selected-product-info .billing').show();
        }else{
            // if there is no annual info is annual payment and will get the only price available
            jQuery('#quote #selected-product-info .billing').hide();
            jQuery('#quote #selected-product-info .billing-total .dynamic-content').html( billing + lang['quote.productInfo.perYear'] );
        }

        jQuery('#quote #selected-product-info .product-name .dynamic-content').html( PMselectedProduct );
        jQuery('#quote #selected-product-info .product-variation .dynamic-content').html( PMselectedProductVariation );
        jQuery('#quote #selected-product-info .product-coverage .dynamic-content').html( PMselectedFinalProduct );
        jQuery('#quote #selected-product-info .product-exemption .dynamic-content').html( exemption );

        // Copy the card in the widget to show on next steps.
        jQuery('#quote #product-info-widget').html( jQuery('#quote #selected-product-info .product-info-card').html() );

        jQuery('#quote #selected-product-info').fadeIn();
        jQuery('html, body').animate({
            scrollTop: jQuery("#selected-product-info").offset().top
        }, 1000);

    });

    // MODAL - SEND EMAIL send
    jQuery("#PMmodal").on('click', '.modal-send-email button', function(e){
        e.preventDefault(); // prevent native submit
        //jQuery(this).attr("disabled");

        jQuery('.modal-send-email .loader-wrapper').show();
        jQuery('.modal-send-email .result').html();


        // NEED TO GET INFO of the file FROM WS
        var fileId = ""; // 323785;
        var filename = ""; // "6600031_201911_2.pdf";
        var tipoFichero = "" // 2;


        var html = jQuery('#quote #selected-product-info .print3').html();
        html += jQuery('#quote #step-1 .print2').html();
        html += jQuery('#quote #step-1 .print1').html();
        var email = jQuery(".modal-send-email input[type=email]").val();
        var product = jQuery('#quote #selected-product-info .print3 .product-name .dynamic-content').html();
        var body = "Le remitimos información del coste de su Seguro de " + jQuery('#quote #selected-product-info .print3 .product-name .dynamic-content').html() +", solicitada a su mediador de seguros, en documento adjunto. <br>Un cordial saludo. <br> La Previsión Mallorquina de Seguros, S.A.";

        // Send email with attachment from /downloads
        jQuery.ajax({
            type: "POST",
            url: "/send-mail-html",
            data: {
                email : email,
                product : product,
                body : body,
                html : html

            },
            success: function(response) {
                jQuery('.modal-send-email .loader-wrapper').hide();
                jQuery('.modal-send-email .result').html(response['body']);
            },
            error: function(response){
                //console.error(response['e']);
            }
        });

    });

    // QUOTES - Stores step 1 data into JS
    function storeStep1Data(){
        window.PMquoteStep1 = {
            productor : window.PMgetRatesData.productor,
            option : window.PMgetRatesData.option,
            productId : window.PMgetRatesData.productId,
            startingDate : window.PMgetRatesData.startingDate,
            profession : window.PMgetRatesData.profession,
            birthdate : window.PMgetRatesData.birthdate,
            gender : window.PMgetRatesData.gender,
            height : window.PMgetRatesData.height,
            weight : window.PMgetRatesData.weight,
            commercialKey : window.PMgetRatesData.commercialKey,
            jobType : window.PMgetRatesData.jobType,
            duration : window.PMgetRatesData.duration,
            enfCob : window.PMenfCob,
            enfSub : window.PMenfSub,
            accCob : window.PMaccCob,
            accSub : window.PMaccSub,
            hospCob : window.PMhospCob,
            hospSub : window.PMhospSub,

            exemption : window.PMselectedFinalProductExemption, // franquicia
            billingCycle : window.PMbillingCycle, // forma pago
            productVariation : window.PMselectedFinalProduct,
            coverages : window.PMselectedFinalProductCoverages, // coberturas
            discount: jQuery('#quote .quote-discount-code').val()

        }
        console.log(PMquoteStep1);
    }

    // QUOTES - step 1 next button
    jQuery('#quote #selected-product-info .quote-step.next').click(function(e){
        storeStep1Data();

        jQuery('#step-2 .quote-birthdate-show').val( jQuery('#step-1 .quote-birthdate').val() );
        jQuery('#step-2 .quote-job-show').val( jQuery('#step-1 .quote-job-picker').val() );
        jQuery('#step-2 .quote-gender-show').val( jQuery('#step-1 .quote-gender option:selected').text() );

        jQuery('#step-1').hide();
        jQuery('#step-2').fadeIn();
    });

    // ------------------- STEP 2 ----------------------

    // QUOTES - toggles check attribute on clicked toggle button
    jQuery("#quote .btn-group-toggle .btn").click( function (e) {
        jQuery(this).parent().find(".btn input").removeAttr("checked");
        jQuery(this).find("input").attr("checked","checked");
    });

    // QUOTES - Phone max numbers
    jQuery("#quote .quote-phone").on("keyup", function(e){
        if (jQuery(this).val().length > 9) {
            jQuery(this).val( jQuery(this).val().substring(0, 9) )
        }
    });

    // QUOTES - postal code max numbers
    jQuery("#quote .quote-postal-code, " +
        "#quote .quote-company-postal-code," +
        "#quote .quote-legal-entity-postal-code").on("keyup", function(e){

        if (jQuery(this).val().length > 5) {
            jQuery(this).val( jQuery(this).val().substring(0, jQuery(this).val().length - 1 ) )
        }

        if( jQuery(this).hasClass("quote-postal-code") ) {
            currentClass = "quote-postal-code";
        }
        if( jQuery(this).hasClass("quote-company-postal-code") ) {
            currentClass = "quote-company-postal-code";
        }
        if( jQuery(this).hasClass("quote-legal-entity-postal-code") ) {
            currentClass = "quote-legal-entity-postal-code";
        }
        resetQuoteCityProvince(currentClass);

        if (jQuery(this).val().length == 5) {

            // Then retrieves extra info of selected product variation in the background
            var url = "/get-data";
            var ws = "getCityProvince";
            var postalCode = jQuery(this).val();

            jQuery.ajax({
                type: "POST",
                url: url,
                data: {
                    ws: ws,
                    postalCode: postalCode
                },
                success: function (response) {
                    if (response['success'] == true) {
                        switch(currentClass){

                            case "quote-postal-code":
                                quote_load_cityProvince(response.data, "quote-postal-code");
                                break;

                            case "quote-company-postal-code":
                                quote_load_cityProvince(response.data, "quote-company-postal-code");
                                break;

                            case "quote-legal-entity-postal-code":
                                quote_load_cityProvince(response.data, "quote-legal-entity-postal-code");
                                break;
                        }
                    } else {
                        //console.error( response.e);
                    }
                },
                error: function (response) {
                    //console.error( lang["WS.error"] );
                }
            });

        }
    });

    // QUOTES - loads city and province when postal code is ok
    function quote_load_cityProvince(data, element){

        citiesSelect = "";
        provincesSelect = "";
        citiesArray = data.cities;
        provincesArray = data.provinces;

        Object.keys(citiesArray).forEach(function(key) {
            citiesSelect += "<option value='" + key + "'>" + citiesArray[key] + "</option>";
        });

        Object.keys(provincesArray).forEach(function(key) {
            provincesSelect += "<option value='" + key + "'>" + provincesArray[key] + "</option>";
        });

        switch(element){

            case "quote-postal-code":
                jQuery('#quote .quote-city').html(citiesSelect);
                jQuery('#quote .quote-city').removeAttr("disabled");
                jQuery('#quote .quote-province').html(provincesSelect);
                jQuery('#quote .quote-province').removeAttr("disabled");
                jQuery("#quote .quote-postal-code").change();
                break;

            case "quote-company-postal-code":
                jQuery('#quote .quote-company-city').html(citiesSelect);
                jQuery('#quote .quote-company-city').removeAttr("disabled");
                jQuery('#quote .quote-company-province').html(provincesSelect);
                jQuery('#quote .quote-company-province').removeAttr("disabled");
                jQuery("#quote .quote-company-postal-code").change();
                break;

            case "quote-legal-entity-postal-code":
                jQuery('#quote .quote-legal-entity-city').html(citiesSelect);
                jQuery('#quote .quote-legal-entity-city').removeAttr("disabled");
                jQuery('#quote .quote-legal-entity-province').html(provincesSelect);
                jQuery('#quote .quote-legal-entity-province').removeAttr("disabled");
                jQuery("#quote .quote-legal-entity-postal-code").change();
                break;
        }


    }

    // QUOTES - Validates extra info fields
    jQuery( '#quote #personal-info input, ' +
        '#quote #personal-info select' )
        .on("input change click keyup", function() {

            // validates input texts
            if (jQuery(this).hasClass("quote-first-name") ||
                jQuery(this).hasClass("quote-last-name") ||
                jQuery(this).hasClass("quote-address") ||
                jQuery(this).hasClass("quote-company-name") ||
                jQuery(this).hasClass("quote-company-address") ||
                jQuery(this).hasClass("quote-legal-entity-name") ||
                jQuery(this).hasClass("quote-legal-entity-address") ||
                jQuery(this).hasClass("quote-another-insurance-name")  ){

                if (jQuery(this).val().length > 0) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            if ( jQuery(this).hasClass("quote-another-insurance-ends") ){
                var valid;

                if( jQuery(this).val().length == 10) {
                    var splitDate = jQuery(this).val().split("/");
                    if (splitDate[0] >= 1 && splitDate[0] <= 31) {
                        if (splitDate[1] >= 1 && splitDate[1] <= 12) {
                            if (splitDate[2] >= 1920 && splitDate[2] <= 2025) {
                                valid = true;
                            } else {
                                valid = false;
                            }
                        } else {
                            valid = false;
                        }
                    } else {
                        valid = false;
                    }
                }else {
                    valid = false;
                }

                if (valid) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            if ( jQuery(this).hasClass("quote-another-insurance-price") ){
                if (jQuery(this).val().length > 1) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            if (jQuery(this).hasClass("quote-legal-entity-id") ){
                if ( isValidCif( jQuery(this).val() ) ) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            if (jQuery(this).hasClass("quote-personal-id") ){
                if (isValidDoc( jQuery(this).val() ) ) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                    jQuery(this).next().hide();
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                    jQuery(this).next().show();
                }
            }

            if (jQuery(this).hasClass("quote-city") ||
                jQuery(this).hasClass("quote-province") ||
                jQuery(this).hasClass("quote-company-city") ||
                jQuery(this).hasClass("quote-company-province")) {
                if (jQuery(this).children("option:selected").val() > 0) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            if (jQuery(this).hasClass("quote-phone") ) {
                if (jQuery(this).val().length == 9) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                    jQuery(this).next().hide();
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                    jQuery(this).next().show();
                }
            }

            if ( jQuery(this).hasClass("quote-postal-code") ) {
                if (jQuery(this).val().length == 5) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }

                if (jQuery("#quote .quote-city").children("option:selected").val() > 0) {
                    jQuery("#quote .quote-city").removeClass("invalid");
                    jQuery("#quote .quote-city").addClass("valid");
                } else {
                    jQuery("#quote .quote-city").addClass("invalid");
                    jQuery("#quote .quote-city").removeClass("valid");
                }

                if (jQuery("#quote .quote-province").children("option:selected").val() > 0) {
                    jQuery("#quote .quote-province").removeClass("invalid");
                    jQuery("#quote .quote-province").addClass("valid");
                } else {
                    jQuery("#quote .quote-province").addClass("invalid");
                    jQuery("#quote .quote-province").removeClass("valid");
                }
            }

            if ( jQuery(this).hasClass("quote-company-postal-code") ) {
                if (jQuery(this).val().length == 5) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }

                if (jQuery("#quote .quote-company-city").children("option:selected").val() > 0) {
                    jQuery("#quote .quote-company-city").removeClass("invalid");
                    jQuery("#quote .quote-company-city").addClass("valid");
                } else {
                    jQuery("#quote .quote-company-city").addClass("invalid");
                    jQuery("#quote .quote-company-city").removeClass("valid");
                }

                if (jQuery("#quote .quote-company-province").children("option:selected").val() > 0) {
                    jQuery("#quote .quote-company-province").removeClass("invalid");
                    jQuery("#quote .quote-company-province").addClass("valid");
                } else {
                    jQuery("#quote .quote-company-province").addClass("invalid");
                    jQuery("#quote .quote-company-province").removeClass("valid");
                }
            }

            if ( jQuery(this).hasClass("quote-legal-entity-postal-code") ) {
                if (jQuery(this).val().length == 5) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }

                if (jQuery("#quote .quote-legal-entity-city").children("option:selected").val() > 0) {
                    jQuery("#quote .quote-legal-entity-city").removeClass("invalid");
                    jQuery("#quote .quote-legal-entity-city").addClass("valid");
                } else {
                    jQuery("#quote .quote-legal-entity-city").addClass("invalid");
                    jQuery("#quote .quote-legal-entity-city").removeClass("valid");
                }

                if (jQuery("#quote .quote-legal-entity-province").children("option:selected").val() > 0) {
                    jQuery("#quote .quote-legal-entity-province").removeClass("invalid");
                    jQuery("#quote .quote-legal-entity-province").addClass("valid");
                } else {
                    jQuery("#quote .quote-legal-entity-province").addClass("invalid");
                    jQuery("#quote .quote-legal-entity-province").removeClass("valid");
                }
            }

            if (jQuery(this).hasClass("quote-email") ||
                jQuery(this).hasClass("quote-legal-entity-email")) {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if ( regex.test( jQuery(this).val() ) ) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                    jQuery(this).next().hide();
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                    jQuery(this).next().show();
                }
            }
            //console.log( jQuery(this).attr("class") );
            step2EnableNextButton();
        });

    // QUOTE - postal code  and price (only numbers)
    jQuery( '#quote #personal-info').on('keypress', ".quote-postal-code, .quote-company-postal-code, .quote-legal-entity-postal-code, .quote-another-insurance-price", function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }

        step2EnableNextButton();
    });

    // QUOTES - checks if NIF/NIE is valid
    function isValidDoc(value) {
        var validChars = 'TRWAGMYFPDXBNJZSQVHLCKET';
        var nifRexp = /^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKET]{1}$/i;
        var nieRexp = /^[XYZ]{1}[0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKET]{1}$/i;
        var str = value.toString().toUpperCase();

        if (!nifRexp.test(str) && !nieRexp.test(str)) return false;

        var nie = str
            .replace(/^[X]/, '0')
            .replace(/^[Y]/, '1')
            .replace(/^[Z]/, '2');

        var letter = str.substr(-1);
        var charIndex = parseInt(nie.substr(0, 8)) % 23;

        if (validChars.charAt(charIndex) === letter) return true;

        return false;
    }

    // QUOTES - checks if CIF is valid
    function isValidCif(cif) {
        if (!cif || cif.length !== 9) {
            return false;
        }

        var letters = ['J', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        var digits = cif.substr(1, cif.length - 2);
        var letter = cif.substr(0, 1);
        var control = cif.substr(cif.length - 1);
        var sum = 0;
        var i;
        var digit;

        if (!letter.match(/[A-Z]/)) {
            return false;
        }

        for (i = 0; i < digits.length; ++i) {
            digit = parseInt(digits[i]);

            if (isNaN(digit)) {
                return false;
            }

            if (i % 2 === 0) {
                digit *= 2;
                if (digit > 9) {
                    digit = parseInt(digit / 10) + (digit % 10);
                }

                sum += digit;
            } else {
                sum += digit;
            }
        }

        sum %= 10;
        if (sum !== 0) {
            digit = 10 - sum;
        } else {
            digit = sum;
        }

        if (letter.match(/[ABEH]/)) {
            return String(digit) === control;
        }
        if (letter.match(/[NPQRSW]/)) {
            return letters[digit] === control;
        }

        return String(digit) === control || letters[digit] === control;
    }

    // QUOTES - special check for fields
    jQuery( '#quote #personal-info .quote-address-type,' +
        '#quote #personal-info .quote-job-location,' +
        '#quote #personal-info .quote-company-address-pick,' +
        '#quote #personal-info .quote-company-address-type,' +
        '#quote #personal-info .quote-legal-entity-address-type').change( function() {

        if (jQuery(this).children("option:selected").val() != null) {
            jQuery(this).removeClass("invalid");
            jQuery(this).addClass("valid");
        } else {
            jQuery(this).addClass("invalid");
            jQuery(this).removeClass("valid");
        }

    });

    // QUOTES - toggles another insurance extra info
    jQuery("#quote .quote-another-insurance.yes").click( function(e){
        jQuery("#quote .quote-another-insurance-extra-info").fadeIn();
        step2EnableNextButton();
    });
    jQuery("#quote .quote-another-insurance.no").click( function(e){
        jQuery("#quote .quote-another-insurance-extra-info").hide();
        step2EnableNextButton();
    });

    // QUOTES - toggles legal entity extra info
    jQuery("#quote .quote-legal-entity-type.legal-entity").click( function(e){
        jQuery("#quote .legalEntityInfo").fadeIn();
        step2EnableNextButton();
    });
    jQuery("#quote .quote-legal-entity-type.natural-person").click( function(e){
        jQuery("#quote .legalEntityInfo").hide();
        step2EnableNextButton();
    });

    // QUOTES - show hidden fields when OTHER is selected
    jQuery("#step-2 .quote-company-address-pick").change(function() {
        if ( jQuery(this).val() == companyAddressTypeOthers ) {
            jQuery(".companyAddressWrapper").removeClass("d-none").addClass("d-flex");
            jQuery("#compAddressType, #companyAddress, #companyState, #companyPostalCode, #companyCity").attr("required", true);
        } else {
            jQuery(".companyAddressWrapper").removeClass("d-flex").addClass("d-none");
            jQuery("#compAddressType, #companyAddress, #companyState, #companyPostalCode, #companyCity").attr("required", false);
        }
    });

    // QUOTES - Validates extra info and enables getRate button
    function step2EnableNextButton( ) {
        allValid = true;
        jQuery( '#quote #personal-info input:visible, ' +
            '#quote #personal-info select:visible' )
            .each(function() {
                if( !jQuery(this).hasClass("valid") ){
                    allValid = false;
                    // console.log( "FAIL | " + this.className.split(' ')[2] );
                }else{
                    // console.log( "OK | " + this.className.split(' ')[2] );
                }
            });
        //console.log( "------------------------");

        if( allValid ){
            jQuery('#step-2 .quote-step.next').removeAttr("disabled");
        }else{
            jQuery('#step-2 .quote-step.next').attr("disabled", "disabled");
        }

        return allValid;
    }

    // QUOTES - Saves step 2 data into JS variable
    function storeStep2Data(){
        window.PMquoteStep2 = {
            firstName : jQuery('#quote .quote-first-name').val(),
            lastName : jQuery('#quote .quote-last-name').val(),
            personalId : jQuery('#quote .quote-personal-id').val(),
            email : jQuery('#quote .quote-email').val(),
            phone : jQuery('#quote .quote-phone').val(),
            documentationLanguage : jQuery('#quote .quote-documentation-language').val(),
            addressType : jQuery('#quote .quote-address-type').val(),
            address : jQuery('#quote .quote-address').val(),
            postalCode : jQuery('#quote .quote-postal-code').val(),
            city : jQuery('#quote .quote-city').val(),
            province : jQuery('#quote .quote-province').val(),
            companyName : jQuery('#quote .quote-company-name').val(),
            jobLocation : jQuery('#quote .quote-job-location').val(),
            companyAddressPick : jQuery('#quote .quote-company-address-pick').val(),

            companyAddressType : jQuery('#quote .quote-company-address-type').val(),
            companyAddress : jQuery('#quote .quote-company-address').val(),
            companyPostalCode : jQuery('#quote .quote-company-postal-code').val(),
            companyCity : jQuery('#quote .quote-company-city').val(),
            companyProvince : jQuery('#quote .quote-company-province').val(),

            anotherInsurance : jQuery('#quote .quote-another-insurance.active > input').val(),
            anotherInsuranceName : jQuery('#quote .quote-another-insurance-name > input').val(),
            anotherInsuranceAmount : jQuery('#quote .quote-another-insurance-price > input').val(),
            anotherInsuranceEnds : jQuery('#quote .quote-another-insurance-ends > input').val(),

            legalEntityType : jQuery('#quote .quote-legal-entity-type.active').data("person-type"),
            legalEntityName : jQuery('#quote .quote-legal-entity-name').val(),
            legalEntityId : jQuery('#quote .quote-legal-entity-id').val(),
            legalEntityEmail : jQuery('#quote .quote-legal-entity-email').val(),
            legalEntityAddressType : jQuery('#quote .quote-legal-entity-address-type').val(),
            legalEntityAddress : jQuery('#quote .quote-legal-entity-address').val(),
            legalEntityPostalCode : jQuery('#quote .quote-legal-entity-postal-code').val(),
            legalEntityCity : jQuery('#quote .quote-legal-entity-city').val(),
            legalEntityProvince : jQuery('#quote .quote-legal-entity-province').val(),

            additionalBeneficiary : jQuery('#quote .quote-beneficiary').val(),
            additionalIncreasedValue : jQuery('#quote .quote-increased-value').val()
        }
    }

    // QUOTES - step 2 previous button
    jQuery('#quote #step-2 .step-buttons .quote-step.previous').click(function(e){
        jQuery('#step-2').hide();
        jQuery('#step-1').fadeIn();
    });

    // QUOTES - step 2 next button
    jQuery('#quote #step-2 .step-buttons .quote-step.next').click(function(e){
        if( step2EnableNextButton() ){
            storeStep2Data();

            jQuery('#step-2').hide();

            jQuery('#quote #step-3 .loader-wrapper').show();

            // Retrieves health form data from WS
            var url = "/get-data";
            var ws = "getHealthForm";
            var productor = window.PMquoteStep1.productor;
            //var product = window.PMquoteStep1.productId;
            var product = jQuery("#quote input[name='quote-product-modality']:checked").val();
            var commercialKey = window.PMquoteStep1.commercialKey;


            jQuery.ajax({
                type: "POST",
                url: url,
                data: {
                    ws : ws,
                    productor : productor,
                    product : product,
                    commercialKey : commercialKey

                },
                success: function (response) {
                    if (response['success'] == true) {
                        healthFormRequired = true;
                        quote_load_healthForm(response.data);
                    } else {
                        console.error( response.e);
                    }

                    if (healthFormRequired){
                        jQuery('#step-3').fadeIn();
                    }else{
                        jQuery('#step-4').fadeIn();
                    }
                },
                error: function (response) {
                    console.error( lang["WS.error"] );
                }
            });
        }
    });

    // ------------------- STEP 3 ----------------------

    // QUOTES - Loads html code of health form
    function quote_load_healthForm(data){
        //console.log(data);
        window.PMquoteHealthFormId = data.id;
        jQuery('#quote #health-form .dynamic-content').html(data.html);
        /*if( jQuery( ".datetimepickerHealth input" ).length ){
            jQuery( ".datetimepickerHealth input" ).datepicker({ maxDate: '-1D', changeMonth: true, changeYear: true, yearRange: "-70:+0" });
            jQuery( ".datetimepickerHealth input" ).datepicker("option", jQuery.datepicker.regional[ "{{ $currentLanguage  }}" ]);
        }*/
        // jQuery('#quote #step-3 .step-buttons .quote-step.next').removeAttr("disabled");
        jQuery('#health-form label.active').removeClass("active");
        jQuery('#quote #step-3 .loader-wrapper').hide();
    }

    // QUOTE - Health form displays hidden sub questions
    jQuery("#quote #health-form").on('click', ".answer-radio-group label", function (e) {
        let input = jQuery(this).find("input");
        let id = input.data("id");
        if ( input.val() == "SI" ) {
            enableAnswer(".answer-wrapper[data-id=" + id +"]", input.attr("required"));
        } else {
            disableAnswer(".answer-wrapper[data-id=" + id +"]");
        }

    });

    // QUOTE - Health form displays hidden sub questions
    jQuery("#quote #health-form").on('change', ".single-question label", function (e) {
        let qg = jQuery(this).closest(".questions-group");
        let gId = qg.attr("id");

        let answers = qg.find("input[type=radio]:checked");
        let questionsNumber = qg.find(".answer-radio-group").length;
        if ( answers.length == questionsNumber ) {
            var allNo = true;
            for (var i = answers.length - 1; i >= 0; i--) {
                allNo = allNo && jQuery(answers[i]).val() == "NO";
            }

            if ( allNo ) {
                qg.collapse('hide')
                jQuery("[data-ref-id=" + gId + "] input[value=NO]").prop("checked", true);
                jQuery("[data-ref-id=" + gId + "] input[value=NO]").closest("label").addClass("active");
                jQuery("[data-ref-id=" + gId + "] input[value=SI]").prop("checked", false);
                jQuery("[data-ref-id=" + gId + "] input[value=SI]").closest("label").removeClass("active");
            }
        }
    });

    // QUOTE - Health form displays hidden sub questions
    jQuery("#quote #health-form").on('click', "[data-ref-id] label", function (e) {
        let gid = jQuery(this).closest("[data-ref-id]").data('ref-id');
        let input = jQuery(this).find("input");
        let id = input.data("id");
        if ( input.val() == "SI" ) {
            enableAnswer("#" + gid + " :not(.answer-radio-group) + .answer-wrapper", input.attr("required"));
            jQuery("#" + gid).collapse('show');
            setAnswers("#" + gid + " .answer-radio-group", true);
        } else {
            jQuery("#" + gid).collapse('hide');
            disableAnswer("#" + gid + " :not(.answer-radio-group) + .answer-wrapper");
            setAnswers("#" + gid + " .answer-radio-group", false);
        }
    });

    // QUOTE - Health form helper
    function enableAnswer(selector, required) {
        jQuery(selector).addClass("d-block d-lg-flex").removeClass("d-none");
        if (required) {
            jQuery(selector).find("input, select, textarea").attr("required", true);
        }
    }

    // QUOTE - Health form helper
    function disableAnswer(selector) {
        jQuery(selector).addClass("d-none").removeClass("d-block d-lg-flex");
        jQuery(selector).find("input, select, textarea").attr("required", false);
    }

    // QUOTE - Health form helper
    function setAnswers(selector, value) {

        if (value) {
            jQuery(selector + " label").removeClass("active");
            jQuery(selector + " input").prop("checked", false);
        } else {
            jQuery(selector + " [value=NO]").click();
        }
    }

    // QUOTE - Detects changes on visible extra fields
    jQuery("#quote #health-form")
        .on('input change click keyup',
            "label, input, textarea, select", function (e) {

                if (jQuery(this).is("select")) {
                    if (jQuery(this).val() != null ) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                if (jQuery(this).is("input")) {
                    if (jQuery(this).val().length > 1) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                if (jQuery(this).is("textarea")) {
                    if (jQuery(this).val().length > 1) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                if (jQuery(this).is("label")) {
                    jQuery(this).parent().find('input').removeClass("invalid");
                    jQuery(this).parent().find('input').addClass("valid");
                }

                if ( jQuery(this).hasClass("date-input") ){
                    var valid;

                    if( jQuery(this).val().length == 10) {
                        var splitDate = jQuery(this).val().split("/");
                        if (splitDate[0] >= 1 && splitDate[0] <= 31) {
                            if (splitDate[1] >= 1 && splitDate[1] <= 12) {
                                if (splitDate[2] >= 1920 && splitDate[2] <= 2021) {
                                    valid = true;
                                } else {
                                    valid = false;
                                }
                            } else {
                                valid = false;
                            }
                        } else {
                            valid = false;
                        }
                    }else {
                        valid = false;
                    }

                    if (valid) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                step3EnableNextButton();
            });

    // QUOTE - Validates all visible fields (extra fields when selecting yes)
    function step3EnableNextButton(){
        allValid = true;
        jQuery( "#health-form input:visible, " +
            "#health-form input[type=radio], " +
            "#health-form textarea:visible, " +
            "#health-form select:visible" )
            .each(function() {
                if( !jQuery(this).hasClass("valid") ){
                    allValid = false;
                    //console.log( "FAIL | " + this.className.split(' ')[2] );
                }else{
                    //console.log( "OK | " + this.className.split(' ')[2] );
                }
            });

        if( allValid ){
            jQuery('#step-3 .quote-step.next').removeAttr("disabled");
        }else{
            jQuery('#step-3 .quote-step.next').attr("disabled", "disabled");
        }

        return allValid;
    }

    // QUOTE - step 3 previous button
    jQuery('#quote #step-3 .step-buttons .quote-step.previous').click(function(e){
        jQuery('#step-3').hide();
        resetHealthForm();
        jQuery('#step-2').fadeIn();
    });

    // QUOTE - step 3 next button
    jQuery('#quote #step-3 .step-buttons .quote-step.next').click(function(e){

        if( step3EnableNextButton() ){
            jQuery('#quote #step-3 .quote-step .loadingIcon').fadeIn();
            jQuery('#quote #step-3 #health-form .loading-lock').fadeIn();

            // Gets form data
            var formData = {};
            jQuery( "#health-form input[type=number], " +
                "#health-form input[type=text], " +
                "#health-form textarea, " +
                "#health-form select").each(function() {
                if ( jQuery(this).val() != null ) {
                    formData[jQuery(this).attr("name")] = jQuery(this).val();
                }
            });
            jQuery("#health-form input[type=radio]:checked").each(function() {
                formData[jQuery(this).attr("name")] = jQuery(this).val();
            });

            window.PMhealthFormData = formData;

            //console.log(formData);
            // Retrieves health form data from WS
            var url = "/get-data";
            var ws = "validateHealthForm";
            var product = jQuery("#quote input[name='quote-product-modality']:checked").val();

            jQuery.ajax({
                type: "POST",
                url: url,
                data: {
                    ws: ws,
                    productor: window.PMquoteStep1.productor,
                    product: product, //window.PMquoteStep1.productId,
                    formId: window.PMquoteHealthFormId,
                    formData: formData

                },
                success: function (response) {
                    if (response['success'] == true) {
                        jQuery('#step-3').hide();
                        jQuery('#quote #step-3 .quote-step .loadingIcon').hide();
                        jQuery('#quote #step-3 #health-form .loading-lock').hide();
                        jQuery('#step-4').fadeIn();

                        if( response.exclusions ) {
                            var message;
                            response.exclusions.forEach(function(item) {
                                message += "<li><p class='third my-0'>" + item.value + "</p></li>";
                            });
                            displayModal("health", lang["quote.exclusions"], message, lang["text.continue"]);
                        }

                    } else {
                        /*if ( response.data.error == "I" ) {
                            jQuery("#exclusions").empty();
                            response.exclusions.forEach(function(item) {
                                jQuery("#exclusions").append("<li><p class='third my-0'>" + item.value + "</p></li>");
                            });
                            jQuery("#modalI").modal();
                        } else */
                        if ( response.data.error == "P" ) {
                            displayModal("error-p", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                        } else if ( response.data.error == "R" ) {
                            displayModal("error-r", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                        }
                        console.error( response.e);
                    }
                },
                error: function (response) {
                    console.error( lang["WS.error"] );
                }
            });

        }
    });

    // ------------------- STEP 4 ----------------------

    // QUOTE - IBAN fields
    jQuery("#quote .quote-iban").on('keypress', function (evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }else{
            if( jQuery(this).hasClass("quote-iban-country") ){
                var inputLength = event.target.value.length;
                if( inputLength <= 4 ){
                    var thisVal = 'ES';
                    thisVal += event.target.value.replace("ES","");
                    jQuery(event.target).val(thisVal);
                }
            }
        }
    });
    // QUOTE - IBAN fields
    jQuery("#quote .quote-iban").on('keyup', function (evt) {
        if (jQuery(this).val().length == 4) {
            jQuery(this).removeClass("invalid");
            jQuery(this).addClass("valid");
            jQuery(this).next().focus();
        } else {
            jQuery(this).addClass("invalid");
            jQuery(this).removeClass("valid");
        }
        step4EnableNextButton();
    });


    // QUOTE - Validates step 4 and enables next step button
    function step4EnableNextButton( ) {
        allValid = true;
        jQuery('#step-4 .quote-iban').each(function() {
            if( !jQuery(this).hasClass("valid") ){
                allValid = false;
            }
        });

        if( allValid ){


            var ibanCountry = jQuery('#quote .quote-iban-country').val();
            var ibanControl = jQuery('#quote .quote-iban-control').val();
            var ibanEntity = jQuery('#quote .quote-iban-entity').val();
            var ibanOffice = jQuery('#quote .quote-iban-office').val();
            var ibanDc = jQuery('#quote .quote-iban-dc').val();
            var ibanAccount = jQuery('#quote .quote-iban-account').val();

            var currentIBAN = ibanCountry + ibanControl + ibanEntity + ibanOffice + ibanDc + ibanAccount;

            if( IBAN.isValid( currentIBAN ) ){
                jQuery('#step-4 .step-buttons .next').removeAttr("disabled");
                jQuery('#step-4 #payment-input .error').hide();
            }else{
                jQuery('#step-4 .step-buttons .next').attr("disabled", "disabled");
                jQuery('#step-4 #payment-input .error').show();
            }

        }else{
            jQuery('#step-4 .step-buttons .next').attr("disabled", "disabled");
            jQuery('#step-4 #payment-input .error').hide();
        }

        return allValid;
    }

    // QUOTE - Stores step 4 data into JS
    function storeStep4Data(){
        window.PMquoteStep4 = {
            ibanCountry : jQuery('#quote .quote-iban-country').val(),
            ibanControl : jQuery('#quote .quote-iban-control').val(),
            ibanEntity : jQuery('#quote .quote-iban-entity').val(),
            ibanOffice : jQuery('#quote .quote-iban-office').val(),
            ibanDc : jQuery('#quote .quote-iban-dc').val(),
            ibanAccount : jQuery('#quote .quote-iban-account').val()
        }
    }

    // QUOTES - step 4 previous button
    jQuery('#quote #step-4 .step-buttons .quote-step.previous').click(function(e){

        jQuery('#step-4').hide();

        if (healthFormRequired) {
            jQuery('#step-3').fadeIn();
        }else{
            jQuery('#step-2').fadeIn();
        }

    });

    // QUOTE - step 4 next button
    jQuery('#step-4 .step-buttons .next').click(function(){

        storeStep4Data();

        // Runs validation on all fields
        jQuery( '#quote .product-extra-info input, ' +
            '#quote .product-extra-info select' )
            .change();

        if( step4EnableNextButton() ){

            jQuery('#step-4 .step-buttons .next .loadingIcon').fadeIn();
            jQuery('#quote .quote-bank-account').attr("disabled","disabled");
            jQuery('#step-4 .step-buttons .next').attr("disabled","disabled");
            jQuery('#step-4 .step-buttons .prev').attr("disabled","disabled");

            var url = "/get-data";
            var ws = "submitPolicy";

            var productor = window.PMquoteStep1.productor;
            var option = window.PMquoteStep1.option;
            //var productId = window.PMquoteStep1.productId;
            var productId = jQuery("#quote input[name='quote-product-modality']:checked").val();

            var startingDate = window.PMquoteStep1.startingDate;
            var profession = window.PMquoteStep1.profession;
            var birthdate  = window.PMquoteStep1.birthdate;
            var gender = window.PMquoteStep1.gender;
            var height = window.PMquoteStep1.height;
            var weight = window.PMquoteStep1.weight;
            var paymentMethod  = window.PMquoteStep1.billingCycle;

            var name  = window.PMquoteStep2.firstName;
            var surname  = window.PMquoteStep2.lastName;
            var docId  = window.PMquoteStep2.personalId;
            // var docType  =  Generated on PMWShandler
            var email  = window.PMquoteStep2.email;
            var phone  = window.PMquoteStep2.phone;
            var insuredLanguage  = window.PMquoteStep2.documentationLanguage;

            var streetType  = window.PMquoteStep2.addressType;
            var address  = window.PMquoteStep2.address;
            var postalCode  = window.PMquoteStep2.postalCode;
            var city  = window.PMquoteStep2.city;
            var province  = window.PMquoteStep2.province; // is not sent;

            var companyName  = window.PMquoteStep2.companyName;
            var workLocationType  = window.PMquoteStep2.jobLocation;
            var companyAddressType  = window.PMquoteStep2.companyAddressPick;

            var companyStreetType  = window.PMquoteStep2.companyAddressType;
            var companyAddress  = window.PMquoteStep2.companyAddress;
            var companyPostalCode  = window.PMquoteStep2.companyPostalCode;
            var companyCity  = window.PMquoteStep2.companyCity;
            var companyProvince  = window.PMquoteStep2.companyProvince; // is not sent;


            var hasMorePolicies  = window.PMquoteStep2.anotherInsurance;
            var anotherInsuranceName  = window.PMquoteStep2.anotherInsuranceName;
            var anotherInsuranceAmount  = window.PMquoteStep2.anotherInsuranceAmount;
            var anotherInsuranceEnds  = window.PMquoteStep2.anotherInsuranceEnds;

            var holderType  = window.PMquoteStep2.legalEntityType;

            var holderName  = window.PMquoteStep2.legalEntityName;
            var holderDocId  = window.PMquoteStep2.legalEntityId
            // var holderDocType  =   Generated on PMWShandler;
            var holderEmail  = window.PMquoteStep2.legalEntityEmail;
            var holderStreetType  = window.PMquoteStep2.legalEntityAddressType;
            var holderAddress  = window.PMquoteStep2.legalEntityAddress;
            var holderCity  = window.PMquoteStep2.legalEntityCity;
            var holderProvince  = window.PMquoteStep2.legalEntityProvince;

            // no inputs but we use the personal data to fill it
            var holderSurname  = window.PMquoteStep2.lastName;
            var holderBirthdate  = window.PMquoteStep1.birthdate;
            var holderPhone  = window.PMquoteStep2.phone;
            var holderLanguage  = window.PMquoteStep2.documentationLanguage;

            var beneficiary = window.PMquoteStep2.additionalBeneficiary;
            var increasedValue = window.PMquoteStep2.additionalIncreasedValue;

            // var healthQ  = window.PMhealthFormData - Handler sends processed questions automatically

            var ibanCountry = window.PMquoteStep4.ibanCountry;
            var ibanControl = window.PMquoteStep4.ibanControl;
            var ibanEntity = window.PMquoteStep4.ibanEntity;
            var ibanOffice = window.PMquoteStep4.ibanOffice;
            var ibanDc = window.PMquoteStep4.ibanDc;
            var ibanAccount = window.PMquoteStep4.ibanAccount;

            var IBAN = ibanCountry + ibanControl + ibanEntity + ibanOffice + ibanDc + ibanAccount;

            // var date  = window.PMquoteStep2.phone; To be sent by PMWShandler
            var dataPreferences  = "N";
            var commercialkey  = window.PMquoteStep1.commercialKey;

            var coverageData = window.PMquoteStep1.coverages;

            /*
            $coverageData[] = array(
                "capital"		=> number_format($coverageAmount, 2, ",", ""),
                "codigo"		=> $coverageCode,
                "duracion"		=> $coverageDuration,
                "franquicia"	=> $coverageFran
            );
            */

            jQuery.ajax({
                type: "POST",
                url: url,
                data: {
                    ws: ws,
                    productor : productor,
                    option : option,
                    productId : productId,
                    startingDate : startingDate,
                    profession : profession,
                    birthdate : birthdate,
                    gender : gender,
                    height : height,
                    weight : weight,
                    paymentMethod : paymentMethod,
                    name : name,
                    surname : surname,
                    docId : docId,
                    email : email,
                    phone : phone,
                    insuredLanguage : insuredLanguage,
                    streetType : streetType,
                    address : address,
                    postalCode : postalCode,
                    city : city,
                    province : province,
                    companyName : companyName,
                    workLocationType : workLocationType,
                    companyAddressType : companyAddressType,
                    companyStreetType : companyStreetType,
                    companyAddress : companyAddress,
                    companyPostalCode : companyPostalCode,
                    companyCity : companyCity,
                    companyProvince : companyProvince,
                    hasMorePolicies : hasMorePolicies,
                    anotherInsuranceName  : anotherInsuranceName,
                    anotherInsuranceAmount  : anotherInsuranceAmount,
                    anotherInsuranceEnds : anotherInsuranceEnds,
                    holderType : holderType,
                    holderName : holderName,
                    holderDocId : holderDocId,
                    holderEmail : holderEmail,
                    holderStreetType : holderStreetType,
                    holderAddress : holderAddress,
                    holderCity : holderCity,
                    holderProvince : holderProvince,
                    holderSurname : holderSurname,
                    holderBirthdate : holderBirthdate,
                    holderPhone : holderPhone,
                    holderLanguage : holderLanguage,
                    beneficiary : beneficiary,
                    increasedValue : increasedValue,
                    IBAN : IBAN,
                    dataPreferences : dataPreferences,
                    commercialKey : commercialkey,
                    coverageData : coverageData
                },
                success: function (response) {
                    if (typeof response.e === 'undefined') {
                        quote_load_submitPolicy(response.data);
                    } else {
                        displayModal("health", lang["quote.modal.pending"], response.e, lang["quote.modal.close"]);
                        jQuery('#step-4 .step-buttons .next .loadingIcon').hide();
                        jQuery('#quote .quote-bank-account').removeAttr("disabled");
                        jQuery('#step-4 .step-buttons .prev').removeAttr("disabled");
                        jQuery('#step-4 .step-buttons .next').removeAttr("disabled");
                    }

                },
                error: function (response) {
                    displayModal("health", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                    jQuery('#step-4 .step-buttons .next .loadingIcon').hide();
                    jQuery('#quote .quote-bank-account').removeAttr("disabled");
                    jQuery('#step-4 .step-buttons .prev').removeAttr("disabled");
                    jQuery('#step-4 .step-buttons .next').removeAttr("disabled");
                }
            });

        }
    });


    // QUOTE - Loads returned data and load next step
    function quote_load_submitPolicy( data ) {
        // Stores this info in a global array to access it later on
        window.PMsubmitPolicy = data;

        // Display message on screen
        policyStatus = false;
        var message;
        if( typeof data.mensajeError !== 'undefined'){
            message = data.mensajeError;
        }else if( typeof data.P_ESTADO_EMISION !== 'undefined'){
            message = lang['quote.sign.policy.status'] + "<span class='data font-weight-bold'>" + data.P_ESTADO_EMISION + "</span><br>";
            message += lang['quote.sign.policy.request'] + "<span class='data' font-weight-bold>" + data.P_NUMERO_SOLICITUD + "</span><br>";
            if( data.P_CODIGO_ESTADO == "F" ) {
                message += lang['quote.sign.policy.id'] + "<span class='data font-weight-bold'>" + data.P_NUMERO_POLIZA + "</span><br>";
            }
        }
        jQuery("#step-5 .thank-you .message").html(message);

        // TESTING
        // window.PMsigningMode = "P,S,A";
        // TESTING

        if( window.PMsigningMode){
            // split array
            var signingMethods = window.PMsigningMode.split(",");
            var i;

            // Loads screens for each available option
            for( i=0; i < signingMethods.length; i++ ){
                /**
                 *   P = papel
                 *   A = asíncrono
                 *   S = síncrona
                 */
                switch( signingMethods[i] ){
                    case "P":
                        // Displays the screen to download the paperwork to sign it manually
                        jQuery("#step-5 .hand-write-method").fadeIn();
                        jQuery("#step-5 .hand-write-method-button").show();
                        quote_load_policyRequestDownload(data.P_NUMERO_SOLICITUD);
                        break;

                    case "A":
                        // Displays Logalty asynchronous sign instructions to inform client
                        /**
                         *   F = Formalizada
                         */
                        if( data.P_CODIGO_ESTADO == "F" ){
                            jQuery("#step-5 .logalty-method .logalty p").html(lang["quote.sign.logalty.instructions"]);
                        }else{
                            jQuery("#step-5 .logalty-method .logalty p").html(lang["quote.sign.logalty.notFormalized"]);
                        }
                        jQuery("#step-5 .logalty-method").fadeIn();
                        jQuery("#step-5 .logalty-method-button").show();
                        break;

                    case "S":

                        if( data.P_CODIGO_ESTADO == "F" ) {
                            // Displays Logalty synchronous sign iframe
                            jQuery("#step-5 .logalty-synchronous-method").fadeIn();
                            jQuery("#step-5 .logalty-synchronous-method-button").show();

                            // retrieves document to sign
                            var url = "/get-data";
                            var ws = "getDocument";
                            var productor = window.PMquoteStep1.productor;
                            var source = 2;
                            var type = "SO";
                            var format  = "A4";

                            jQuery.ajax({
                                type: "POST",
                                url: url,
                                data: {
                                    ws: ws,
                                    productor : productor,
                                    docId : data.P_NUMERO_SOLICITUD,
                                    source : source,
                                    type : type,
                                    format : format
                                },
                                success: function (response) {
                                    //console.log( response );
                                    if (response['success'] == true) {
                                        window.PMwidgetStep5 = {
                                            contenidoFichero : response.data.contenidoFichero
                                        }

                                        // Initiates logalty iframe
                                        loadLogalty( data.P_NUMERO_SOLICITUD, data.P_NUMERO_POLIZA, response.data.contenidoFichero);

                                    } else {
                                        displayModal("health", lang["quote.modal.error"], "OK", lang["quote.modal.close"]);
                                    }

                                },
                                error: function (response) {
                                    displayModal("health", lang["quote.modal.error"], "KO", lang["quote.modal.close"]); // response.e
                                }

                            });
                        }else{
                            if( signingMethods.length == 1){
                                jQuery("#step-5 .container").append("<p>" + lang["quote.sign.logalty.notFormalized"] + "</p>");
                            }
                        }
                        break;
                }
            }

            // Displays screen with options to pick from if there is more than one method
            if( signingMethods.length > 1){

                jQuery("#step-5 .choose-signing-method").fadeIn();

                // Hide options
                jQuery(".signing-method-screen").hide();
            }
        }


        // Unlocks STEP 4
        jQuery('#step-4 .step-buttons .next .loadingIcon').hide();
        jQuery('#quote .quote-bank-account').removeAttr("disabled");
        jQuery('#step-4 .step-buttons .prev').removeAttr("disabled");
        jQuery('#step-4 .step-buttons .next').removeAttr("disabled");

        // Display STEP 5
        jQuery('#step-4').hide();
        jQuery('#step-5').fadeIn();
    }

    // QUOTE - gets the policy request to download and sign
    function quote_load_policyRequestDownload(docId){

        // var url = "/get-data";
        // var ws = "getDocument";
        var productor = window.PMquoteStep1.productor;
        var source = 2;
        var type = "SO";
        var format  = "A4";

        jQuery('#quote-download-form .docId').prop("value", docId);
        jQuery('#quote-download-form .productor').prop("value", productor);
        jQuery('#quote-download-form .source').prop("value", source);
        jQuery('#quote-download-form .type').prop("value", type);
        jQuery('#quote-download-form .format').prop("value",format);

        jQuery('#send-policy-request .productor').prop("value",window.PMquoteStep1.productor);
        jQuery('#send-policy-request .refId').prop("value", docId);

        /*
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                ws: ws,
                productor : productor,
                docId : docId,
                source : source,
                type : type,
                format : format
            },
            success: function (response) {
                //console.log( response );
                if (response['success'] == true) {

                    jQuery('#quote #quote-download-policy-request').attr("href", response.data.url );

                    window.PMquoteStep5 = {
                        url : response.data.url,
                        base64 : response.data.base64
                    }
                } else {
                    displayModal("health", lang["quote.modal.error"], "OK", lang["quote.modal.close"]);
                }

            },
            error: function (response) {
                displayModal("health", lang["quote.modal.error"], "KO", lang["quote.modal.close"]); // response.e
            }

        });
        */

    }


    // ------------------- STEP 5 ----------------------

    // QUOTE - pick signing method
    jQuery("#quote .choose-signing-method .card").click(function(e){

        var selectedSigningMethod;

        jQuery('.choose-signing-method').hide();
        if( jQuery(this).hasClass("logalty")){
            selectedSigningMethod = "logalty";
            jQuery('.logalty-method').fadeIn();
        }
        if( jQuery(this).hasClass("logalty-synchronous")){
            selectedSigningMethod = "logalty-synchronous";
            jQuery('.logalty-synchronous-method').fadeIn();
        }
        if( jQuery(this).hasClass("hand-write")){
            selectedSigningMethod = "hand-write";
            jQuery('.hand-write-method').fadeIn();
        }
        jQuery('#step-5 .step-buttons').fadeIn();

        window.PMquoteStep5 = {
            signingMethod : selectedSigningMethod
        }

    });

    // QUOTE - step 5 previous button
    jQuery('#quote #step-5 .step-buttons .quote-step.previous').click(function(e){
        resetBankAccount();
        jQuery('#step-5').hide();
        jQuery('#step-4').fadeIn();
    });

    // QUOTE - pick signing method
    jQuery("#quote .quote-insurance-policy-upload").change(function(e){
        jQuery("#quote .quote-insurance-policy-upload-button").hide();
        jQuery("#quote .quote-insurance-policy-upload-button").fadeIn();
    });

    // QUOTE - step 5 next button
    jQuery('#quote #step-5 .step-buttons .quote-step.next').click(function(e){
        window.location.href = jQuery("#top-menu .nav-item:first-child a").attr("href");
    });


    // QUOTE - loads logalty
    function loadLogalty( requestId, policyId, doc) {
        var url = "/get-data";
        var ws = "getLgtSignAccess";

        // Other variables are declared on view as global
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                ws: ws,
                requestId: requestId,
                policyId: policyId,
                doc: doc
            },
            success: function (response) {
                if (response['success'] == true) {
                    //console.log( response.data );
                    jQuery("#logaltyFrame").attr("src",response.data);
                    setTimeout(function() {
                        jQuery("#step-5 .loader-wrapper").hide();
                    }, 3000);

                } else {
                    displayModal("connection", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                    console.error(response.e);
                }
            },
            error: function (response) {
                displayModal("connection", lang["quote.modal.error"], lang["WS.error"], lang["quote.modal.close"]);
                console.error(lang["WS.error"]);
            }
        });
    }


    // ------------------- ADVISOR ----------------------

    // ADVISOR - LOADS ALL FIELDS DATA
    function advisor_load_fields(){

        // Load Freelancers fee
        var freelancerFees = "";
        Object.keys(window.PMadvisorCuotaAutonomos).forEach(function(key) {
            freelancerFees += "<option value='" + key + "'>" + window.PMadvisorCuotaAutonomos[key]['nombre'] + "</option>\n";
        });
        jQuery('#quote .advisor-freelancer-fee').html(freelancerFees);

        // Load Age
        var ages = "";
        for (var i = 18; i <= window.PMadvisorMaxAge; i++) {
            ages += "<option value='" + i + "'>" + i + "</option>\n";
        }
        jQuery('#quote .advisor-age').html(ages);

        // Load Jobs

        // Loads jobs
        var jobPicker = [];
        var jobSelect = "";
        Object.keys(window.PMadvisorJobs).forEach(function(key) {
            jobPicker.push(PMadvisorJobs[key]['nombre'] );
            jobSelect += "<option value='" + key + "'>" + window.PMadvisorJobs[key]['nombre'] + "</option>\n";
        });
        window.PMadvisorJobPicker = jobPicker.sort();

        jQuery('#quote .advisor-job').html(jobSelect);

        // Loads autocomplete input text.
        jQuery( function() {

            var accentMap = {
                "á": "a",
                "é": "e",
                "í": "i",
                "ó": "o",
                "ú": "u",
                "ü": "u"
            };

            // "Remove" accents
            var normalize = function(termOriginal) {
                var term = termOriginal.toLowerCase();
                var ret = "";
                for ( var i = 0; i < term.length; i++ ) {
                    ret += accentMap[ term.charAt(i) ] || term.charAt(i);
                }
                return ret;
            };

            jQuery( "#quote .advisor-job-picker" ).autocomplete({
                minLength: 0,

                source: function( request, response ) {
                    var matcher = new RegExp( jQuery.ui.autocomplete.escapeRegex( request.term ), "i" );
                    response( jQuery.grep( PMadvisorJobPicker, function( value ) {
                        value = value.label || value.value || value;
                        return matcher.test( value ) || matcher.test( normalize( value ) );
                    }) );
                },

                select: function(event,ui) {
                    this.value=ui.item.value;
                    jQuery(this).trigger('change');
                    return false;
                }
            });
        });

    }

    // ADVISOR - additional restrictions (freelancer fee - age)
    jQuery("#quote .recomendador").on('change', 'select:visible', function (e) {

        //console.log("check");
        if ( jQuery("#quote .recomendador .advisor-freelancer-fee").val() == 2 &&
            jQuery("#quote .recomendador .advisor-age").val() <= 47){
            jQuery("#quote .recomendador select:visible").addClass("invalid");
            jQuery("#quote .recomendador select:visible").removeClass("valid");
            //console.log("KO");
        } else {
            jQuery("#quote .recomendador select:visible").addClass("valid");
            jQuery("#quote .recomendador select:visible").removeClass("invalid");
            //console.log("OK");
        }

        enableAdviceButton();
    });

    // ADVISOR - Job selection
    jQuery("#quote .advisor-job-picker").on('input change click keyup', function (e) {
        var i = 0;
        var found = false;
        jQuery(".advisor-job option").each(function () {
            if (this.text == jQuery(".advisor-job-picker").val()) {
                jQuery(".advisor-job").prop("selectedIndex", i);
                found = true;
            } else {
                i++;
            }
        });
        if (found) {
            jQuery(this).addClass("valid");
            jQuery(this).removeClass("invalid");
        } else {
            jQuery(this).addClass("invalid");
            jQuery(this).removeClass("valid");
        }

        enableAdviceButton();
    });

    // ADVISOR - Checks required fields
    jQuery("#quote .advisor-income").on("input change click keyup", function (e) {
        if (jQuery(this).val() != "" &&
            jQuery(this).val() >= parseInt( jQuery(this).prop( "min" ) ) &&
            jQuery(this).val() <= parseInt( jQuery(this).prop( "max" ) ) ) {
            jQuery(this).removeClass("invalid");
            jQuery(this).addClass("valid");
        } else {
            jQuery(this).addClass("invalid");
            jQuery(this).removeClass("valid");
        }

        enableAdviceButton();
    });

    // ADVISOR - Enable button if required fields valid
    function enableAdviceButton( ) {
        allValid = true;
        jQuery( '#quote .recomendador input:visible, ' +
            '#quote .recomendador select:visible' )
            .each(function() {
                if( !jQuery(this).hasClass("valid") ){
                    allValid = false;
                }
            });

        if( allValid ){
            jQuery('.get-advice .advice-button').removeAttr("disabled");
            jQuery('.get-advice .advice-button').addClass("active");
        }else{
            jQuery('.get-advice .advice-button').attr("disabled", "disabled");
            jQuery('.get-advice .advice-button').removeClass("active");
        }

        return allValid;
    }

    // ADVISOR - Display recommended info
    jQuery('.get-advice .advice-button').click(function() {

        // Runs validation on all fields
        jQuery( '#quote .recomendador input:visible, ' +
            '#quote .recomendador select:visible' )
            .change();

        allValid = enableAdviceButton();

        // If all valid gets rates
        if( allValid ) {

            jQuery('#quote .advisor-results').hide();

            //-----------------------------------
            // RETA
            //-----------------------------------

            // CUOTA DE AUTONOMO
            var cuotaAutonomo = window.PMadvisorCuotaAutonomos[jQuery('#quote .advisor-freelancer-fee').val()].cuota;
            jQuery('.advisor-results .block1.row2 .col2').html(cuotaAutonomo.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // TIPO DE COTIZACION
            var tipoAplicable = window.PMadvisorCuotaAutonomos[jQuery('#quote .advisor-freelancer-fee').val()].tipoAplicable;
            if( isNaN( tipoAplicable ) ) {
                jQuery('.advisor-results .block1.row2 .col3').html(tipoAplicable.toLocaleString('de', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }else{
                jQuery('.advisor-results .block1.row2 .col3').html(tipoAplicable.toLocaleString('de', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + " %");
            }

            // BASE DE COTIZACION
            var baseCotizacion = window.PMadvisorCuotaAutonomos[jQuery('#quote .advisor-freelancer-fee').val()].baseCotizacion;
            jQuery('.advisor-results .block1.row2 .col4').html(baseCotizacion.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );


            //-----------------------------------
            // PRESTACION PUBLICA
            //-----------------------------------

            // BRUTO PRIMER MES CONTINGENCIA COMUN SIN CUOTA
            var BrutoMes1ComunSinCuota = ( ( ( baseCotizacion / 30 ) * 0.6 ) * 17 ) + ( ( ( baseCotizacion / 30 ) * 0.75 ) * 10 );
            jQuery('.advisor-results .block2.row2 .col2').html(BrutoMes1ComunSinCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // BRUTO SEGUNDO MES CONTINGENCIA COMUN SIN CUOTA
            var BrutoMes2ComunSinCuota = baseCotizacion * 0.75;
            jQuery('.advisor-results .block2.row2 .col3').html(BrutoMes2ComunSinCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // SUCESIVOS COMUN SIN CUOTA
            var BrutoSucesivosComunSinCuota = baseCotizacion * 0.75;
            jQuery('.advisor-results .block2.row2 .col4').html(BrutoSucesivosComunSinCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );


            // BRUTO PRIMER MES CONTINGENCIA PROFESIONAL SIN CUOTA
            var BrutoMes1ProfesionalSinCuota = ( ( ( baseCotizacion / 30 ) * 0.75 ) * 29 );
            jQuery('.advisor-results .block2.row3 .col2').html(BrutoMes1ProfesionalSinCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // BRUTO SEGUNDO MES CONTINGENCIA PROFESIONAL SIN CUOTA
            var BrutoMes2ProfesionalSinCuota = baseCotizacion * 0.75;
            jQuery('.advisor-results .block2.row3 .col3').html(BrutoMes2ProfesionalSinCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // SUCESIVOS PROFESIONAL SIN CUOTA
            var BrutoSucesivosProfesionalSinCuota = baseCotizacion * 0.75;
            jQuery('.advisor-results .block2.row3 .col4').html(BrutoSucesivosProfesionalSinCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );


            // BRUTO PRIMER MES CONTINGENCIA COMUN CON CUOTA
            var BrutoMes1ComunConCuota = ( ( ( baseCotizacion / 30 ) * 0.6 ) * 17 ) + ( ( ( baseCotizacion / 30 ) * 0.75 ) * 10 ) - cuotaAutonomo;
            jQuery('.advisor-results .block2.row5 .col2').html(BrutoMes1ComunConCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // BRUTO SEGUNDO MES CONTINGENCIA COMUN CON CUOTA
            var BrutoMes2ComunConCuota = baseCotizacion * 0.75 - cuotaAutonomo;
            jQuery('.advisor-results .block2.row5 .col3').html(BrutoMes2ComunConCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // SUCESIVOS COMUN CON CUOTA
            var BrutoSucesivosComunConCuota = baseCotizacion * 0.75;
            jQuery('.advisor-results .block2.row5 .col4').html(BrutoSucesivosComunConCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );


            // BRUTO PRIMER MES CONTINGENCIA PROFESIONAL CON CUOTA
            var BrutoMes1ProfesionalConCuota = ( ( ( baseCotizacion / 30 ) * 0.75 ) * 29 ) - cuotaAutonomo;
            jQuery('.advisor-results .block2.row6 .col2').html(BrutoMes1ProfesionalConCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // BRUTO SEGUNDO MES CONTINGENCIA PROFESIONAL CON CUOTA
            var BrutoMes2ProfesionalConCuota = baseCotizacion * 0.75 - cuotaAutonomo;
            jQuery('.advisor-results .block2.row6 .col3').html(BrutoMes2ProfesionalConCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // SUCESIVOS PROFESIONAL CON CUOTA
            var BrutoSucesivosProfesionalConCuota = baseCotizacion * 0.75;
            jQuery('.advisor-results .block2.row6 .col4').html(BrutoSucesivosProfesionalConCuota.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );


            //-----------------------------------
            // CAPITAL A GARANTIZAR
            //-----------------------------------

            // MERMA DIARIA
            var necesidadEconomicaMensual = jQuery('#quote .advisor-income').val();
            var mediaMes1 = ( ( BrutoMes1ComunSinCuota + BrutoMes1ProfesionalSinCuota ) / 2 ) / 30;
            var mediaMes2 = ( ( BrutoMes2ComunSinCuota + BrutoMes2ProfesionalSinCuota ) / 2 ) / 30;
            var mediaAnual = ( mediaMes1 + ( mediaMes2 * 11 ) ) / 12;

            var mermaDiaria = Math.round( ( necesidadEconomicaMensual / 30 ) - mediaAnual );
            jQuery('.advisor-results .block3.row2 .col2').html(mermaDiaria.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // MERMA MENSUAL
            var mermaMensual = mermaDiaria * 30;
            jQuery('.advisor-results .block3.row2 .col3').html(mermaMensual.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // RECOMENDACION DIARIA
            var recomendacionDiaria = mermaDiaria * window.PMajusteRecomendacion;
            jQuery('.advisor-results .block3.row4 .col2').html(recomendacionDiaria.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // RECOMENDACION MENSUAL
            var recomendacionMensual = recomendacionDiaria * 30;
            jQuery('.advisor-results .block3.row4 .col3').html(recomendacionMensual.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );


            //-----------------------------------
            // CALCULAR EL COSTE DEL SEGURO
            //-----------------------------------

            var costeResultados = window.PMadvisorGrupoRiesgo[window.PMadvisorJobs[jQuery("#quote .advisor-job").val()].grupoRiesgo][jQuery("#quote .advisor-age").val()];

            // SIN FRANQUICIA
            var costeFranquiciaSin = Math.round ( ( ( ( ( ( costeResultados["sfrq"] / 10 ) * recomendacionDiaria ) / 12 ) * window.PMajustePrimaTotalMensual ) + Number.EPSILON ) * 100 ) / 100;
            jQuery('.advisor-results .block4.row2 .col3').html(costeFranquiciaSin.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // FRANQUICIA 3 DIAS
            var costeFranquicia3 = Math.round ( ( ( ( ( ( costeResultados["frq3"] / 10 ) * recomendacionDiaria ) / 12 ) * window.PMajustePrimaTotalMensual ) + Number.EPSILON ) * 100 ) / 100;
            jQuery('.advisor-results .block4.row3 .col3').html(costeFranquicia3.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // FRANQUICIA 7 DIAS
            var costeFranquicia7 = Math.round ( ( ( ( ( ( costeResultados["frq7"] / 10 ) * recomendacionDiaria ) / 12 ) * window.PMajustePrimaTotalMensual ) + Number.EPSILON ) * 100 ) / 100;
            jQuery('.advisor-results .block4.row4 .col3').html(costeFranquicia7.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // FRANQUICIA 15 DIAS
            var costeFranquicia15 = Math.round ( ( ( ( ( ( costeResultados["frq15"] / 10 ) * recomendacionDiaria ) / 12 ) * window.PMajustePrimaTotalMensual ) + Number.EPSILON ) * 100 ) / 100;
            jQuery('.advisor-results .block4.row5 .col3').html(costeFranquicia15.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );

            // FRANQUICIA 30 DIAS
            var costeFranquicia30 = Math.round ( ( ( ( ( ( costeResultados["frq30"] / 10 ) * recomendacionDiaria ) / 12 ) * window.PMajustePrimaTotalMensual ) + Number.EPSILON ) * 100 ) / 100;
            jQuery('.advisor-results .block4.row6 .col3').html(costeFranquicia30.toLocaleString('de', { style: 'currency', currency: 'EUR', currencyDisplay : 'symbol' }) );


            window.PMadvisorResult = {
                cuotaAutonomo : cuotaAutonomo,
                tipoAplicable : tipoAplicable,
                baseCotizacion : baseCotizacion,
                BrutoMes1ComunSinCuota : BrutoMes1ComunSinCuota,
                BrutoMes2ComunSinCuota : BrutoMes2ComunSinCuota,
                BrutoSucesivosComunSinCuota : BrutoSucesivosComunSinCuota,
                BrutoMes1ProfesionalSinCuota : BrutoMes1ProfesionalSinCuota,
                BrutoMes2ProfesionalSinCuota : BrutoMes2ProfesionalSinCuota,
                BrutoSucesivosProfesionalSinCuota : BrutoSucesivosProfesionalSinCuota,
                BrutoMes1ComunConCuota : BrutoMes1ComunConCuota,
                BrutoMes2ComunConCuota : BrutoMes2ComunConCuota,
                BrutoSucesivosComunConCuota : BrutoSucesivosComunConCuota,
                BrutoMes1ProfesionalConCuota : BrutoMes1ProfesionalConCuota,
                BrutoMes2ProfesionalConCuota : BrutoMes2ProfesionalConCuota,
                BrutoSucesivosProfesionalConCuota : BrutoSucesivosProfesionalConCuota,
                mermaDiaria : mermaDiaria,
                mermaMensual : mermaMensual,
                recomendacionDiaria : recomendacionDiaria,
                recomendacionMensual : recomendacionMensual,
                costeFranquiciaSin : costeFranquiciaSin,
                costeFranquicia3 : costeFranquicia3,
                costeFranquicia7 : costeFranquicia7,
                costeFranquicia15 : costeFranquicia15,
                costeFranquicia30 : costeFranquicia30
            };

            //console.log(window.PMadvisorResult);

            jQuery('#quote .advisor-results').fadeIn();

        }

    });



    // ------------------- UPLOAD POLICY REQUEST ----------------------------

    // QUOTE - Validates extra info fields
    if (jQuery('#send-policy-request').length) {
        jQuery('#send-policy-request input:visible, ' +
            '#send-policy-request select:visible')
            .on("input change click keyup", function () {

                // validates fields

                if (jQuery(this).hasClass("productor")) {
                    if (jQuery(this).val() != "") {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                if (jQuery(this).hasClass("doc")) {
                    if (jQuery(this).val() != "") {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                if (jQuery(this).hasClass("refId")) {
                    if (jQuery(this).val() > 0) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                enableUploadPolicyRequestButton();

            });

        function enableUploadPolicyRequestButton(){
            allValid = true;
            jQuery( '#send-policy-request input:visible, ' +
                '#send-policy-request select:visible' )
                .each(function() {
                    if( !jQuery(this).hasClass("valid") ){
                        allValid = false;
                    }
                });

            if( allValid ){
                jQuery('#send-policy-request button').removeAttr("disabled");
                jQuery('#send-policy-request button').addClass("active");
            }else{
                jQuery('#send-policy-request button').attr("disabled", "disabled");
                jQuery('#send-policy-request button').removeClass("active");
            }

            return allValid;
        }


        jQuery("#send-policy-request button").click( function (e) {

            // Hides next blocks and displays loader
            resetProductVariations();

            // jQuery('#send-policy-request button .loader-wrapper').fadeIn();
            jQuery('#send-policy-request button .loadingIcon').css('display', 'inline');
            jQuery('#send-policy-request button').attr("disabled", "disabled");

            var url = "/get-data";
            var ws = "uploadDocument";
            //var productor = jQuery("#send-policy-request .productor").val();
            var refId =  jQuery("#send-policy-request .refId").val();
            var docId =  jQuery("#send-policy-request .docId").val();
            var folderId =  jQuery("#send-policy-request .folderId").val();
            var docType =  jQuery("#send-policy-request .docType").val();


            /*
            var doc;
            var selectedFile = document.getElementById("doc").files;
            //Check File is not Empty
            if (selectedFile.length > 0) {
                // Select the very first file from list
                var fileToLoad = selectedFile[0];
                // FileReader function for read the file.
                var fileReader = new FileReader();
                // Onload of file read the file content
                var base64;
                fileReader.onload = function(fileLoadedEvent) {
                    base64 = fileLoadedEvent.target.result;
                    // Print data in console
                    //console.log(doc);
                };
                // Convert data to base64
                fileReader.readAsDataURL(fileToLoad);
            }

            console.log(fileToLoad);

            */

            var selectedFile = document.getElementById("doc").files;
            //Check File is not Empty
            if (selectedFile.length > 0) {
                // Select the very first file from list
                var fileToLoad = selectedFile[0];
                // FileReader function for read the file.
                var fileReader = new FileReader();
                // Onload of file read the file content
                var base64;
                fileReader.onload = function(fileLoadedEvent) {
                    base64 = fileLoadedEvent.target.result;
                    // Print data in console
                    base64 = base64.replace("data:application/pdf;base64,", "");
                    //console.log(base64);

                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            ws: ws,
                            //productor: productor,
                            refId : refId,
                            docId : docId,
                            folderId : folderId,
                            docType : docType,
                            doc : base64
                        },
                        success: function (response) {
                            jQuery('#send-policy-request button').removeAttr("disabled");
                            jQuery('#send-policy-request button .loadingIcon').css('display', 'none');
                            if (response['success'] == true) {
                                displayModal("health", lang["uploadPolicyRequest.ok.modal.title"], lang["uploadPolicyRequest.ok.modal.message"], lang["quote.modal.close"]);
                            } else {
                                console.error( response.e);
                                displayModal("health", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                            }
                        },
                        error: function (response) {
                            console.error( response.e);
                            displayModal("health", lang["quote.modal.error"], lang["WS.error"], lang["quote.modal.close"]);
                            jQuery('#send-policy-request button').removeAttr("disabled");
                            jQuery('#send-policy-request button .loadingIcon').css('display', 'none');
                        }
                    });

                };
                // Convert data to base64
                fileReader.readAsDataURL(fileToLoad);
            }
        });

    }

    // PANEL - Sends private login form as JSON and gets response
    if (jQuery('#loginFormPrivate').length){

        jQuery('#loginFormPrivate').on('submit', function(e) {
            e.preventDefault(); // prevent native submit
            jQuery('#loginFormPrivate .loadingIcon').show();

            var user = jQuery("#loginFormPrivate input[name='user']").val();
            var pass = jQuery("#loginFormPrivate input[name='password']").val();
            var gestor = null;
            var loginType = jQuery("#loginFormPrivate input[name='login-type']").val();
            var action = jQuery("#loginFormPrivate input[name='action']").val();
            var userPM = jQuery("#loginFormPrivate input[name='pm-user']").val();

            jQuery.ajax({
                type: "POST",
                url: "/send-login-form",
                data: {
                    user: user,
                    pass: pass,
                    gestor: gestor,
                    loginType: loginType,
                    action: action,
                    userPM: userPM,
                },
                success: function(response){
                    if(response['success'] == true){
                        window.location.href = response['redirect'];
                    }else{
                        jQuery('#loginFormPrivate .loadingIcon').hide();
                        jQuery('#loginFormPrivate .error-message').html( response['e']);
                        jQuery('#loginFormPrivate .error-message').hide();
                        jQuery('#loginFormPrivate .error-message').fadeIn(1000);
                    }
                },
                error: function(response){
                    jQuery('#loginFormPrivate .loadingIcon').hide();
                    jQuery('#loginFormPrivate .error-message').html( lang["error.login"] );
                    jQuery('#loginFormPrivate .error-message').hide();
                    jQuery('#loginFormPrivate .error-message').fadeIn(1000);
                    console.error( lang["login.form.error"] );
                }
            });
        });

    }

    // PANEL - new slider button
    if (jQuery('#sliders .add-new').length) {
        jQuery('#sliders .add-new button').click(function() {
            // Hides sliders list
            jQuery('#sliders .list-all').hide();
            jQuery('#sliders .add-new').hide();

            resetSliderForm();

            // Displays slider form
            jQuery('#sliders .new-slider').fadeIn();
            jQuery('#sliders .go-back').fadeIn();
        });
    }

    // PANEL - go back button
    if (jQuery('#sliders .go-back').length) {
        jQuery('#sliders .go-back button').click(function() {
            jQuery('#sliders .new-slider').hide();
            jQuery('#sliders .go-back').hide();
            jQuery('#sliders .list-all').fadeIn();
            jQuery('#sliders .add-new').fadeIn();
            listAllSliders();
        });
    }

    // PANEL - sliders list action buttons
    jQuery("#sliders .list-all")
        .on('click', ".edit-button.action-button", function (e) {
            jQuery('#sliders .add-new button').click();

            var url = "/slider-ajax";
            var action = "get";
            var id = jQuery(this).data("id");

            jQuery.ajax({
                type: "POST",
                url: url,
                data: {
                    action : action,
                    id : id
                },
                success: function(response) {
                    //console.log(response);
                    jQuery(".slider-id").val( response['id'] );
                    jQuery(".slider-name").val( response['name'] );
                    document.querySelector('#slider-color').jscolor.fromString( response['color'] )
                    jQuery(".slider-header").val( response['header'] );
                    jQuery(".slider-description").val( response['description'] );
                    jQuery(".preview-image").attr("src", response['image'] );
                    jQuery("#uploaded-image").addClass("valid");
                    jQuery(".preview-image").fadeIn();

                    jQuery(".slider-name").change();
                },
                error: function(response){
                    console.error(response);
                }
            });

        });
    jQuery("#sliders .list-all")
        .on('click', "tr:not('.highlight') .delete-button.action-button", function (e) {
            jQuery(this).parent().parent().addClass("highlight");

            displayModal("delete-slider-modal", lang["slider.modal.delete.title"], "<p class='text-center'>" + lang["slider.modal.delete.message"] + "<br><b>" + jQuery(this).parent().parent().find("td:nth-child(2)").html() + "</b></p>", lang["slider.modal.delete.yes"], lang["slider.modal.delete.no"]);
        });
    // PANEL - sliders delete slider

    if (jQuery('#PMmodal').length) {
        jQuery("#panel")
            .on('click', "#PMmodal.custom-delete-slider-modal .btn-primary", function (e) {

                var url = "/slider-ajax";
                var action = "delete";
                var id = jQuery('.list-all tr.highlight td:first-child').html();

                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        action : action,
                        id : id
                    },
                    success: function(response) {
                        listAllSliders();
                    },
                    error: function(response){
                        console.error(response);
                    }
                });
            });
        jQuery("#panel")
            .on('click', "#PMmodal.custom-delete-slider-modal .btn-secondary", function (e) {

                jQuery('.list-all tr.highlight').removeClass("highlight");
            });
    }


    // PANEL - sliders preview image
    if (jQuery('#sliders #uploaded-image').length) {
        jQuery("#uploaded-image").change(function(){
            if (this.files && this.files[0]) {
                jQuery('#preview-image').hide();
                var reader = new FileReader();
                reader.onload = function (e) {
                    jQuery('#preview-image').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
                jQuery('#preview-image').fadeIn();

                jQuery(".slider-image").addClass("valid");
                enableSliderSaveButton();
            }
        });
    }


    // PANEL - Slider edit validation
    if (jQuery('#sliders form').length) {
        jQuery( '#sliders form input, ' +
            '#sliders form textarea' )
            .on("input change click keyup", function() {

                // validates fields
                if (jQuery(this).hasClass("slider-name")) {

                    if (jQuery(this).val().length > 3) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                if (jQuery(this).hasClass("slider-color")){
                    if (jQuery(this).val().length == 7) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }


                // console.log( jQuery(this).attr("class") );
                enableSliderSaveButton();
            });

    }

    // PANEL - Slider edit enables save button
    function enableSliderSaveButton( ) {
        allValid = true;
        //console.log( " --------------------  " );
        jQuery( '#sliders form input:visible, ' +
            '#sliders form textarea:visible' )
            .each(function() {
                if( !jQuery(this).hasClass("valid") ){
                    //console.log( " FALSE - " + jQuery(this).attr('class') );
                    allValid = false;
                }else{
                    //console.log( " TRUE - " + jQuery(this).attr('class') );
                }
            });

        if( allValid ){
            jQuery('#sliders form button').removeAttr("disabled");
            jQuery('#sliders form button').addClass("active");
        }else{
            jQuery('#sliders form button').attr("disabled", "disabled");
            jQuery('#sliders form button').removeClass("active");
        }

        return allValid;
    }

    // PANEL - Slider edit submit button
    jQuery('#sliders form button').on('click', function(e) {
        e.preventDefault(); // prevent native submit

        jQuery(this).attr("disabled","disabled");

        var url = "/slider-ajax";
        var action = "save";
        var id = jQuery("#sliders .slider-id").val();
        var name = jQuery("#sliders .slider-name").val();
        var color = jQuery("#sliders .slider-color").val();
        var header = jQuery("#sliders .slider-header").val();
        var description = jQuery("#sliders .slider-description").val();
        var image = jQuery("#sliders #preview-image").attr("src");

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                action : action,
                id : id,
                name : name,
                color : color,
                header : header,
                description : description,
                image : image
            },
            success: function(response) {
                jQuery('#sliders .new-slider').hide();
                jQuery('#sliders .go-back').hide();
                jQuery('#sliders .list-all').fadeIn();
                jQuery('#sliders .add-new').fadeIn();
                listAllSliders();
            },
            error: function(response){
                console.error(response);
            }
        });

    });

    // PANEL - Gets sliders list on load
    if( jQuery("#sliders .list-all table").length ){
        listAllSliders();
    }

    // PANEL - Lists all
    function listAllSliders() {

        var url = "/slider-ajax";
        var action = "getAll";

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                action : action,
                output : "table"
            },
            success: function(response) {
                jQuery("#sliders .list-all table tbody").html(response);
            },
            error: function(response){
                console.error(response);
            }
        });

    }

    if( jQuery("#app-home-slider").length ){

        var url = "/slider-ajax";
        var action = "getAll";

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                action : action,
                output : "slider"
            },
            success: function(response) {
                jQuery(".splide__list").html(response);

                new Splide( '#app-home-slider', {
                    type   : 'loop',
                    interval : 5000,
                    height: 300,
                } ).mount();

                jQuery("#app-home-slider").fadeIn();
            },
            error: function(response){
                console.error(response);
            }
        });
    }











    // ------------------- RESETS ----------------------

    function resetProductVariations(){
        jQuery('#quote .product-variations').hide();
        jQuery('#quote .product-modalities').hide();

        resetProductExtraInfo();
        resetAdvisorResults();
        resetProductModalities();
    }

    function resetProductModalities(){
        jQuery('#quote .product-modalities input').prop("checked", false);
        window.PMSelectedProductModality = null;
    }

    function resetAdvisorResults(){
        jQuery('#quote .advisor-results').hide();

    }

    function resetProductExtraInfo(){
        jQuery('#quote .product-extra-info').hide();
        jQuery('#quote .product-extra-info input, ' +
            '#quote .product-extra-info select').val("");
        jQuery('#quote .product-extra-info input, ' +
            '#quote .product-extra-info select').removeClass("valid");
        jQuery('#quote .product-extra-info input, ' +
            '#quote .product-extra-info select').removeClass("invalid");
        jQuery('#quote .product-extra-info .quote-job').addClass("valid");

        resetGetRatesButton();
    }

    function resetProductSpecifics(){
        jQuery('#quote .product-specifics').hide();
        jQuery('#quote .product-specifics input, ' +
            '#quote .product-specifics select').val("");
        jQuery('#quote .product-specifics input, ' +
            '#quote .product-specifics select').removeClass("valid");
        jQuery('#quote .product-specifics input, ' +
            '#quote .product-specifics select').removeClass("invalid");
        jQuery('#quote .product-specifics .quote-job').addClass("valid");

    }

    function resetGetRatesButton(){
        jQuery('#quote .get-rates button').attr("disabled","disabled");
        jQuery('#quote .get-rates button').removeClass("active");
        jQuery('#quote .get-rates').hide();
        jQuery('#quote .get-rates .loadingIcon').hide();

        resetRatesTable();
    }

    function resetRatesTable(){
        jQuery('#quote .rates-table').hide();
        jQuery('#quote .rates-table-description').hide();
        jQuery('#quote .rates-table-selection-description').hide();

        resetRatesTableActionsBilling();
    }

    function resetRatesTableActionsBilling(){
        jQuery('#quote .rates-table .billing-cycle input').attr("disabled","disabled");
        jQuery('#quote .rates-table .billing-cycle input').prop("checked", false);
        jQuery('#quote .rates-table .billing-cycle > div').show();
        jQuery('#quote .table-actions .action-minibutton').attr("disabled", "disabled").removeClass("active");

        resetSelectedProductInfo();
    }

    function resetSelectedProductInfo(){
        jQuery('#quote #selected-product-info').hide();
        jQuery('#quote #selected-product-info .billing-total').show();
    }

    function resetPersonalInfo(){
        jQuery('#quote .quote-first-name').val("");
        jQuery('#quote .quote-last-name').val("");
        jQuery('#quote .quote-personal-id').val("");
        jQuery('#quote .quote-phone').val("");
        jQuery('#quote .quote-email').val("");
        jQuery('#quote .quote-address').val("");
        jQuery('#quote .quote-postal-code').val("");
        jQuery('#quote .quote-company-name').val("");
        jQuery('#quote .quote-company-address').val("");
        jQuery('#quote .btn-group-toggle label:nth-child(2)').click();
        jQuery('#quote .btn-group-toggle input').addClass("valid");

        resetQuoteCityProvince();
        resetQuoteCompanyCityProvince();
    }

    function resetQuoteCityProvince(element){

        switch(element){

            case "quote-postal-code":
                jQuery('#quote .quote-city').html("");
                jQuery('#quote .quote-city').attr("disabled","disabled");
                jQuery('#quote .quote-province').html("");
                jQuery('#quote .quote-province').attr("disabled","disabled");
                break;

            case "quote-company-postal-code":
                jQuery('#quote .quote-company-city').html("");
                jQuery('#quote .quote-company-city').attr("disabled","disabled");
                jQuery('#quote .quote-company-province').html("");
                jQuery('#quote .quote-company-province').attr("disabled","disabled");
                break;

            case "quote-legal-entity-postal-code":
                jQuery('#quote .quote-legal-entity-city').html("");
                jQuery('#quote .quote-legal-entity-city').attr("disabled","disabled");
                jQuery('#quote .quote-legal-entity-province').html("");
                jQuery('#quote .quote-legal-entity-province').attr("disabled","disabled");
                break;
        }

    }

    function resetQuoteCompanyCityProvince(){
        jQuery('#quote .quote-company-postal-code').val("");
    }

    function resetHealthForm(){
        jQuery('#quote #health-form .dynamic-content').html("");
        jQuery('#quote #step-3 .step-buttons .quote-step.next').attr("disabled","disabled");
    }

    function resetBankAccount(){
        jQuery('#step-4 .step-buttons .next .loadingIcon').hide();
        jQuery('#quote .quote-bank-account').removeAttr("disabled");
        jQuery('#step-4 .step-buttons .next').removeAttr("disabled");
        jQuery('#step-4 .step-buttons .prev').removeAttr("disabled");
    }

    function resetAdvisor(){
        jQuery('#quote .advisor-income').val("");
        jQuery('.get-advice .advice-button').attr("disabled", "disabled");
        jQuery('.get-advice .advice-button').removeClass("active");
        jQuery("#quote .advisor-freelancer-fee").prop("selectedIndex", 0);
        jQuery("#quote .advisor-age").prop("selectedIndex", 0);
        jQuery("#quote .advisor-job").prop("selectedIndex", 0);

        resetAdviceResults();
    }

    function resetAdviceResults(){
        // Hide results table
        jQuery('#quote .advisor-results').hide();
    }

    function resetSliderForm(){

        jQuery("#sliders .slider-id").val("");
        jQuery("#sliders .slider-name").val("");
        document.querySelector('#slider-color').jscolor.fromString( "FFFFFF" )
        jQuery("#sliders .slider-header").val("");
        jQuery("#sliders .slider-description").val("");
        jQuery("#sliders #uploaded-image").val("");
        jQuery("#sliders #preview-image").attr("src","").hide();
        jQuery("#sliders form button").attr("disabled","disabled");
        jQuery("#sliders form button").removeAttr("active");
    }





});
