@php
    App::setLocale('es');
    $title = __('menu.quote.text');
    $pm = new \App\Http\Middleware\PMWShandler();
    $currentLanguage = App::getLocale();

    $personTypes = $pm->getPersonTypes();
    $paymentMethods = $pm->getPaymentMethods();
    $addressTypes = $pm->getAddressTypes();
    $companyAddressTypes = $pm->getCompanyAddressTypes();
    $jobLocationTypes = $pm->getWorkLocationTypes();
    $languages = $pm->getLanguages();
@endphp

@extends('app.layouts.core-nosidebar')

@section('content')
    <div id="quote">

        <section class="header pb-4 row hiddenprint">
            <div class="col">
                <div class="row pt-xl-3">
                    <div class="breadcrumbs txt-navy-blue pb-2 col-12 col-lg-6"><a href="{{ __('menu.home.url') }}">{{ __('menu.home.text') }}</a> > {{ __('menu.quote.text') }}</div>
                    <div class="col-12 col-lg-6">{{ session('home.nombreProductor') }}  </div>
                </div>
                <div class="separator bg-lime-yellow"></div>
            </div>
        </section>

        <section id="step-1" class="pb-5 row">
            <div class="col">
                <div class="row">
                    <div class="form col-12 col-md-6 col-lg-6 col-xl-6 hiddenprint">
                        <form autocomplete="off">
                            <div class="loading-lock"></div>
                            <div class="container-fluid">

                                @php
                                    try {
                                        $productores = $pm->getProductores();
                                    } catch (Throwable $e) {
                                        report($e);
                                        return false;
                                    }

                                @endphp
                                @if( $productores )
                                    <div class="select-user hiddenprint row" style="display: none;">
                                        <div class="col-4">
                                            <label for="productor">{{ __('quote.pickProductor') }}</label>
                                        </div>
                                        <div class="col-8">
                                            <select class="form-control" id="quote-productor" autocomplete="off" required>
                                                <option value=""></option>
                                                @if(empty($productores["id"]))
                                                    @foreach( $productores as $row )
                                                        <option value="{{ $row["id"] }}">{{ $row["id"] }} - {{ $row["name"] }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="{{ $productores["id"] }}">{{ $productores["id"] }} - {{ $productores["name"] }}</option>
                                                @endif

                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="toggles row dynamic-block my-3 hiddenprint " style="display: none;">
                                    <h4>{{ __('quote.quoteType') }}</h4>
                                    <div class="separator thin bg-dark-grey"></div>
                                    <div class="col-4 pt-3 px-2 px-md-3">
                                        <button class="subsidio text-black bold py-2 px-4 border-0 rounded w-100">{{ __('quote.type.subsidio') }}</button>
                                    </div>
                                    <div class="col-4 pt-3 px-2 px-md-3">
                                        <button class="precio text-black bold py-2 px-4 border-0 rounded w-100">{{ __('quote.type.precio') }}</button>
                                    </div>
                                    <div class="col-4 pt-3 px-2 px-md-3">
                                        <button class="recomendador text-black bold py-2 px-4 border-0 rounded w-100">{{ __('quote.type.recomendador') }}</button>
                                    </div>
                                </div>

                                <div class="subsidio content row dynamic-block fields-wrapper" style="display: none;">
                                    <div class="productsList w-100">
                                        <h4>{{ __('quote.productList') }}</h4>
                                        <div class="separator thin bg-dark-grey"></div>
                                        <div class="col pt-3">
                                            <div class="row">
                                                @if( Session::has('quote.products') )
                                                    @php
                                                        $products = Session::get('quote.products');
                                                    @endphp
                                                    @foreach( $products as $key => $item )
                                                        <div class="checkboxWithLabel col-6">
                                                            <label>
                                                                <input type="radio" name="quote-product" class="quote-product" value="{{ $key }}">
                                                                <div>
                                                                    {{ $item["name"] }}
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="col error-message">{{ __('error.quote.products') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product-variations content w-100 my-3" style="display: none;">
                                        <h4>{{ __('quote.productVariations') }}</h4>
                                        <div class="separator thin bg-dark-grey"></div>
                                        <div class="dynamic-content col pt-3">
                                            <div class="row">
                                                @php
                                                    // Dynamically filled with JS
                                                @endphp
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product-modalities content w-100 my-3" style="display: none;">
                                        <h4>{{ __('quote.productModalities') }}</h4>
                                        <div class="separator thin bg-dark-grey"></div>
                                        <div class="dynamic-content col pt-3">
                                            <div class="row">
                                                @php
                                                    // Dynamically filled with JS
                                                @endphp
                                            </div>
                                        </div>
                                    </div>
                                    {{--
                                        Los campos select y/o con modificaConfiguracion=S deben salir en el orden indicado en el WS
                                        Un campo “select” es una lista de valores, te lo indica en “tipoCampoHtml”.
                                        En algunos campos de este tipo (los que tienen “modificaConfiguracion”=S) es
                                        necesario volver a llamar al servicio cada vez que el usuario seleccione un
                                        valor diferente de la lista, es decir, cada vez que se modifique el valor del campo.
                                        En cuyo caso hay que pasar en el parámetro P_CAMPO_MODIFICADO el nombre del
                                        campo de tipo “select” que se ha modificado.

                                    --}}

                                    <div class="product-extra-info content w-100" style="display: none;">
                                        <h4>{{ __('quote.extraInfo') }}</h4>
                                        <div class="separator thin bg-dark-grey"></div>

                                        <div class="dynamic-content row pt-3 d-flex">
                                            <div class="col-6 align-self-end">
                                                <label class="quote-job-type-label mb-1" for="quote-job-type"></label>
                                                <select class="form-control quote-job-type valid" data-index="0" name="quote-job-type"required>

                                                </select>
                                            </div>
                                            <div class="col-6 align-self-end">
                                            <label class="quote-job-label mb-1" for="quote-job-picker"></label>
                                                <select class="form-control quote-job valid" data-index="1" name="quote-job" style="display: none;" autocomplete="off" required>
                                                        @php
                                                            // Dynamically filled with JS
                                                        @endphp
                                                    </select>
                                                <input class="form-control quote-job-picker" data-index="1" name="quote-job-picker" required>
                                                    @php
                                                        // plugin loaded from JS quote_load_ProductConfiguration()
                                                    @endphp

                                            </div>
                                        </div>
                                        <div class="dynamic-content row pt-3 quote-commercialKey">
                                        </div>

                                        <div class="row">
                                        <div class="dynamic-content col">

                                            <div class="row pt-3 d-flex">
                                                <div class="col-3 align-self-end">
                                                    <label class="quote-birthdate-label mb-1 control-label" for="quote-birthdate"></label>
                                                    <input id="datepicker" type="text" class="form-control w-100 quote-birthdate date-input" data-index="3" name="quote-birthdate" maxlength="10" autocomplete="off" required>                                                    {{--<script>
                                                        jQuery( function() {
                                                            jQuery( "#datepicker" ).datepicker({ maxDate: '-18Y -1D', changeMonth: true, changeYear: true, yearRange: "-70:+0" });
                                                            jQuery( "#datepicker" ).datepicker("option", jQuery.datepicker.regional[ "{{ $currentLanguage  }}" ]);
                                                        } );
                                                    </script>--}}
                                                </div>
                                                <div class="col-3 align-self-end">
                                                    <label class="quote-gender-label mb-1 control-label" for="quote-gender"></label>
                                                    <select class="form-control quote-gender" data-index="3" name="quote-gender" autocomplete="off" required>                                                        @php
                                                            // Dynamically filled with JS
                                                        @endphp
                                                    </select>
                                                </div>
                                                <div class="col-3 align-self-end quote-height-wrapper">
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp
                                                </div>
                                                <div class="col-3 align-self-end quote-weight-wrapper">
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp
                                                </div>
                                            </div>

                                            <div class="row pt-3 quote-price-wrapper">
                                                <div class="col-4">
                                                    <label class="quote-price-label mb-1" for="quote-price">{{ __('quote.amount') }}</label>
                                                    <input type='number' class="form-control w-100 quote-price" data-index="3" name="quote-price" autocomplete="off" required>
                                                </div>
                                            </div>

                                            <div class="quote-benefit-wrapper row pt-3 d-flex">
                                                @php
                                                    // Dynamically filled with JS
                                                @endphp

                                            </div>
                                            <div class="row pt-3 d-flex">
                                                <div class="quote-franchise-wrapper col-6">
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp

                                                </div>
                                                <div class="quote-durationField-wrapper col-6">
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp

                                                </div>
                                            </div>
                                            <div class="row pt-3 d-flex">
                                                <div class="quote-duration-wrapper  col-6">
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp

                                                </div>
                                                <div class="quote-discount-wrapper  col-6">
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp

                                                </div>
                                            </div>


                                        </div>
                                                    </div>
                                    <div class= "row">
                                       <div class="static-content col">

                                            <div class="row pt-3">
                                                <div class="col">
                                                    <label class="quote-starting-date-label mb-1" for="quote-starting-date">{{ __('quote.startDate') }}</label>
                                                    <input type="text" class="form-control w-100 quote-starting-date" name="quote-starting-date" id="quote-starting-date" maxlength="10" autocomplete="off" required readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                                    </div>

                                    <div class="get-rates content w-100 mt-4" style="display: none;">
                                        <div class="separator thin bg-dark-grey"></div>
                                        <div class="col">
                                            <button class="quote-button benefit text-white bold py-2 px-3 px-md-5 border-0 rounded w-100 mt-4 position-relative bg-light-grey hiddenprint" disabled>
                                                {{ __('quote.getRates') }}
                                                <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                                            </button>
                                            <button class="quote-button price text-white bold py-2 px-4 border-0 rounded w-100 mt-4 position-relative bg-light-grey" disabled>
                                                {{ __('quote.getRates') }}
                                                <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="loader-wrapper w-100 pt-5 text-center" style="display:none;">
                                        <i class="fas fa-circle-notch fa-spin fa-2x txt-navy-blue"></i>
                                    </div>
                                    <div class="loader-wrapper-get-rates w-100 pt-5 text-center" style="display:none;">
                                        <h3 class="w-100 text-center">{{ __('quote.loadGetRates') }}</h3>
                                        <i class="fas fa-circle-notch fa-spin fa-2x txt-navy-blue"></i>
                                    </div>


                                </div>

                                <div class="recomendador row dynamic-block" style="display: none;">
                                    <div class="w-100">
                                        <h4>{{ __('advisor.title') }}</h4>
                                        <div class="separator thin bg-dark-grey"></div>
                                        <div class="row pt-3">
                                            <div class="col-4">
                                                <label class="advisor-income-label mb-1" for="advisor-income">{{ __('advisor.income') }}</label>
                                                <input type="number" class="form-control w-100 advisor-income" name="advisor-income" min="10" max="100000" step="1" autocomplete="off" required>
                                            </div>
                                            <div class="col-8">
                                                <label class="advisor-freelancer-fee-label mb-1" for="advisor-freelancer-fee">{{ __('advisor.freelancerFee') }}</label>
                                                <select class="form-control advisor-freelancer-fee valid" name="advisor-freelancer-fee"  autocomplete="off" required>
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row pt-3">
                                            <div class="col-4">
                                                <label class="advisor-age-label mb-1" for="advisor-age">{{ __('advisor.age') }}</label>
                                                <select class="form-control advisor-age valid" name="advisor-age"  autocomplete="off" required>
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp
                                                </select>
                                            </div>
                                            <div class="col-8">
                                                <label class="advisor-job-label mb-1" for="advisor-job-picker">{{ __('advisor.job') }}</label>
                                                <select class="form-control advisor-job valid" name="advisor-job" style="display: none;" autocomplete="off" required>
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp
                                                </select>
                                                <input class="form-control advisor-job-picker" required>
                                                @php
                                                    // plugin loaded from JS quote_load_ProductConfiguration()
                                                @endphp
                                                {{--<label class="advisor-job-label mb-1" for="advisor-job">{{ __('advisor.job') }}</label>
                                                <select class="form-control advisor-job valid" name="advisor-job" autocomplete="off" required>
                                                    @php
                                                        // Dynamically filled with JS
                                                    @endphp
                                                </select>--}}
                                            </div>
                                        </div>

                                        <div class="get-advice w-100 mt-4" >
                                            <div class="separator thin bg-dark-grey"></div>
                                            <div class="col">
                                                <button class="advice-button price text-white bold py-2 px-4 border-0 rounded w-100 mt-4 position-relative valid" disabled>
                                                    {{ __('advisor.advice') }}
                                                    <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>

                    <div class="data col-12 col-md-6 col-lg-6 col-xl-6 rates-table pt-5 pt-md-0 print-center" style="display: none;">
                        <div style="display: none" class="showprint">
                            <script>
                                var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                                var f=new Date();
                                document.write("Panel de precios impreso el: " + f.getDate() + " de " + meses[f.getMonth()] + " de " + f.getFullYear());
                            </script>
                        </div>
                        <h4 style="display: none;" class="showprint">{{ __('quote.printTitle.uno') }}</h4>
                        <div class="row">
                            <div class="col datos-salida">
                                @php
                                    // Dynamically filled with JS
                                @endphp
                            </div>
                        </div>
                        <div class="row">
                            <div class="col optional-coverages">
                                @php
                                    // Dynamically filled with JS
                                @endphp
                            </div>
                        </div>
                        <div class="row print1">
                            <div class="col">
                                <div class="rates-table-description"></div>
                                <table class="table" >
                                    @php
                                        // Dynamically filled with JS
                                    @endphp
                                </table>

                            </div>
                        </div>
                        <div class="row print2">
                            <style type="text/css">
                                .display-block {
                                    display: block;
                                }
                                .display-none {
                                    display: none;
                                }
                            </style>
                            <div class="col descript-option">
                                @php
                                    // Dynamically filled with JS
                                @endphp
                                <div class="rates-table-selection-description"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-9 table-actions">
                                <div class="row">
                                    <div class="col">
                                        {{ __("quote.text.billingCycle") }}
                                    </div>
                                </div>
                                <div class="row billing-cycle">
                                    @php
                                        // Dynamically filled with JS
                                    @endphp
                                </div>
                            </div>
                            <div class="col-3 table-actions mb-3">
                                <button class="action-minibutton send-email text-white bold py-1 px-2 border-0 mt-2 position-relative w-100 text-left rounded hiddenprint">
                                    {{ __("text.send") }}
                                    <img class="button-icon right" src="/img/paper-airplane-white.png">
                                </button>
                                <button class="action-minibutton print text-white bold py-1 px-2 border-0 mt-2 position-relative w-100 text-left rounded hiddenprint">
                                    {{ __("text.print") }}
                                    <img class="button-icon right" src="/img/print.png">
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col instructions hiddenprint">
                                <p>{!! __("quote.instructions.selectRowAndBillingCycle") !!}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-6 col-xl-6 advisor-results pt-5 pt-md-0" style="display: none;">
                        <div class="row">
                            <div class="col">
                                <table class="table" >
                                    <tr class="block1">
                                        <td class="title" colspan="5">{{ __("advisor.table.block1.title") }}</td>
                                    </tr>
                                    <tr class="separator">
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr class="block1 row1">
                                        <td class="col1">{{ __("advisor.table.block1.row1.col1") }}</td>
                                        <td class="col2">{{ __("advisor.table.block1.row1.col2") }}</td>
                                        <td class="col3">{{ __("advisor.table.block1.row1.col3") }}</td>
                                        <td class="col4">{{ __("advisor.table.block1.row1.col4") }}</td>
                                        <td></td>
                                    </tr>
                                    <tr class="block1 row2">
                                        <td class="col1">{{ __("advisor.table.block1.row2.col1") }}</td>
                                        <td class="col2"></td>
                                        <td class="col3"></td>
                                        <td class="col4"></td>
                                        <td></td>
                                    </tr>

                                    <tr class="block2">
                                        <td class="title" colspan="5">{{ __("advisor.table.block2.title") }}</td>
                                    </tr>
                                    <tr class="separator">
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr class="block2 row1">
                                        <td class="col1">{{ __("advisor.table.block2.row1.col1") }}</td>
                                        <td class="col2">{{ __("advisor.table.block2.row1.col2") }}</td>
                                        <td class="col3">{{ __("advisor.table.block2.row1.col3") }}</td>
                                        <td class="col4">{{ __("advisor.table.block2.row1.col4") }}</td>
                                        <td></td>
                                    </tr>
                                    <tr class="block2 row2">
                                        <td class="col1">{{ __("advisor.table.block2.row2.col1") }}</td>
                                        <td class="col2"></td>
                                        <td class="col3"></td>
                                        <td class="col4"></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block2 row3">
                                        <td class="col1">{{ __("advisor.table.block2.row3.col1") }}</td>
                                        <td class="col2"></td>
                                        <td class="col3"></td>
                                        <td class="col4"></td>
                                        <td></td>
                                    </tr>
                                    <tr class="separator">
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr class="block2 row4">
                                        <td class="col1">{{ __("advisor.table.block2.row4.col1") }}</td>
                                        <td class="col2">{{ __("advisor.table.block2.row4.col2") }}</td>
                                        <td class="col3">{{ __("advisor.table.block2.row4.col3") }}</td>
                                        <td class="col4">{{ __("advisor.table.block2.row4.col4") }}</td>
                                        <td></td>
                                    </tr>
                                    <tr class="block2 row5">
                                        <td class="col1">{{ __("advisor.table.block2.row5.col1") }}</td>
                                        <td class="col2"></td>
                                        <td class="col3"></td>
                                        <td class="col4"></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block2 row6">
                                        <td class="col1">{{ __("advisor.table.block2.row6.col1") }}</td>
                                        <td class="col2"></td>
                                        <td class="col3"></td>
                                        <td class="col4"></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block2 row7">
                                        <td class="legal" style="border: none !important;" colspan="4">{{ __("advisor.table.block2.legal") }}</td>
                                    </tr>


                                    <tr class="block3">
                                        <td class="title" colspan="5">{{ __("advisor.table.block3.title") }}</td>
                                    </tr>
                                    <tr class="separator">
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr class="block3 row1">
                                        <td class="col1">{{ __("advisor.table.block3.row1.col1") }}</td>
                                        <td class="col2">{{ __("advisor.table.block3.row1.col2") }}</td>
                                        <td class="col3">{{ __("advisor.table.block3.row1.col3") }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block3 row2">
                                        <td class="col1"></td>
                                        <td class="col2"></td>
                                        <td class="col3"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="separator">
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr class="block3 row3">
                                        <td class="col1">{{ __("advisor.table.block3.row3.col1") }}</td>
                                        <td class="col2">{{ __("advisor.table.block3.row3.col2") }}</td>
                                        <td class="col3">{{ __("advisor.table.block3.row3.col3") }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block3 row4">
                                        <td class="col1"></td>
                                        <td class="col2"></td>
                                        <td class="col3"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    <tr class="block4">
                                        <td class="title" colspan="5">{{ __("advisor.table.block4.title") }}</td>
                                    </tr>
                                    <tr class="separator">
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr class="block4 row1">
                                        <td class="col1">{{ __("advisor.table.block4.row1.col1") }}</td>
                                        <td class="col2">{{ __("advisor.table.block4.row1.col2") }}</td>
                                        <td class="col3">{{ __("advisor.table.block4.row1.col3") }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block4 row2">
                                        <td class="col1"></td>
                                        <td class="col2">{{ __("advisor.table.block4.row2.col2") }}</td>
                                        <td class="col3"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block4 row3">
                                        <td class="col1"></td>
                                        <td class="col2">{{ __("advisor.table.block4.row3.col2") }}</td>
                                        <td class="col3"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block4 row4">
                                        <td class="col1"></td>
                                        <td class="col2">{{ __("advisor.table.block4.row4.col2") }}</td>
                                        <td class="col3"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block4 row5">
                                        <td class="col1"></td>
                                        <td class="col2">{{ __("advisor.table.block4.row5.col2") }}</td>
                                        <td class="col3"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="block4 row6">
                                        <td class="col1"></td>
                                        <td class="col2">{{ __("advisor.table.block4.row6.col2") }}</td>
                                        <td class="col3"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="separator">
                                        <td colspan="5"></td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>


                </div>

                <div id="selected-product-info" class="row" style="display: none;">
                    <div class="col wrapper m-5">
                        <div class="row py-5">
                            <div class="col-12 col-md-12 text-center">
                                <h4 class="hiddenprint">{{ __('quote.createBudget') }}</h4>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col d-none d-md-block">
                            </div>
                            <div class="col-12 col-md-3 text-center">
                                <button id="generate-budget" class="bg-lime-yellow text-white bold py-3 px-3 px-md-5 border-0 rounded mt-0 position-relative font-weight-bold hiddenprint">
                                    {{ __('quote.generateBudget') }}
                                    <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                                </button>
                            </div>
                            <div class="col d-none d-md-block">
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col d-none d-md-block">
                            </div>
                            <div class="col-6 col-md-3 text-center">
                                <button id="send-budget" class="action-button budget text-white bold py-1 px-2 border-0 mt-2 position-relative w-100 text-left rounded hiddenprint" disabled>
                                    {{ __('quote.sendBudget') }}
                                    <img class="button-icon right" src="/img/paper-airplane-white.png">
                                </button>
                            </div>
                            <div class="col-6 col-md-3 text-center">
                                <a href="" target="_blank" class="print-budget">
                                    <button id="print-budget" class="action-button budget text-white bold py-1 px-2 border-0 mt-2 position-relative w-100 text-left rounded hiddenprint" disabled>
                                        {{ __('quote.printBudget') }}
                                        <img class="button-icon right" src="/img/print.png">
                                    </button>
                                </a>
                            </div>
                            <div class="col d-none d-md-block">
                            </div>
                        </div>
                        <div class="row print3">
                            {{--<div class="col-12 col-md-6 col-lg-6 col-xl-6 text-center discount-code py-0 my-0 py-md-5 my-md-5">
                                <p class="txt-dark-grey mt-5">{{ __('quote.discountCodeInput') }}</p>
                                <input type="text" class="quote-discount-code w-100" name="quote-discount-code">
                            </div>--}}
                            <div class="col product-info-card text-center my-5">
                                <h4 style="display: none;" class="showprint">{{ __('quote.printTitle.dos') }}</h4>
                                <h4 class="hiddenprint" style="text-align: center !important;">{{ __('quote.productInfo') }}</h4>
                                <div class="card border-white" style="border: 1px solid #002A66; border-radius: 5px; max-width: 300px; margin: auto;">
                                    <div class="card-body text-left">
                                        <p class="card-text product-name txt-dark-grey text-center" style="text-align: center !important;">{{ __('quote.productInfo.name') }} <br><span class="dynamic-content txt-navy-blue" style="color: #002A66 !important;"></span></p>
                                        <p class="card-text product-variation txt-dark-grey text-center" style="text-align: center !important;">{{ __('quote.productInfo.variation') }} <br><span class="dynamic-content txt-navy-blue" style="color: #002A66 !important;"></span></p>
                                        <p class="card-text product-product txt-dark-grey text-center" style="text-align: center !important;">{{ __('quote.productInfo.product') }} <br><span class="dynamic-content txt-navy-blue" style="color: #002A66 !important;"></span></p>
                                        <p class="card-text product-coverage txt-dark-grey text-center" style="text-align: center !important;">{{ __('quote.productInfo.coverage') }} <br><span class="dynamic-content txt-navy-blue" style="color: #002A66 !important;"></span></p>
                                        <p class="card-text product-exemption txt-dark-grey text-center" style="text-align: center !important;">{{ __('quote.productInfo.exemption') }} <br><span class="dynamic-content txt-navy-blue" style="color: #002A66 !important;"></span></p>
                                        <p class="card-text billing txt-dark-grey text-center" style="text-align: center !important;">{{ __('quote.productInfo.billing') }} <span class="dynamic-content txt-navy-blue" style="color: #002A66 !important;"></span></p>
                                        <p class="card-text billing-total txt-dark-grey text-center" style="text-align: center !important;">{{ __('quote.productInfo.billingTotal') }} <span class="dynamic-content txt-navy-blue" style="color: #002A66 !important;"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col d-none d-md-block">
                            </div>
                            {{--
                            <div class="col-3 text-center">
                                <button class="quote-step previous bg-white txt-dark-grey bold py-3 px-5 border-0 rounded mt-4 position-relative">
                                    {{ __('quote.stepPrevious') }}
                                </button>
                            </div>
                            --}}
                            <div class="col-12 col-md-3 text-center">
                                <button class="quote-step next bg-lime-yellow text-white bold py-3 px-3 px-md-5 border-0 rounded mt-0 mt-md-4 position-relative font-weight-bold hiddenprint">
                                    {{ __('quote.stepNext') }}
                                </button>
                            </div>
                            <div class="col d-none d-md-block">
                            </div>
                        </div>

                        <div style="position: absolute;right: 0; display: none" class="showprint"><img src="/img/logo-pm.png"></div>
                    </div>
                    <div class="loader-wrapper-get-budget w-100 pt-5 text-center" style="display:none;">
                        <h3 class="w-100 text-center"> {{ __('quote.loadGetBudget') }}</h3>
                        <i class="fas fa-circle-notch fa-spin fa-2x txt-navy-blue"></i>
                    </div>
                </div>
            </div>
        </section>

        <section id="step-2" class="pb-2 row" style="display: none;">
            <form autocomplete="off" class="w-100">
                <div class="col">
                    <div class="container">

                        <div class="row">
                            <div id="personal-info" class="col">
                                <h4 class="m-auto">{{ __('quote.personalInfo') }}</h4>

                                <div class="row pt-xl-3">
                                    <div class="col-12 col-lg-4">
                                        <label for="quote-first-name" class="quote-first-name-label mb-1">{{ __('text.firstName') }}</label>
                                        <input type="text" class="form-control w-100 quote-first-name" name="quote-first-name" required maxlength="20">
                                        <div class="error" style="display:none;">{{ __('quote.name.error') }}</div>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label for="quote-last-name" class="quote-last-name-label mb-1">{{ __('text.lastName') }}</label>
                                        <input type="text" class="form-control w-100 quote-last-name" name="quote-last-name" required maxlength="30">
                                        <div class="error" style="display:none;">{{ __('quote.lastname.error') }}</div>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label for="quote-personal-id" class="quote-personal-id-label mb-1">{{ __('quote.personalId') }}</label>
                                        <input type="text" class="form-control w-100 quote-personal-id" name="quote-personal-id" required maxlength="9">
                                        <div class="error" style="display:none;">{{ __('quote.personalId.error') }}</div>
                                    </div>
                                </div>
                                <div class="row pt-xl-3">
                                    <div class="col-12 col-lg-4">
                                        <label for="birthdate" class="quote-birthdate-label mb-1"></label>
                                        <input type="text" class="form-control w-100 quote-birthdate-show valid" name="quote-birthdate-show" required maxlength="10" disabled="disabled">
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
                                <div class="row pt-xl-3">
                                    <div class="col-12 col-lg-4">
                                        <label for="quote-email" class="quote-email-label mb-1">{{ __('quote.email') }}</label>
                                        <input type="email" class="form-control w-100 quote-email" name="quote-email" required maxlength="50">
                                        <div class="error" style="display:none;">{{ __('quote.email.error') }}</div>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label for="quote-phone" class="quote-phone-label mb-1">{{ __('quote.phone') }}</label>
                                        <input type="number" class="form-control w-100 quote-phone" name="quote-phone" required max="799999999" min="600000000">
                                        <div class="error" style="display:none;">{{ __('quote.phone.error') }}</div>
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
                                <div class="row pt-xl-5">
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
                                <div class="row pt-xl-3">
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

                                <div class="row pt-xl-5">
                                    <div class="col-12 col-lg-4">
                                        <label for="quote-company-name" class="quote-company-name-label mb-1">{{ __('quote.company.name') }}<i class="fas fa-info-circle" title="{{ __('quote.company.name.tooltip') }}"></i></label>
                                        <input type="text" class="form-control w-100 quote-company-name" name="quote-company-name" required maxlength="50">
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label for="quote-job-location" class="quote-job-location-label mb-1">{{ __('quote.jobLocation') }}</label>
                                        <select class="form-control w-100 quote-job-location valid" name="quote-job-location" required>
                                            <?php
                                            foreach ($jobLocationTypes as $key => $value) {
                                                echo '<option value="' . $key . '">' . $value . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div id="companyAddressTypeWrapper" class="col-12 col-lg-4">
                                        <label for="quote-company-address-pick" class="quote-company-address-pick-label mb-1">{{ __('quote.company.address') }}</label>
                                        <select class="form-control w-100 quote-company-address-pick valid" name="quote-company-address-pick"  required>
                                            <?php
                                            foreach ($companyAddressTypes as $key => $value) {
                                                echo '<option value="' . $key . '">' . $value . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row companyAddressWrapper pt-xl-3 d-none">
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
                                <div class="row companyAddressWrapper pt-xl-3 d-none">
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
                                <div class="row companyAddressWrapper pt-xl-3 d-none">
                                    <div class="col-12 col-lg-6">
                                        <label for="quote-company-email" class="quote-company-email-label mb-1">{{ __('quote.company.email') }}</label>
                                        <input type="email" class="form-control w-100 quote-company-email" name="quote-company-email" required maxlength="50">
                                        <div class="error" style="display:none;">{{ __('quote.email.error') }}</div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="quote-company-phone" class="quote-company-phone-label mb-1">{{ __('quote.company.phone') }}</label>
                                        <input type="number" class="form-control w-100 quote-company-phone" name="quote-company-phone" required max="799999999" min="600000000">
                                        <div class="error" style="display:none;">{{ __('quote.phone.error') }}</div>
                                    </div>
                                </div>
                                <div class="row pt-xl-5">
                                    <div class="col-12 col-lg-6">
                                        <label>{{ __('quote.anotherInsurance') }}</label>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-radio btn-radio-left text-center mt-lg-0 quote-another-insurance yes">
                                                <input type="radio" class="form-check-input position-static valid quote-another-insurance yes d-none" name="quote-another-insurance" value="S">{{ __('text.yes') }}
                                            </label>
                                            <label class="btn btn-radio btn-radio-right text-center mt-lg-0 active quote-another-insurance no">
                                                <input type="radio" class="form-check-input position-static valid quote-another-insurance no d-none" name="quote-another-insurance" value="N" checked>{{ __('text.no') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row quote-another-insurance-extra-info pt-xl-3" style="display: none;">
                                    <div class="col-12 col-lg-4">
                                        <div class="form-group">
                                            <label for="quote-another-insurance-name" class="mb-1">{{ __('quote.anotherInsurance.name') }}</label>
                                            <input type="text" class="form-control w-100 quote-another-insurance-name" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <div class="form-group">
                                            <label for="quote-another-insurance-amount" class="mb-1">{{ __('quote.amount') }}</label>
                                            <input type="number" class="form-control w-100 quote-another-insurance-price">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <div class="form-group">
                                            <label for="quote-another-insurance-ends" class="mb-1">{{ __('quote.anotherInsurance.ends') }}</label>
                                            <input type="text" class="form-control w-100 quote-another-insurance-ends date-input" maxlength="10">
                                         {{--   <script>
                                                jQuery( function() {
                                                    jQuery( ".quote-another-insurance-ends" ).datepicker({ minDate: '1D', changeMonth: true, changeYear: true, yearRange: "0:+10" });
                                                    jQuery( ".quote-another-insurance-ends" ).datepicker("option", jQuery.datepicker.regional[ "{{ $currentLanguage  }}" ]);
                                                } );
                                            </script>--}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="secondary primary-color my-4 mb-lg-3">{{ __('quote.buyerInfo') }}</h4>
                                        <p class="second gray-color">{{ __('quote.buyerInfoExplanation') }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="nav nav-pills" id="contractTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link nav-link-custom-left valid quote-legal-entity-type natural-person"  data-toggle="tab" data-person-type="F" href="#pf" role="tab" aria-controls="pf" aria-selected="true">{{ __('quote.naturalPerson') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link nav-link-custom-right valid quote-legal-entity-type legal-entity"  data-toggle="tab" href="#pj" role="tab" data-person-type="J" aria-controls="pj" aria-selected="false">{{ __('quote.legalEntity') }}</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content personEntityInfo" style="display: none;">
                                            <div class="row pt-xl-3">
                                                <div class="col-12 col-lg-4">
                                                    <label for="quote-person-entity-name" class="quote-person-entity-name-label mb-1">{{ __('text.firstName') }}</label>
                                                    <input type="text" class="form-control w-100 quote-person-entity-name" name="quote-person-entity-name" required maxlength="100">
                                                    <div class="error" style="display:none;">{{ __('quote.name.error') }}</div>
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <label for="quote-person-entity-last-name" class="quote-person-entity-last-name-label mb-1">{{ __('text.lastName') }}</label>
                                                    <input type="text" class="form-control w-100 quote-person-entity-last-name" name="quote-person-entity-last-name" required maxlength="30">
                                                    <div class="error" style="display:none;">{{ __('quote.lastname.error') }}</div>
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <label for="quote-person-entity-personal-id" class="quote-person-entity-personal-id-label mb-1">{{ __('quote.personalId') }}</label>
                                                    <input type="text" class="form-control w-100 quote-person-entity-personal-id" name="quote-person-entity-personal-id" required maxlength="9">
                                                    <div class="error" style="display:none;">{{ __('quote.personalId.error') }}</div>
                                                </div>
                                            </div>
                                            <div class="row pt-xl-3">
                                                <div class="col-12 col-lg-4">
                                                    <label for="birthdate" class="quote-person-entity-birthdate-label mb-1">{{ __('text.birthdate') }}</label>
                                                    <input type="text" class="form-control w-100 quote-person-entity-birthdate-show valid" name="quote-person-entity-birthdate-show" required maxlength="10">
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <label for="quote-person-entity-email" class="quote-person-entity-email-label mb-1">{{ __('quote.email') }}</label>
                                                    <input type="email" class="form-control w-100 quote-person-entity-email" name="quote-email" required maxlength="50">
                                                    <div class="error" style="display:none;">{{ __('quote.email.error') }}</div>
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <label for="quote-person-entity-phone" class="quote-person-entity-phone-label mb-1">{{ __('quote.phone') }}</label>
                                                    <input type="number" class="form-control w-100 quote-person-entity-phone" name="quote-person-entity-phone" required max="799999999" min="600000000">
                                                    <div class="error" style="display:none;">{{ __('quote.phone.error') }}</div>
                                                </div>
                                            </div>
                                            <div class="row pt-xl-3">

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
                                            <div class="row pt-xl-3">
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
                                        <div class="tab-content legalEntityInfo" style="display: none;">
                                            <div class="row pt-xl-3">
                                                <div class="col-12 col-lg-4">
                                                    <label for="quote-legal-entity-name" class="quote-legal-entity-name-label mb-1">{{ __('quote.legalEntity.name') }}</label>
                                                    <input type="text" class="form-control w-100 quote-legal-entity-name" name="quote-legal-entity-name" required maxlength="100">
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <label for="quote-legal-entity-id" class="quote-legal-entity-id-label mb-1">{{ __('quote.legalEntity.id') }}</label>
                                                    <input type="text" class="form-control w-100 quote-legal-entity-id" name="quote-legal-entity-id" required maxlength="9">
                                                </div>

                                            </div>
                                            <div class="row pt-xl-3">
                                                <div class="col-12 col-lg-4">
                                                    <label for="quote-legal-entity-email" class="quote-legal-entity-email-label mb-1">{{ __('quote.email') }}</label>
                                                    <input type="email" class="form-control w-100 quote-legal-entity-email" name="quote-legal-entity-email" required maxlength="50">
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <label for="quote-legal-entity-phone" class="quote-legal-entity-phone-label mb-1">{{ __('quote.phone') }}</label>
                                                    <input type="number" class="form-control w-100 quote-legal-entity-phone" name="quote-legal-entity-phone" required max="799999999" min="600000000">
                                                    <div class="error" style="display:none;">{{ __('quote.phone.error') }}</div>
                                                </div>
                                            </div>
                                            <div class="row pt-xl-3">
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
                                            <div class="row pt-xl-3">
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


                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="secondary primary-color my-4 mb-lg-3">{{ __('quote.additionalInfo') }}</h4>
                                    </div>
                                </div>
                                <div class="row pt-xl-3">
                                    <div class="col-12 col-lg-4">
                                        <label for="quote-beneficiary" class="quote-beneficiary-label mb-1">{{ __('quote.beneficiary') }}</label>
                                        <select class="form-control w-100 quote-beneficiary valid" name="quote-beneficiary" required>
                                            <option value="A" selected="selected">{{ __('quote.insured') }}</option>
                                            <option value="T">{{ __('quote.holder') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <label for="quote-increased-value" class="quote-increased-value-label mb-1">{{ __('quote.increasedValue') }}</label>
                                        <select class="form-control w-100 quote-increased-value valid" name="quote-increased-value" required>
                                            <option value="0">0%</option>
                                            <option value="1">1%</option>
                                            <option value="2" selected="selected">2%</option>
                                            <option value="3">3%</option>
                                            <option value="4">4%</option>
                                            <option value="5">5%</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-lg-4">

                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="step-buttons my-3 row">
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
                </div>
            </form>
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
                <div class="container">

                    <div class="row mb-5">
                        <div id="product-info-widget" class="col text-center widget-payment-method">
                            <div class="col product-info-card text-center my-5 widget-payment-method">
                                <div class="card border-white px-4 pb-1">
                                    <h4></h4>
                                    <div class="separator bg-gradient-blue mb-3"></div>
                                    <div class="card-body text-center">
                                        <p class="card-text billing txt-dark-grey text-center">{{ __('widget.payment') }} <span class="payment-method txt-navy-blue"></span></p>
                                        <p class="card-text billing txt-dark-grey payment-amount text-center"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div id="payment-input" class="col px-3 px-md-5">
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
                    <div class="row">
                        <div id="hiring-method" class="col px-3 py-md-5 text-center" style="display: none;">
                            <div class="col">
                                <h4 class="text-center title mb-0">{{ __('quote.sign.select') }}</h4>
                                <div class="separator bg-navy-blue mb-3 mt-3"></div>
                                <div id="select-signing">

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="step-buttons my-3 row">
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

                    <div class="row mb-5 hand-write-method signing-method-screen" style="display: none;">
                        <div class="col">
                            <div class="row pt-1">
                                <div class="col hand-write text-center txt-navy-blue mb-3">
                                    <h4 class="text-center title my-3">{!! __('quote.sign.handWrite.title.step1') !!}</h4>
                                    <p class="instructions">{!! __('quote.sign.handWrite.instructions.step1') !!}</p>
                                </div>
                            </div>
                            <div class="row pt-1">
                                <div class="col hand-write text-center txt-navy-blue mb-3">

                                    <div class="col-12">
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
                                    <p></p>
                                    <div class="">
                                        <form id="send-policy-request" autocomplete="off">
                                            <div class="row my-4">
                                                <div class="col">
                                                    <input type="file" name="doc" class="doc" id="doc" required accept=".pdf,.jpg,.jpeg,.png">
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
                                                    <button id="send-policy-request-button" class="send-policy-request-button bg-lime-yellow text-white py-2 px-3 rounded border-0 position-relative">
                                                        <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                                                        {{ __('text.send') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div id="dowload-condition" class="row pt-1" style="display: none;">
                                <div class="row pt-1">
                                    <div class="col hand-write text-center txt-navy-blue mb-3">
                                        <h4 class="text-center title my-3">{!! __('quote.sign.handWrite.title.poliza.step1') !!}</h4>
                                        <p class="instructions">{!! __('quote.sign.handWrite.instructions.poliza.step1') !!}</p>
                                    </div>
                                </div>
                                <div class="col-12 hand-write text-center txt-navy-blue mb-3">
                                    <p class="">{!! __('quote.sign.handWrite.instructionscp.step1') !!}</p>
                                    <div class="col-12">
                                        <form id="quote-download-policy-cp-form" action="/download" method="get">
                                            <input type="hidden" name="docId" class="docId" value="">
                                            <input type="hidden" name="productor" class="productor" value="">
                                            <input type="hidden" name="source" class="source" value="">
                                            <input type="hidden" name="type" class="type" value="">
                                            <input type="hidden" name="format" class="format" value="">
                                            <input type="hidden" name="downloadType" class="downloadType" value="document">
                                            <input type="submit" id="quote-download-policy-cp-request" class="quote-download-policy-cp-request"  value="{!! __('text.download') !!}">
                                        </form>
                                        <form id="send-policy-request-cp" autocomplete="off">
                                            <div class="select-file row my-4">
                                                <div class="col-12 col-md-6 pt-2 text-right mb-2 mb-md-0">
                                                    <label for="file">{!! __('uploadPolicyRequest.pick.file') !!}</label>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <select class="form-control file" id="file" name="file" autocomplete="off" required="">
                                                        <option value="" disabled="" selected=""></option>
                                                        <option value="CP">{!! __('quote.sign.CP') !!}</option>
                                                        <option value="CG">{!! __('quote.sign.CG') !!}</option>
                                                    </select>
                                                    <input type="hidden" name="docId" class="docId" value="">
                                                    <input type="hidden" name="folderId" class="folderId" value="">
                                                </div>
                                            </div>
                                            <div class="row my-4">
                                                <div class="col">
                                                    <input type="file" name="doc" class="doc" id="doc-cp" required accept=".pdf,.jpg,.jpeg,.png">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <input type="hidden" name="refId" class="refId" value="">
                                                    <input type="hidden" name="productor" class="productor" value="">

                                                    <input type="hidden" name="docId" class="docId" value="444">
                                                    <input type="hidden" name="folderId" class="folderId" value="11">
                                                    <input type="hidden" name="docType" class="docType" value="application/pdf">
                                                    <input type="hidden" name="sendType" class="sendType" value="policyRequest">
                                                    <button id="send-policy-request-button" class="send-policy-request-button bg-lime-yellow text-white py-2 px-3 rounded border-0 position-relative">
                                                        <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                                                        {{ __('text.send') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div style="display:none;">
                                    <div class="col-12">
                                        <form id="quote-download-policy-cg-form" action="/download" method="get">
                                            <input type="hidden" name="docId" class="docId" value="">
                                            <input type="hidden" name="productor" class="productor" value="">
                                            <input type="hidden" name="source" class="source" value="">
                                            <input type="hidden" name="type" class="type" value="">
                                            <input type="hidden" name="format" class="format" value="">
                                            <input type="hidden" name="downloadType" class="downloadType" value="document">
                                            <input type="submit" id="quote-download-policy-cg-request" class="quote-download-policy-cg-request"  value="{!! __('text.download') !!}">
                                        </form>
                                        <form id="send-policy-request-cg" autocomplete="off">
                                            <div class="row my-4">
                                                <div class="col">
                                                    <input type="file" name="doc" class="doc" id="doc-cg" required accept=".pdf,.jpg,.jpeg,.png">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col text-center">
                                                    <input type="hidden" name="refId" class="refId" value="">
                                                    <input type="hidden" name="productor" class="productor" value="">

                                                    <input type="hidden" name="docId" class="docId" value="442">
                                                    <input type="hidden" name="folderId" class="folderId" value="10">
                                                    <input type="hidden" name="docType" class="docType" value="application/pdf">
                                                    <input type="hidden" name="sendType" class="sendType" value="policyRequest">
                                                    <button id="send-policy-request-button" class="send-policy-request-button bg-lime-yellow text-white py-2 px-3 rounded border-0 position-relative">
                                                        <i class="fas fa-circle-notch fa-spin loadingIcon"></i>
                                                        {{ __('text.send') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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


    </div>

@endsection

@include('app.layouts.modal')
