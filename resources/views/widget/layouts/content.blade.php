
<script>
    @php
        /*
        TEST URLS
        https://imediador.wldev.es/widget?p=D4XTP9L1JHMGMM2VDRDJ (iframe final)
        https://imediador.wldev.es/widget?p=58448VDL2O9C3DG5DBY1
        https://imediador.wldev.es/widget?p=FC4Z57XC439P6192J2D2
        https://imediador.wldev.es/widget?p=D4XTP9L1JHMGMM2VDRDJ&dev=1 (iframe debugging)

        HTML CODE IFRAME
        <div id="PMmodalWidget" style="display: none;">
            <div class="wrapper">
                <div class="close-button">X</div>
                <iframe class="PMwidget basic" src="https://imediador.wldev.es/widget?p=D4XTP9L1JHMGMM2VDRDJ"></iframe>
                <iframe class="PMwidget medium" src="https://imediador.wldev.es/widget?p=58448VDL2O9C3DG5DBY1"></iframe>
                <iframe class="PMwidget protect" src="https://imediador.wldev.es/widget?p=FC4Z57XC439P6192J2D2"></iframe>
                <link rel="stylesheet" href="https://imediador.wldev.es/widget/style.css">
                <script src="https://imediador.wldev.es/widget/custom.js"></script>
            </div>
        </div>

        NIF: 65539037P
        CIF: B64052574
        IBAN: ES6000491500051234567892

        */

        // Clears session to prevent issues when also working with imediador directly
        App::setLocale('es');
        $title = __('widget.title');
        $pm = new \App\Http\Middleware\PMWShandler();
        $currentLanguage = App::getLocale();

        //if( !isset($_GET['dev']) ){
        //   app('debugbar')->disable();
        //   config(['app.debug' => false]);
        //}
        // Needs to load User, Pass (and productor) from GET
        if( !isset($_GET['p']) || strlen($_GET['p']) < 3 ){
           die();
        }

        // Loads quote info using token
        $pm->getAccessData( $_GET['p'] );
        //app('debugbar')->info('getAccessData');
        //app('debugbar')->info($pm);
        $pm->validateUser( Session::get('widget.productVariation') );
        //app('debugbar')->info('validateUser');
        //app('debugbar')->info($pm);

        // Passes user and pass to JS to send as parameters for Cross-Domain widget

        echo "var PMu = '" . Session::get('login.user') . "';\n";
        echo "var PMp = '" . Session::get('login.pass') . "';\n";
        echo "var PMproductor = " . Session::get('widget.productor') . ";\n";
        echo "var PMproduct = " . Session::get('widget.product') . ";\n";
        //echo "var PMproduct = 'ÉLITE-PM PROFESIONAL';\n";
        echo "var PMproductVariation = " . Session::get('widget.product') . ";\n";

        if( Session::has('extraInfo.datosSalida.P_CABECERA') && Session::get('extraInfo.datosSalida.P_CABECERA') != "" ){
            echo "var PMheader = '" . Session::get('extraInfo.datosSalida.P_CABECERA') . "';\n";
        }
        if( Session::has('extraInfo.datosSalida.P_PIE') && Session::get('extraInfo.datosSalida.P_PIE') != "" ){
            echo "var PMfooter = '" . Session::get('extraInfo.datosSalida.P_PIE') . "';\n";
        }

        // User Info form data load
        $personTypes = $pm->getPersonTypes();
        $paymentMethods = $pm->getPaymentMethods();
        $addressTypes = $pm->getAddressTypes();
        $companyAddressTypes = $pm->getCompanyAddressTypes();
        $jobLocationTypes = $pm->getWorkLocationTypes();
        $languages = $pm->getLanguages();

    @endphp
    var PMweight = 80;
    var PMheight = 180;
    var companyAddressTypeOthers = "O";
    var PMentryChannel  = "WG";
    var PMapplication  = "WIDGETS";


</script>
<div id="quote" class="widget">
    <form autocomplete="off">
        <section id="step-1" class="pb-2 row">
            <div class="form col">
                <div class="loading-lock" style="display: none;"></div>
                <div class="container p-0">
                    <div class="product-extra-info content w-100" style="display: none;">
                        <h4>Contenido dinámico</h4>
                        <div class="separator thin bg-dark-grey"></div>
                        <div id="inputWidget" class="dynamic-content row pt-3 d-flex">
                            @php
                                // Dynamically filled with JS
                            @endphp

                        </div>
                    </div>

                    <div id="selected-product-info" class="pb-5 row" style="display: none;" >

                        <div class="col wrapper mt-5">
                            <h4 class="text-center mx-auto mt-4">{{ __('widget.pickPayment') }}</h4>
                            <div class="row dynamic-content">
                                @php
                                    // Dynamically filled with JS
                                @endphp
                            </div>
                            <div class="row my-4">
                                <div class="col text-center">
                                    <button class="quote-step previous bg-white txt-dark-grey bold py-3 px-3 border-0 rounded mt-0 position-relative font-weight-bold">
                                        {{ __('quote.stepPrevious') }}
                                    </button>
                                </div>
                                <div class="col text-center">
                                    <button class="quote-step next bg-lime-yellow text-white bold py-3 px-3 border-0 rounded position-relative font-weight-bold">
                                        {{ __('quote.stepBuy') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="loader-wrapper w-100 pt-5 text-center">
                        <i class="fas fa-circle-notch fa-spin fa-2x txt-navy-blue"></i>
                    </div>
                </div>
            </div>
            {{-- Modalidad de pago y franquicia vienen de WS --}}
        </section>


        <section id="step-2" class="pb-2 row" style="display: none;">
            <div class="col">
                <div class="container">

                    <div class="row">
                        <div id="personal-info" class="col p-0">
                            <h4 class="m-auto text-center">{{ __('quote.personalInfo') }}</h4>

                            <div class="row pt-lg-3 pt-xl-3">
                                <div class="col-12 col-lg-4">
                                    <label for="quote-first-name" class="quote-first-name-label mb-1">{{ __('text.firstName') }}</label>
                                    <input type="text" class="form-control w-100 quote-first-name" name="quote-first-name" required maxlength="50">
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-last-name" class="quote-last-name-label mb-1">{{ __('text.lastName') }}</label>
                                    <input type="text" class="form-control w-100 quote-last-name" name="quote-last-name" required maxlength=50>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-personal-id" class="quote-personal-id-label mb-1">{{ __('quote.personalId') }}</label>
                                    <input type="text" class="form-control w-100 quote-personal-id" name="quote-personal-id" required maxlength="9">
                                </div>
                            </div>
                            <div class="row pt-lg-3 pt-xl-3">
                                <div class="col-12 col-lg-4">
                                    <label for="birthdate" class="quote-birthdate-label mb-1"></label>
                                    <input type="text" class="form-control w-100 quote-birthdate-show valid" name="quote-birthdate-show" required maxlength=10 disabled="disabled">
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-job-show" class="quote-job-label mb-1"></label>
                                    <input type="text" class="form-control w-100 quote-job-show valid" name="quote-job-show" disabled="disabled" required>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-gender-show" class="quote-gender-label mb-1"></label>
                                    <input type="text" class="form-control w-100 quote-gender-show valid" name="quote-gender-show" disabled="disabled" required>
                                </div>
                            </div>
                            <div class="row pt-lg-3 pt-xl-3">
                                <div class="col-12 col-lg-4">
                                    <label for="quote-email" class="quote-email-label mb-1">{{ __('quote.email') }}</label>
                                    <input type="email" class="form-control w-100 quote-email" name="quote-email" required maxlength="50">
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-phone" class="quote-phone-label mb-1">{{ __('quote.phone') }}</label>
                                    <input type="number" class="form-control w-100 quote-phone" name="quote-phone" required max="799999999" min="600000000">
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-documentation-language" class="quote-documentation-language-label mb-1">{{ __('quote.documentationLanguage') }}</label>
                                    <select class="form-control w-100 quote-documentation-language valid" name="quote-documentation-language" required>
                                        <?php
                                        foreach ($languages as $key => $value) {
                                            echo '<option value="' . $key . '">' . $value . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row pt-lg-3 pt-xl-5">
                                <div class="col-12 col-lg-4">
                                    <label for="quote-address-type" class="quote-address-type-label mb-1">{{ __('quote.addressType') }}</label>
                                    <select class="form-control w-100 quote-address-type" name="quote-address-type" required>
                                        <option value="" disabled selected>-</option>
                                        <?php
                                        foreach ($addressTypes as $key => $value) {
                                            echo '<option value="' . $key . '">' . $value . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-8">
                                    <label for="quote-address" class="quote-address-label mb-1">{{ __('quote.address') }}</label>
                                    <input type="text" class="form-control w-100 quote-address" name="quote-address" required maxlength="50">
                                </div>
                            </div>
                            <div class="row pt-lg-3 pt-xl-3">
                                <div class="col-12 col-lg-4">
                                    <label for="quote-postal-code" class="quote-postal-code-label mb-1">{{ __('quote.postalCode') }}</label>
                                    <input type="text" class="form-control w-100 quote-postal-code" name="quote-postal-code" maxlength="5" required>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-city" class="quote-city-label mb-1">{{ __('quote.city') }}</label>
                                    <select class="form-control w-100 quote-city" name="quote-city" required disabled="disabled">
                                        @php
                                            // Dynamically filled with JS
                                        @endphp
                                    </select>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-province" class="quote-province-label mb-1">{{ __('quote.province') }}</label>
                                    <select class="form-control w-100 quote-province" name="quote-province" required disabled="disabled">
                                        @php
                                            // Dynamically filled with JS
                                        @endphp
                                    </select>
                                </div>
                            </div>

                            <div class="row pt-lg-3 pt-xl-5">
                                <div class="col-12 col-lg-4">
                                    <label for="quote-company-name" class="quote-company-name-label mb-1">{{ __('quote.company.name') }}</label>
                                    <input type="text" class="form-control w-100 quote-company-name" name="quote-company-name" required maxlength="50">
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-job-location" class="quote-job-location-label mb-1">{{ __('quote.jobLocation') }}</label>
                                    <select class="form-control w-100 quote-job-location" name="quote-job-location" required>
                                        <?php
                                        foreach ($jobLocationTypes as $key => $value) {
                                            echo '<option value="' . $key . '">' . $value . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id="companyAddressTypeWrapper" class="col-12 col-lg-4">
                                    <label for="quote-company-address-pick" class="quote-company-address-pick-label mb-1">{{ __('quote.company.address') }}</label>
                                    <select class="form-control w-100 quote-company-address-pick" name="quote-company-address-pick"  required>
                                        <?php
                                        foreach ($companyAddressTypes as $key => $value) {
                                            echo '<option value="' . $key . '">' . $value . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row companyAddressWrapper pt-lg-3 pt-xl-3 d-none">
                                <div class="col-12 col-lg-4">
                                    <label for="quote-company-address-type" class="quote-company-address-type-label mb-1">{{ __('quote.addressType') }}</label>
                                    <select class="form-control w-100 quote-company-address-type" name="quote-company-address-type">
                                        <?php
                                        echo '<option disabled selected value=""> - </option>';
                                        foreach ($addressTypes as $key => $value) {
                                            echo '<option value="' . $key . '">' . $value . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-8">
                                    <label for="quote-company-address" class="quote-company-address-label mb-1">{{ __('quote.company.address') }}</label>
                                    <input type="text" class="form-control w-100 quote-company-address" name="quote-company-address" maxlength="50">
                                </div>
                            </div>
                            <div class="row companyAddressWrapper pt-lg-3 pt-xl-3 d-none">
                                <div class="col-12 col-lg-4">
                                    <label for="quote-company-postal-code" class="quote-company-postal-code-label mb-1">{{ __('quote.postalCode') }}</label>
                                    <input type="text" class="form-control w-100 quote-company-postal-code" name="quote-company-postal-code" maxlength="5">
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-company-city" class="quote-company-city-label mb-1">{{ __('quote.city') }}</label>
                                    <select class="form-control w-100 quote-company-city" name="quote-company-city" disabled="disabled">
                                        <option disabled selected value> - </option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label for="quote-company-province" class="quote-company-province-label mb-1">{{ __('quote.province') }}</label>
                                    <select type="text" class="form-control w-100 quote-company-province" name="quote-company-province" disabled="disabled">
                                        <option disabled selected value> - </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row pt-lg-5 pt-xl-5">
                                <div class="col-12 col-lg-6">
                                    <label>{{ __('quote.anotherInsurance') }}</label>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-radio btn-radio-left text-center mt-lg-0 quote-another-insurance">
                                            <input type="radio" class="form-check-input position-static valid quote-another-insurance d-none" name="quote-another-insurance" value="S">{{ __('text.yes') }}
                                        </label>
                                        <label class="btn btn-radio btn-radio-right text-center mt-lg-0 active quote-another-insurance">
                                            <input type="radio" class="form-check-input position-static valid quote-another-insurance d-none" name="quote-another-insurance" value="N" checked>{{ __('text.no') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h4 class="secondary primary-color mb-2 mb-lg-3 mt-4">{{ __('quote.buyerInfo') }}</h4>
                                    <p class="second gray-color">{{ __('quote.buyerInfoExplanation') }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-pills" id="contractTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link nav-link-custom-left active valid quote-legal-entity-type natural-person"  data-toggle="tab" data-person-type="F" href="#pf" role="tab" aria-controls="pf" aria-selected="true">{{ __('quote.naturalPerson') }}</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link nav-link-custom-right valid quote-legal-entity-type legal-entity"  data-toggle="tab" href="#pj" role="tab" data-person-type="J" aria-controls="pj" aria-selected="false">{{ __('quote.legalEntity') }}</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content legalEntityInfo" style="display: none;">
                                        <div class="row pt-lg-3 pt-xl-3">
                                            <div class="col-12 col-lg-4">
                                                <label for="quote-legal-entity-name" class="quote-legal-entity-name-label mb-1">{{ __('quote.legalEntity.name') }}</label>
                                                <input type="text" class="form-control w-100 quote-legal-entity-name" name="quote-legal-entity-name" required maxlength="100">
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <label for="quote-legal-entity-id" class="quote-legal-entity-id-label mb-1">{{ __('quote.legalEntity.id') }}</label>
                                                <input type="text" class="form-control w-100 quote-legal-entity-id" name="quote-legal-entity-id" required maxlength="9">
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <label for="quote-legal-entity-email" class="quote-legal-entity-email-label mb-1">{{ __('quote.email') }}</label>
                                                <input type="email" class="form-control w-100 quote-legal-entity-email" name="quote-legal-entity-email" required maxlength="50">
                                            </div>
                                        </div>
                                        <div class="row pt-lg-3 pt-xl-3">
                                            <div class="col-12 col-lg-4">
                                                <label for="quote-legal-entity-address-type" class="quote-legal-entity-address-type-label mb-1">{{ __('quote.addressType') }}</label>
                                                <select class="form-control w-100 quote-legal-entity-address-type" name="quote-legal-entity-address-type" required>
                                                    <option value="" disabled selected>-</option>
                                                    <?php
                                                    foreach ($addressTypes as $key => $value) {
                                                        echo '<option value="' . $key . '">' . $value . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-8">
                                                <label for="quote-legal-entity-address" class="quote-legal-entity-address-label mb-1">{{ __('quote.address') }}</label>
                                                <input type="text" class="form-control w-100 quote-legal-entity-address" name="quote-legal-entity-address" required maxlength="100">
                                            </div>
                                        </div>
                                        <div class="row pt-lg-3 pt-xl-3">
                                            <div class="col-12 col-lg-4">
                                                <label for="quote-legal-entity-postal-code" class="quote-legal-entity-postal-code-label mb-1">{{ __('quote.postalCode') }}</label>
                                                <input type="text" class="form-control w-100 quote-legal-entity-postal-code" name="quote-legal-entity-postal-code" maxlength="5" required>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <label for="quote-legal-entity-city" class="quote-legal-entity-city-label mb-1">{{ __('quote.city') }}</label>
                                                <select class="form-control w-100 quote-legal-entity-city" name="quote-legal-entity-city" required disabled="disabled">
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp
                                                </select>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <label for="quote-legal-entity-province" class="quote-legal-entity-province-label mb-1">{{ __('quote.province') }}</label>
                                                <select class="form-control w-100 quote-legal-entity-province" name="quote-legal-entity-province" required disabled="disabled">
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-buttons my-3 row">
                        <div class="col text-center">
                            <button class="quote-step previous bg-white txt-dark-grey bold py-3 px-3 border-0 rounded mt-4 position-relative font-weight-bold">
                                {{ __('quote.stepPrevious') }}
                            </button>
                        </div>
                        <div class="col text-center">
                            <button class="quote-step next bg-lime-yellow text-white bold py-3 px-3 border-0 rounded mt-4 position-relative font-weight-bold">
                                {{ __('quote.stepNext') }}
                                <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="step-3" class="pb-5 row" style="display: none;">
            <div class="col">
                <div class="row">
                    <div id="health-form" class="col-12 col-md-9">
                        <div class="loading-lock"></div>
                        <h4>{{ __('quote.healthForm') }}</h4>
                        <div class="loader-wrapper w-100 pt-5 text-center" style="display:none;">
                            <i class="fas fa-circle-notch fa-spin fa-2x txt-navy-blue"></i>
                        </div>
                        <div class="dynamic-content">
                            @php
                                // Dynamically filled with JS
                            @endphp
                        </div>
                    </div>
                    <div id="product-info-widget" class="col-3 d-none d-md-block text-center my-5">
                        @php
                            // Dynamically filled with JS
                            // copied from first step product info card
                        @endphp
                    </div>
                </div>
                <div class="step-buttons my-5 row">
                    <div class="col d-none d-md-block">
                    </div>
                    <div class="col text-center">
                        <button class="quote-step previous bg-white txt-dark-grey bold py-3 px-3 px-md-5 border-0 rounded mt-0 mt-md-4 position-relative font-weight-bold">
                            {{ __('quote.stepPrevious') }}
                        </button>
                    </div>
                    <div class="col text-center">
                        <button class="quote-step next bg-lime-yellow text-white bold py-3 px-3 px-md-5 border-0 rounded mt-0 mt-md-4 position-relative font-weight-bold" disabled>
                            {{ __('quote.stepNext') }}
                            <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                        </button>
                    </div>
                    <div class="col d-none d-md-block">
                    </div>
                </div>
            </div>
        </section>

        <section id="step-4" class="pb-2 row"  style="display: none;">
            <div class="col">
                <div class="container p-0">
                    <div class="row">
                        <div id="product-info-widget" class="col text-center widget-payment-method">
                            <div class="col p-0 product-info-card text-center my-5 widget-payment-method">
                                <div class="card border-white px-4 pb-1">
                                    <h4></h4>
                                    <div class="separator bg-gradient-blue mb-3"></div>
                                    <div class="card-body text-center">
                                        <p class="card-text billing txt-dark-grey">{{ __('widget.payment') }} <span class="payment-method txt-navy-blue"></span></p>
                                        <p class="card-text billing txt-dark-grey payment-amount"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div id="payment-input" class="col py-5">
                            <h4 class="m-auto text-center">{{ __('quote.bankTransfer') }}</h4>
                            <div class="row pt-3">
                                <div class="col">
                                    <div class="separator bg-navy-blue mb-3"></div>
                                    <form autocomplete="off">
                                        <label class="quote-bank-account-label mb-1" for="quote-bank-account">{{ __('quote.bankAccount') }}</label>
                                        <input type="text" class="form-control quote-iban quote-iban-country" name="quote-iban-country" autocomplete="off" placeholder="ESXX" maxlength="4" required>
                                        <input type="text" class="form-control quote-iban quote-iban-control" name="quote-iban-control" autocomplete="off" placeholder="XXXX"  maxlength="4" required>
                                        <input type="text" class="form-control quote-iban quote-iban-entity" name="quote-iban-entity" autocomplete="off" placeholder="XXXX" maxlength="4" required>
                                        <input type="text" class="form-control quote-iban quote-iban-office" name="quote-iban-office" autocomplete="off" placeholder="XXXX" maxlength="4" required>
                                        <input type="text" class="form-control quote-iban quote-iban-dc" name="quote-iban-dc" autocomplete="off" placeholder="XXXX" maxlength="4" required>
                                        <input type="text" class="form-control quote-iban quote-iban-account" name="quote-iban-account" autocomplete="off" placeholder="XXXX" maxlength="4" required>
                                        <div class="error mt-5"  style="display:none;">{{ __('quote.ibanError') }}</div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-buttons my-3 row">
                        <div class="col text-center">
                            <button class="quote-step previous bg-white txt-dark-grey bold py-3 px-3 border-0 rounded mt-4 position-relative font-weight-bold">
                                {{ __('quote.stepPrevious') }}
                            </button>
                        </div>
                        <div class="col text-center">
                            <button class="quote-step next bg-lime-yellow text-white bold py-3 px-3 border-0 rounded mt-4 position-relative font-weight-bold" disabled>
                                {{ __('quote.stepNext') }}
                                <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <section id="step-5" class="pb-2 row" style="display: none;">
            <div class="col">
                <div class="container">

                    <div class="row my-5">
                        <div class="col thank-you">
                            <h4 class="text-center title mb-0">{{ __('quote.sign.policy.info') }}</h4>
                            <div class="row p-3">
                                <div class="col">
                                    <p class="text-left message"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="text-center title mb-3">{{ __('quote.sign.title') }}</h4>

                    <div class="row mb-5 choose-signing-method" style="display: none;">
                        <div class="col">
                            <div class="row pt-1">
                                <div class="col text-center txt-navy-blue mb-4 my-xl-5 hand-write-method-button" style="display: none;">
                                    <div class="card border-white hand-write">
                                        <p class="font-weight-bold">{{ __('quote.sign.handWrite') }}</p>
                                    </div>
                                </div>
                                <div class="col text-center txt-navy-blue mb-4 my-xl-5 logalty-method-button" style="display: none;">
                                    <div class="card border-white logalty">
                                        <p class="font-weight-bold">{{ __('quote.sign.logalty') }}</p>
                                    </div>
                                </div>
                                <div class="col text-center txt-navy-blue mb-4 my-xl-5 logalty-synchronous-method-button" style="display: none;">
                                    <div class="card border-white logalty-synchronous">
                                        <p class="font-weight-bold">{{ __('quote.sign.logalty.synchronous') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5 hand-write-method signing-method-screen" style="display: none;">
                        <div class="col">
                            <div class="row pt-1">
                                <div class="col hand-write text-center txt-navy-blue mb-3">
                                    <p class="">{!! __('quote.sign.handWrite.instructions.step1') !!}</p>
                                    <form id="quote-download-form" action="/download" method="get">
                                        <input type="hidden" name="docId" class="docId" value="">
                                        <input type="hidden" name="productor" class="productor" value="">
                                        <input type="hidden" name="source" class="source" value="">
                                        <input type="hidden" name="type" class="type" value="">
                                        <input type="hidden" name="format" class="format" value="">
                                        <input type="hidden" name="downloadType" class="downloadType" value="document">
                                        <input type="submit" id="quote-download-policy-request" class="quote-download-policy-request"  value="{!! __('text.download') !!}">
                                    </form>
                                </div>
                            </div>
                            <div class="row pt-1">
                                <div class="col hand-write text-center txt-navy-blue my-3">
                                    <p class="">{!! __('quote.sign.handWrite.instructions.step2.now') !!}</p>
                                    <form id="send-policy-request" autocomplete="off">
                                        <div class="row my-4">
                                            <div class="col">
                                                <input type="file" name="doc" class="doc" id="doc" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <input type="hidden" name="refId" class="refId" value="">
                                                <input type="hidden" name="productor" class="productor" value="">

                                                <input type="hidden" name="docId" class="docId" value="467">
                                                <input type="hidden" name="folderId" class="folderId" value="65">
                                                <input type="hidden" name="docType" class="docType" value="application/pdf">
                                                <input type="hidden" name="sendType" class="sendType" value="policyRequest">
                                                <button id="send-policy-request-button" class="send-policy-request-button bg-lime-yellow text-white py-2 px-3 rounded border-0 position-relative" disabled>
                                                    <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                                                    {{ __('text.send') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <p class="mt-4">{!! __('quote.sign.handWrite.instructions.step2.later') !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5 logalty-method signing-method-screen" style="display: none;">
                        <div class="col">
                            <div class="row pt-1">
                                <div class="col logalty text-center txt-navy-blue mt-3 mb-4 mb-xl-5">
                                    <p class=""></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="logalty-synchronous-widget logalty-synchronous-method signing-method-screen" style="display: none;">
                    <div class="loader-wrapper w-100 py-3 text-center">
                        <i class="fas fa-circle-notch fa-spin fa-2x txt-navy-blue"></i>
                    </div>
                    <iframe id="logaltyFrame" src="" width="100%" height="1000" style="border: none;"></iframe>
                </div>
                <div class="step-buttons mb-5 row" style="display: none;">
                    {{--<div class="col text-center">
                        <button class="quote-step previous bg-white txt-dark-grey bold py-3 px-5 border-0 rounded mt-4 position-relative font-weight-bold">
                            {{ __('quote.stepPrevious') }}
                        </button>
                    </div>--}}
                    <div class="col text-center">
                        <button class="quote-step next bg-lime-yellow text-white bold py-3 px-3 px-md-5 border-0 rounded mt-0 mt-md-4 position-relative font-weight-bold">
                            {{ __('quote.stepNext.final') }}
                        </button>
                    </div>
                </div>
            </div>
        </section>



    </form>
</div>
