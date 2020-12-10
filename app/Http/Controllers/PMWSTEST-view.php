<?php
require "logaltyclient/autoload.php";

use App\Http\Controllers\PMWSTEST;
use App\Http\Controllers\PMWS;
use WL\Http\LogaltyClient\LogaltyClient;
use WL\Http\LogaltyClient\TransactionDTO;
use WL\Http\LogaltyClient\FormDTO;
use WL\Http\LogaltyClient\FormElementDTO;
use WL\Http\LogaltyClient\PropertyDTO;


$pmapi = new PMWSTEST();

$user = "6600031";
$pass = "G7742";
$language = "C";
$managerCode = "abc123";
$pmUserCode = "451";
$productor = "130810617";
$accessCode = "AUYHYO28KNBJ2LU3HHU6";
// $productor = null;


// $franchise = null;
$paymentMethod = "1";
// $paymentMethod = null;
$profession = "3072";
$birthdate = "14/03/2002";
$gender = "H";
$height = "180";
$weight = "80";
$enfSub = "30";
$accSub = "40";
$hospSub = "50";
$price = "100";


echo "<pre>";
// echo "Login Mediador:\n";
// $response = $pmapi->login($user, $pass, $language);
// var_dump($response);
$productGroup = "1";
//
//echo "Login Gestor:\n";
//$response = $pmapi->login($user, $pass, $language, $managerCode);
//var_dump($response);
//
//echo "Login Usuario interno:\n";
//$response = $pmapi->loginInt($user, $pass, $language, $pmUserCode);
//var_dump($response);

// echo "Idiomas:\n";
// $response = $pmapi->getLanguages();
// var_dump($response);

// echo "Obtener productores:\n";
// $response = $pmapi->getProductors($user, $pass, $language);
// $response = $pmapi->getProductors($user, $pass, $language, $productor);
// var_dump($response);

// echo "Obtener género:\n";
// $response = $pmapi->getGenders($language);
// var_dump($response);

// echo "Obtener profesiones:\n";
// $response = $pmapi->getProfessions($user, $pass, $language);
// var_dump($response);

// echo "Obtener formas de pago:\n";
// $response = $pmapi->getPaymentMethods($language);
// var_dump($response);
// echo "Obtener tipos de personas:\n";
// $response = $pmapi->getPersonTypes($language);
// var_dump($response);
// echo "Obtener tipos de vía:\n";
// $response = $pmapi->getAddressTypes($language);
// var_dump($response);
// echo "Obtener tipos de dirección:\n";
// $response = $pmapi->getCompanyAddressTypes($language);
// var_dump($response);
// echo "Obtener lugares habituales de trabajo:\n";
// $response = $pmapi->getWorkLocationTypes($language);
// var_dump($response);




// echo "Obtener modalidades de producto:\n";
// $response = $pmapi->getProductVariations($user, $pass, $language, $productor, $productGroup);
// $response = $pmapi->getProductVariations($user, $pass, $language, $productor, $productGroup, $pmUserCode);
// var_dump($response);
$productVariationId = "19";
$productId = "21";
// $productVariationId = "101";
$option = "10";
$optionInv = "1";
$enfCob = "CAPITAL_120";
$accCob = "CAPITAL_121";
$hospCob = "CAPITAL_122";


//echo "Obtener configuración de producto:\n";
 // echo $user . " | " . $pass . " | " . $language . " | " . $productGroup . " | " . $productVariationId;
// $response = $pmapi->getProductConfiguration($user, $pass, $language, $productor, $productGroup, $productVariationId);
// var_dump($response);
$franchise = "10";
$period = "365";
$discount = "4";
$discountYears = "2";
$discountComMed = "5";
$discountComDel = "";
$sobreprimaDel = "";
$financeCharge = "P";
$paymentChannel = "2";

// echo "Obtener cuadro tarifas:\n";
// $pmUserCode = null;
// 6600031, G7742, C, 130810011, 12, 1, null,"3072", "10/03/1970", "H", "180", "80", "CAPITAL_11", "50", "CAPITAL_12", "200", "CAPITAL_13", "299", null, null
// 6600031, G7742, C, 130810617, 10, 21, , 1002, 10/10/1970, H, 180, 80, CAPITAL_120, 60, CAPITAL_121, 55, CAPITAL_122, 50
// echo $user.", ". $pass.", ". $language.", ". $productor.", ". $option.", ". $productId.", ". null.", ". $profession.", ". $birthdate.", ". $gender.", ". $height.", ". $weight.", ". $enfCob.", ". $enfSub.", ". $accCob.", ". $accSub.", ". $hospCob.", ". $hospSub.", ". null.", ". null."<br>";
// echo "6600031, G7742, C, 130810008, 12, 1, , 3072, 08/03/2002, H, 180, 80, CAPITAL_11, 50, CAPITAL_12, 60, CAPITAL_13, 76, ,<br>";
// $response = $pmapi->getRates("6600031", "G7742", "C", "130810008", "12", "1", null,"1002", "08/03/1980", "H", "180", "80", "CAPITAL_11", "50", "CAPITAL_12", "60", "CAPITAL_13", "70", null, null);
$rates_args["user"] = $user;
$rates_args["pass"] = $pass;
$rates_args["language"] = $language;
$rates_args["productor"] = $productor;
$rates_args["option"] = $option;
$rates_args["productId"] = $productId;
$rates_args["franchise"] = $franchise;
$rates_args["profession"] = $profession;
$rates_args["birthdate"] = $birthdate;
$rates_args["gender"] = $gender;
$rates_args["height"] = $height;
$rates_args["weight"] = $weight;
$rates_args["enfCob"] = $enfCob;
$rates_args["enfSub"] = $enfSub;
$rates_args["accCob"] = $accCob;
$rates_args["accSub"] = $accSub;
$rates_args["hospCob"] = $hospCob;
$rates_args["hospSub"] = $hospSub;
$rates_args["paymentMethod"] = $paymentMethod;
$rates_args["period"] = $period;
// $rates_args["pmUserCode"] = $pmUserCode;
$rates_args["discount"] = $discount;
$rates_args["discountYears"] = $discountYears;
$rates_args["discountComMed"] = $discountComMed;
$rates_args["discountComDel"] = $discountComDel;
$rates_args["sobreprimaDel"] = $sobreprimaDel;
$rates_args["financeCharge"] = $financeCharge;
$rates_args["paymentChannel"] = $paymentChannel;
// $response = $pmapi->getRates($rates_args);
// var_dump($response);

//echo "Obtener tarificación por precio:\n";
// $pmUserCode = null;
// $response = $pmapi->getRatesByPrice($user, $pass, $language, $productor, $optionInv, $price, $franchise, $profession, $birthdate, $gender, $height, $weight, $period, $pmUserCode);
// var_dump($response);


// echo "Obtener lista de ficheros cartera:\n";
//$response = $pmapi->getFilesList($user, $pass, $language);
// $response = $pmapi->getFilesList($user, $pass, $language, $pmUserCode);
//var_dump($response);
$fileId = "241561";


// echo "Obtener fichero:\n";
//$response = $pmapi->getFile($user, $pass, $language, $fileId);
// $response = $pmapi->getFile($user, $pass, $language, $fileId, $pmUserCode);
//var_dump($response);


// echo "Obtener documento:\n";
$docId = "2020040095";
$source = "2";
$type = "SO";
$format = "A4";
// $response = $pmapi->getDocument($user, $pass, $language, $productor, $docId, $source, $type, $format);
// $response = $pmapi->getDocument($user, $pass, $language, $productor, $docId, $source, $type, $format, $pmUserCode);
// var_dump($response);



// echo "Subir documento:\n";
$folderId = 65;
$docId = 467;
$refId = 2020040071;
$docName = "PRUEBA.pdf";
$docType = "application/pdf";
$doc = "UEsDBAoAAAAAAOCYuCg8z1FoRAAAAEQAAAAJAAAAZWljYXIuY29tWDVPIVAlQEFQWzRcUFpYNTQo
UF4pN0NDKTd9JEVJQ0FSLVNUQU5EQVJELUFOVElWSVJVUy1URVNULUZJTEUhJEgrSCpQSwECFAAK
AAAAAADgmLgoPM9RaEQAAABEAAAACQAAAAAAAAABACAA/4EAAAAAZWljYXIuY29tUEsFBgAAAAAB
AAEANwAAAGsAAAAAAA==";
// $response = $pmapi->uploadDocument($user, $pass, $language, $productor, $folderId, $docId, $refId, $docName, $docType, $doc);
// var_dump($response);

// echo "Obtener lista objetivos:\n";
  // $response = $pmapi->getGoals($user, $pass, $language, $productor);
// $response = $pmapi->getGoals($user, $pass, $language, $productor, $pmUserCode);
 // var_dump($response);

// echo "Obtener lista de municipios:\n";
// $response = $pmapi->getMunicipalities($user, $pass, $language, "15001");
// $response = $pmapi->getGoals($user, $pass, $language, $productor, $pmUserCode);
// var_dump($response);

//echo "Obtener cuestionario de salud:\n";
//$response = $pmapi->getHealthForm($user, $pass, $language, $productor, $productId);
//var_dump($response);
$healthFormId = 1;

//echo "Evaluar cuestionario de salud:\n";
//$response = $pmapi->validateHealthForm($user, $pass, $language, $productor, $productId, $healthFormId, $form);
// var_dump($response);

// echo "Obtener datos de acceso:\n";
// $response = $pmapi->getAccessData($language, $accessCode);
// var_dump($response);

// echo "Validar usuario:\n";
// $response = $pmapi->validateUser($user, $pass, $language, $productVariationId);
// var_dump($response);


// echo "Alta poliza:\n";
$data["user"] = $user;
$data["pass"] = $pass;
$data["language"] = $language;
$data["productor"] = $productor;
$data["productId"] = $productId;
$data["profession"] = $profession;
$data["birthdate"] = $birthdate;
$data["gender"] = $gender;
$data["height"] = $height;
$data["weight"] = $weight;
$data["preclient"] = "";
$data["gender"] = $gender;
$data["name"] = "";
$data["surname"] = "";
$data["docId"] = "";
$data["docType"] = "";
$data["email"] = "";
$data["phone"] = "";
$data["streetType"] = "";
$data["address"] = "";
$data["postalCode"] = "";
$data["city"] = "";
$data["insuredLanguage"] = "";
$data["companyName"] = "";
$data["companyAddressType"] = "";
$data["companyStreetType"] = "";
$data["companyAddress"] = "";
$data["companyCity"] = "";
$data["workLocationType"] = "";
$data["paymentMethod"] = "";
$data["hasMorePolicies"] = "";
$data["extraCompanyName"] = "";
$data["extraInsurancePrice"] = "";
$data["extraInsuranceDate"] = "";
$data["IBANcountryCode"] = "";
$data["IBANcontrolCode"] = "";
$data["IBANentity"] = "";
$data["IBANoffice"] = "";
$data["IBANdc"] = "";
$data["IBANaccount"] = "";
$data["holderType"] = "";
$data["holderLanguage"] = "";
$data["holderName"] = "";
$data["holderSurname"] = "";
$data["holderBirthdate"] = "";
$data["holderPhone"] = "777888999";
$data["holderEmail"] = "";
$data["holderDocType"] = "";
$data["holderDocId"] = "";
$data["holderAddress"] = "";
$data["holderCity"] = "";
$data["holderStreetType"] = "";
$data["date"] = "";
$data["dataPreferences"] = "";
$data["coverageData"] = array(
	array(
		"price"	=> "",
		"code"	=> "",
		"duration"	=> "",
		"franchise"	=> "",
	),
	array(
		"price"	=> "",
		"code"	=> "",
		"duration"	=> "",
		"franchise"	=> "",
	)
);
// $data["healthQ"] = array();

$data["pmUserCode"] = $pmUserCode;
// $response = $pmapi->submitPolicy($data);

// --------------------------------------------------------------------------------------------------------------


$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . "dummy.pdf";
$mimeType = mime_content_type($file);
$docbase64 = base64_encode(file_get_contents($file));
$pm = new PMWS();
$a = $pm->getLgtSignAccess("2000000", "w2019", $language, "2020060100", "605570", $docbase64, $mimeType);

?>
<iframe src="<?php echo $a->getUrl(); ?>" width="1200" height="500"></iframe>

<!-- -------------------------------------------------------------------------------------------------------------- -->


<?php

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/
/* EXAMPLE DOWNLOAD 																							*/
/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

// $fileDummy = dirname(__FILE__) . DIRECTORY_SEPARATOR . "dummy.pdf";
// $docbase64 = base64_encode(file_get_contents($fileDummy));
// $decoded = base64_decode($docbase64);
// $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'file-' . time() . '.pdf';

// $r = file_put_contents($file, $decoded);

// if (file_exists($file)) {
// 	header('Content-Description: File Transfer');
// 	header('Content-Type: application/octet-stream');
// 	header('Content-Disposition: attachment; filename="'.basename($file).'"');
// 	header('Expires: 0');
// 	header('Cache-Control: must-revalidate');
// 	header('Pragma: public');
// 	header('Content-Length: ' . filesize($file));
// 	readfile($file);
// 	unlink($file);
// 	exit;

// }


echo "</pre>";
?>


