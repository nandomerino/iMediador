<?php
namespace App\Http\Middleware;

use App;
use App\Http\Controllers\PMWS;
use DebugBar\DebugBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

const QUESTION_TYPE_TEXT = "A";
const QUESTION_TYPE_DATE = "F";
const QUESTION_TYPE_NUMBER = "N";
const ANSWER_SUFFIX_TEXT = "_text";
const ANSWER_SUFFIX_DATE = "_date";
const ANSWER_SUFFIX_NUMBER = "_number";
const ANSWER_SUFFIX_SELECT = "_select";
const ANSWER_SUFFIX_RADIO = "_radio";
const ANSWER_SUFFIX_GROUP = "_group";
const ANSWER_RADIO_HIDDEN = "RADIO_HIDDEN";
const ANSWER_RADIO_YES = "RADIO_YES";
const ANSWER_RADIO_NO = "RADIO_NO";
const ANSWER_RADIO_UNSET = "RADIO_UNSET";

const HOLDER_TYPE_F = "F";
const HOLDER_TYPE_J = "J";

const COMPANY_ADDRESS_TYPE_I = "I";
const COMPANY_ADDRESS_TYPE_O = "O";

class PMWShandler
{
    private $PMWS;
    private $language;
    private $user;
    private $pass;
    private $gestor;

    /**
     * PMWShandler constructor
     */
    public function __construct()
    {
        $this->PMWS = new PMWS();
        $this->getSessionInfo();
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @param mixed $pass
     */
    public function setPass($pass): void
    {
        $this->pass = $pass;
    }

    function getSessionInfo(){
        $this->getLanguage();
        $this->user = session('login.user');
        $this->pass = session('login.pass');
        $this->gestor = session('login.gestor');
        $this->userPM = session('login.userPM');
        // file_put_contents('/var/www/vhosts/wldev.es/imediador.wldev.es/public/test.txt', "User: " . session('login.user') . " | Pass: " . session('login.pass') );
    }

    /**
     * @param $user
     * @param $pass
     * @param $gestor
     * @param $loginType
     * @param $action
     * @return bool
     * @throws \SoapFault
     *
     * Validates login information and stores retrieved data into session
     */
    public function login($user, $pass, $gestor, $loginType, $action, $userPM = null)
    {
        switch( $loginType ){
            case "app-login":
                if( $gestor && strlen( $gestor) > 0){
                    $info = $this->PMWS->login($user, $pass, $this->language, $gestor);
                }else{
                    $info = $this->PMWS->login($user, $pass, $this->language);
                }
                break;

            case "private-login":
                $info = $this->PMWS->loginInt($user, $pass, $this->language, $userPM);
                break;
        }
        app('debugbar')->info($info);
        $data = $info->return;

        if( $data->correcto == "S") {
            // Store user info in the session and redirects to app home
            if (property_exists($data->datosSalida, "listaParametros")) {
                foreach ($data->datosSalida->listaParametros as $row) {
                    switch( $row->nombreParametro ){
                        case "P_TEXTO_HOME_1":
                            $homeMessage1 = $row->valorParametro;
                            break;
                        case "P_TEXTO_HOME_2":
                            $homeMessage2 = $row->valorParametro;
                            break;
                    }

                }
            } else {
                $homeMessage1 = null;
                $homeMessage2 = null;
            }

            if (property_exists($data->datosAgrProductos, "listaAgrProductos")) {

                if (!is_array($data->datosAgrProductos->listaAgrProductos)) {

                    $products[$data->datosAgrProductos->listaAgrProductos->codigo]["name"] = $data->datosAgrProductos->listaAgrProductos->descripcion;

                } else {

                    foreach ($data->datosAgrProductos->listaAgrProductos as $row) {

                        $products[$row->codigo]["name"] = $row->descripcion;

                    }
                }

            } else {
                $products = null;
            }

            if (property_exists($data->datosCuadrosHome, "listaCuadrosHome")) {
                $i = 0;
                foreach ($data->datosCuadrosHome->listaCuadrosHome as $row) {
                    // Info boxes
                    $infoBoxes[$i]["data"] = $row->numero;
                    $infoBoxes[$i]["name"] = $row->texto;

                    // Recent activity blocks
                    if( isset($row->detalleActividad->listaDetalleActividad) && is_array($row->detalleActividad->listaDetalleActividad)){

                        $recentActivity[$i]["data"] = $row->numero;
                        $recentActivity[$i]["name"] = $row->texto;
                        foreach ( $row->detalleActividad->listaDetalleActividad as $key => $value ) {
                            if($key == 0){
                                $recentActivity[$i]["table"]["header"] = $value;
                            }else{
                                $recentActivity[$i]["table"]["rows"][$key] = $value;
                            }
                        }
                    }

                    $i++;
                }
            } else {
                $infoBoxes = null;
                $recentActivity = null;
            }

            if (property_exists($data, "datosSliders")) {
                // Sliders to show
                if( isset($data->datosSliders->idSlider) && is_array($data->datosSliders->idSlider)){
                    $showSliders = $data->datosSliders->idSlider;
                }
            }else{
                $showSliders = null;
            }

            session([
                'login' => [
                    'user' => $user,
                    'pass' => $pass,
                    'gestor' => $gestor,
                    'loginType' => $loginType,
                    'userPM' => $userPM,
                    'action' => $action,
                    'loggedSince' => time(),
                ],
                'home' => [
                    'homeMessage1' => $homeMessage1,
                    'homeMessage2' => $homeMessage2,
                    'infoBoxes' => $infoBoxes,
                    'recentActivity' => $recentActivity,
                    'showSliders' => $showSliders
                ],
                'quote' => [
                    'products' => $products
                ]
            ]);

            $response = true;

        }else{
            $response = $data->mensajeError;
        }

        return $response;
    }

    /**
     * @param $productor
     * @param $productGroup
     * @param null $pmUserCode
     * @return bool
     * @throws \SoapFault
     *
     * Gets subproducts of the selected product
     */
    function getProductVariations($productor, $productGroup, $entryChannel = null, $application = null, $u = null, $p = null)
    {

        if($u != null && $p != null){
            $this->user = $u;
            $this->pass = $p;
        }

        // app('debugbar')->info($this->user . " | " . $this->pass . " | " . $this->language . " | " . $productor . " | " . $productGroup . " | " . $entryChannel . " | " . $application . " | " . $pmUserCode);
        $response = $this->PMWS->getProductVariations($this->user, $this->pass, $this->language, $productor, $productGroup, $entryChannel, $application, $this->userPM);
        //app('debugbar')->info($response);


        $data = $response->return;

        if( $data->correcto == "S" ){
            foreach( $data->datosProductos->listaProductos as $row ){
                $modalityList = array();
                $i = $row->codigo;
                $productVariations[$i]['name'] = $row->descripcion;
                $productVariations[$i]['default'] = $row->porDefecto;
                $productVariations[$i]['value'] = $row->descripcionImporte;
                $productVariations[$i]['option'] = $row->opcionTarificacion;
                if ( ! is_array($row->productosModalidad->listaValores)) {
                    $modalityList[0]['modalityId'] = $row->productosModalidad->listaValores->codigo;
                    $modalityList[0]['modalityName'] = $row->productosModalidad->listaValores->descripcion;
                } else {
                    $j=0;
                    foreach ($row->productosModalidad->listaValores as $modality) {
                        $modalityList[$j]['modalityId'] = $modality->codigo;
                        $modalityList[$j]['modalityName'] = $modality->descripcion;
                        $j++;
                    }
                }
                $productVariations[$i]['modalityList'] = $modalityList;
                //$productVariations[$i]['modalityId'] = $row->productosModalidad->listaValores->codigo;
                //$productVariations[$i]['modalityName'] = $row->productosModalidad->listaValores->descripcion;
                $productVariations[$i]['reverseQuote'] = $row->opcionTarificaInversa;
                $productVariations[$i]['WS'] = $row;
            }
        }else{
            $productVariations = $data->mensajeError;
        }

        //guardamos en la sesion
        session([
            'quote' => [
                'productVariations' => $productVariations
            ]
        ]);

        return $productVariations;
    }



    /**
     * @param string $productor
     * @return bool
     * @throws \SoapFault
     *
     * Gets all productores for logged user
     */
    function getProductores($productor = "")
    {
        $response = $this->PMWS->getProductors($this->user, $this->pass, $this->language, $productor);
        // app('debugbar')->info($response);
        $data = $response->return;
        if( $data->correcto == "S" ){
            $i=0;
            foreach( $data->listaProductores->array as $row ){
                $productores[$i]['id'] = $row->codigoProductor;
                $productores[$i]['name'] = $row->nombreProductor;
                $i++;
            }
        }else{
            $productores = false;
        }

        session([
            'productores' => $productores
            ]);
        return $productores;
    }

    /**
     * Gets current language and turns it into the PM WS equivalent to use
     */
    function getLanguage()
    {
        $locale = App::getLocale();

        // Gets all languages from ERP
        // $WSlanguages = $this->PMWS->getLanguages();

        $WSlocales['es'] = "C";
        $WSlocales['cat'] = "A";
        $WSlocales['eus'] = "E";
        $WSlocales['gal'] = "G";

        switch ($locale) {
            case "cat":
                $lang = $WSlocales['cat'];
                break;
            case "eus":
                $lang = $WSlocales['eus'];
                break;
            case "gal":
                $lang = $WSlocales['gal'];
                break;
            case "es":
            default:
                $lang = $WSlocales['es'];
        }

        $this->language = $lang;

    }


    /**
     * Gets all languages and turns it into the PM WS equivalent to use
     */
    function getLanguages()
    {
        // Gets all languages from ERP
        $response = $this->PMWS->getLanguages();
        //app('debugbar')->info($response);
        if ($response) {
            foreach($response->Idioma as $entry){
                if($entry->activoIdioma == "S"){
                    $result[$entry->idIdioma->__toString()] = $entry->descIdioma->__toString();
                }
            }
        } else {
            $result = $response;
        }
        //app('debugbar')->info($result);
        return $result;


    }

    /**
     * @return bool
     * @throws \SoapFault
     */
    function getJobs()
    {

        $response = $this->PMWS->getProfessions($this->user, $this->pass, $this->language);
        $data = $response->return;
        if( $data->correcto == "S" ){
            foreach( $data->arrayobjprofesion->array as $row ){
                $jobs[$row->codigoProfesion]['name'] = $row->nombreProfesion;
                $jobs[$row->codigoProfesion]['orden'] = $row->orden;
                $jobs[$row->codigoProfesion]['grupoAjeno'] = $row->grupoAjeno;
                $jobs[$row->codigoProfesion]['grupoPropio'] = $row->grupoPropio;
            }
        }else{
            $jobs = $data->mensajeError;
        }
        return $jobs;

    }

    /**
     * @param $user
     * @param $pass
     * @param $language
     * @param null $productor
     * @param $productGroup
     * @param $productVariationId
     * @param null $pmUserCode
     *
     * Gets extra info of the chosen product
     */
    function getProductConfiguration($productor = null, $productId, $productModalityId, $entryChannel = null, $application = null, $modifiedField = null, $u = null, $p = null)
    {

        if($u != null && $p != null){
            $this->user = $u;
            $this->pass = $p;
        }
        // app('debugbar')->info($this->user . " | " . $this->pass . " | " . $this->language . " | " . $productor . " | " . $productId . " | " . $productVariationId . " | " . $entryChannel . " | " . $application);

        $response = $this->PMWS->getProductConfiguration($this->user, $this->pass, $this->language, $productor, $productId, $productModalityId, $entryChannel, $application, $this->userPM, $modifiedField);
        //app('debugbar')->info('pmwshandler getProductConfiguration');
        //app('debugbar')->info($response);

        $data = $response->return;
        if( $data->correcto == "S" ){

            foreach( $data->datosSalida->listaParametros as $row ){
                $productConfig[$row->nombreParametro] = $row->valorParametro;
            }

            foreach( $data->datosConfProducto->listaCampos as $row ){
                // Get benefit codes
                $productConfig[$row->nombre]["WS"] = $row;

                if( $row->nombre == "P_FECHA_NACIMIENTO_CLIENTE"){
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                }

                if( $row->nombre == "P_TALLA"){
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    $productConfig[$row->nombre]["min"] = $row->valorMinimo;
                    $productConfig[$row->nombre]["max"] = $row->valorMaximo;
                }

                if( $row->nombre == "P_PESO"){
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    $productConfig[$row->nombre]["min"] = $row->valorMinimo;
                    $productConfig[$row->nombre]["max"] = $row->valorMaximo;
                }

                if( $row->nombre == "P_SEXO"){
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    foreach( $row->listaValores->listaValores as $innerRow ) {
                        $productConfig[$row->nombre]["values"][$innerRow->codigo] = $innerRow->descripcion;
                    }
                }

                if( $row->nombre == "P_FRANQUICIA"){
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    foreach( $row->listaValores->listaValores as $innerRow ) {
                        $productConfig[$row->nombre]["values"][$innerRow->codigo] = $innerRow->descripcion;
                    }
                }

                if( $row->nombre == "P_PERIODO_COBERTURA"){
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    $productConfig[$row->nombre]["hidden"] = $row->esOculto;
                    $productConfig[$row->nombre]["fieldType"] = $row->tipoCampoHTML;
                    $productConfig[$row->nombre]["attributes"] = $row->atributosHTML;
                    if( is_array( $row->listaValores->listaValores ) ){
                        $productConfig[$row->nombre]["array"] = true;
                        foreach( $row->listaValores->listaValores as $innerRow ) {
                            $productConfig[$row->nombre]["values"][$innerRow->codigo] = $innerRow->descripcion;
                            //$productConfig[$row->nombre]["values"]["codigo"] = $innerRow->codigo;
                        }
                    }else{
                        $productConfig[$row->nombre]["array"] = false;
                        $productConfig[$row->nombre]["values"][$row->listaValores->listaValores->codigo] = $row->listaValores->listaValores->descripcion;
                        $productConfig[$row->nombre]["values"]["codigo"] = $row->listaValores->listaValores->codigo;
                    }
                }

                if( $row->nombre == "P_PROFESION_CLIENTE"){
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    foreach( $row->listaValores->listaValores as $innerRow ) {
                        $productConfig[$row->nombre]["values"][$innerRow->codigo] = $innerRow->descripcion;
                    }
                }

                if( $row->nombre == "P_REGIMEN_SEG_SOCIAL"){
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    $productConfig[$row->nombre]["fieldType"] = $row->tipoCampoHTML;
                    $productConfig[$row->nombre]["attributes"] = $row->atributosHTML;
                    $productConfig[$row->nombre]["hidden"] = $row->esOculto;

                    if( $row->listaValores != null ) {
                        if (is_array($row->listaValores->listaValores)) {
                            foreach ($row->listaValores->listaValores as $innerRow) {
                                $productConfig[$row->nombre]["values"][$innerRow->codigo] = $innerRow->descripcion;
                            }
                        } else {
                            $productConfig[$row->nombre]["values"][$row->listaValores->listaValores->codigo] = $row->listaValores->listaValores->descripcion;
                        }
                    }
                }

                if( $row->tipoCampoCobertura == "C"){
                    $productConfig["coberturas"][$row->cobertura]["name"] = $row->nombre;
                    $productConfig["coberturas"][$row->cobertura]["min"] = $row->valorMinimo;
                    $productConfig["coberturas"][$row->cobertura]["max"] = $row->valorMaximo;
                    $productConfig["coberturas"][$row->cobertura]["fieldType"] = $row->tipoCampoHTML;
                    $productConfig["coberturas"][$row->cobertura]["attributes"] = $row->atributosHTML;
                    $productConfig["coberturas"][$row->cobertura]["label"] = $row->etiquetaPre;
                }

                if( $row->tipoCampoCobertura == "D"){
                    $productConfig["duracion"][$row->cobertura]["name"] = $row->nombre;
                    $productConfig["duracion"][$row->cobertura]["min"] = $row->valorMinimo;
                    $productConfig["duracion"][$row->cobertura]["max"] = $row->valorMaximo;
                    $productConfig["duracion"][$row->cobertura]["fieldType"] = $row->tipoCampoHTML;
                    $productConfig["duracion"][$row->cobertura]["attributes"] = $row->atributosHTML;
                    $productConfig["duracion"][$row->cobertura]["label"] = $row->etiquetaPre;

                }

                if( $row->nombre == "P_CLAVE_COMERCIAL"){
                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    $productConfig[$row->nombre]["fieldType"] = $row->tipoCampoHTML;
                    $productConfig[$row->nombre]["attributes"] = $row->atributosHTML;
                    $productConfig[$row->nombre]["hidden"] = $row->esOculto;

                    if( $productConfig[$row->nombre]["fieldType"] == "select"){

                        //app('debugbar')->info($row->listaValores->listaValores);
                        if( is_array ($row->listaValores->listaValores) ){
                            foreach( $row->listaValores->listaValores as $innerRow ) {
                                $productConfig[$row->nombre]["values"][$innerRow->codigo] = $innerRow->descripcion;
                            }
                        }else{
                            $productConfig[$row->nombre]["values"][$row->listaValores->listaValores->codigo] = $row->listaValores->listaValores->descripcion;
                        }

                    }
                }

                if( $row->nombre == "P_DESCUENTO_06" ||
                    $row->nombre == "P_ANYOS_DTO_06" ||
                    $row->nombre == "P_SOBREPRIMA_DEL" ||
                    $row->nombre == "P_DTO_COMISION_MED" ||
                    $row->nombre == "P_DTO_COMISION_DEL" ||
                    $row->nombre == "P_RECARGO_FINANCIACION" ||
                    $row->nombre == "P_CANAL_COBRO" ){

                    $productConfig[$row->nombre]["name"] = $row->etiquetaPre;
                    $productConfig[$row->nombre]["hidden"] = $row->esOculto;
                    $productConfig[$row->nombre]["fieldType"] = $row->tipoCampoHTML;
                    $productConfig[$row->nombre]["attributes"] = $row->atributosHTML;
                    if( $row->listaValores != null ) {
                        if (is_array($row->listaValores->listaValores)) {
                            $productConfig[$row->nombre]["array"] = true;
                            foreach ($row->listaValores->listaValores as $innerRow) {
                                $productConfig[$row->nombre]["values"][$innerRow->codigo] = $innerRow->descripcion;
                            }
                        } else {
                            $productConfig[$row->nombre]["array"] = false;
                            $productConfig[$row->nombre]["values"][$row->listaValores->listaValores->codigo] = $row->listaValores->listaValores->descripcion;
                        }
                    }
                }


            }
        }else{
            $productConfig = $data->mensajeError;
        }

        session([
            'productConfig' => $productConfig
        ]);

        return $productConfig;
    }

    /**
     * @param $productor - (optional) selected productor
     * @param $option - Proporcionada con las variaciones
     * @param $productId - selected product
     * @param $profession - selected profession
     * @param $birthdate - user birthdate
     * @param $gender - user gender
     * @param $height - user height
     * @param $weight - user weigth
     * @param $enfCob - cobertura por enfermedad
     * @param $enfSub - subsidio por enfermedad
     * @param $accCob - cobertura por accidente
     * @param $accSub - subsidio por accidente
     * @param $hospCob - cobertura por hospitalizacion
     * @param $hospSub - subsidio por hospitalizacion
     * @return bool
     * @throws \SoapFault
     *
     * Gets rates for current quote
     */
    function getRates( $parameters )
    {

        if($parameters["u"] != null && $parameters["p"] != null){
            $parameters["user"] = $parameters["u"] ;
            $parameters["pass"] = $parameters["p"] ;
        }else{
            $parameters["user"] = $this->user;
            $parameters["pass"] = $this->pass;
        }

        $parameters["language"] = $this->language;
        $parameters["pmUserCode"] = $this->userPM;

        //app('debugbar')->info($parameters);
        $response = $this->PMWS->getRates($parameters);
        //app('debugbar')->info($response);

        $data = $response->return;
        if( $data->correcto == "S" ){
            $headers = false;
            $i = 0;
            $currentBillingCycles = 0;
            if (! is_array($data->datosOpcionCuadro->listaOpcionCuadro)){

               //app('debugbar')->info("Creando Array listaOpcionCuadro");
               //app('debugbar')->info($data->datosOpcionCuadro->listaOpcionCuadro);

               //we build as it was an array with 1 row
                 $listaAux = array();
                 $listaAux[] = $data->datosOpcionCuadro->listaOpcionCuadro;
                 $data->datosOpcionCuadro->listaOpcionCuadro = $listaAux;
            }

//            if( is_array( $data->datosOpcionCuadro->listaOpcionCuadro) ) {

                // prepare header array;
                $rates["headers"][0] = __('quote.text.exemption');
                $rates["headers"][1] = __('quote.header1');
                $rates["headers"][2] = __('quote.header2');
                $rates["headers"][3] = __('quote.header3');
                $rates["headers"][4] = __('quote.header4');

                $y = 0;
                $rates["table"] = array();
                $colsNumber = 1;
                $xSet = 0;
                foreach ($data->datosOpcionCuadro->listaOpcionCuadro as $row) {

                    // general data
                    foreach ($row->datosGenerales->array as $row2) {
                        if ($row2->nombre == "P_PRODUCTO_TARIFICADO") {
                            $rates["name"] = $row2->valor;
                        }
                    }

                    // billing cycles
                    $j = 0;
                    foreach ($row->tarificaciones->array as $row2) {
                        $rates["billingCycles"][$j] = $row2->formaPago;
                        $j++;
                    }


                    //==============================================
                    // Generate array to display on table
                    //vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv


                    // Update column

                    if( $row->coberturas->array[0]->franquicia == 0 ){
                        // Fix to display more than 1 set of results
                        /*
                        if($colsNumber > 4){
                            $colsNumber = 1;
                            $y = 0;
                            $xSet += 100;
                        }
                        $colsNumber++;
                        */
                        $y++;

                    }

                    // Get exemption (franquicia)
                    $x = $row->coberturas->array[0]->franquicia;
                    //$x = $row->coberturas->array[0]->franquicia + $xSet;
                    // $rates["table"][$x]['franquicia'] = $x;

                    // Get price
                    foreach (array_reverse($row->tarificaciones->array) as $row2) {
                        if( $row2->formaPago == 1){
                            $rates["table"][$x][$y]["price"] = $row2->primaNetaAnual;
                        }
                    }

                    // Get coverages (coberturas)
                    $j = 0;
                    foreach ($row->coberturas->array as $row2) {
                        $rates["table"][$x][$y]["coverages"][$j]["capital"] = $row2->capital;
                        $rates["table"][$x][$y]["coverages"][$j]["codigo"] = $row2->codigo;
                        $rates["table"][$x][$y]["coverages"][$j]["descripcion"] = $row2->descripcion;
                        $rates["table"][$x][$y]["coverages"][$j]["duracion"] = $row2->duracion;
                        $rates["table"][$x][$y]["coverages"][$j]["franquicia"] = $row2->franquicia;
                        $rates["table"][$x][$y]["coverages"][$j]["primaNeta"] = $row2->primaNeta;
                        $j++;
                    }

                    // Get quotes
                    $j = 0;
                    foreach (array_reverse($row->tarificaciones->array) as $row2) {
                        $rates["table"][$x][$y]["quotes"][$j]["formaPago"] = $row2->formaPago;
                        $rates["table"][$x][$y]["quotes"][$j]["primaNetaAnual"] = str_replace(".", ",", $row2->primaNetaAnual);
                        $rates["table"][$x][$y]["quotes"][$j]["primaNetaFraccionada"] = str_replace(".", ",", $row2->primaNetaFraccionada);
                        $rates["table"][$x][$y]["quotes"][$j]["primaTotalAnual"] = str_replace(".", ",", $row2->primaTotalAnual);
                        $rates["table"][$x][$y]["quotes"][$j]["recargosImpuestos"] = str_replace(".", ",", $row2->recargosImpuestos);
                        $j++;
                    }

                    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


                    // quotes (keep for compatibility with Widget)
                    $j = 0;
                    foreach (array_reverse($row->tarificaciones->array) as $row2) {
                        $rates["rows"][$i]["quotes"][$j]["formaPago"] = $row2->formaPago;
                        $rates["rows"][$i]["quotes"][$j]["primaNetaAnual"] = str_replace(".", ",", $row2->primaNetaAnual);
                        $rates["rows"][$i]["quotes"][$j]["primaNetaFraccionada"] = str_replace(".", ",", $row2->primaNetaFraccionada);
                        $rates["rows"][$i]["quotes"][$j]["primaTotalAnual"] = str_replace(".", ",", $row2->primaTotalAnual);
                        $rates["rows"][$i]["quotes"][$j]["recargosImpuestos"] = str_replace(".", ",", $row2->recargosImpuestos);
                        $j++;
                    }

                    $i++;
                    $x++;
               }
/*               
        // app('debugbar')->info($rates);
            }else{

                // app('debugbar')->info($data->datosOpcionCuadro->listaOpcionCuadro);
                // general data
                foreach ($data->datosOpcionCuadro->listaOpcionCuadro->datosGenerales->array as $row2) {
                    if ($row2->nombre == "P_PRODUCTO_TARIFICADO") {
                        $rates["name"] = $row2->valor;
                    }
                }

                $j = 0;
                // coverages
                if( is_array($data->datosOpcionCuadro->listaOpcionCuadro->coberturas->array) ) {
                    foreach ($data->datosOpcionCuadro->listaOpcionCuadro->coberturas->array as $row2) {
                        $rates["coverages"][$j]["capital"] = $row2->capital;
                        $rates["coverages"][$j]["codigo"] = $row2->codigo;
                        $rates["coverages"][$j]["descripcion"] = $row2->descripcion;
                        $rates["coverages"][$j]["duracion"] = $row2->duracion;
                        $rates["coverages"][$j]["franquicia"] = $row2->franquicia;
                        $rates["coverages"][$j]["primaNeta"] = $row2->primaNeta;
                        $j++;
                    }
                }else{
                    $row2 = $data->datosOpcionCuadro->listaOpcionCuadro->coberturas->array;
                    $rates["coverages"][$j]["capital"] = $row2->capital;
                    $rates["coverages"][$j]["codigo"] = $row2->codigo;
                    $rates["coverages"][$j]["descripcion"] = $row2->descripcion;
                    $rates["coverages"][$j]["duracion"] = $row2->duracion;
                    $rates["coverages"][$j]["franquicia"] = $row2->franquicia;
                    $rates["coverages"][$j]["primaNeta"] = $row2->primaNeta;
                }

                // quotes
                $j = 0;
                foreach (array_reverse($data->datosOpcionCuadro->listaOpcionCuadro->tarificaciones->array) as $row2) {
                    $rates["quotes"][$j]["formaPago"] = $row2->formaPago;
                    $rates["quotes"][$j]["primaNetaAnual"] = str_replace(".", ",", $row2->primaNetaAnual);
                    $rates["quotes"][$j]["primaNetaFraccionada"] = str_replace(".", ",", $row2->primaNetaFraccionada);
                    $rates["quotes"][$j]["primaTotalAnual"] = str_replace(".", ",", $row2->primaTotalAnual);
                    $rates["quotes"][$j]["recargosImpuestos"] = str_replace(".", ",", $row2->recargosImpuestos);
                    $j++;
                }

                $i++;
               }
*/
        }else{
            $rates = $data->mensajeError;
        }
        session([
            'rates' => $rates
        ]);
        return $rates;
    }

    /**
     * @param null $productor
     * @param $option
     * @param $price
     * @param null $franchise
     * @param string $jobType
     * @param $profession
     * @param $birthdate
     * @param $gender
     * @param $height
     * @param $weight
     * @param $period
     * @param $commercialKey
     * @param null $pmUserCode
     * @return mixed
     * @throws \SoapFault
     */
    function getRatesByPrice( $productor = null, $option, $productCode, $price, $franchise = null, $jobType = "A", $profession, $birthdate, $gender, $height, $weight, $duration, $commercialKey )
    {

        $response = $this->PMWS->getRatesByPrice($this->user, $this->pass, $this->language, $productor, $option, $productCode, $price, $franchise, $jobType, $profession, $birthdate, $gender, $height, $weight, $duration, $commercialKey, $this->userPM);
        // app('debugbar')->info($response);

        $data = $response->return;
        if( $data->correcto == "S" ){
            //app('debugbar')->info('getRatesByPrice Correcto');
            $headers = false;
            $i = 0;
            $currentBillingCycles = 0;
            
            if (! is_array( $data->datosOpcionCuadro->listaOpcionCuadro) ){
               //app('debugbar')->info("Creando Array listaOpcionCuadro");
               //app('debugbar')->info($data->datosOpcionCuadro->listaOpcionCuadro);

               //we build as it was an array with 1 row
                 $listaAux = array();
                 $listaAux[] = $data->datosOpcionCuadro->listaOpcionCuadro;
                 $data->datosOpcionCuadro->listaOpcionCuadro = $listaAux;

            }
            
            //if( is_array( $data->datosOpcionCuadro->listaOpcionCuadro) ) {

                // prepare header array;
                $rates["headers"][0] = __('quote.text.exemption');
                $rates["headers"][1] = __('quote.header1');
                $rates["headers"][2] = __('quote.header2');
                $rates["headers"][3] = __('quote.header3');
                $rates["headers"][4] = __('quote.header4');

                $y = 0;
                $rates["table"] = array();
                foreach ($data->datosOpcionCuadro->listaOpcionCuadro as $row) {

                    // general data
                    foreach ($row->datosGenerales->array as $row2) {
                        if ($row2->nombre == "P_PRODUCTO_TARIFICADO") {
                            $rates["name"] = $row2->valor;
                        }
                    }

                    // billing cycles
                    if( is_array($row->tarificaciones->array) ) {
                        $j = 0;
                        foreach ($row->tarificaciones->array as $row2) {
                            $rates["billingCycles"][$j] = $row2->formaPago;
                            $j++;
                        }
                    }else{
                        $rates["billingCycles"][0] = $row->tarificaciones->array->formaPago;
                    }



                    //==============================================
                    // Generate array to display on table
                    //vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv


                    // Update column
                    if( $row->coberturas->array[0]->franquicia == 0 ){
                        $y++;
                    }

                    // Get exemption (franquicia)
                    $x = $row->coberturas->array[0]->franquicia;
                    // $rates["table"][$x]['franquicia'] = $x;

                    // Get price
                    if( is_array($row->tarificaciones->array) ) {
                        foreach (array_reverse($row->tarificaciones->array) as $row2) {
                            if( $row2->formaPago == 1){
                                $rates["table"][$x][$y]["price"] = $row2->primaNetaAnual;
                            }
                        }
                    }else{
                        $rates["table"][$x][$y]["price"] = $row->tarificaciones->array->primaNetaAnual;
                    }

                    // Get coverages (coberturas)
                    $j = 0;
                    $capital = 0;
                    $gotFirstOne = false;
                    foreach ($row->coberturas->array as $row2) {
                        if( $row2->capital > 0 && !$gotFirstOne ){
                            $capital = $row2->capital;
                            $gotFirstOne = true;
                        }
                        $rates["table"][$x][$y]["coverages"][$j]["capital"] = $row2->capital;
                        $rates["table"][$x][$y]["coverages"][$j]["codigo"] = $row2->codigo;
                        $rates["table"][$x][$y]["coverages"][$j]["descripcion"] = $row2->descripcion;
                        $rates["table"][$x][$y]["coverages"][$j]["duracion"] = $row2->duracion;
                        $rates["table"][$x][$y]["coverages"][$j]["franquicia"] = $row2->franquicia;
                        $rates["table"][$x][$y]["coverages"][$j]["primaNeta"] = $row2->primaNeta;
                        $j++;
                    }
                    // Loads capital to display on the ratesByPrice option
                    $rates["table"][$x][$y]["capital"] = $capital;

                    // Get quotes
                    $j = 0;
                    if( is_array($row->tarificaciones->array) ) {
                        foreach (array_reverse($row->tarificaciones->array) as $row2) {
                            $rates["table"][$x][$y]["quotes"][$j]["formaPago"] = $row2->formaPago;
                            $rates["table"][$x][$y]["quotes"][$j]["primaNetaAnual"] = str_replace(".", ",", $row2->primaNetaAnual);
                            $rates["table"][$x][$y]["quotes"][$j]["primaNetaFraccionada"] = str_replace(".", ",", $row2->primaNetaFraccionada);
                            $rates["table"][$x][$y]["quotes"][$j]["primaTotalAnual"] = str_replace(".", ",", $row2->primaTotalAnual);
                            $rates["table"][$x][$y]["quotes"][$j]["recargosImpuestos"] = str_replace(".", ",", $row2->recargosImpuestos);
                            $j++;
                        }
                    }else{
                        $rates["table"][$x][$y]["quotes"][$j]["formaPago"] = $row->tarificaciones->array->formaPago;
                        $rates["table"][$x][$y]["quotes"][$j]["primaNetaAnual"] = str_replace(".", ",", $row->tarificaciones->array->primaNetaAnual);
                        $rates["table"][$x][$y]["quotes"][$j]["primaNetaFraccionada"] = str_replace(".", ",", $row->tarificaciones->array->primaNetaFraccionada);
                        $rates["table"][$x][$y]["quotes"][$j]["primaTotalAnual"] = str_replace(".", ",", $row->tarificaciones->array->primaTotalAnual);
                        $rates["table"][$x][$y]["quotes"][$j]["recargosImpuestos"] = str_replace(".", ",", $row->tarificaciones->array->recargosImpuestos);
                    }

                    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

                    $j = 0;
                    // quotes (keep for compatibility with Widget)
                    if( is_array($row->tarificaciones->array) ) {
                        foreach (array_reverse($row->tarificaciones->array) as $row2) {
                            $rates["rows"][$i]["quotes"][$j]["formaPago"] = $row2->formaPago;
                            $rates["rows"][$i]["quotes"][$j]["primaNetaAnual"] = str_replace(".", ",", $row2->primaNetaAnual);
                            $rates["rows"][$i]["quotes"][$j]["primaNetaFraccionada"] = str_replace(".", ",", $row2->primaNetaFraccionada);
                            $rates["rows"][$i]["quotes"][$j]["primaTotalAnual"] = str_replace(".", ",", $row2->primaTotalAnual);
                            $rates["rows"][$i]["quotes"][$j]["recargosImpuestos"] = str_replace(".", ",", $row2->recargosImpuestos);
                            $j++;
                        }
                    }else{
                        $rates["rows"][$i]["quotes"][$j]["formaPago"] = $row->tarificaciones->array->formaPago;
                        $rates["rows"][$i]["quotes"][$j]["primaNetaAnual"] = str_replace(".", ",", $row->tarificaciones->array->primaNetaAnual);
                        $rates["rows"][$i]["quotes"][$j]["primaNetaFraccionada"] = str_replace(".", ",", $row->tarificaciones->array->primaNetaFraccionada);
                        $rates["rows"][$i]["quotes"][$j]["primaTotalAnual"] = str_replace(".", ",", $row->tarificaciones->array->primaTotalAnual);
                        $rates["rows"][$i]["quotes"][$j]["recargosImpuestos"] = str_replace(".", ",", $row->tarificaciones->array->recargosImpuestos);
                    }

                    $i++;
                    $x++;
                }

/*                
                // app('debugbar')->info($rates);
            }else{

                // app('debugbar')->info($data->datosOpcionCuadro->listaOpcionCuadro);
                // general data
                foreach ($data->datosOpcionCuadro->listaOpcionCuadro->datosGenerales->array as $row2) {
                    if ($row2->nombre == "P_PRODUCTO_TARIFICADO") {
                        $rates["name"] = $row2->valor;
                    }
                }

                $j = 0;
                // coverages
                if( is_array($data->datosOpcionCuadro->listaOpcionCuadro->coberturas->array) ) {
                    foreach ($data->datosOpcionCuadro->listaOpcionCuadro->coberturas->array as $row2) {
                        $rates["coverages"][$j]["capital"] = $row2->capital;
                        $rates["coverages"][$j]["codigo"] = $row2->codigo;
                        $rates["coverages"][$j]["descripcion"] = $row2->descripcion;
                        $rates["coverages"][$j]["duracion"] = $row2->duracion;
                        $rates["coverages"][$j]["franquicia"] = $row2->franquicia;
                        $rates["coverages"][$j]["primaNeta"] = $row2->primaNeta;
                        $j++;
                    }
                }else{
                    $row2 = $data->datosOpcionCuadro->listaOpcionCuadro->coberturas->array;
                    $rates["coverages"][$j]["capital"] = $row2->capital;
                    $rates["coverages"][$j]["codigo"] = $row2->codigo;
                    $rates["coverages"][$j]["descripcion"] = $row2->descripcion;
                    $rates["coverages"][$j]["duracion"] = $row2->duracion;
                    $rates["coverages"][$j]["franquicia"] = $row2->franquicia;
                    $rates["coverages"][$j]["primaNeta"] = $row2->primaNeta;
                }

                // quotes
                $j = 0;
                foreach (array_reverse($data->datosOpcionCuadro->listaOpcionCuadro->tarificaciones->array) as $row2) {
                    $rates["quotes"][$j]["formaPago"] = $row2->formaPago;
                    $rates["quotes"][$j]["primaNetaAnual"] = str_replace(".", ",", $row2->primaNetaAnual);
                    $rates["quotes"][$j]["primaNetaFraccionada"] = str_replace(".", ",", $row2->primaNetaFraccionada);
                    $rates["quotes"][$j]["primaTotalAnual"] = str_replace(".", ",", $row2->primaTotalAnual);
                    $rates["quotes"][$j]["recargosImpuestos"] = str_replace(".", ",", $row2->recargosImpuestos);
                    $j++;
                }

                $i++;
            }
*/
        }else{
            $rates = $data->mensajeError;
        }
        session([
            'rates' => $rates
        ]);
        return $rates;
    }

    /**
     * @return bool
     * @throws \SoapFault
     */
    function getCityProvince($postalCode, $u = null, $p = null)
    {

        if($u != null && $p != null){
            $this->user = $u;
            $this->pass = $p;
        }
        $response = $this->PMWS->getMunicipalities($this->user, $this->pass, $this->language, $postalCode);
        //app('debugbar')->info($response);
        $data = $response->return;
        if( $data->correcto == "S" ){
            if( is_array($data->arrayobjpoblacion->array) ){
                foreach( $data->arrayobjpoblacion->array as $row ){
                    $cpInfo["cities"][$row->codigo] = $row->descripcion;
                    $cpInfo["provinces"][$row->codigo] = $row->descripcionProvincia;
                }
            }else{
                $cpInfo["cities"][$data->arrayobjpoblacion->array->codigo] = $data->arrayobjpoblacion->array->descripcion;
                $cpInfo["provinces"][$data->arrayobjpoblacion->array->codigo] = $data->arrayobjpoblacion->array->descripcionProvincia;
            }

        }else{
            $cpInfo = $data->mensajeError;
        }
        //app('debugbar')->info($cpInfo);
        return $cpInfo;

    }

    /**
     * @param $productor
     * @param $product
     * @return array
     */
    function getHealthForm($productor, $product, $commercialKey, $u = null, $p = null)
    {
        if($u != null && $p != null){
            $this->user = $u;
            $this->pass = $p;
        }
        $response = $this->PMWS->getHealthForm($this->user, $this->pass, $this->language, $productor, $product, $commercialKey);
        //app('debugbar')->info($response);
        $data = $response->return;
        if( $data->correcto == "S" ){
            $healthForm = array();
            $healthForm["id"] = $data->datosSalida->listaParametros->valorParametro;
            $healthForm["groups"] = array();
            foreach ($data->agrupaciones->listaAgrupaciones as $group) {
                if (!array_key_exists($group->codigoAgrupacion, $healthForm["groups"])) {
                    $healthForm["groups"][$group->codigoAgrupacion] = array();
                }
                $healthForm["groups"][$group->codigoAgrupacion]["bulkAnswer"] = $this->get_question_default_value($group->valoresDefecto);
                // $healthForm["groups"][$group->codigoAgrupacion]["bulkAnswer"] = ANSWER_RADIO_NO;
                $healthForm["groups"][$group->codigoAgrupacion]["desc"] = $group->descripcionAgrupacion;
                $healthForm["groups"][$group->codigoAgrupacion]["questions"] = array();

                if ( is_array($group->preguntas->listaPreguntas)){
                    foreach ($group->preguntas->listaPreguntas as $question) {
                        $healthForm["groups"][$group->codigoAgrupacion]["questions"][$question->codigoPregunta] = $this->buildQuestion($question);
                    }
                } else {
                    $healthForm["groups"][$group->codigoAgrupacion]["questions"][$group->preguntas->listaPreguntas->codigoPregunta] = $this->buildQuestion($group->preguntas->listaPreguntas);
                }
            }
            // generates HTML code
            $healthFormData["id"] = $healthForm["id"];
            $healthFormData["html"] = $this->healthFormToHTML($healthForm);
        } else {
            $healthFormData = $data->codigoError;
        }
        //app('debugbar')->info($healthFormData);

        return $healthFormData;
    }

    /**
     * obtenerCuestionarioSalud helper
     * @param $question
     * @return array
     */
    private function buildQuestion($question) {

        $result["desc"] = $question->descripcionPregunta;
        $result["required"] = $question->preguntaObligatoria == "S" ? true: false;
        $result["radio"] = $this->get_question_default_value($question->valorDefecto);
        $result["date"] = $question->fechaOcurrenciaObligatoria == "S" ? true: false;
        $result["type"] = $question->tipoDato;

        if ( !empty($question->listaValores->listaClaveValor) ) {
            $result["options"] = array();
            foreach ($question->listaValores->listaClaveValor as $option) {
                $result["options"][$option->clave] = $option->valor;
            }
        }

        return $result;
    }

    /**
     * obtenerCuestionarioSalud helper
     * @param $value
     * @return mixed
     */
    private function get_question_default_value($value) {
        if ( $value == null ) {
            return ANSWER_RADIO_HIDDEN;
        } else if ( $value == "S" ) {
            return ANSWER_RADIO_YES;
        } else if ( $value == "N" ) {
            return ANSWER_RADIO_NO;
        } else if ( $value == "NM" ) {
            return ANSWER_RADIO_UNSET;
        }
    }

    private function healthFormToHTML($form){

        // saving all output to return it
        ob_start();
        //app('debugbar')->info("before foreach");
        foreach ($form["groups"] as $groupId => $group) {
            ?>
            <div class="row">
                <div class="col-12 col-lg-9 mt-4">
                    <h5 class="secondary"><?php echo $group["desc"]; ?></h5>
                </div>

                <?php
                //app('debugbar')->info("X");
                $hasBulkQuestion = false;
                // default values to prevent undefined variables
                $bYesActive = false;
                $bNoActive = true;
                if ( $group["bulkAnswer"] != ANSWER_RADIO_HIDDEN ) {
                    //app('debugbar')->info("Y");
                    $hasBulkQuestion = true;
                    if ( $group["bulkAnswer"] == ANSWER_RADIO_YES ) {
                        $bYesActive = true;
                        $bNoActive = false;
                        $show = true;
                    } else if ( $group["bulkAnswer"] == ANSWER_RADIO_NO ) {
                        $bYesActive = false;
                        $bNoActive = true;
                        $show = true;
                    } else if ( $group["bulkAnswer"] == ANSWER_RADIO_UNSET ) {
                        $bYesActive = false;
                        $bNoActive = false;
                        $show = false;
                    }
                    ?>
                    <div class="col-12 col-lg-3 btn-group btn-group-toggle answer-radio-group group-question mt-4 justify-content-lg-end" data-toggle="buttons" data-ref-id="group-<?php echo $groupId; ?>">
                        <label class="btn btn-radio btn-radio-left text-center mt-lg-0 <?php if ($bYesActive) { echo "active"; } ?>">
                            <input type="radio" value="SI" class="form-check-input position-static" name="<?php echo $groupId . ANSWER_SUFFIX_GROUP; ?>" <?php if ($bYesActive) { echo " checked "; } ?> ><?php echo __('text.yes') ?>
                        </label>
                        <label class="btn btn-radio btn-radio-right text-center mt-lg-0 <?php if ($bNoActive) { echo "active"; } ?>">
                            <input type="radio" value="NO" class="form-check-input position-static" name="<?php echo $groupId . ANSWER_SUFFIX_GROUP; ?>" <?php if ($bNoActive) { echo " checked "; } ?> ><?php echo __('text.no') ?>
                        </label>
                    </div>

                <?php
                } else {
                    $show = true;
                }
                ?>
            </div>
            <div class="row <?php if ( $hasBulkQuestion ) { echo "questions-group"; } ?> collapse <?php if ( $show ) { echo "show"; } ?>" id="group-<?php echo $groupId; ?>">
                <div class="col-12">
                    <?php
                    foreach ($group["questions"] as $qId => $q) {
                        ?>

                        <fieldset class="form-group">
                            <div class="row">
                                <legend class="col-12 col-lg-9 col-form-label">
                                    <p class="second mb-2"><?php echo $q["desc"]; ?></p>
                                </legend>
                                <?php

                                //app('debugbar')->info("Z");
                                if ( $q["radio"] != ANSWER_RADIO_HIDDEN ) {

                                    if ( $q["radio"] == ANSWER_RADIO_YES ) {
                                        $yesActive = true;
                                        $noActive = false;
                                    } else if ( $q["radio"] == ANSWER_RADIO_NO ) {
                                        $yesActive = false;
                                        $noActive = true;
                                    } else {
                                        $yesActive = false;
                                        $noActive = false;
                                    }
                                    ?>
                                    <div class="col-12 col-lg-3 btn-group btn-group-toggle answer-radio-group single-question justify-content-lg-end" data-toggle="buttons">
                                        <label class="btn btn-radio btn-radio-left text-center mt-lg-0 <?php if ($yesActive) { echo "active"; } ?>">
                                            <input type="radio" class="form-check-input position-static" name="<?php echo $qId . ANSWER_SUFFIX_RADIO; ?>" id="<?php echo 'y' . $qId; ?>" value="SI" <?php if ($q["required"]) { echo "required"; } if ($yesActive) { echo " checked "; } ?> data-id="<?php echo $qId; ?>"><?php echo __('text.yes') ?>
                                        </label>
                                        <label class="btn btn-radio btn-radio-right text-center mt-lg-0 <?php if ($noActive || $bNoActive) { echo "active"; } ?>">
                                            <input type="radio" class="form-check-input position-static" name="<?php echo $qId . ANSWER_SUFFIX_RADIO; ?>" id="<?php echo 'n' . $qId; ?>" value="NO" <?php if ($q["required"]) { echo "required"; } if ($noActive || $bNoActive) { echo " checked "; } ?> data-id="<?php echo $qId; ?>"><?php echo __('text.no') ?>
                                        </label>
                                    </div>
                                    <?php
                                }
                                if ( $yesActive) {
                                    $vClass = "d-block d-lg-flex";
                                } else if ( $noActive || $bNoActive ) {
                                    $vClass = "d-none";
                                } else {
                                    if ( $q["radio"] == ANSWER_RADIO_HIDDEN || $q["radio"] == ANSWER_RADIO_YES ) {
                                        $vClass = "d-block d-lg-flex";
                                    } else if ( $q["radio"] == ANSWER_RADIO_NO || $q["radio"] == ANSWER_RADIO_UNSET ) {
                                        $vClass = "d-none";
                                    }
                                }
                                ?>
                                <div class="answer-wrapper w-100 <?php echo $vClass; ?>" data-id="<?php echo $qId; ?>">
                                    <?php
                                    if ( !empty($q["options"]) ) {
                                        ?>
                                        <div class="col-12 col-lg-4 text-lg-right text-left">
                                            <select class="form-control" name="<?php echo $qId . ANSWER_SUFFIX_SELECT; ?>" <?php if ( $q["required"] && ($q["radio"] == ANSWER_RADIO_HIDDEN || $q["radio"] == ANSWER_RADIO_YES || $yesActive) ) { echo "required"; } ?>>
                                                <option disabled selected value> <?php echo __('text.choose') ?> </option>
                                                <?php
                                                foreach ($q["options"] as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php
                                    } else {
                                        if ( $q["type"] == QUESTION_TYPE_TEXT ) {
                                            ?>
                                            <div class="col-12 col-lg-8">
                                                <textarea name="<?php echo $qId . ANSWER_SUFFIX_TEXT; ?>" rows="4" placeholder="<?php echo __('text.enterDetails') ?>" <?php if ( $q["required"] && ($q["radio"] == ANSWER_RADIO_HIDDEN || $q["radio"] == ANSWER_RADIO_YES || $yesActive) ) { echo "required"; } ?>></textarea>
                                            </div>
                                            <?php
                                        } else if ( $q["type"] == QUESTION_TYPE_NUMBER ) {
                                            ?>
                                            <div class="col-12 col-lg-4">
                                                <input type="number" name="<?php echo $qId . ANSWER_SUFFIX_NUMBER; ?>" value="" <?php if ( $q["required"] && ($q["radio"] == ANSWER_RADIO_HIDDEN || $q["radio"] == ANSWER_RADIO_YES || $yesActive) ) { echo "required"; } ?>>
                                            </div>
                                            <?php
                                        }
                                    }

                                    if ( $q["date"] ) {
                                        ?>
                                        <div class="col-12 col-lg-4">
                                            <div class="form-group">
                                                <div class="input-group date datetimepickerHealth">
                                                    <input type="text"  class="form-control date-input" placeholder="<?php echo __('text.dateFormat') ?>" name="<?php echo $qId . ANSWER_SUFFIX_DATE; ?>" value="" <?php if ( !$noActive && ($q["required"] && ($q["radio"] == ANSWER_RADIO_HIDDEN || $q["radio"] == ANSWER_RADIO_YES || $yesActive)) ) { echo "required"; } ?> maxlength=10/>
                                                    <div class="invalid-feedback"><?php echo __('text.dateFormat') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </fieldset>

                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }

        $healthFormHTML = ob_get_contents();
        ob_end_clean();

        return $healthFormHTML;
    }

    /**
     * @param $productor
     * @param $product
     * @param $formId
     * @param $form
     * @return array
     */
    function validateHealthForm($productor, $product, $formId, $form, $u = null, $p = null)
    {

        if($u != null && $p != null){
            $this->user = $u;
            $this->pass = $p;
        }
        //app('debugbar')->info($form);
        $questions = $this->transformQuestions($form);
        //app('debugbar')->info($questions);

        session([
            'healthForm' => $questions
        ]);

        $response = $this->PMWS->validateHealthForm($this->user, $this->pass, $this->language, $productor, $product, $formId, $questions);
        // app('debugbar')->info($response);
        $data = $response->return;
        if( $data->correcto == "S" ){
            $healthFormAnswers = array();
            $healthFormAnswers["result"] = $data->datosSalida->listaParametros->valorParametro;

            if ( is_array($data->listaIncidencias) || isset($data->listaIncidencias->listaClaveValor ) ) {
                $healthFormAnswers["exclusions"] = array();
                if ( is_array($data->listaIncidencias->listaClaveValor) ) {
                    $iList = $data->listaIncidencias->listaClaveValor;
                } else {
                    $iList = $data->listaIncidencias;
                }
                foreach ($iList as $i) {
                    $healthFormAnswers["exclusions"][] = array(
                        "key"	=> $i->clave,
                        "value"	=> $i->valor
                    );
                }
            }

        } else {
            $healthFormAnswers = $data->codigoError;
        }

        //app('debugbar')->info($healthFormAnswers);
        return $healthFormAnswers;
    }

    /**
     * validateHealthForm helper
     * @param $form
     * @return array
     */
    private function transformQuestions( $form ) {

        $questions = array();
        foreach ($form as $key => $value) {
            if ( $this->endsWith($key, ANSWER_SUFFIX_SELECT) ) {
                $rKey = substr($key, 0, -strlen(ANSWER_SUFFIX_SELECT));
                if (!array_key_exists($rKey, $questions)) {
                    $questions[$rKey] = array();
                    $questions[$rKey]["id"] = $rKey;
                }

                if ( !isset($questions[$rKey]["text"]) || empty($questions[$rKey]["text"]) || $questions[$rKey]["text"] != "NO" ) {
                    $questions[$rKey]["code"] = $value;
                }
            } else if ( $this->endsWith($key, ANSWER_SUFFIX_TEXT) ) {
                $rKey = substr($key, 0, -strlen(ANSWER_SUFFIX_TEXT));
                if (!array_key_exists($rKey, $questions)) {
                    $questions[$rKey] = array();
                    $questions[$rKey]["id"] = $rKey;
                }

                if ( !isset($questions[$rKey]["text"]) || empty($questions[$rKey]["text"]) || $questions[$rKey]["text"] != "NO" ) {
                    $questions[$rKey]["text"] = $value;
                }
            } else if ( $this->endsWith($key, ANSWER_SUFFIX_DATE) ) {
                $rKey = substr($key, 0, -strlen(ANSWER_SUFFIX_DATE));
                if (!array_key_exists($rKey, $questions)) {
                    $questions[$rKey] = array();
                    $questions[$rKey]["id"] = $rKey;
                }

                if ( !isset($questions[$rKey]["text"]) || empty($questions[$rKey]["text"]) || $questions[$rKey]["text"] != "NO" ) {
                    $questions[$rKey]["date"] = $value;
                }
            } else if ( $this->endsWith($key, ANSWER_SUFFIX_NUMBER) ) {
                $rKey = substr($key, 0, -strlen(ANSWER_SUFFIX_NUMBER));
                if (!array_key_exists($rKey, $questions)) {
                    $questions[$rKey] = array();
                    $questions[$rKey]["id"] = $rKey;
                }

                if ( !isset($questions[$rKey]["text"]) || empty($questions[$rKey]["text"]) || $questions[$rKey]["text"] != "NO" ) {
                    $questions[$rKey]["text"] = $value;
                }
            } else if ( $this->endsWith($key, ANSWER_SUFFIX_RADIO) ) {
                $rKey = substr($key, 0, -strlen(ANSWER_SUFFIX_RADIO));
                if (!array_key_exists($rKey, $questions)) {
                    $questions[$rKey] = array();
                    $questions[$rKey]["id"] = $rKey;
                }
                if ( $value == "NO") {
                    $questions[$rKey]["text"] = $value;

                    if ( isset($questions[$rKey]["code"]) ) {
                        unset($questions[$rKey]["code"]);
                    }

                    if ( isset($questions[$rKey]["date"]) ) {
                        unset($questions[$rKey]["date"]);
                    }
                }
            }
        }
        return $questions;
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     * transformQuestions helper
     */
    private function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * @param $token
     * @return mixed
     *
     * Widget - gets quote info by providing a token to the WS
     */
    function getAccessData($token)
    {

        $response = $this->PMWS->getAccessData($this->language, $token);
        // app('debugbar')->info($response);
        $data = $response->return;

        if( $data->correcto == "S" ){
            if (property_exists($data->datosSalida, "listaParametros")) {
                foreach ($data->datosSalida->listaParametros as $row) {
                    switch( $row->nombreParametro ){
                        case "P_USUARIO":
                            $user = $row->valorParametro;
                            break;
                        case "P_PASSWORD":
                            $pass = $row->valorParametro;
                            break;
                        case "P_CODIGO_PRODUCTOR":
                            $productor = $row->valorParametro;
                            break;
                        case "P_AGRUPACION":
                            $product = $row->valorParametro;
                            break;
                        case "P_PRODUCTO":
                            $productVariation = $row->valorParametro;
                            break;
                    }
                }

                session([
                    'login' => [
                        'user' => $user,
                        'pass' => $pass,
                        'gestor' => null,
                    ],
                    'widget' => [
                        'productor' => $productor,
                        'product' => $product, // productGroup
                        'productVariation' => $productVariation // product
                    ]
                ]);

                $auth = true;

            }
        }else{
            $auth = $data->mensajeError;
        }

        return $auth;

    }

    /**
     * @param $token
     * @return mixed
     *
     * Widget - gets customization info
     */
    function validateUser($productVariationId)
    {

        $response = $this->PMWS->validateUser($this->user, $this->pass, $this->language, $productVariationId);
        //app('debugbar')->info($response);
        $data = $response->return;

        if( $data->correcto == "S" ){
            if (property_exists($data->datosLogos, "listaParametros")) {
                if( is_array( $data->datosLogos->listaParametros ) ){
                    foreach ($data->datosLogos->listaParametros as $row) {
                        $extraInfo["datosLogos"][$row->nombreParametro] = $row->valorParametro;
                    }
                }else{
                    $extraInfo["datosLogos"][$data->datosLogos->listaParametros->nombreParametro] = $data->datosLogos->listaParametros->valorParametro;
                }

            }
            if (property_exists($data->datosSalida, "listaParametros")) {
                foreach ($data->datosSalida->listaParametros as $row) {
                    $extraInfo["datosSalida"][$row->nombreParametro] = $row->valorParametro;
                }
                session([
                    'extraInfo' => [
                        'datosLogos' => $extraInfo["datosLogos"],
                        'datosSalida' => $extraInfo["datosSalida"]
                    ]
                ]);

                $extraInfo = true;
            }
        }else{
            $extraInfo = $data->mensajeError;
        }
        return $extraInfo;

    }

    function getPersonTypes() {
        $response = $this->PMWS->getPersonTypes($this->language);
        //app('debugbar')->info($response);

        if ($response) {

            foreach($response->TiposPersona as $entry){
                $result[$entry->idTipoPersona->__toString()] = $entry->descTipoPersona->__toString();
            }
        } else {
            $result = $response;
        }

        //app('debugbar')->info($result);
        return $result;
    }

    function getPaymentMethods() {
        $response = $this->PMWS->getPaymentMethods($this->language);
        //app('debugbar')->info($response);

        if ($response) {

            foreach($response->FormaPago as $entry){
                $result[$entry->idFormaPago->__toString()] = $entry->descFormaPago->__toString();
            }
        } else {
            $result = $response;
        }
        //app('debugbar')->info($result);
        return $result;
    }

    function getAddressTypes() {
        $response = $this->PMWS->getAddressTypes($this->language);
        //app('debugbar')->info($response);

        if ($response) {

            foreach($response->TipoVia as $entry){
                $result[$entry->idVia->__toString()] = $entry->descVia->__toString();
            }
        } else {
            $result = $response;
        }
        //app('debugbar')->info($result);
        return $result;
    }

    function getCompanyAddressTypes() {
        $response = $this->PMWS->getCompanyAddressTypes($this->language);
        //app('debugbar')->info($response);

        if ($response) {

            foreach($response->TiposDireccion as $entry){
                if ( $entry->idTipoDireccion->__toString() != COMPANY_ADDRESS_TYPE_I ) {
                    $result[$entry->idTipoDireccion->__toString()] = $entry->descTipoDireccion->__toString();
                }
            }
        } else {
            $result = $response;
        }
        //app('debugbar')->info($result);
        return $result;
    }

    function getWorkLocationTypes() {
        $response = $this->PMWS->getWorkLocationTypes($this->language);
        //app('debugbar')->info($response);

        if ($response) {
            foreach($response->LugarHabitualTrabajo as $entry){
                $result[$entry->idLugarHabitual->__toString()] = $entry->descLugarHabitual->__toString();
            }
        } else {
            $result = $response;
        }
        //app('debugbar')->info($result);
        return $result;
    }

    /**
     * @param $data - array with data for request. Available arguments:
     * @param "user" - logged in username
     * @param "pass" - logged in password
     * @param "language" - logged in language
     * @param "preclient" - preclient code
     * @param "productor" - (optional) selected productor
     * @param "productId" - selected product
     * @param "profession" - selected profession
     * @param "birthdate" - user birthdate
     * @param "gender" - user gender
     * @param "height" - user height
     * @param "weight" - user weigth
     * @param "name" - insured person name
     * @param "surname" - insured person surname
     * @param "docId" - insured person documentId
     * @param "docType" - insured person document type
     * @param "email" - insured person email
     * @param "phone" - insured person phone
     * @param "streetType" - insured person street type
     * @param "address" - insured person address
     * @param "postalCode" - insured person postal Code
     * @param "city" - insured person city
     * @param "insuredLanguage" - insured person language
     * @param "companyName" - company name
     * @param "companyAddressType" - company address type
     * @param "companyStreetType" - company street type
     * @param "companyAddress" - company address
     * @param "companyCity" - company city
     * @param "workLocationType" - insured person work location type
     * @param "paymentMethod" - chosen payment method
     * @param "hasMorePolicies" - whether insurance person has more policies. Options: "S"/"N"
     * @param "extraCompanyName" - name of company where person has other policies
     * @param "extraInsurancePrice" - price of other policies
     * @param "extraInsuranceDate" - expiration date of other policies
     * @param "IBANcountryCode" - IBAN country Code
     * @param "IBANcontrolCode" - IBAN control Code
     * @param "IBANentity" - IBAN entity code
     * @param "IBANoffice" - IBAN office code
     * @param "IBANdc" - IBAN dc code
     * @param "IBANaccount" - IBAN account number
     * @param "holderType" - holder type
     * @param "holderLanguage" - holder language
     * @param "holderName" - holder name
     * @param "holderSurname" - holder surname
     * @param "holderBirthdate" - holder birthdate
     * @param "holderPhone" - holder phone
     * @param "holderEmail" - holder email
     * @param "holderDocType" - holder document type
     * @param "holderDocId" - holder document id
     * @param "holderAddress" - holder address
     * @param "holderCity" - holder city
     * @param "holderStreetType" - holder street type
     * @param "date" - date d/m/Y
     * @param "dataPreferences" - whether user rejects consents. Options: "S"/"N"
     * @param "coverageData" - array with coverages. Each item has the following mandatory fields: "price", "code", "duration", "franchise"
     * @param "healthQ" - Health form questions. validateHealthForm format is used here
     * @param "pmUserCode" - (optional)
     * @param "channel" - (optional). Default: IM or GI depending on pmUserCode
     * @param "app" - (optional). Default: IMEDIADOR
     * @return bool - false or submit response
     * @throws \SoapFault
     */
    public function submitPolicy($parameters) {

        if($parameters["u"] != null && $parameters["p"] != null){
            $parameters["user"] = $parameters["u"] ;
            $parameters["pass"] = $parameters["p"] ;
        }else{
            $parameters["user"] = $this->user;
            $parameters["pass"] = $this->pass;
        }
        $parameters["language"] = $this->language;
        $parameters["pmUserCode"] = $this->userPM;

        // Split IBAN into requested variables
        $parameters["IBAN"] = str_replace(" ", "", trim($parameters["IBAN"]) );
        $parameters["IBANcountry"] = substr($parameters["IBAN"], 0, 2);
        $parameters["IBANcontrol"] = substr($parameters["IBAN"], 2, 2);
        $parameters["IBANentity"] = substr($parameters["IBAN"], 4, 4);
        $parameters["IBANoffice"] = substr($parameters["IBAN"], 8, 4);
        $parameters["IBANdc"] = substr($parameters["IBAN"], 12, 2);
        $parameters["IBANaccount"] = substr($parameters["IBAN"], 14, 10);

        // if customer is natural person copies uses his data for holder as well
        if( $parameters["holderName"] == "" ){
            $parameters["holderName"] = $parameters["name"];
            $parameters["holderDocId"] = $parameters["docId"];
            $parameters["holderEmail"] = $parameters["email"];
            $parameters["holderStreetType"] = $parameters["streetType"];
            $parameters["holderAddress"] = $parameters["address"];
            $parameters["holderCity"] = $parameters["city"];
            $parameters["holderProvince"] = $parameters["province"];
        }

        // get doc types from different variables
        $parameters["docType"] = $this->getDocType( $parameters["docId"]  );
        $parameters["holderDocType"] = $this->getDocType( $parameters["holderDocId"]  );

        // Extra variables
        if( isset($parameters["startingDate"]) &&  $parameters["startingDate"] != null){
            $parameters["date"] = $parameters["startingDate"];
        }else{
            $parameters["date"] = date("d/m/Y");
        }


        // transforms Health form questions
        if( app('session')->has('healthForm') ){
            $parameters["healthQ"] = session('healthForm');
        }
        //app('debugbar')->info($parameters);
        $response = $this->PMWS->submitPolicy($parameters);
        app('debugbar')->info($response);

        $data = $response->return;

        if( $data->correcto == "S" ){
            if (property_exists($data, "datosSalida")) {
                if( is_array( $data->datosSalida->array ) ){
                    foreach ($data->datosSalida->array as $row) {
                        $submitPolicy[$row->nombre] = $row->valor;
                    }
                }
            }

        }else{
            $submitPolicy = $data->mensajeError;
        }

        return $submitPolicy;

    }

    /**
     * @param $productor
     * @param $docId
     * @param $source
     * @param $type
     * @param $format
     * @param null $pmUserCode
     * @return mixed
     * @throws \SoapFault
     */
    function getDocument($productor, $docId, $source, $type, $format, $pmUserCode = null, $u = null, $p = null)
    {

        if($u != null && $p != null){
            $this->user = $u;
            $this->pass = $p;
        }

        $response = $this->PMWS->getDocument($this->user, $this->pass, $this->language, $productor, $docId, $source, $type, $format, $pmUserCode);
        // app('debugbar')->info($response);

        $data = $response->return;
        if( $data->correcto == "S" ){

            $doc = [];
            $doc["contenidoFichero"] = $data->contenidoFichero;

        }else{
            $doc = $data->mensajeError;
        }
        return $doc;
    }

    function getFilesList($pmUserCode = null) {

        $response = $this->PMWS->getFilesList($this->user, $this->pass, $this->language, $pmUserCode);
        //

        $data = $response->return;
        if( $data->correcto == "S" ){
            if (property_exists($data->listaFicheros, "listaFicherosCartera")) {
                if( is_array( $data->listaFicheros->listaFicherosCartera ) ){
                    $i=0;
                    foreach ($data->listaFicheros->listaFicherosCartera as $row) {
                        $documentsList[$i]['nombreFichero'] = $row->nombreFichero;
                        $documentsList[$i]['fechaDescarga'] = $row->fechaDescarga;
                        $documentsList[$i]['descFichero'] = $row->descFichero;
                        $documentsList[$i]['codigo'] = $row->codigo;
                        $documentsList[$i]['tipoFichero'] = $row->tipoFichero;
                        $i++;
                    }
                }
            }

        }else{
            $documentsList = $data->mensajeError;
        }
        return $documentsList;
    }

    function getFile($fileId, $pmUserCode = null){

        $response = $this->PMWS->getFile($this->user, $this->pass, $this->language, $fileId, $pmUserCode);
        // app('debugbar')->info($response);

        $data = $response->return;
        if( $data->correcto == "S" ){

            $file = [];
            $file["contenidoFichero"] = $data->contenidoFichero;

        }else{
            $file = $data->mensajeError;
        }
        return $file;
    }

    function uploadDocument($folderId, $docId, $refId, $docType, $doc, $docName = null) {

        // get docName, docType and doc (base64) from file

        $response = $this->PMWS->uploadDocument($this->user, $this->pass, $this->language, $folderId, $docId, $refId, $docType, $doc, $docName);
        // app('debugbar')->info($response);

        $data = $response->return;
        if( $data->correcto == "S" ){

            $upload = [];
            //$doc["contenidoFichero"] = $data->contenidoFichero;

        }else{
            $upload = $data->mensajeError;
        }
        return $upload;
    }

    function getLgtSignAccess( $requestId, $policyId, $doc, $u = null, $p = null)
    {

        if($u != null && $p != null){
            $this->user = $u;
            $this->pass = $p;
        }

        $response = $this->PMWS->getLgtSignAccess($this->user, $this->pass, $this->language, $requestId, $policyId, $doc);
        // app('debugbar')->info($response);

        $url = $response->getUrl();

        return $url;
    }

    /**
     * Helper function to detect personal id type
     * @param $doc
     * @return string
     */
    private function getDocType($doc) {
        if ( strlen($doc) == 9) {
            if ( is_numeric(substr($doc, 0 , 1)) ) {
                return "N";
            } else if ( is_numeric(substr($doc, -1)) ) {
                return "C";
            }
            return "E";
        }
        return "N";
    }


    function getGoals($productor = null, $pmUserCode = null) {

        $response = $this->PMWS->getGoals($this->user, $this->pass, $this->language, $productor, $this->userPM);
        //app('debugbar')->info($response);

        $data = $response->return;
        if( $data->correcto == "S" ){

            $campaigns = [];
            $i = 0;
            if( is_array($data->datosObjetivos->objetivo) ) {
                foreach ($data->datosObjetivos->objetivo as $row) {
                    $campaigns[$i]["codigo"] = $row->codigo;
                    $campaigns[$i]["descripcion"] = $row->descripcion;
                    $campaigns[$i]["titulo"] = $row->titulo;
                    $campaigns[$i]["valorActual"] = $row->valorActual;

                    if( is_array($row->tramosIncentivos->tramoIncentivo) ) {
                        $j = 0;
                        foreach ($row->tramosIncentivos->tramoIncentivo as $row2) {
                            $campaigns[$i]["tramosIncentivos"][$j]["desde"]  = $row2->desde;
                            $campaigns[$i]["tramosIncentivos"][$j]["hasta"]  = $row2->hasta;
                            $campaigns[$i]["tramosIncentivos"][$j]["incentivo"]  = $row2->incentivo;
                            $j++;
                        }
                    }else{
                        $j = 0;
                        $row2 = $row->tramosIncentivos->tramoIncentivo;
                        $campaigns[$i]["tramosIncentivos"][$j]["desde"]  = $row2->desde;
                        $campaigns[$i]["tramosIncentivos"][$j]["hasta"]  = $row2->hasta;
                        $campaigns[$i]["tramosIncentivos"][$j]["incentivo"]  = $row2->incentivo;
                    }
                    $i++;
                }
            }else{
                $row = $data->datosObjetivos->objetivo;
                $campaigns[$i]["codigo"] = $row->codigo;
                $campaigns[$i]["descripcion"] = $row->descripcion;
                $campaigns[$i]["titulo"] = $row->titulo;
                $campaigns[$i]["valorActual"] = $row->valorActual;

                if( is_array($row->tramosIncentivos->tramoIncentivo) ) {
                    $j = 0;
                    foreach ($row->tramosIncentivos->tramoIncentivo as $row2) {
                        $campaigns[$i]["tramosIncentivos"][$j]["desde"]  = $row2->desde;
                        $campaigns[$i]["tramosIncentivos"][$j]["hasta"]  = $row2->hasta;
                        $campaigns[$i]["tramosIncentivos"][$j]["incentivo"]  = $row2->incentivo;
                        $j++;
                    }
                }else{
                    $j = 0;
                    $row2 = $row->tramosIncentivos->tramoIncentivo;
                    $campaigns[$i]["tramosIncentivos"][$j]["desde"]  = $row2->desde;
                    $campaigns[$i]["tramosIncentivos"][$j]["hasta"]  = $row2->hasta;
                    $campaigns[$i]["tramosIncentivos"][$j]["incentivo"]  = $row2->incentivo;
                }

            }

        }else{
            $campaigns = $data->mensajeError;
        }
        return $campaigns;
    }

}
