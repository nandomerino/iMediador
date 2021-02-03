<?php
namespace App\Http\Controllers;

use SoapClient;
use SimpleXMLElement;


/**
 * Class PMWSTEST
 * @package App\Http\Controllers
 */
class PMWSTEST extends controller
{

    const OPTION_ELITE = "10";
    const OPTION_GOLDEN = "11";
    const OPTION_STANDARD = "12";

    private $baseUrl;
    private $environment;

    /**
     * PMWSTEST constructor.
     */
    function __construct() {
        $this->baseUrl = config('filesystems.disks.pmapi.url');
        $this->environment = config('filesystems.disks.pmapi.devmode');
    }

    /**
     * @param $user - user from login form
     * @param $pass - password from login form
     * @param $language - current language
     * @param null $managerCode - gestor from login form
     * @return bool
     * @throws \SoapFault
     */
    function login($user, $pass, $language, $managerCode = null) {

        $endpoint = $this->baseUrl . "/v4/wsautenticacion" . $this->environment . "/Autenticacion?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> "IM");

        if ( $managerCode != null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CODIGO_GESTOR",
                "valorParametro"	=> $managerCode);
        }

        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->obtenerDatosMediador($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $user - user from PM login form
     * @param $pass - password from PM login form
     * @param $language - current language
     * @param $pmUserCode - PM user code
     * @return bool
     * @throws \SoapFault
     */
    function loginInt($user, $pass, $language, $pmUserCode) {

        $endpoint = $this->baseUrl . "/v4/wsautenticacion" . $this->environment . "/Autenticacion?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> $this->getChannel($pmUserCode));
        $inputData[] =  array(
            "nombreParametro"	=> "P_USUARIO_INTERNO",
            "valorParametro"	=> $pmUserCode);

        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->obtenerDatosMediador($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param $language - current language
     * @param $accessCode - access code
     * @return bool or access data
     * @throws \SoapFault
     */
    function getAccessData($language, $accessCode) {
        $endpoint = $this->baseUrl . "/v4/wsautenticacion" . $this->environment . "/Autenticacion?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_ACCESO",
            "valorParametro"	=> $accessCode);


        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "EMISION_WEB"
        );

        $params = array(
            "pTipoAcceso"	=> "INT",
            "pUsuario"		=> "FICTICIO",
            "pPassword"		=> "FICTICIO",
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->obtenerDatosAcceso($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $user - user from PM login form
     * @param $pass - password from PM login form
     * @param $language - current language
     * @param $productVariationId - product code
     * @return bool
     * @throws \SoapFault
     */
    function validateUser($user, $pass, $language, $productVariationId) {

        $endpoint = $this->baseUrl . "/v4/wsautenticacion" . $this->environment . "/Autenticacion?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> "WSD");
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTO",
            "valorParametro"	=> $productVariationId);

        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "EMISION_WEB"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->validarUsuario($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param string $productor - (optional)
     * @return bool - false or productors list
     * @throws \SoapFault
     */
    function getProductors($user, $pass, $language, $productor = "") {

        $endpoint = $this->baseUrl . "/v3/wsmediador" . $this->environment . "/Mediador?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
            "valorParametro"	=> $productor);

        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->getProductores($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;

    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $productor - selected productor
     * @param $productGroup - product group from login
     * @param $entryChannel - optional for quote widget
     * @param $application - optional for quote widget
     * @param null $pmUserCode - optional
     * @return bool
     * @throws \SoapFault
     */
    function getProductVariations($user, $pass, $language, $productor, $productGroup, $entryChannel, $application, $pmUserCode = null) {

        $endpoint = $this->baseUrl . "/v4/wsproducto" . $this->environment . "/Producto?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
            "valorParametro"	=> $productor);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_AGRUPACION",
            "valorParametro"	=> $productGroup);

        if( isset($entryChannel) ){
            $pEntryChannel = $entryChannel;
        }else{
            $pEntryChannel = $this->getChannel($pmUserCode);
        }
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> $pEntryChannel);

        if ( $pmUserCode !==  null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_USUARIO_INTERNO",
                "valorParametro"	=> $pmUserCode);
        }

        $cData = array();
        if( isset($application) ){
            $pApplication = $application;
        }else{
            $pApplication = "IMEDIADOR";
        }
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> $pApplication
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->obtenerListaProductos($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;

    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $productor - selected productor
     * @param $productGroup - product group from login
     * @param $productVariationId - product variation from getProductVariations
     * @param $modifiedField - optional. Modified field name
     * @param $pmUserCode - optional
     * @return bool - false or producto variations list
     * @throws \SoapFault
     */
    function getProductConfiguration($user, $pass, $language, $productor = null, $productGroup, $productVariationId, $entryChannel, $application, $modifiedField = null, $pmUserCode = null) {

        $endpoint = $this->baseUrl . "/v4/wsproducto" . $this->environment . "/Producto?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        if ( $productor != null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
                "valorParametro"	=> $productor);
        }
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_AGRUPACION",
            "valorParametro"	=> $productGroup);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTO",
            "valorParametro"	=> $productVariationId);

        if( isset($entryChannel) ){
            $pEntryChannel = $entryChannel;
        }else{
            $pEntryChannel = $this->getChannel($pmUserCode);
        }
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> $pEntryChannel);

        if ( $pmUserCode !==  null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_USUARIO_INTERNO",
                "valorParametro"	=> $pmUserCode);
        }

        if ( $modifiedField !==  null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CAMPO_MODIFICADO",
                "valorParametro"	=> $modifiedField);
        }


        $cData = array();
        if( isset($application) ){
            $pApplication = $application;
        }else{
            $pApplication = "IMEDIADOR";
        }
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> $pApplication
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->obtenerConfiguracionProducto($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;

    }


    /**
     * @return SimpleXMLElement
     */
    function getLanguages() {
        $endpoint = $this->baseUrl . "/recursos/maestro/idiomas.xml";
        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        curl_close($curl);
        return new SimpleXMLElement($result);
    }

    /**
     * @param $language
     * @return SimpleXMLElement
     */
    function getGenders($language) {
        $endpoint = $this->baseUrl . "/recursos/maestro/" . $language . "/tiposSexo.xml";
        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        curl_close($curl);
        return new SimpleXMLElement($result);
    }

    /**
     * @param $language
     * @return SimpleXMLElement
     */
    function getPaymentMethods($language) {
        $endpoint = $this->baseUrl . "/recursos/maestro/" . $language . "/formasPago.xml";
        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        curl_close($curl);
        return new SimpleXMLElement($result);
    }

    /**
     * @param $language
     * @return SimpleXMLElement
     */
    function getPersonTypes($language) {
        $endpoint = $this->baseUrl . "/recursos/maestro/" . $language . "/tiposPersona.xml";
        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        curl_close($curl);
        return new SimpleXMLElement($result);
    }


    /**
     * @param $language
     * @return SimpleXMLElement
     */
    function getAddressTypes($language) {
        $endpoint = $this->baseUrl . "/recursos/maestro/" . $language . "/tiposVia.xml";
        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        curl_close($curl);
        return new SimpleXMLElement($result);
    }


    /**
     * @param $language
     * @return SimpleXMLElement
     */
    function getCompanyAddressTypes($language) {
        $endpoint = $this->baseUrl . "/recursos/maestro/" . $language . "/tiposDireccion.xml";
        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        curl_close($curl);
        return new SimpleXMLElement($result);
    }


    /**
     * @param $language
     * @return SimpleXMLElement
     */
    function getWorkLocationTypes($language) {
        $endpoint = $this->baseUrl . "/recursos/maestro/" . $language . "/lugaresHabitualesTrabajo.xml";
        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        curl_close($curl);
        return new SimpleXMLElement($result);
    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $scheme - regimen seguridad social. Default: A
     * @return mixed - false or professions list
     * @throws \SoapFault
     */
    function getProfessions($user, $pass, $language, $scheme = "A") {

        $endpoint = $this->baseUrl . "/v3/wspmgen" . $this->environment . "/Pmgen?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_REGIMEN_SEG_SOCIAL",
            "valorParametro"	=> $scheme);

        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->listaProfesiones($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    function getMunicipalities($user, $pass, $language, $postalCode) {
        $endpoint = $this->baseUrl . "/v3/wspmgen" . $this->environment . "/Pmgen?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_POSTAL",
            "valorParametro"	=> $postalCode);


        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->listaPoblaciones($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;

    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param null $pmUserCode - (optional)
     * @return mixed - false or files list
     * @throws \SoapFault
     */
    function getFilesList($user, $pass, $language, $pmUserCode = null) {

        $endpoint = $this->baseUrl . "/wsobtenerficheros" . $this->environment . "/WsObtenerFicherosPort?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> $this->getChannel($pmUserCode));
        if ( $pmUserCode !==  null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_USUARIO_INTERNO",
                "valorParametro"	=> $pmUserCode);
        }

        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->obtenerListaFicherosCartera($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * Retrieves user files
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param null $pmUserCode - (optional)
     * @return mixed - false or files list
     * @throws \SoapFault
     */
    function getFile($user, $pass, $language, $fileId, $pmUserCode = null) {

        $endpoint = $this->baseUrl . "/wsobtenerficheros" . $this->environment . "/WsObtenerFicherosPort?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_FICHERO",
            "valorParametro"	=> $fileId);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> $this->getChannel($pmUserCode));
        if ( $pmUserCode !==  null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_USUARIO_INTERNO",
                "valorParametro"	=> $pmUserCode);
        }

        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->obtenerFicheroCartera($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * Retrieves generic documents like contracts and policies to sign
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $productor - selected productor
     * @param $docId - document code
     * @param $source - document source (available values: 1 -> Poliza; 2 -> Solicitud)
     * @param $type - document type (available values: SO -> Solicitud; CP -> Condiciones particulares; CG -> Condiciones generales)
     * @param $format - document format (available values: Condiciones generales -> A3,A4; Otherwise -> A4)
     * @param null $pmUserCode - (optional)
     * @return mixed - false or document response
     * @throws \SoapFault
     */
    function getDocument($user, $pass, $language, $productor, $docId, $source, $type, $format, $pmUserCode = null) {

        $endpoint = $this->baseUrl . "/v3/wsdocumentacion" . $this->environment . "/Documentacion?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
            "valorParametro"	=> $productor);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> $this->getChannel($pmUserCode));
        $inputData[] =  array(
            "nombreParametro"	=> "P_IDIOMA_IMPRESION",
            "valorParametro"	=> $language);
        if ( $pmUserCode !==  null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_USUARIO_INTERNO",
                "valorParametro"	=> $pmUserCode);
        }

        $docData = array();
        $docData[] = array(
            "codigo"			=> $docId,
            "formato"			=> $format,
            "origen"			=> $source,
            "tipo"				=> $type
        );

        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );
        if ( $pmUserCode !==  null ) {
            $cData[] =  array(
                "nombreParametro"	=> "P_USU_INTERNO",
                "valorParametro"	=> $pmUserCode);
        }

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosDocumento"	=> $docData,
            "pDatosConexion"=> $cData
        );

        $result = $client->getDocumento($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $folderId - available values: SO -> 65; CP -> 11; CG -> 10
     * @param $docId - available values: SO -> 467; CP -> 444; CG -> 442
     * @param $refId - id de poliza o solicitud asociada
     * @param $docName - (optional) document name. Required when uploading general conditions document
     * @param $docType - available values: application/pdf, image/gif, image/jpeg, image/png, image/tiff
     * @param $doc - file base64
     * @return mixed - false or response
     * @throws \SoapFault
     */
    function uploadDocument($user, $pass, $language, $folderId, $docId, $refId, $docName, $docType, $doc) {

        $endpoint = $this->baseUrl . "/wsdocumentacionmtom" . $this->environment . "/WsDocumentacionMTOMPort?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
            "valorParametro"	=> $user);
        $inputData[] =  array(
            "nombreParametro"	=> "P_ID_CARPETA",
            "valorParametro"	=> $folderId);
        $inputData[] =  array(
            "nombreParametro"	=> "P_ID_DOC",
            "valorParametro"	=> $docId);
        $inputData[] =  array(
            "nombreParametro"	=> "P_ID_VINCULADO",
            "valorParametro"	=> $refId);
        $inputData[] =  array(
            "nombreParametro"	=> "P_NOMBRE_DOCUMENTO",
            "valorParametro"	=> $docName);
        $inputData[] =  array(
            "nombreParametro"	=> "P_TIPO_CONTENIDO",
            "valorParametro"	=> $docType);


        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDocumento" => $doc,
            "pDatosConexion"=> $cData
        );

        $result = $client->subirDocumento($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $productor - (optional) selected productor
     * @param null $pmUserCode - (optional)
     * @return mixed - false or targets list
     * @throws \SoapFault
     */
    function getGoals($user, $pass, $language, $productor = null, $pmUserCode = null) {

        $endpoint = $this->baseUrl . "/v3/wsmediador" . $this->environment . "/Mediador?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        if ( $productor != null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
                "valorParametro"	=> $productor);
        }
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> $this->getChannel($pmUserCode));
        if ( $pmUserCode !==  null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_USUARIO_INTERNO",
                "valorParametro"	=> $pmUserCode);
        }

        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->getListaObjetivos($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $data - array with data for request. Available arguments:
     * @param "user" - logged in username
     * @param "pass" - logged in password
     * @param "language" - logged in language
     * @param "productor" - (optional) selected productor
     * @param "jobType" - (optional) regimen seguridad social. Default: A
     * @param "option" - Proporcionada con las variaciones
     * @param "productId" - selected product
     * @param "franchise" - (optional) selected franchise or null for all franchises
     * @param "profession" - selected profession
     * @param "birthdate" - user birthdate
     * @param "gender" - user gender
     * @param "height" - user height
     * @param "weight" - user weigth
     * @param "enfCob" - cobertura por enfermedad
     * @param "enfSub" - subsidio por enfermedad
     * @param "accCob" - cobertura por accidente
     * @param "accSub" - subsidio por accidente
     * @param "hospCob" - cobertura por hospitalizacion
     * @param "hospSub" - subsidio por hospitalizacion
     * @param "paymentMethod" - (optional) selected number of installments
     * @param "pmUserCode" - (optional)
     * @param "period" - (optional)
     * @param "discount" - (optional) product option
     * @param "discountYears" - (optional) product option
     * @param "discountComMed" - (optional) product option
     * @param "discountComDel" - (optional) product option
     * @param "sobreprimaDel" - (optional) product option
     * @param "financeCharge" - (optional) product option
     * @param "paymentChannel" - (optional) product option
     * @return bool - false or rates list
     * @throws \SoapFault
     */
    function getRates($data) {

        $endpoint = $this->baseUrl . "/v4/wstarificacion" . $this->environment . "/Tarificacion?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        if ( isset($data["productor"]) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
                "valorParametro"	=> $data["productor"]);
        }
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTO",
            "valorParametro"	=> $data["productId"]);

        if ( isset($data["entryChannel"]) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CANAL_ENTRADA",
                "valorParametro"	=> $data["entryChannel"]);
        } else {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CANAL_ENTRADA",
                "valorParametro"	=> $this->getChannel($data));
        }

        $inputData[] =  array(
            "nombreParametro"	=> "P_TIPO_TARIFICACION",
            "valorParametro"	=> "P");
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_OPCION",
            "valorParametro"	=> $data["option"]);

        if ( isset($data["franchise"]) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_FRANQUICIA",
                "valorParametro"	=> $data["franchise"]);
        }
        if ( isset($data["pmUserCode"]) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_USUARIO_INTERNO",
                "valorParametro"	=> $data["pmUserCode"]);
        }

        $fData = array();
        if ( isset($data["commercialKey"]) ) {
            $fData[] =  array(
                "nombreParametro"	=> "P_CLAVE_COMERCIAL",
                "valorParametro"	=> $data["commercialKey"]);
        }else{
            $fData[] = array(
                "nombreParametro"	=> "P_CLAVE_COMERCIAL",
                "valorParametro"	=> ""
            );
        }


        $scheme = "A";
        if ( isset($data["jobType"]) ) {
            $scheme = $data["jobType"];
        }
        $fData[] = array(
            "nombreParametro"	=> "P_REGIMEN_SEG_SOCIAL",
            "valorParametro"	=> $scheme
        );
        $fData[] = array(
            "nombreParametro"	=> "P_PROFESION_CLIENTE",
            "valorParametro"	=> $data["profession"]
        );
        $fData[] = array(
            "nombreParametro"	=> "P_FECHA_NACIMIENTO_CLIENTE",
            "valorParametro"	=> $data["birthdate"]
        );
        $fData[] = array(
            "nombreParametro"	=> "P_SEXO",
            "valorParametro"	=> $data["gender"]
        );
        $fData[] = array(
            "nombreParametro"	=> "P_TALLA",
            "valorParametro"	=> $data["height"]
        );
        $fData[] = array(
            "nombreParametro"	=> "P_PESO",
            "valorParametro"	=> $data["weight"]
        );
        if ( isset($data["paymentMethod"]) ) {
            $fData[] = array(
                "nombreParametro"	=> "P_FORMA_PAGO",
                "valorParametro"	=> $data["paymentMethod"]
            );
        }
        if ( isset($data["duration"]) ) {
            $fData[] = array(
                "nombreParametro"	=> "P_PERIODO_COBERTURA",
                "valorParametro"	=> $data["duration"]
            );
        }
        if ( isset($data["discount"]) ) {
            $fData[] = array(
                "nombreParametro"	=> "P_DESCUENTO_06",
                "valorParametro"	=> $data["discount"]
            );
        }
        if ( isset($data["discountYears"]) ) {
            $fData[] = array(
                "nombreParametro"	=> "P_ANYOS_DTO_06",
                "valorParametro"	=> $data["discountYears"]
            );
        }
        if ( isset($data["discountComMed"]) ) {
            $fData[] = array(
                "nombreParametro"	=> "P_DTO_COMISION_MED",
                "valorParametro"	=> $data["discountComMed"]
            );
        }
        if ( isset($data["discountComDel"]) ) {
            $fData[] = array(
                "nombreParametro"	=> "P_DTO_COMISION_DEL",
                "valorParametro"	=> $data["discountComDel"]
            );
        }
        if ( isset($data["sobreprimaDel"]) ) {
            $fData[] = array(
                "nombreParametro"	=> "P_SOBREPRIMA_DEL",
                "valorParametro"	=> $data["sobreprimaDel"]
            );
        }
        if ( isset($data["financeCharge"]) ) {
            $fData[] = array(
                "nombreParametro"	=> "P_RECARGO_FINANCIACION",
                "valorParametro"	=> $data["financeCharge"]
            );
        }
        if ( isset($data["paymentChannel"]) ) {
            $fData[] = array(
                "nombreParametro"	=> "P_CANAL_COBRO",
                "valorParametro"	=> $data["paymentChannel"]
            );
        }


        $fData = array_merge($fData, $this->getCapitalGarantia($data["enfCob"], $data["enfSub"], $data["accCob"], $data["accSub"], $data["hospCob"], $data["hospSub"]));



        $cData = array();
        if ( isset($data["application"]) ) {
            $cData[] = array(
                "nombreParametro"	=> "P_APLICACION",
                "valorParametro"	=> $data["application"]
            );
        } else {
            $cData[] = array(
                "nombreParametro"	=> "P_APLICACION",
                "valorParametro"	=> "IMEDIADOR"
            );
        }

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $data["user"],
            "pPassword"		=> $data["pass"],
            "pIdioma"		=> $data["language"],
            "pDatosEntrada"	=> $inputData,
            "pValoresCampos"=> $fData,
            "pDatosConexion"=> $cData
        );

        app('debugbar')->info($params);
        $result = $client->obtenerCuadroTarifas($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $productor - (optional) selected productor
     * @param $option - datosProductos.listaProductos.opcionTarificaInversa
     * @param $price - Cuota mensual que se desea obtener
     * @param $franchise - (optional) selected franchise or null for all franchises
     * @param "scheme" - (optional) regimen seguridad social. Default: A
     * @param $profession - selected profession
     * @param $birthdate - user birthdate
     * @param $gender - user gender
     * @param $height - user height
     * @param $weight - user weigth
     * @param $period -
     * @param null $pmUserCode - (optional)
     * @return bool - false or rates list
     * @throws \SoapFault
     */
    function getRatesByPrice($user, $pass, $language, $productor = null, $option, $price, $franchise = null, $jobType = "A", $profession, $birthdate, $gender, $height, $weight, $duration, $commercialKey, $pmUserCode = null) {

        $endpoint = $this->baseUrl . "/v4/wstarificacion" . $this->environment . "/Tarificacion?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        if ( $productor !== null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
                "valorParametro"	=> $productor);
        }
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> $this->getChannel($pmUserCode));
        $inputData[] =  array(
            "nombreParametro"	=> "P_PRIMA_BUSCADA",
            "valorParametro"	=> $price);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_OPCION",
            "valorParametro"	=> $option);
        $inputData[] =  array(
            "nombreParametro"	=> "P_FRANQUICIA",
            "valorParametro"	=> $franchise === null ? "" : $franchise);
        if ( $pmUserCode !==  null ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_USUARIO_INTERNO",
                "valorParametro"	=> $pmUserCode);
        }
        if ( isset($commercialKey) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CLAVE_COMERCIAL",
                "valorParametro"	=> $commercialKey
            );
        }else{
            $inputData[] = array(
                "nombreParametro"	=> "P_CLAVE_COMERCIAL",
                "valorParametro"	=> ""
            );
        }
        $inputData[] = array(
            "nombreParametro"	=> "P_REGIMEN_SEG_SOCIAL",
            "valorParametro"	=> $jobType
        );
        $inputData[] = array(
            "nombreParametro"	=> "P_PROFESION_CLIENTE",
            "valorParametro"	=> $profession
        );
        $inputData[] = array(
            "nombreParametro"	=> "P_FECHA_NACIMIENTO_CLIENTE",
            "valorParametro"	=> $birthdate
        );
        $inputData[] = array(
            "nombreParametro"	=> "P_SEXO",
            "valorParametro"	=> $gender
        );
        $inputData[] = array(
            "nombreParametro"	=> "P_TALLA",
            "valorParametro"	=> $height
        );
        $inputData[] = array(
            "nombreParametro"	=> "P_PESO",
            "valorParametro"	=> $weight
        );
        $inputData[] = array(
            "nombreParametro"	=> "P_PERIODO_COBERTURA",
            "valorParametro"	=> $duration
        );


        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );
        app('debugbar')->info($params);
        $result = $client->obtenerSubsidiosPrima($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $productor - (optional) selected productor
     * @return bool - false or rates list
     * @throws \SoapFault
     */
    function getHealthForm($user, $pass, $language, $productor, $product, $commercialKey) {
        // $product = 23;
        // $user = "2300000";
        // $pass = "4213V";
        // $productor = "2300000";
        $endpoint = $this->baseUrl . "/v4/wssolicitud" . $this->environment . "/Solicitud?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
            "valorParametro"	=> $productor);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTO",
            "valorParametro"	=> $product);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> "IM");

        if ( isset($commercialKey) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CLAVE_COMERCIAL",
                "valorParametro"	=> $commercialKey);
        }


        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData
        );

        app('debugbar')->info($params);
        $result = $client->obtenerCuestionarioSalud($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    function validateHealthForm($user, $pass, $language, $productor, $product, $formId, $form) {

        $endpoint = $this->baseUrl . "/v4/wssolicitud" . $this->environment . "/Solicitud?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
            "valorParametro"	=> $productor);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTO",
            "valorParametro"	=> $product);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CANAL_ENTRADA",
            "valorParametro"	=> "IM");
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_CUESTIONARIO",
            "valorParametro"	=> $formId);


        $cData = array();
        $cData[] = array(
            "nombreParametro"	=> "P_APLICACION",
            "valorParametro"	=> "IMEDIADOR"
        );

        $hData = $this->buildHealthArray($form);

        $params = array(
            "pTipoAcceso"	=> "GEN",
            "pUsuario"		=> $user,
            "pPassword"		=> $pass,
            "pIdioma"		=> $language,
            "pDatosEntrada"	=> $inputData,
            "pDatosConexion"=> $cData,
            "pDatosDecSalud"=> $hData
        );

        app('debugbar')->info($params);
        $result = $client->evaluarCuestionarioSalud($params);

        if (is_soap_fault($result)) {
            var_dump($result);
        }

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
    public function submitPolicy($data) {

        //app('debugbar')->info($data);
        $endpoint = $this->baseUrl . "/v3/wspoliza" . $this->environment . "/Poliza?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTOR",
            "valorParametro"	=> $data["productor"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PRODUCTO",
            "valorParametro"	=> $data["productId"]);

        $scheme = "A";
        if ( isset($data["scheme"]) ) {
            $scheme = $data["scheme"];
        }
        $inputData[] =  array(
            "nombreParametro"	=> "P_REGIMEN_SEG_SOCIAL",
            "valorParametro"	=> $scheme);

        if ( isset($data["preclient"]) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CODIGO_PRECLIENTE",
                "valorParametro"	=> $data["preclient"]);
        }
        $inputData[] =  array(
            "nombreParametro"	=> "P_PROFESION_CLIENTE",
            "valorParametro"	=> $data["profession"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_SEXO",
            "valorParametro"	=> $data["gender"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_FECHA_NACIMIENTO_CLIENTE",
            "valorParametro"	=> $data["birthdate"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_TALLA",
            "valorParametro"	=> $data["height"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_PESO",
            "valorParametro"	=> $data["weight"]);

        if ( isset($data["commercialKey"]) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CLAVE_COMERCIAL",
                "valorParametro"	=> $data["commercialKey"]);
        } else {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CLAVE_COMERCIAL",
                "valorParametro"	=> "DUE");
        }
        $inputData[] =  array(
            "nombreParametro"	=> "P_NOMBRE_ASEGURADO",
            "valorParametro"	=> $data["name"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_APELLIDO_1_ASEGURADO",
            "valorParametro"	=> $data["surname"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_DOCUMENTO_ASEGURADO",
            "valorParametro"	=> $data["docId"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_TIPO_DOCUMENTO_ASEGURADO",
            "valorParametro"	=> $data["docType"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_EMAIL_ASEGURADO",
            "valorParametro"	=> $data["email"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_TELEFONO_ASEGURADO",
            "valorParametro"	=> $data["phone"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_TIPO_VIA_ASEGURADO",
            "valorParametro"	=> $data["streetType"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_DIRECCION_ASEGURADO",
            "valorParametro"	=> $data["address"]);
        $inputData[] =  array(
            "nombreParametro"	=> "CODPOS",
            "valorParametro"	=> $data["postalCode"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_POBLACION_ASEGURADO",
            "valorParametro"	=> $data["city"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_IDIOMA_ASEGURADO",
            "valorParametro"	=> $data["insuredLanguage"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_NOMBRE_EMPRESA",
            "valorParametro"	=> $data["companyName"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_TIPO_DIRECCION_EMPRESA",
            "valorParametro"	=> $data["companyAddressType"]);

        if( $data["companyAddressType"] == "O") {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CODIGO_TIPO_VIA_EMPRESA",
                "valorParametro"	=> $data["companyStreetType"]);
            $inputData[] =  array(
                "nombreParametro"	=> "P_DIRECCION_EMPRESA",
                "valorParametro"	=> $data["companyAddress"]);
            $inputData[] =  array(
                "nombreParametro"	=> "P_POBLACION_EMPRESA",
                "valorParametro"	=> $data["companyCity"]);
        }

        $inputData[] =  array(
            "nombreParametro"	=> "P_LUGAR_HABITUAL_TRABAJO",
            "valorParametro"	=> $data["workLocationType"]);

        $inputData[] =  array(
            "nombreParametro"	=> "P_FORMA_PAGO",
            "valorParametro"	=> $data["paymentMethod"]);

        if ( isset($data["hasMorePolicies"]) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_TIENE_OTRO_SEGURO",
                "valorParametro"	=> $data["hasMorePolicies"]);

            if ( $data["hasMorePolicies"] == "S" ) {
                if ( isset($data["extraCompanyName"]) ) {
                    $inputData[] =  array(
                        "nombreParametro"	=> "P_COMPAÑIA_OTRO_SEGURO",
                        "valorParametro"	=> $data["anotherInsuranceName"]);
                }
                if ( isset($data["extraInsurancePrice"]) ) {
                    $inputData[] =  array(
                        "nombreParametro"	=> "P_CAPITAL_OTRO_SEGURO",
                        "valorParametro"	=> $data["anotherInsurancePrice"]);
                }
                if ( isset($data["extraInsuranceDate"]) ) {
                    $inputData[] =  array(
                        "nombreParametro"	=> "P_OTRO_SEGURO_FECHA_VENCIM",
                        "valorParametro"	=> $data["anotherInsuranceEnds"]);
                }
            }
        }

        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_PAIS_IBAN",
            "valorParametro"	=> $data["IBANcountry"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_DIGITO_CONTROL_IBAN",
            "valorParametro"	=> $data["IBANcontrol"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_ENTIDAD_CUENTA",
            "valorParametro"	=> $data["IBANentity"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_OFICINA_CUENTA",
            "valorParametro"	=> $data["IBANoffice"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_DC_CUENTA",
            "valorParametro"	=> $data["IBANdc"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CUENTA_CUENTA",
            "valorParametro"	=> $data["IBANaccount"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_TIPO_TOMADOR",
            "valorParametro"	=> $data["holderType"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_IDIOMA_TOMADOR",
            "valorParametro"	=> $data["holderLanguage"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_NOMBRE_TOMADOR",
            "valorParametro"	=> $data["holderName"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_APELLIDO_1_TOMADOR",
            "valorParametro"	=> $data["holderSurname"]);

        if ( !empty($data["holderBirthdate"]) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_FECHA_NACIMIENTO_TOMADOR",
                "valorParametro"	=> $data["holderBirthdate"]);
        }

        $inputData[] =  array(
            "nombreParametro"	=> "P_TELEFONO_TOMADOR",
            "valorParametro"	=> $data["holderPhone"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_EMAIL_TOMADOR",
            "valorParametro"	=> $data["holderEmail"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_TIPO_DOCUMENTO_TOMADOR",
            "valorParametro"	=> $data["holderDocType"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_DOCUMENTO_TOMADOR",
            "valorParametro"	=> $data["holderDocId"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_DIRECCION_TOMADOR",
            "valorParametro"	=> $data["holderAddress"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_POBLACION_TOMADOR",
            "valorParametro"	=> $data["holderCity"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_CODIGO_TIPO_VIA_TOMADOR",
            "valorParametro"	=> $data["holderStreetType"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_FECHA_EFECTO",
            "valorParametro"	=> $data["date"]);
        $inputData[] =  array(
            "nombreParametro"	=> "P_BENEFICIARIO",
            "valorParametro"	=> "A");
        $inputData[] =  array(
            "nombreParametro"	=> "P_OPOSICION_DATOS",
            "valorParametro"	=> $data["dataPreferences"]);

        if ( isset($data["entryChannel"]) ) {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CANAL_ENTRADA",
                "valorParametro"	=> $data["entryChannel"]);
        } else {
            $inputData[] =  array(
                "nombreParametro"	=> "P_CANAL_ENTRADA",
                "valorParametro"	=> $this->getChannel($data));
        }


        $coverageData = array();
        for ($i=0; $i < count($data["coverageData"]); $i++) {
            $coverageData[] = array(
                "capital"		=> $data["coverageData"][$i]["capital"],
                "codigo"		=> $data["coverageData"][$i]["codigo"],
                "descripcion"	=> $data["coverageData"][$i]["descripcion"],
                "duracion"		=> $data["coverageData"][$i]["duracion"],
                "franquicia"	=> $data["coverageData"][$i]["franquicia"],
                "primaNeta"	    => $data["coverageData"][$i]["primaNeta"]
            );
        }



        $cData = array();
        if ( isset($data["application"]) ) {
            $cData[] = array(
                "nombreParametro"	=> "P_APLICACION",
                "valorParametro"	=> $data["application"]
            );
        } else {
            $cData[] = array(
                "nombreParametro"	=> "P_APLICACION",
                "valorParametro"	=> "IMEDIADOR"
            );
        }


        if ( isset($data["healthQ"]) ) {
            $healthData = array();
            $healthData = $this->buildHealthArray($data["healthQ"]);
            $params = array(
                "pTipoAcceso"		=> "GEN",
                "pUsuario"			=> $data["user"],
                "pPassword"			=> $data["pass"],
                "pIdioma"			=> $data["language"],
                "pDatosEntrada"		=> $inputData,
                "pDatosCobertura"	=> $coverageData,
                "pDatosDecSalud"	=> $healthData,
                "pDatosConexion"	=> $cData
            );
        }else{
            $params = array(
                "pTipoAcceso"		=> "GEN",
                "pUsuario"			=> $data["user"],
                "pPassword"			=> $data["pass"],
                "pIdioma"			=> $data["language"],
                "pDatosEntrada"		=> $inputData,
                "pDatosCobertura"	=> $coverageData,
                "pDatosConexion"	=> $cData
            );
        }



        app('debugbar')->info($params);
        $result = $client->altaPoliza($params);

        if (is_soap_fault($result)) {
            return false;
        }

        return $result;
    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $requestId - request identifier
     * @param $policyId - policy identifier
     * @return bool - false or params list
     * @throws \SoapFault
     */
    public function getSignParams($user, $pass, $language, $requestId, $policyId)
    {
        $endpoint = $this->baseUrl . "/v4/wslogalty" . $this->environment . "/WsLogalty?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"   => "P_SOLICITUD",
            "valorParametro"    => $requestId);
        $inputData[] =  array(
            "nombreParametro"   => "P_POLIZA",
            "valorParametro"    => $policyId);
        $inputData[] =  array(
            "nombreParametro"   => "P_ID_ENVIO_DOC",
            "valorParametro"    => "7");


        $cData = array();
        $cData[] = array(
            "nombreParametro"   => "P_APLICACION",
            "valorParametro"    => "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"   => "GEN",
            "pUsuario"      => $user,
            "pPassword"     => $pass,
            "pIdioma"       => $language,
            "pDatosEntrada" => $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->obtenerParamsEnvio($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }

    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $policyId - policy identifier
     * @return bool - successful request
     * @throws \SoapFault
     */
    public function createUserAccess($user, $pass, $language, $policyId)
    {
        $endpoint = $this->baseUrl . "/v4/wslogalty" . $this->environment . "/WsLogalty?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"   => "P_POLIZA",
            "valorParametro"    => $policyId);

        $cData = array();
        $cData[] = array(
            "nombreParametro"   => "P_APLICACION",
            "valorParametro"    => "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"   => "GEN",
            "pUsuario"      => $user,
            "pPassword"     => $pass,
            "pIdioma"       => $language,
            "pDatosEntrada" => $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->generarAccesoAreaClientes($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /**
     * @param $user - logged in username
     * @param $pass - logged in password
     * @param $language - logged in language
     * @param $requestId - request identifier
     * @param $policyId - policy identifier
     * @return bool - successful request
     * @throws \SoapFault
     */
    public function createUserRequest($user, $pass, $language, $requestId, $policyId, $guid, $lgtStatus)
    {
        $endpoint = $this->baseUrl . "/v4/wslogalty" . $this->environment . "/WsLogalty?WSDL";
        $client = new SoapClient($endpoint);

        $inputData = array();
        $inputData[] =  array(
            "nombreParametro"   => "P_SOLICITUD",
            "valorParametro"    => $requestId);
        $inputData[] =  array(
            "nombreParametro"   => "P_POLIZA",
            "valorParametro"    => $policyId);
        $inputData[] =  array(
            "nombreParametro"   => "P_GUID",
            "valorParametro"    => $guid);
        $inputData[] =  array(
            "nombreParametro"   => "P_ESTADO",
            "valorParametro"    => $lgtStatus);
        $inputData[] =  array(
            "nombreParametro"   => "P_ID_ENVIO_DOC",
            "valorParametro"    => "7");

        $cData = array();
        $cData[] = array(
            "nombreParametro"   => "P_APLICACION",
            "valorParametro"    => "IMEDIADOR"
        );

        $params = array(
            "pTipoAcceso"   => "GEN",
            "pUsuario"      => $user,
            "pPassword"     => $pass,
            "pIdioma"       => $language,
            "pDatosEntrada" => $inputData,
            "pDatosConexion"=> $cData
        );

        $result = $client->insertarRequest($params);

        if (is_soap_fault($result)) {
            $result = false;
        }

        return $result;
    }


    /*
    * H E L P E R S
    */

    private function buildHealthArray( $form ) {
        $hData = array();
        foreach ($form as $answer) {
            if ( array_key_exists("code", $answer) ) {
                if ( array_key_exists("date", $answer) ) {
                    $hData[] = array(
                        "codigoPregunta"	=> $answer["id"],
                        "codigoRespuesta"	=> $answer["code"],
                        "fechaOcurrencia"	=> $answer["date"]
                    );
                } else {
                    $hData[] = array(
                        "codigoPregunta"	=> $answer["id"],
                        "codigoRespuesta"	=> $answer["code"]
                    );
                }
            } else {
                if ( array_key_exists("date", $answer) ) {
                    $hData[] = array(
                        "codigoPregunta"	=> $answer["id"],
                        "textoRespuesta"	=> $answer["text"],
                        "fechaOcurrencia"	=> $answer["date"]
                    );
                } else {
                    $hData[] = array(
                        "codigoPregunta"	=> $answer["id"],
                        "textoRespuesta"	=> $answer["text"]
                    );
                }
            }
        }

        return $hData;
    }


    /**
     * @param $option
     * @param $enfCob
     * @param $enfSub
     * @param $accCob
     * @param $accSub
     * @param $hospCob
     * @param $hospSub
     * @return array
     */
    private function getCapitalGarantia($enfCob, $enfSub, $accCob, $accSub, $hospCob, $hospSub) {

        $fData = array();
        $fData[] = array(
            "nombreParametro"	=> $enfCob,
            "valorParametro"	=> $enfSub
        );

        if ( isset($accCob) && isset($accSub) ) {
            $fData[] = array(
                "nombreParametro"	=> $accCob,
                "valorParametro"	=> $accSub
            );
        }

        if ( isset($hospCob) && isset($hospSub) ) {
            $fData[] = array(
                "nombreParametro"	=> $hospCob,
                "valorParametro"	=> $hospSub
            );
        }

        return $fData;
    }

    /**
     * @param $option
     * @return string
     */
    private function getProCode($option) {
        if ( $option === PMWSTEST::OPTION_ELITE) {
            return "21";
        } else if ( $option === PMWSTEST::OPTION_GOLDEN) {
            return "20";
        } else if ( $option === PMWSTEST::OPTION_STANDARD) {
            return "18";
        }

        return "";
    }

    private function getChannel($data) {
        if ( isset($data["pmUserCode"]) ){
            return "GI";
        }
        return "IM";
    }
}

?>
