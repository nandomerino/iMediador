<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Middleware\PMWShandler;

class PMWSjs extends Controller
{
    private $PMWShandler;
    private $parameters;

    public function getData(Request $request)
    {
        $this->PMWShandler = new PMWShandler();

        // Gets sent variables variables
        $this->parameters = $request->all();

        switch ($this->parameters["ws"]) {
            case "changePassword":
                $response = $this->changePassword();
                break;
            case "getProductVariations":
                $response = $this->getProductVariations();
                break;
            case "getProductConfiguration":
                $response = $this->getProductConfiguration();
                break;
            case "getRates":
                $response = $this->getRates();
                break;
            case "getRatesByPrice":
                $response = $this->getRatesByPrice();
                break;
            case "getBudget":
                $response = $this->getBudget();
                break;
            case "getBudgetDocument":
                $response = $this->getBudgetDocument();
                break;
            case "getCityProvince":
                $response = $this->getCityProvince();
                break;
            case "getHealthForm":
                $response = $this->getHealthForm();
                break;
            case "validateHealthForm":
                $response = $this->validateHealthForm();
                break;
            case "submitPolicy":
                $response = $this->submitPolicy();
                break;
            case "getReceipt":
                $response = $this->getReceipt();
                break;
            case "getDocument":
                $response = $this->getDocument();
                break;
            case "uploadDocument":
                $response = $this->uploadDocument();
                break;
            case "getLgtSignAccess":
                $response = $this->getLgtSignAccess();
                break;
            case "getProductsList":
                $response = $this->getProductsList();
                break;
            case "consultas":
                $response = $this->consultas();
                break;

            default:
                $response = false;
        }
        return $response;
    }



    public function changePassword()
    {
        // extra parameters for quote widget
        if( !isset( $this->parameters["language"]) ) {
            $this->parameters["language"] = null;
        }
        if( !isset( $this->parameters["u"]) ) {
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }


        // Call PM WS
        $data = $this->PMWShandler->changePassword(
            $this->parameters["user"],
            $this->parameters["password"],
            $this->parameters["passwordNew"]
        );
        //app('debugbar')->info('changePassword data');
        //app('debugbar')->info($data);

        if  (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }

    }

    public function getProductsList(){

            $quote = session('quote');
            //return $quote['productVariations'];
            return response()->json(['success' => true, 'data' => $quote['productVariations']]);

    }

    public function getProductVariations()
    {
        // extra parameters for quote widget
        if( !isset( $this->parameters["entryChannel"]) ){
            $this->parameters["entryChannel"] = null;
        }
        if( !isset( $this->parameters["application"]) ) {
            $this->parameters["application"] = null;
        }
        if( !isset( $this->parameters["u"]) ) {
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }

        // Call PM WS
        $data = $this->PMWShandler->getProductVariations(
            $this->parameters["productor"],
            $this->parameters["product"],
            $this->parameters["entryChannel"],
            $this->parameters["application"],
            $this->parameters["u"],
            $this->parameters["p"]
        );
        app('debugbar')->info('getProductVariations $data');
        app('debugbar')->info($data);

        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }

    }

    public function getProductConfiguration()
    {
        $modField = null;

        // extra parameters for quote widget
        if( !isset( $this->parameters["entryChannel"]) ){
            $this->parameters["entryChannel"] = null;
        }
        if( !isset( $this->parameters["application"]) ) {
            $this->parameters["application"] = null;
        }
        if( !isset( $this->parameters["modifiedField"]) ) {
            $this->parameters["modifiedField"] = null;
        } else {
            $modField = json_decode($this->parameters["modifiedField"]);
        }
        if( !isset( $this->parameters["u"]) ) {
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }

        // Call PM WS
        $data = $this->PMWShandler->getProductConfiguration(
            $this->parameters["productor"],
            $this->parameters["product"],
           // $this->parameters["productVariation"],
            $this->parameters["productModality"] ?? null,
            $this->parameters["entryChannel"],
            $this->parameters["application"],
            $modField,//$this->parameters["modifiedField"],
            $this->parameters["u"],
            $this->parameters["p"]
        );
        app('debugbar')->info('getProductConfiguration PWSJ $data');
        app('debugbar')->info($data);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }

    }

    public function getRates()
    {
        //app('debugbar')->info($this->parameters);

        // Call PM WS
        $parameters = array();
        $parameters["productor"] = $this->parameters["productor"];
        $parameters["option"] = $this->parameters["option"];
        $parameters["productId"] = $this->parameters["productId"];
        $parameters["profession"] = $this->parameters["profession"];
        $parameters["birthdate"] = $this->parameters["birthdate"];
        $parameters["gender"] = $this->parameters["gender"];
        $parameters["height"] = $this->parameters["height"];
        $parameters["weight"] = $this->parameters["weight"];
        $parameters["enfCob"] = $this->parameters["enfCob"];
        $parameters["enfSub"] = $this->parameters["enfSub"];
        $parameters["accCob"] = $this->parameters["accCob"];
        $parameters["accSub"] = $this->parameters["accSub"];
        $parameters["hospCob"] = $this->parameters["hospCob"];
        $parameters["hospSub"] = $this->parameters["hospSub"];
        $parameters["covidPrestacionCob"] = $this->parameters["covidPrestacionCob"] ?? null;
        $parameters["covidPrestacionSub"] = $this->parameters["covidPrestacionSub"] ?? null;
        $parameters["covidUCICob"] = $this->parameters["covidUCICob"] ?? null;
        $parameters["covidUCISub"] = $this->parameters["covidUCISub"] ?? null;
        $parameters["covidHospitalizacionCob"] = $this->parameters["covidHospitalizacionCob"] ?? null;
        $parameters["covidHospitalizacionSub"] = $this->parameters["covidHospitalizacionSub"] ?? null;
        $parameters["jobType"] = $this->parameters["jobType"];
        $parameters["hiring"] = $this->parameters["hiring"]?? null;
        $parameters["duration"] = $this->parameters["duration"] ?? null;



        // extra parameters for quote widget
        if( !isset( $this->parameters["discount"]) ){
            $this->parameters["discount"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountYears"]) ){
            $this->parameters["discountYears"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountSobreprima"]) ){
            $this->parameters["discountSobreprima"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountCommisionMed"]) ){
            $this->parameters["discountCommisionMed"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountCommisionDel"]) ){
            $this->parameters["discountCommisionDel"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountRecargoFinanciacion"]) ){
            $this->parameters["discountRecargoFinanciacion"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountCobro"]) ){
            $this->parameters["discountCobro"] = null;
        }
        $parameters["discount"] = $this->parameters["discount"];
        $parameters["discountYears"] = $this->parameters["discountYears"];
        $parameters["sobreprimaDel"] = $this->parameters["discountSobreprima"];
        $parameters["discountComMed"] = $this->parameters["discountCommisionMed"];
        $parameters["discountComDel"] = $this->parameters["discountCommisionDel"];
        $parameters["financeCharge"] = $this->parameters["discountRecargoFinanciacion"];
        $parameters["paymentChannel"] = $this->parameters["discountCobro"];

        // extra parameters for quote widget
        if( !isset( $this->parameters["commercialKey"]) ){
            $this->parameters["commercialKey"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["entryChannel"]) ){
            $this->parameters["entryChannel"] = null;
        }
        if( !isset( $this->parameters["application"]) ) {
            $this->parameters["application"] = null;
        }
        $parameters["commercialKey"] = $this->parameters["commercialKey"];
        $parameters["entryChannel"] = $this->parameters["entryChannel"];
        $parameters["application"] = $this->parameters["application"];

        if( !isset( $this->parameters["u"]) ){
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }
        $parameters["u"] = $this->parameters["u"];
        $parameters["p"] = $this->parameters["p"];


        if( !isset( $this->parameters["Cob4"]) ) {
            $this->parameters["Cob4"] = null;
        }
        if( !isset( $this->parameters["Sub4"]) ) {
            $this->parameters["Sub4"] = null;
        }
        if( !isset( $this->parameters["Cob5"]) ) {
            $this->parameters["Cob5"] = null;
        }
        if( !isset( $this->parameters["Sub5"]) ) {
            $this->parameters["Sub5"] = null;
        }
        $parameters["Cob4"] = $this->parameters["Cob4"];
        $parameters["Sub4"] = $this->parameters["Sub4"];
        $parameters["Cob5"] = $this->parameters["Cob5"];
        $parameters["Sub5"] = $this->parameters["Sub5"];
        $parameters["franchise"] = $this->parameters["franchise"] ?? null;

        //app('debugbar')->info($enf);


          $data = $this->PMWShandler->getRates($parameters);
          //app('debugbar')->info($data);
          if (is_array($data)) {
               return response()->json(['success' => true, 'data' => $data]);
          } else {
               return response()->json(['success' => false, 'e' => $data]);
          }

    }

    public function getRatesByPrice()
    {
        //app('debugbar')->info($this->parameters);



        $data = $this->PMWShandler->getRatesByPrice(
            $this->parameters["productor"],
            $this->parameters["option"],
            $this->parameters["productCode"],
            $this->parameters["price"],
            $this->parameters["franchise"] ?? null,
            $this->parameters["jobType"],
            $this->parameters["profession"],
            $this->parameters["birthdate"],
            $this->parameters["gender"],
            $this->parameters["height"],
            $this->parameters["weight"],
            $this->parameters["duration"] ?? null,
            $this->parameters["commercialKey"]
        );
        //app('debugbar')->info('pmwsjs getRatesByPrice:');
        //app('debugbar')->info($data);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }
    }

    public function getBudget()
    {
        //app('debugbar')->info($this->parameters);

        // Call PM WS
        $parameters = array();
        $parameters["productor"] = $this->parameters["productor"];
        $parameters["option"] = $this->parameters["option"];
        $parameters["productId"] = $this->parameters["productId"];
        $parameters["profession"] = $this->parameters["profession"];
        $parameters["birthdate"] = $this->parameters["birthdate"];
        $parameters["gender"] = $this->parameters["gender"];
        $parameters["height"] = $this->parameters["height"];
        $parameters["weight"] = $this->parameters["weight"];
        $parameters["jobType"] = $this->parameters["jobType"];
        $parameters["duration"] = $this->parameters["duration"] ?? null;
        $parameters["date"] = $this->parameters["date"];
        $parameters["coverages"] = $this->parameters["coverages"];
        $parameters["paymentMethod"] = $this->parameters["paymentMethod"];



        // extra parameters for quote widget
        if( !isset( $this->parameters["discount"]) ){
            $this->parameters["discount"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountYears"]) ){
            $this->parameters["discountYears"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountSobreprima"]) ){
            $this->parameters["discountSobreprima"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountCommisionMed"]) ){
            $this->parameters["discountCommisionMed"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountCommisionDel"]) ){
            $this->parameters["discountCommisionDel"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountRecargoFinanciacion"]) ){
            $this->parameters["discountRecargoFinanciacion"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["discountCobro"]) ){
            $this->parameters["discountCobro"] = null;
        }
        $parameters["discount"] = $this->parameters["discount"];
        $parameters["discountYears"] = $this->parameters["discountYears"];
        $parameters["sobreprimaDel"] = $this->parameters["discountSobreprima"];
        $parameters["discountComMed"] = $this->parameters["discountCommisionMed"];
        $parameters["discountComDel"] = $this->parameters["discountCommisionDel"];
        $parameters["financeCharge"] = $this->parameters["discountRecargoFinanciacion"];
        $parameters["paymentChannel"] = $this->parameters["discountCobro"];

        // extra parameters for quote widget
        if( !isset( $this->parameters["commercialKey"]) ){
            $this->parameters["commercialKey"] = null;
        }
        // extra parameters for quote widget
        if( !isset( $this->parameters["entryChannel"]) ){
            $this->parameters["entryChannel"] = null;
        }
        if( !isset( $this->parameters["application"]) ) {
            $this->parameters["application"] = null;
        }
        $parameters["commercialKey"] = $this->parameters["commercialKey"];
        $parameters["entryChannel"] = $this->parameters["entryChannel"];
        $parameters["application"] = $this->parameters["application"];

        if( !isset( $this->parameters["u"]) ){
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }
        $parameters["u"] = $this->parameters["u"];
        $parameters["p"] = $this->parameters["p"];


        if( !isset( $this->parameters["Cob4"]) ) {
            $this->parameters["Cob4"] = null;
        }
        if( !isset( $this->parameters["Sub4"]) ) {
            $this->parameters["Sub4"] = null;
        }
        if( !isset( $this->parameters["Cob5"]) ) {
            $this->parameters["Cob5"] = null;
        }
        if( !isset( $this->parameters["Sub5"]) ) {
            $this->parameters["Sub5"] = null;
        }
        $parameters["Cob4"] = $this->parameters["Cob4"];
        $parameters["Sub4"] = $this->parameters["Sub4"];
        $parameters["Cob5"] = $this->parameters["Cob5"];
        $parameters["Sub5"] = $this->parameters["Sub5"];
        $parameters["franchise"] = $this->parameters["franchise"] ?? null;

        //app('debugbar')->info($parameters);


        $data = $this->PMWShandler->getBudget($parameters);
        //app('debugbar')->info($data);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }

    }

   public function getBudgetDocument()
    {
        // Call PM WS
        $parameters = array();
        $parameters["productor"] = $this->parameters["productor"];
        $parameters["budgetNumber"] = $this->parameters["budgetNumber"];

        if( !isset( $this->parameters["u"]) ){
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }
        $parameters["u"] = $this->parameters["u"];
        $parameters["p"] = $this->parameters["p"];
        //app('debugbar')->info($parameters);
        $data = $this->PMWShandler->getBudgetDocument($parameters);
        //app('debugbar')->info($data);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data,  'budgetNumber' => $parameters["budgetNumber"]]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }

    }

    public function getCityProvince()
    {
        if( !isset( $this->parameters["u"]) ){
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }

        $data = $this->PMWShandler->getCityProvince(
            $this->parameters["postalCode"],
            $this->parameters["u"],
            $this->parameters["p"]
        );
        //app('debugbar')->info($data);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }

    }

    function getHealthForm()
    {
        if( !isset( $this->parameters["commercialKey"]) ){
            $this->parameters["commercialKey"] = null;
        }
        if( !isset( $this->parameters["u"]) ) {
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }

        $data = $this->PMWShandler->getHealthForm(
            $this->parameters["productor"],
            $this->parameters["product"],
            $this->parameters["commercialKey"],
            $this->parameters["u"],
            $this->parameters["p"]
        );

        //app('debugbar')->info($data);
        if ( is_array($data) ) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }
    }

    function validateHealthForm()
    {
        if( !isset( $this->parameters["u"]) ) {
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }

        //app('debugbar')->info($this->parameters);
        $data = $this->PMWShandler->validateHealthForm(
            $this->parameters["productor"],
            $this->parameters["product"],
            $this->parameters["formId"],
            $this->parameters["formData"],
            $this->parameters["u"],
            $this->parameters["p"]
        );
        //app('debugbar')->info($this->parameters);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }
    }

    public function submitPolicy()
    {
        //app('debugbar')->info('PWJS $this->parameters');
        //app('debugbar')->info($this->parameters);
        // Call PM WS

        // extra parameters for quote widget
        if( !isset( $this->parameters["entryChannel"]) ){
            $this->parameters["entryChannel"] = null;
        }
        if( !isset( $this->parameters["application"]) ) {
            $this->parameters["application"] = null;
        }
        if( !isset( $this->parameters["u"]) ) {
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }

        // Call PM WS
        $data = $this->PMWShandler->submitPolicy($this->parameters);
        //app('debugbar')->info('PWJS $data');
        //app('debugbar')->info($data);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }

    }

    public function getDocument()
    {
        if( !isset( $this->parameters["pmuser"]) ){
            $this->parameters["pmuser"] = null;
        }
        if( !isset( $this->parameters["u"]) ){
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }

        // Call PM WS
        $data = $this->PMWShandler->getDocument(
            $this->parameters["productor"],
            $this->parameters["docId"],
            $this->parameters["source"],
            $this->parameters["type"],
            $this->parameters["format"],
            $this->parameters["pmuser"],
            $this->parameters["u"],
            $this->parameters["p"]
        );
        //app('debugbar')->info($data);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }
    }

    public function getReceipt()
    {
        // extra parameters for quote widget
        if( !isset( $this->parameters["entryChannel"]) ){
            $this->parameters["entryChannel"] = null;
        }
        if( !isset( $this->parameters["application"]) ) {
            $this->parameters["application"] = null;
        }
        if( !isset( $this->parameters["u"]) ) {
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }

        // Call PM WS
        $data = $this->PMWShandler->getReceipt($this->parameters);
        app('debugbar')->info('PWJS $data');
        app('debugbar')->info($data);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }
    }


    public function uploadDocument()
    {
        // Call PM WS
        $data = $this->PMWShandler->uploadDocument(
            //$this->parameters["productor"],
            $this->parameters["folderId"],
            $this->parameters["docId"],
            $this->parameters["refId"],
            $this->parameters["docType"],
            $this->parameters["doc"]
        );
        //app('debugbar')->info($data);
        if (is_array($data)) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }
    }

    public function getLgtSignAccess()
    {

        if( !isset( $this->parameters["u"]) ){
            $this->parameters["u"] = null;
        }
        if( !isset( $this->parameters["p"]) ) {
            $this->parameters["p"] = null;
        }

        // Call PM WS
        $data = $this->PMWShandler->getLgtSignAccess(
            $this->parameters["requestId"],
            $this->parameters["policyId"],
            $this->parameters["doc"],
            $this->parameters["u"],
            $this->parameters["p"]
        );
        //app('debugbar')->info($data);
        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'e' => $data]);
        }
    }

}
