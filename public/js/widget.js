var healthFormRequired = false;

jQuery( document ).ready(function() {

    // Loads widget first step
    getProductVariations();
    getProductConfiguration();
    setHeaderFooter();

    // WIDGET - Retrieves info required for getRates
    function setHeaderFooter() {
        if (typeof PMheader !== 'undefined') {
            jQuery('header').html(PMheader);
        }
        if (typeof PMfooter !== 'undefined') {
            jQuery('footer').html(PMfooter);
        }
    }


    // WIDGET - Retrieves info required for getRates
    function getProductVariations() {
        var url = "/get-data";
        var ws = "getProductVariations";

        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                ws: ws,
                productor: PMproductor,
                product: PMproduct,
                entryChannel: PMentryChannel,
                application: PMapplication,
                u: PMu,
                p: PMp
            },
            success: function (response) {
                if (response['success'] == true) {
                    quote_load_ProductVariations(response.data);
                } else {
                    console.error(response.e);
                    displayModal("connection", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                }
            },
            error: function (response) {
                console.error(lang["WS.error"]);
            }
        });
    }

    // WIDGET - Loads product variations dynamically from WS
    function quote_load_ProductVariations(data) {
        // Stores this info in a global array to access it later on
        window.PMproductVariations = data[window.PMproductVariation];

        jQuery('#step-1 h4 .product-name').html( lang["widget.product.prefix"] + window.PMproductVariations.name);
        jQuery('#step-4 #product-info-widget h4').html( lang["widget.product.prefix"] + window.PMproductVariations.name);

    }

    // WIDGET - Gets more info to use on getRates
    function getProductConfiguration() {
        var url = "/get-data";
        var ws = "getProductConfiguration";

        // Other variables are declared on view as global
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {
                ws: ws,
                productor: PMproductor,
                product: PMproduct,
                productVariation: PMproductVariation,
                entryChannel: PMentryChannel,
                application: PMapplication,
                u: PMu,
                p: PMp
            },
            success: function (response) {
                if (response['success'] == true) {
                    quote_load_ProductConfiguration(response.data);
                } else {
                    console.error(response.e);
                    displayModal("connection", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                }
            },
            error: function (response) {
                console.error(lang["WS.error"]);
            }
        });
    }

    // WIDGET - Loads extra info dynamically from WS
    function quote_load_ProductConfiguration(data) {
        // Stores this info in a global array to access it later on
        window.PMproductConfig = data;

        // Signing method Logalty/handwriting
        if( typeof data.P_ES_EMISION_LOGALTY !== 'undefined' ) {
            window.PMsigningMode = data.P_ES_EMISION_LOGALTY;
        }else{
            window.PMsigningMode = null;
        }

        // Loads jobs
        var jobsArray = data.P_PROFESION_CLIENTE.values;
        var jobPicker = [];
        var jobSelect = "";

        Object.keys(jobsArray).forEach(function (key) {
            jobPicker.push(jobsArray[key]);
            jobSelect += "<option value='" + key + "'>" + jobsArray[key] + "</option>";
        });
        window.PMjobPicker = jobPicker;
        window.PMjobSelect = jobSelect;

        // Loads Gender
        var genderArray = data.P_SEXO.values;
        var genderSelect = "<option value='' disabled selected></option>";
        Object.keys(genderArray).forEach(function (key) {
            genderSelect += "<option value='" + key + "'>" + genderArray[key] + "</option>";
        });

        // Loads Job type
        if (typeof data.P_REGIMEN_SEG_SOCIAL.values !== 'undefined'){
            var jobTypeArray = data.P_REGIMEN_SEG_SOCIAL.values;
            var jobTypeSelect = "<option value='' disabled selected></option>";
            Object.keys(jobTypeArray).forEach(function (key) {
                jobTypeSelect += "<option value='" + key + "'>" + jobTypeArray[key] + "</option>";
            });
        }else{
            jQuery('.quote-job-type-wrapper').hide();
        }

        // Loads coverages
        // There are no field names coming from the WS so we have to set them manually
        var benefitsArray = data.coberturas;
        window.PMcoberturas = data.coberturas;
        window.PMperiodocobertura = data.P_PERIODO_COBERTURA;
        var cols;
        var benefits = "";
        var i = 1;
        cols = Math.floor(12 / Object.keys(benefitsArray).length);
        Object.keys(benefitsArray).forEach(function (key) {
            FieldName = key;

            benefits += "<input type='hidden' class='form-control w-100 quote-benefit valid quote-benefit-" + FieldName + "' name='quote-benefit-" + FieldName + "' min='" + benefitsArray[key].min + "' max='" + benefitsArray[key].max + "' step='1' autocomplete='off' data-name='" + benefitsArray[key].name + "' " + benefitsArray[key].attributes + "required>\n";
            /*benefits += "<input type='hidden' class='form-control w-100 quote-benefit valid quote-benefit-" + FieldName + "' name='quote-benefit-" + FieldName + "' min='" + benefitsArray[key].min + "' max='" + benefitsArray[key].max + "' step='1' autocomplete='off' placeholder='" + benefitsArray[key].min + " - " + benefitsArray[key].max + "' data-name='" + benefitsArray[key].name + "' " + benefitsArray[key].attributes + "required>\n";*/

            i++;
            // TODO: min/max values received are not the ones that the WS is using,
            //  sometimes we get a WS response telling us it exceeds the max while it's withint received paramenters.
            //  the attribute value is also wrong in most cases
        });

        // Loads durations
        // There are no field names coming from the WS so we have to set them manually
        var durationArray = data.duracion;
        window.PMduracion = data.duracion;
        var duration = "";
        Object.keys(durationArray).forEach(function (key) {
            FieldName = key;
            duration += "<input type='hidden' class='form-control w-100 quote-duration valid quote-duration-" + FieldName + "' name='quote-duration-" + FieldName + "' data-name='" + durationArray[key].name + "' " + durationArray[key].attributes + " required>\n";
            i++;
        });

        // Load commercial key
        window.PMcommercialKey = data.P_CLAVE_COMERCIAL;

        var hidden = "";
        if (data.P_CLAVE_COMERCIAL.hidden == "S") {
            hidden = " type='hidden' ";
        }
        var commercialKey = "<" + data.P_CLAVE_COMERCIAL.fieldType + hidden + " class='form-control w-100 quote-commercial-key valid' name='quote-commercial-key' " + data.P_CLAVE_COMERCIAL.attributes + " required>\n";

        if (data.P_CLAVE_COMERCIAL.fieldType == "select") {

            var claveCommercialArray = data.P_CLAVE_COMERCIAL.values;
            var claveCommercialSelect = "";

            Object.keys(claveCommercialArray).forEach(function (key) {
                claveCommercialSelect += "<option value='" + key + "'>" + claveCommercialArray[key] + "</option>";
            });
            commercialKey += claveCommercialSelect;

            commercialKey += "</select>";
        }


        // Load dynamic data and displays updated block
        jQuery('#quote .quote-job-picker').autocomplete({
            source: jobPicker,
            // triggers change event when selecting from the list, really important
            select: function (event, ui) {
                this.value = ui.item.value;
                jQuery(this).trigger('change');
                return false;
            }
        });

        // load info button if there is text for it
        if (typeof data.P_PROFESION_CLIENTE.WS.textoAyuda !== 'undefined' && data.P_PROFESION_CLIENTE.WS.textoAyuda != null) {
            jobLabel = data.P_PROFESION_CLIENTE.name + ' <i class="fas fa-info-circle" title="' + data.P_PROFESION_CLIENTE.WS.textoAyuda + '"></i>';
        } else {
            jobLabel = data.P_PROFESION_CLIENTE.name;
        }

        jQuery('#quote .quote-job-label').html(jobLabel);
        jQuery('#quote .quote-job-type-label').html(data.P_REGIMEN_SEG_SOCIAL.name);
        jQuery('#quote .product-extra-info .quote-job').html(jobSelect);
        jQuery('#quote .product-extra-info .quote-gender').html(genderSelect);
        jQuery('#quote .product-extra-info .quote-job-type').html(jobTypeSelect);

        jQuery('#quote .quote-birthdate-label').html(data.P_FECHA_NACIMIENTO_CLIENTE.name);
        jQuery('#quote .quote-gender-label').html(data.P_SEXO.name);
        jQuery('#quote .product-extra-info .quote-weight').prop("min", data.P_PESO.min);
        jQuery('#quote .product-extra-info .quote-weight').prop("max", data.P_PESO.max);
        jQuery('#quote .product-extra-info .quote-weight-label').html(data.P_PESO.name);
        jQuery('#quote .product-extra-info .quote-height').prop("min", data.P_TALLA.min);
        jQuery('#quote .product-extra-info .quote-height').prop("max", data.P_TALLA.max);
        jQuery('#quote .product-extra-info .quote-height-label').html(data.P_TALLA.name);

        extraFields = benefits + duration + commercialKey;
        jQuery('#quote .product-extra-info .quote-hidden-fields-wrapper .col').html(extraFields);


        //jQuery('#quote .product-extra-info .dynamic-content .row').html(output);
        jQuery('#quote #step-1 .loader-wrapper').hide();
        jQuery('header').fadeIn();
        jQuery('#quote #step-1 .product-extra-info').fadeIn();
        jQuery('footer').fadeIn();
    }

    // GENERAL - Display modal
    if (jQuery('#PMmodal').length) {

        // sets focus on modal
        /*
        jQuery('#PMmodal').on('shown.bs.modal', function () {
            jQuery('#PMmodal').trigger('focus')
        })
        */

        // Shows modal with specified content (accepts HTML)
        function displayModal(customClass, title, body, primaryButton, secondaryButton = "") {
            if (typeof customClass !== 'undefined' &&
                typeof title !== 'undefined' &&
                typeof body !== 'undefined' &&
                (typeof primaryButton !== 'undefined' || typeof secondaryButton !== 'undefined')) {

                jQuery('#PMmodal').removeClass(function (index, className) {
                    return (className.match(/(^|\s)custom-\S+/g) || []).join(' ');
                });
                jQuery('#PMmodal').addClass("custom-" + customClass);
                jQuery('#PMmodal .modal-title').html(title);
                jQuery('#PMmodal .modal-body').html(body);
                if (primaryButton.length > 1) {
                    jQuery('#PMmodal .btn-primary').html(primaryButton);
                    jQuery('#PMmodal .btn-primary').removeClass("d-none");
                }
                if (secondaryButton.length > 1) {
                    jQuery('#PMmodal .btn-secondary').html(secondaryButton);
                    jQuery('#PMmodal .btn-secondary').removeClass("d-none");
                }
                jQuery('#PMmodal').modal('show');
                jQuery('.modal-backdrop').toggleClass("show");
            } else {
                console.error("Not enough parameters for the modal");
            }

        }
    }

    // WIDGET - Triggers validation to enable next step button
    jQuery('.widget #step-1 .product-extra-info input,' +
        '.widget #step-1 .product-extra-info select')
        .on("input change click keyup", function () {
// validates fields

            if (jQuery(this).hasClass("quote-gender")) {
                if (jQuery(this).val() != "" && jQuery(this).val() != null) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            if (jQuery(this).hasClass("quote-job-picker")) {
                if (jQuery(".product-extra-info .quote-job-picker").length > 0) {
                    var i = 0;
                    var found = false;
                    jQuery(".product-extra-info .quote-job option").each(function () {
                        if (this.text == jQuery(".product-extra-info .quote-job-picker").val()) {
                            jQuery(".product-extra-info .quote-job").prop("selectedIndex", i)
                            jQuery(".product-extra-info .quote-job-picker").removeClass("invalid");
                            jQuery(".product-extra-info .quote-job-picker").addClass("valid");
                            found = true;
                        } else {
                            i++;
                        }
                    });
                    if (!found) {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            step1EnableNextButton();
        });

    // WIDGET - validates job type
    jQuery('.widget #step-1 .quote-job-type').change(function () {

        if (jQuery(this).children("option:selected").val() != null &&
            jQuery(this).children("option:selected").val() != "") {
            jQuery(this).removeClass("invalid");
            jQuery(this).addClass("valid");
        } else {
            jQuery(this).addClass("invalid");
            jQuery(this).removeClass("valid");
        }

        jQuery(".product-extra-info .quote-job-picker").change();

    });


    // WIDGET - validates birthdate
    jQuery('.widget #step-1 .quote-birthdate').change(function () {

        // validates fields
        // 5 years old minimum requirement
        var minutes = 1000 * 60;
        var hours = minutes * 60;
        var days = hours * 24;
        var years = days * 365.25;

        var fiveYears = 5 * Math.ceil(years);
        var timestampFive = Date.now() - fiveYears;

        var adultYears = 18 * Math.ceil(years);
        var timestampAdult = Date.now() - adultYears;

        // get input date and turn it into timestamp
        var dateText = jQuery(this).val();
        var inputDate = dateText.split("/");
        var inputDay =  inputDate[0];
        var inputMonth = inputDate[1];
        inputDate = new Date(inputDate[2], inputDate[1] - 1, inputDate[0], 0, 0, 0, 0);
        timestampInputDate = inputDate.valueOf();

        var dateDifferenceFive = timestampFive - timestampInputDate;
        var dateDifferenceAdult = timestampAdult - timestampInputDate;
        //console.log(dateDifference);

        // Basic validation
        var validInput = false;
        if( inputDay <= 31 && inputMonth <= 12){
            validInput = true;
        }


        if (jQuery(this).val().length == 10 && dateDifferenceFive > 0 && validInput) {
            if (dateDifferenceAdult > 0) {
                jQuery(this).removeClass("invalid");
                jQuery(this).addClass("valid");
            } else {
                // if it's underage we show modal and can't continue
                displayModal("health", lang["quote.modal.underage.title"], lang["quote.modal.underage.message"], lang["quote.modal.underage.button"]);
                jQuery(this).addClass("invalid");
                jQuery(this).removeClass("valid");
            }
        } else {
            jQuery(this).addClass("invalid");
            jQuery(this).removeClass("valid");
        }

        step1EnableNextButton();
    });

    // WIDGET - Validates step 1 and enables next step button
    function step1EnableNextButton() {
        allValid = true;
        jQuery('.widget #step-1 .product-extra-info input:visible,' +
            '.widget #step-1 .product-extra-info select:visible')
            .each(function () {
                if (!jQuery(this).hasClass("valid")) {
                    allValid = false;
                }
            });

        if (allValid) {
            jQuery('#step-1 .get-rates button').removeAttr("disabled");
        } else {
            // jQuery('#step-1 .get-rates button').attr("disabled", "disabled");
        }

        return allValid;
    }

    // WIDGET - get Rates and display info card
    jQuery('#step-1 .get-rates button').click(function () {

        jQuery('#quote #selected-product-info').hide();
        jQuery('#step-1 .quote-step.next').attr("disabled","disabled");
        jQuery('#quote #selected-product-info .dynamic-content').html("");

        // Runs validation on all fields
        jQuery('#quote .product-extra-info input, ' +
            '#quote .product-extra-info select')
            .change();

        if (step1EnableNextButton()) {

            jQuery('#quote .get-rates .loadingIcon').fadeIn();
            jQuery('#quote .form .loading-lock').fadeIn();
            jQuery('#quote .get-rates .quote-button').attr("disabled", "disabled");

            var url = "/get-data";
            var ws = "getRates";
            // var productor = jQuery("#quote-productor").val();
            var option = window.PMproductVariations.option;
            var productId = window.PMproductVariation;
            var profession = jQuery("#quote .quote-job").val();
            var jobType = jQuery("#quote .quote-job-type").val();
            var duration = jQuery('#quote .quote-duration').val();
            var commercialKey = jQuery('#quote .quote-commercial-key').val();
            var birthdate = jQuery("#quote .quote-birthdate").val();
            var gender = jQuery("#quote .quote-gender").val();
            var height = PMheight;
            var weight = PMweight;
            var enfCob = jQuery("#quote .quote-benefit:nth-child(1)").data("name");
            var enfSub = jQuery("#quote .quote-benefit:nth-child(1)").val();
            if (jQuery("#quote .quote-benefit:nth-child(2)").length) {
                var accCob = jQuery("#quote .quote-benefit:nth-child(2)").data("name");
                var accSub = jQuery("#quote .quote-benefit:nth-child(2)").val();
            } else {
                var accCob = null;
                var accSub = null;
            }
            if (jQuery("#quote .quote-benefit:nth-child(3)").length) {
                var hospCob = jQuery("#quote .quote-benefit:nth-child(3)").data("name");
                var hospSub = jQuery("#quote .quote-benefit:nth-child(3)").val();
            } else {
                var hospCob = null;
                var hospSub = null;
            }
            if (jQuery("#quote .quote-benefit:nth-child(4)").length) {
                var Cob4 = jQuery("#quote .quote-benefit:nth-child(4)").data("name");
                var Sub4 = jQuery("#quote .quote-benefit:nth-child(4)").val();
            } else {
                var Cob4 = null;
                var Sub4 = null;
            }
            if (jQuery("#quote .quote-benefit:nth-child(5)").length) {
                var Cob5 = jQuery("#quote .quote-benefit:nth-child(5)").data("name");
                var Sub5 = jQuery("#quote .quote-benefit:nth-child(5)").val();
            } else {
                var Cob5 = null;
                var Sub5 = null;
            }

            // TODO: add new LENGTH parameter so the results are correct
            //  waiting for client response on increased development time

            var i = 1;

            // Stores it to use later
            window.PMgetRatesData = {
                productor: PMproductor,
                option: option,
                productId: productId,
                profession: profession,
                jobType: jobType,
                duration: duration,
                commercialKey: commercialKey,
                birthdate: birthdate,
                gender: gender,
                height: height,
                weight: weight,
                enfCob: enfCob,
                enfSub: enfSub
            }


            jQuery.ajax({
                type: "POST",
                url: url,
                data: {
                    ws: ws,
                    productor: PMproductor,
                    option: option,
                    productId: productId,
                    profession: profession,
                    jobType: jobType,
                    duration: duration,
                    commercialKey: commercialKey,
                    birthdate: birthdate,
                    gender: gender,
                    height: height,
                    weight: weight,
                    enfCob: enfCob,
                    enfSub: enfSub,
                    accCob: accCob,
                    accSub: accSub,
                    hospCob: hospCob,
                    hospSub: hospSub,
                    Cob4: Cob4,
                    Sub4: Sub4,
                    Cob5: Cob5,
                    Sub5: Sub5,
                    entryChannel: PMentryChannel,
                    application: PMapplication,
                    u: PMu,
                    p: PMp
                },
                success: function (response) {
                    if (response['success'] == true) {
                        // TODO: there are products that send information in a different structure and won't work
                        quote_load_Rates(response.data);

                    } else {
                        console.error(response.e);
                        displayModal("health", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                        jQuery('#quote .get-rates .loadingIcon').hide();
                        jQuery('#quote .form .loading-lock').hide();
                        jQuery('#quote .get-rates .quote-button').removeAttr("disabled");
                    }
                },
                error: function (response) {
                    console.error(lang["WS.error"]);
                    displayModal("health", lang["quote.modal.error"], lang["WS.error"], lang["quote.modal.close"]);
                    jQuery('#quote .get-rates .loadingIcon').hide();
                    jQuery('#quote .form .loading-lock').hide();
                    jQuery('#quote .get-rates .quote-button').removeAttr("disabled");
                }
            });

            // TODO: getRates() and display info on .selected-product-info

        }
    });

    // WIDGET - Process rates and display the table
    function quote_load_Rates(data) {

        //console.log(data);
        window.PMrates = data;
        var paymentMethod = '';

        // Payment methods
        for (i = 0; i < data.quotes.length; i++) {

            switch (data.quotes[i].formaPago) {
                case 1:
                    description = lang["quote.annual.text"];
                    dataField = lang["quote.annual.field"];
                    break;
                case 2:
                    description = lang["quote.biannual.text"];
                    dataField = lang["quote.biannual.field"];

                    break;
                case 3:
                    description = lang["quote.quarterly.text"];
                    dataField = lang["quote.quarterly.field"];
                    break;
                case 4:
                    description = lang["quote.monthly.text"];
                    dataField = lang["quote.monthly.field"];
                    break;
            }

            paymentMethod += '<div class="col product-info-card text-center my-4 my-xl-5 widget-payment-method">';
            paymentMethod += '<div class="card border-white" data-payment-method="' + data.quotes[i].formaPago + '" data-payment-price="' + data.quotes[i].primaNetaAnual + '">';
            paymentMethod += '<h4>' + description + '</h4>';
            paymentMethod += '<div class="card-body text-center">';

            if( data.quotes[i].formaPago == 1) {
                paymentMethod += '<p class="card-text billing txt-dark-grey">' + lang['quote.productInfo.billing'] + '&nbsp;<span class="txt-navy-blue">' + data.quotes[i].primaTotalAnual + '€ </span></p>';
            }else{
                var finalPrice = parseFloat(data.quotes[i].primaNetaFraccionada.replace(/,/, '.')) + parseFloat(data.quotes[i].recargosImpuestos.replace(/,/, '.'));
                paymentMethod += '<p class="card-text billing txt-dark-grey">' + lang['quote.productInfo.billing'] + '&nbsp;<span class="txt-navy-blue">' + finalPrice.toFixed(2) + '€ </span></p>';
            }

            paymentMethod += '<p class="card-text billing txt-dark-grey legal-text">' + lang['widget.taxesIncluded'] + '</p>';
            /*
            if( data.quotes[i].formaPago != 1) {
                paymentMethod += '<p class="card-text billing txt-dark-grey">' + lang['quote.productInfo.billing'] + '&nbsp;&nbsp;<span class="txt-navy-blue">' + data.quotes[i].primaNetaFraccionada + '€ </span></p>';
            }else{
                paymentMethod += '<p class="card-text billing txt-dark-grey" style="visibility: hidden;">' + lang['quote.productInfo.billing'] + '&nbsp;&nbsp;<span class="txt-navy-blue">' + data.quotes[i].primaNetaFraccionada + '€ </span></p>';
            }*/
            // paymentMethod += '<p class="card-text billing-total txt-dark-grey">' + lang['quote.productInfo.billingTotal'] + '&nbsp;&nbsp;<span class="txt-navy-blue">' + data.quotes[i].primaNetaAnual + '€ </span></p>';
            paymentMethod += '</div>';
            paymentMethod += '</div>';
            paymentMethod += '</div>';

        }

        jQuery('#quote #selected-product-info .dynamic-content').html(paymentMethod);
        jQuery('#quote #selected-product-info').fadeIn();

        jQuery('#quote .get-rates .quote-button').hide();
        jQuery('#quote .quote-birthdate').attr("disabled", "disabled");
        jQuery('#quote .quote-job-picker').attr("disabled", "disabled");
        jQuery('#quote .quote-gender').attr("disabled", "disabled");
        jQuery('#quote .quote-job-type').attr("disabled", "disabled");
        jQuery('#quote .get-rates .loadingIcon').hide();
        jQuery('#quote .form .loading-lock').hide();
        jQuery('#quote .get-rates .quote-button').removeAttr("disabled");

        // scroll down
        jQuery([document.documentElement, document.body]).animate({
            scrollTop: jQuery("#selected-product-info").offset().top
        }, 1000);

    }

    // WIDGET - Select payment method
    jQuery("#step-1 #selected-product-info").on('click', ".widget-payment-method > .card", function (e) {
        jQuery('#step-1 #selected-product-info .widget-payment-method > .card').removeClass("selected");
        jQuery(this).addClass("selected");
        jQuery('#step-1 .quote-step.next').removeAttr("disabled");
    });

    // WIDGET - Stores step 1 data into JS
    function storeStep1Data() {
        window.PMwidgetStep1 = {
            productId: window.PMgetRatesData.productId,
            birthdate: jQuery('#step-1 .quote-birthdate').val(),
            gender: jQuery('#step-1 .quote-gender').val(),
            job: jQuery('#step-1 .quote-job').val(),
            jobType: jQuery('#step-1 .quote-job-type').val(),
            benefitName: jQuery('#step-1 .quote-benefit').data("name"),
            benefitValue: jQuery('#step-1 .quote-benefit').val(),
            durationName: jQuery('#step-1 .quote-duration').data("name"),
            durationValue: jQuery('#step-1 .quote-duration').val(),
            commercialKey: jQuery('#step-1 .quote-commercial-key').val(),
            paymentMethod: jQuery('.widget-payment-method .card.selected').data("payment-method"),
            paymentPrice: jQuery('#selected-product-info .selected').data("payment-price")
        }
    }

    // WIDGET - Step 1 next button
    jQuery('#step-1 .quote-step.next').click(function () {

        // Copy the card in the widget to show on next steps.
        jQuery('#quote #product-info-widget').html( jQuery('#quote #selected-product-info .product-info-card').html() );


        if (step1EnableNextButton()) {

            storeStep1Data();

            var nombreProducto;
            switch( window.PMwidgetStep1.productId ){
                case 27:
                    nombreProducto = "Hospitalización BASIC";
                    break;
                case 40:
                    nombreProducto = "Hospitalización MEDIUM";
                    break;
                case 41:
                    nombreProducto = "Hospitalización PROTECT";
                    break;
            }

            // Google Enhanced ecommerce
            dataLayer.push({
                'event': 'addToCart',
                'ecommerce': {
                    'currencyCode': 'EUR',
                    'add': {
                        'products': [{
                            'name': nombreProducto,
                            'id': window.PMwidgetStep1.productId,
                            'price': window.PMwidgetStep1.paymentPrice,
                            'quantity': 1
                        }]
                    }
                }
            });

            jQuery('#step-2 .quote-birthdate-show').val(jQuery('#step-1 .quote-birthdate').val());
            jQuery('#step-2 .quote-job-show').val(jQuery('#step-1 .quote-job-picker').val());
            jQuery('#step-2 .quote-gender-show').val(jQuery('#step-1 .quote-gender option:selected').text());

            jQuery('#step-4 .widget-payment-method .card .card-body .payment-method').html(jQuery('.widget-payment-method .card.selected h4').html());
            jQuery('#step-4 .widget-payment-method .card .card-body .payment-amount').html(jQuery('.widget-payment-method .card.selected .card-body .billing').html());

            jQuery('#step-1').hide();
            jQuery('#step-2').fadeIn();
        }
    });

    // WIDGET - Step 1 back button
    jQuery('#step-1 .quote-step.previous').click(function () {

        jQuery('#quote #selected-product-info').hide();
        jQuery('#quote .get-rates .quote-button').fadeIn();
        jQuery('#quote .quote-birthdate').removeAttr("disabled");
        jQuery('#quote .quote-job-picker').removeAttr("disabled");
        jQuery('#quote .quote-gender').removeAttr("disabled");
        jQuery('#quote .quote-job-type').removeAttr("disabled");
        jQuery('#quote .get-rates .quote-button').removeAttr("disabled");
    });

            // WIDGET - toggles check attribute on clicked toggle button
    jQuery("#quote .btn-group-toggle .btn").click(function (e) {
        jQuery(this).parent().find(".btn input").removeAttr("checked");
        jQuery(this).find("input").attr("checked", "checked");
    });

    // WIDGET - Phone max numbers
    jQuery("#quote .quote-phone").on("keyup", function (e) {
        if (jQuery(this).val().length > 9) {
            jQuery(this).val(jQuery(this).val().substring(0, 9))
        }
    });

    // WIDGET - postal code max numbers
    jQuery("#quote .quote-postal-code, " +
        "#quote .quote-company-postal-code," +
        "#quote .quote-legal-entity-postal-code").on("keyup", function (e) {

        if (jQuery(this).val().length > 5) {
            jQuery(this).val(jQuery(this).val().substring(0, jQuery(this).val().length - 1))
        }

        if (jQuery(this).hasClass("quote-postal-code")) {
            currentClass = "quote-postal-code";
        }
        if (jQuery(this).hasClass("quote-company-postal-code")) {
            currentClass = "quote-company-postal-code";
        }
        if (jQuery(this).hasClass("quote-legal-entity-postal-code")) {
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
                    postalCode: postalCode,
                    u: PMu,
                    p: PMp
                },
                success: function (response) {
                    if (response['success'] == true) {
                        switch (currentClass) {

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
                        console.error(response.e);
                    }
                },
                error: function (response) {
                    console.error(lang["WS.error"]);
                }
            });

        }
    });

    // WIDGET - resets city and province inputs
    function resetQuoteCityProvince(element) {

        switch (element) {

            case "quote-postal-code":
                jQuery('#quote .quote-city').html("");
                jQuery('#quote .quote-city').attr("disabled", "disabled");
                jQuery('#quote .quote-province').html("");
                jQuery('#quote .quote-province').attr("disabled", "disabled");
                break;

            case "quote-company-postal-code":
                jQuery('#quote .quote-company-city').html("");
                jQuery('#quote .quote-company-city').attr("disabled", "disabled");
                jQuery('#quote .quote-company-province').html("");
                jQuery('#quote .quote-company-province').attr("disabled", "disabled");
                break;

            case "quote-legal-entity-postal-code":
                jQuery('#quote .quote-legal-entity-city').html("");
                jQuery('#quote .quote-legal-entity-city').attr("disabled", "disabled");
                jQuery('#quote .quote-legal-entity-province').html("");
                jQuery('#quote .quote-legal-entity-province').attr("disabled", "disabled");
                break;
        }

    }

    // WIDGET - loads city and province when postal code is ok
    function quote_load_cityProvince(data, element) {

        citiesSelect = "";
        provincesSelect = "";
        citiesArray = data.cities;
        provincesArray = data.provinces;

        Object.keys(citiesArray).forEach(function (key) {
            citiesSelect += "<option value='" + key + "'>" + citiesArray[key] + "</option>";
        });

        Object.keys(provincesArray).forEach(function (key) {
            provincesSelect += "<option value='" + key + "'>" + provincesArray[key] + "</option>";
        });

        switch (element) {

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

    // WIDGET - Validates extra info fields
    jQuery('#quote #personal-info input, ' +
        '#quote #personal-info select')
        .on("input change click keyup", function () {

            // validates input texts
            if (jQuery(this).hasClass("quote-first-name") ||
                jQuery(this).hasClass("quote-last-name") ||
                jQuery(this).hasClass("quote-address") ||
                jQuery(this).hasClass("quote-company-name") ||
                jQuery(this).hasClass("quote-company-address") ||
                jQuery(this).hasClass("quote-legal-entity-name") ||
                jQuery(this).hasClass("quote-legal-entity-address")) {
                if (jQuery(this).val().length > 2) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            if (jQuery(this).hasClass("quote-legal-entity-id")) {
                if (isValidCif(jQuery(this).val())) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            if (jQuery(this).hasClass("quote-personal-id")) {
                if (isValidDoc(jQuery(this).val())) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
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

            if (jQuery(this).hasClass("quote-phone")) {
                if (jQuery(this).val().length == 9) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }

            if (jQuery(this).hasClass("quote-postal-code")) {
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

            if (jQuery(this).hasClass("quote-company-postal-code")) {
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

            if (jQuery(this).hasClass("quote-legal-entity-postal-code")) {
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
                if (regex.test(jQuery(this).val())) {
                    jQuery(this).removeClass("invalid");
                    jQuery(this).addClass("valid");
                } else {
                    jQuery(this).addClass("invalid");
                    jQuery(this).removeClass("valid");
                }
            }
            //console.log( jQuery(this).attr("class") );
            // step2EnableNextButton();
        });

    // WIDGET - checks if NIF/NIE is valid
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

    // WIDGET - checks if CIF is valid
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

    // WIDGET - special check for fields
    jQuery('#quote #personal-info .quote-address-type,' +
        '#quote #personal-info .quote-job-location,' +
        '#quote #personal-info .quote-company-address-pick,' +
        '#quote #personal-info .quote-company-address-type,' +
        '#quote #personal-info .quote-legal-entity-address-type').change(function () {

        if (jQuery(this).children("option:selected").val() != null &&
            jQuery(this).children("option:selected").val() != "") {
            jQuery(this).removeClass("invalid");
            jQuery(this).addClass("valid");
        } else {
            jQuery(this).addClass("invalid");
            jQuery(this).removeClass("valid");
        }

    });

    // WIDGET - toggles legal entity extra info
    jQuery("#quote .quote-legal-entity-type.legal-entity").click(function (e) {
        jQuery("#quote .legalEntityInfo").fadeIn();
        step2EnableNextButton();
    });
    jQuery("#quote .quote-legal-entity-type.natural-person").click(function (e) {
        jQuery("#quote .legalEntityInfo").hide();
        step2EnableNextButton();
    });

    // WIDGET - Validates extra info and enables getRate button
    function step2EnableNextButton() {
        allValid = true;
        jQuery('#quote #personal-info input:visible, ' +
            '#quote #personal-info select:visible').change();
        jQuery('#quote #personal-info input:visible, ' +
            '#quote #personal-info select:visible')
            .each(function () {
                if (!jQuery(this).hasClass("valid")) {
                    allValid = false;
                    // console.log( "FAIL | " + this.className.split(' ')[2] );
                } else {
                    // console.log( "OK | " + this.className.split(' ')[2] );
                }
            });
        //console.log( "------------------------");

        if (allValid) {
            jQuery('#step-2 .quote-step.next').removeAttr("disabled");
        } else {
           //  jQuery('#step-2 .quote-step.next').attr("disabled", "disabled");
            jQuery([document.documentElement, document.body]).animate({
                scrollTop: jQuery(".invalid").offset().top -50
            }, 1000);
        }

        return allValid;
    }

    // WIDGET - Step 2 next button
    jQuery('#step-2 .quote-step.next').click(function () {
        // Runs validation on all fields
        jQuery('#quote #personal-info input:visible, ' +
            '#quote #personal-info select:visible')
            .change();

        step2EnableNextButton();
    });

    // WIDGET - Step 2 back button
    jQuery('#step-2 .quote-step.previous').click(function () {

        jQuery('#step-2').hide();
        jQuery('#step-1').fadeIn();
    });

    // WIDGET - selecting another city updates province
    jQuery("#quote .quote-city").change(function () {
        jQuery("#quote .quote-province").prop("selectedIndex", jQuery(this).prop("selectedIndex"))
    });
    jQuery("#quote .quote-company-city").change(function () {
        jQuery("#quote .quote-company-province").prop("selectedIndex", jQuery(this).prop("selectedIndex"))
    });

    // WIDGET - show hidden fields when OTHER is selected
    jQuery("#step-2 .quote-company-address-pick").change(function () {
        if (jQuery(this).val() == companyAddressTypeOthers) {
            jQuery(".companyAddressWrapper").removeClass("d-none").addClass("d-flex");
            jQuery("#compAddressType, #companyAddress, #companyState, #companyPostalCode, #companyCity").attr("required", true);
        } else {
            jQuery(".companyAddressWrapper").removeClass("d-flex").addClass("d-none");
            jQuery("#compAddressType, #companyAddress, #companyState, #companyPostalCode, #companyCity").attr("required", false);
        }
    });

    // WIDGET - step 2 previous button
    jQuery('#quote #step-4 .step-buttons .quote-step.previous').click(function (e) {

        jQuery('#step-4').hide();
        jQuery('#step-2').fadeIn();
    });

    // WIDGET - step 2 next button
    jQuery('#quote #step-2 .step-buttons .quote-step.next').click(function (e) {
        if (step2EnableNextButton()) {
            storeStep2Data();
            jQuery('#quote #step-4 .loader-wrapper').show();

            jQuery('#step-2').hide();


                jQuery('#quote #step-3 .loader-wrapper').show();

                // Retrieves health form data from WS
                var url = "/get-data";
                var ws = "getHealthForm";
                var commercialkey = window.PMwidgetStep1.commercialKey;

                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        ws : ws,
                        productor : PMproductor,
                        product : PMproduct,
                        commercialKey : commercialkey,
                        u: PMu,
                        p: PMp

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

    // WIDGET - Saves step 2 data into JS variable
    function storeStep2Data() {
        window.PMwidgetStep2 = {
            firstName: jQuery('#quote .quote-first-name').val(),
            lastName: jQuery('#quote .quote-last-name').val(),
            personalId: jQuery('#quote .quote-personal-id').val(),
            email: jQuery('#quote .quote-email').val(),
            phone: jQuery('#quote .quote-phone').val(),
            documentationLanguage: jQuery('#quote .quote-documentation-language').val(),
            addressType: jQuery('#quote .quote-address-type').val(),
            address: jQuery('#quote .quote-address').val(),
            postalCode: jQuery('#quote .quote-postal-code').val(),
            city: jQuery('#quote .quote-city').val(),
            province: jQuery('#quote .quote-province').val(),
            companyName: jQuery('#quote .quote-company-name').val(),
            jobLocation: jQuery('#quote .quote-job-location').val(),
            companyAddressPick: jQuery('#quote .quote-company-address-pick').val(),

            companyAddressType: jQuery('#quote .quote-company-address-type').val(),
            companyAddress: jQuery('#quote .quote-company-address').val(),
            companyPostalCode: jQuery('#quote .quote-company-postal-code').val(),
            companyCity: jQuery('#quote .quote-company-city').val(),
            companyProvince: jQuery('#quote .quote-company-province').val(),

            anotherInsurance: jQuery('#quote .quote-another-insurance.active > input').val(),

            legalEntityType: jQuery('#quote .quote-legal-entity-type.active').data("person-type"),
            legalEntityName: jQuery('#quote .quote-legal-entity-name').val(),
            legalEntityId: jQuery('#quote .quote-legal-entity-id').val(),
            legalEntityEmail: jQuery('#quote .quote-legal-entity-email').val(),
            legalEntityAddressType: jQuery('#quote .quote-legal-entity-address-type').val(),
            legalEntityAddress: jQuery('#quote .quote-legal-entity-address').val(),
            legalEntityPostalCode: jQuery('#quote .quote-legal-entity-postal-code').val(),
            legalEntityCity: jQuery('#quote .quote-legal-entity-city').val(),
            legalEntityProvince: jQuery('#quote .quote-legal-entity-province').val()
        }
    }

    // WIDGET - Loads html code of health form
    function quote_load_healthForm(data){
        //console.log(data);
        window.PMwidgetHealthFormId = data.id;
        jQuery('#quote #health-form .dynamic-content').html(data.html);
        if( jQuery( ".datetimepickerHealth input" ).length ){
            jQuery( ".datetimepickerHealth input" ).datepicker({ maxDate: '-1D', changeMonth: true, changeYear: true, yearRange: "-70:+0" });
            jQuery( ".datetimepickerHealth input" ).datepicker("option", jQuery.datepicker.regional[ "{{ $currentLanguage  }}" ]);
        }
        jQuery('#quote #step-3 .step-buttons .quote-step.next').removeAttr("disabled");
        jQuery('#quote #step-3 .loader-wrapper').hide();
    }

    // WIDGET - Health form displays hidden sub questions
    jQuery("#quote #health-form").on('click', ".answer-radio-group label", function (e) {
        let input = jQuery(this).find("input");
        let id = input.data("id");
        if ( input.val() == "SI" ) {
            enableAnswer(".answer-wrapper[data-id=" + id +"]", input.attr("required"));
        } else {
            disableAnswer(".answer-wrapper[data-id=" + id +"]");
        }

    });

    // WIDGET - Health form displays hidden sub questions
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

    // WIDGET - Health form displays hidden sub questions
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

    // WIDGET - Health form helper
    function enableAnswer(selector, required) {
        jQuery(selector).addClass("d-block d-lg-flex").removeClass("d-none");
        if (required) {
            jQuery(selector).find("input, select, textarea").attr("required", true);
        }
    }

    // WIDGET - Health form helper
    function disableAnswer(selector) {
        jQuery(selector).addClass("d-none").removeClass("d-block d-lg-flex");
        jQuery(selector).find("input, select, textarea").attr("required", false);
    }

    // WIDGET - Health form helper
    function setAnswers(selector, value) {

        if (value) {
            jQuery(selector + " label").removeClass("active");
            jQuery(selector + " input").prop("checked", false);
        } else {
            jQuery(selector + " [value=NO]").click();
        }
    }

    // WIDGET - Detects changes on visible extra fields
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
                    if (jQuery(this).val().length > 9) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }
                if (jQuery(this).is("textarea")) {
                    if (jQuery(this).val().length > 2) {
                        jQuery(this).removeClass("invalid");
                        jQuery(this).addClass("valid");
                    } else {
                        jQuery(this).addClass("invalid");
                        jQuery(this).removeClass("valid");
                    }
                }

                step3EnableNextButton();
            });

    // WIDGET - Validates all visible fields (extra fields when selecting yes)
    function step3EnableNextButton(){
        allValid = true;
        jQuery( "#health-form input:visible, " +
            "#health-form textarea:visible, " +
            "#health-form select:visible" )
            .each(function() {
                if( !jQuery(this).hasClass("valid") ){
                    allValid = false;
                }
            });

        if( allValid ){
            jQuery('#step-3 .quote-step.next').removeAttr("disabled");
        }else{
            jQuery('#step-3 .quote-step.next').attr("disabled", "disabled");
        }

        return allValid;
    }

    // WIDGET - step 3 previous button
    jQuery('#quote #step-3 .step-buttons .quote-step.previous').click(function(e){
        jQuery('#step-3').hide();
        resetHealthForm();
        jQuery('#step-2').fadeIn();
    });

    function resetHealthForm(){
        jQuery('#quote #health-form .dynamic-content').html("");
        jQuery('#quote #step-3 .step-buttons .quote-step.next').attr("disabled","disabled");
    }

    // WIDGET - step 3 next button
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

            jQuery.ajax({
                type: "POST",
                url: url,
                data: {
                    ws: ws,
                    productor : PMproductor,
                    product : PMproduct,
                    formId: window.PMwidgetHealthFormId,
                    formData: formData,
                    u: PMu,
                    p: PMp

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

    // WIDGET - step 4 previous button
    jQuery('#step-4 .step-buttons .previous').click(function () {

        jQuery('#step-4').hide();
        jQuery('#step-2').hide();

        if (healthFormRequired) {
            jQuery('#step-3').fadeIn();
            console.log(3);
        }else{
            jQuery('#step-2').fadeIn();
            console.log(2);
        }
    });

    // WIDGET - step 4 next button
    jQuery('#step-4 .step-buttons .next').click(function () {

        // Runs validation on all fields
        jQuery('#quote .product-extra-info input, ' +
            '#quote .product-extra-info select')
            .change();

        storeStep4Data();

        if (step4EnableNextButton()) {

            jQuery('#step-4 .step-buttons .next .loadingIcon').fadeIn();
            jQuery('#quote .quote-bank-account').attr("disabled", "disabled");
            jQuery('#step-4 .step-buttons .previous').attr("disabled", "disabled");
            jQuery('#step-4 .step-buttons .next').attr("disabled", "disabled");

            var url = "/get-data";
            var ws = "submitPolicy";

            var productor = PMproductor;
            var option = window.PMproductVariations.option;
            var productId = PMproductVariation;
            var profession = window.PMwidgetStep1.job;
            var birthdate = window.PMwidgetStep1.birthdate;
            var gender = window.PMwidgetStep1.gender;
            var height = PMheight;
            var weight = PMweight;
            var paymentMethod = window.PMwidgetStep1.paymentMethod;

            var name = window.PMwidgetStep2.firstName;
            var surname = window.PMwidgetStep2.lastName;
            var docId = window.PMwidgetStep2.personalId;
            // var docType  =  Generated on PMWShandler
            var email = window.PMwidgetStep2.email;
            var phone = window.PMwidgetStep2.phone;
            var insuredLanguage = window.PMwidgetStep2.documentationLanguage;

            var streetType = window.PMwidgetStep2.addressType;
            var address = window.PMwidgetStep2.address;
            var postalCode = window.PMwidgetStep2.postalCode;
            var city = window.PMwidgetStep2.city;
            var province = window.PMwidgetStep2.province; // is not sent;

            var companyName = window.PMwidgetStep2.companyName;
            var workLocationType = window.PMwidgetStep2.jobLocation;
            var companyAddressType = window.PMwidgetStep2.companyAddressPick;

            var companyStreetType = window.PMwidgetStep2.companyAddressType;
            var companyAddress = window.PMwidgetStep2.companyAddress;
            var companyPostalCode = window.PMwidgetStep2.companyPostalCode;
            var companyCity = window.PMwidgetStep2.companyCity;
            var companyProvince = window.PMwidgetStep2.companyProvince; // is not sent;


            var hasMorePolicies = window.PMwidgetStep2.anotherInsurance;
            /*
            NOT NEEDED FOR THESE PRODUCTS
            "extraCompanyName" - name of company where person has other policies
            "extraInsurancePrice" - price of other policies
            "extraInsuranceDate" - expiration date of other policies
            */

            var holderType = window.PMwidgetStep2.legalEntityType;

            var holderName = window.PMwidgetStep2.legalEntityName;
            var holderDocId = window.PMwidgetStep2.legalEntityId
            // var holderDocType  =   Generated on PMWShandler;
            var holderEmail = window.PMwidgetStep2.legalEntityEmail;
            var holderStreetType = window.PMwidgetStep2.legalEntityAddressType;
            var holderAddress = window.PMwidgetStep2.legalEntityAddress;
            var holderCity = window.PMwidgetStep2.legalEntityCity;
            var holderProvince = window.PMwidgetStep2.legalEntityProvince;

            // no inputs but we use the personal data to fill it
            var holderSurname = window.PMwidgetStep2.lastName;
            var holderBirthdate = window.PMwidgetStep1.birthdate;
            var holderPhone = window.PMwidgetStep2.phone;
            var holderLanguage = window.PMwidgetStep2.documentationLanguage;

            var ibanCountry = window.PMquoteStep4.ibanCountry;
            var ibanControl = window.PMquoteStep4.ibanControl;
            var ibanEntity = window.PMquoteStep4.ibanEntity;
            var ibanOffice = window.PMquoteStep4.ibanOffice;
            var ibanDc = window.PMquoteStep4.ibanDc;
            var ibanAccount = window.PMquoteStep4.ibanAccount;

            var IBAN = ibanCountry + ibanControl + ibanEntity + ibanOffice + ibanDc + ibanAccount;

            // var date  = window.PMwidgetStep2.phone; To be sent by PMWShandler
            var dataPreferences = "N";
            var commercialkey = window.PMwidgetStep1.commercialKey;
            var entryChannel = PMentryChannel;
            var application = PMapplication;

            var coverageData = window.PMrates.coverages;


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
                    productor: productor,
                    option: option,
                    productId: productId,
                    profession: profession,
                    birthdate: birthdate,
                    gender: gender,
                    height: height,
                    weight: weight,
                    paymentMethod: paymentMethod,
                    name: name,
                    surname: surname,
                    docId: docId,
                    email: email,
                    phone: phone,
                    insuredLanguage: insuredLanguage,
                    streetType: streetType,
                    address: address,
                    postalCode: postalCode,
                    city: city,
                    province: province,
                    companyName: companyName,
                    workLocationType: workLocationType,
                    companyAddressType: companyAddressType,
                    companyStreetType: companyStreetType,
                    companyAddress: companyAddress,
                    companyPostalCode: companyPostalCode,
                    companyCity: companyCity,
                    companyProvince: companyProvince,
                    hasMorePolicies: hasMorePolicies,
                    holderType: holderType,
                    holderName: holderName,
                    holderDocId: holderDocId,
                    holderEmail: holderEmail,
                    holderStreetType: holderStreetType,
                    holderAddress: holderAddress,
                    holderCity: holderCity,
                    holderProvince: holderProvince,
                    holderSurname: holderSurname,
                    holderBirthdate: holderBirthdate,
                    holderPhone: holderPhone,
                    holderLanguage: holderLanguage,
                    IBAN: IBAN,
                    dataPreferences: dataPreferences,
                    commercialKey: commercialkey,
                    entryChannel: entryChannel,
                    application: application,
                    coverageData: coverageData,
                    u: PMu,
                    p: PMp
                },
                success: function (response) {
                    if (typeof response.e === 'undefined') {
                        quote_load_submitPolicy(response.data);
                    } else {
                        displayModal("health", lang["quote.modal.pending"], response.e, lang["quote.modal.close"]);
                        jQuery('#step-4 .step-buttons .next .loadingIcon').hide();
                        jQuery('#quote .quote-bank-account').removeAttr("disabled");
                        jQuery('#step-4 .step-buttons .previous').attr("disabled", "disabled");
                        jQuery('#step-4 .step-buttons .next').removeAttr("disabled");
                    }
                },
                error: function (response) {
                    displayModal("health", lang["quote.modal.error"], response.e, lang["quote.modal.close"]);
                    jQuery('#step-4 .step-buttons .next .loadingIcon').hide();
                    jQuery('#quote .quote-bank-account').removeAttr("disabled");
                    jQuery('#step-4 .step-buttons .previous').attr("disabled", "disabled");
                    jQuery('#step-4 .step-buttons .next').removeAttr("disabled");
                }
            });

        }
    });

    // WIDGET - Loads returned data and load next step
    function quote_load_submitPolicy(data) {
        // Stores this info in a global array to access it later on
        window.PMsubmitPolicy = data;


        // Tracking
        if (typeof data.P_CODIGO_ESTADO !== 'undefined' && data.P_CODIGO_ESTADO == "F") {
            // OK
            var nombreProducto;
            switch( window.PMwidgetStep1.productId ){
                case 27:
                    nombreProducto = "Hospitalización BASIC";
                    break;
                case 40:
                    nombreProducto = "Hospitalización MEDIUM";
                    break;
                case 41:
                    nombreProducto = "Hospitalización PROTECT";
                    break;
            }
            dataLayer.push({
                'ecommerce': {
                    'purchase': {
                        'actionField': {
                            'id': data.P_NUMERO_SOLICITUD,
                            'revenue': window.PMwidgetStep1.paymentPrice
                        },
                        'products': [{
                            'name': nombreProducto,
                            'id': window.PMwidgetStep1.productId,
                            'price': window.PMwidgetStep1.paymentPrice,
                            'quantity': 1
                        }]
                    }
                }
            });


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
                            /*
                            jQuery("#step-5 .hand-write-method").fadeIn();
                            jQuery("#step-5 .hand-write-method-button").show();
                            quote_load_policyRequestDownload(data.P_NUMERO_SOLICITUD);
                            */
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
                                var productor = PMproductor;
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
                                        format : format,
                                        u: PMu,
                                        p: PMp
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

    // WIDGET - pick signing method
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

    // WIDGET - step 5 previous button
    jQuery('#quote #step-5 .step-buttons .quote-step.previous').click(function(e){
        resetBankAccount();
        jQuery('#step-5').hide();
        jQuery('#step-4').fadeIn();
    });

    // WIDGET - pick signing method
    jQuery("#quote .quote-insurance-policy-upload").change(function(e){
        jQuery("#quote .quote-insurance-policy-upload-button").hide();
        jQuery("#quote .quote-insurance-policy-upload-button").fadeIn();
    });

    // WIDGET - step 5 next button
    jQuery('#quote #step-5 .step-buttons .quote-step.next').click(function(e){
        window.location.href = jQuery("#top-menu .nav-item:first-child a").attr("href");
    });

    // WIDGET - loads logalty
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
                doc: doc,
                u: PMu,
                p: PMp
            },
            success: function (response) {
                if (response['success'] == true) {
                    //console.log( response.data );
                    jQuery("#logaltyFrame").attr("src",response.data);
                    setTimeout(function() {
                        jQuery("#step-5 .loader-wrapper").hide();
                    }, 4000);

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

    // NOT ADAPTED FOR WIDGET (Firma manuscrita desactivada)
    // QUOTE - gets the policty request to download and sign
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

    }

});
