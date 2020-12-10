<?php
namespace WL\Http\LogaltyClient;

use DOMDocument;
use DOMText;
use Exception;
use DateTime;
use DateInterval;

/**
 * Class LogaltyClient
 * @package WL\Http\LogaltyClient
 */
class LogaltyClient
{
	private const DEFAULT_TIMEZONE = "Europe/Madrid";

	private const XML_SOAP_ENVELOPE_NS = "http://schemas.xmlsoap.org/soap/envelope/";
	private const XML_SOAP_ENVELOPE_TAG_NAME = "soapenv:Envelope";
	private const XML_SOAP_BODY_TAG_NAME = "soapenv:Body";
	private const XML_SOAP_PT_NS = "urn:logalty:schemas:core:1.0";
	private const XML_SOAP_PT_REQUEST_TAG_NAME = "request";
	private const XML_SOAP_PT_REQUEST_META_NS = "urn:logalty:schemas:request:meta:1.0";
	private const XML_SOAP_PT_REQUEST_META_SERVICE_TAG_NAME = "service";
	private const XML_SOAP_PT_REQUEST_META_T2C_TAG_NAME = "time2close";
	private const XML_SOAP_PT_REQUEST_META_T2S_TAG_NAME = "time2save";
	private const XML_SOAP_PT_REQUEST_META_LOPD_TAG_NAME = "lopd";
	private const XML_SOAP_PT_REQUEST_META_SIGNLEVEL_TAG_NAME = "signlevel";
	private const XML_SOAP_PT_REQUEST_META_RETRY_PROTOCOL_TAG_NAME = "retryprotocol";
	private const XML_SOAP_PT_REQUEST_META_SYNC_TAG_NAME = "synchronous";
	private const XML_SOAP_PT_REQUEST_META_TSA_TAG_NAME = "tsa";
	private const XML_SOAP_PT_REQUEST_META_EXTERNALID_TAG_NAME = "externalId";
	private const XML_SOAP_PT_REQUEST_META_SIGNMETHOD_TAG_NAME = "signMethod";
	private const XML_SOAP_PT_REQUEST_META_T2C_VALUE_ATTRIBUTE_NAME = "value";
	private const XML_SOAP_PT_REQUEST_META_T2C_UNIT_ATTRIBUTE_NAME = "unit";
	private const XML_SOAP_PT_REQUEST_META_T2S_VALUE_ATTRIBUTE_NAME = "value";
	private const XML_SOAP_PT_REQUEST_META_T2S_UNIT_ATTRIBUTE_NAME = "unit";
	private const XML_SOAP_PT_REQUEST_META_USERDEFINED_TAG_NAME = "userdefined";
	private const XML_SOAP_PT_REQUEST_META_DUPLICATEDKEYS_TAG_NAME = "duplicateKeyOff";
	private const XML_SOAP_PT_PROCESS_META_NS = "urn:logalty:schemas:process:meta:1.0";
	private const XML_SOAP_PT_PROCESS_META_GENERATOR_TAG_NAME = "generator";
	private const XML_SOAP_PT_PROCESS_META_CREATIONDATE_TAG_NAME = "creation-date";
	private const XML_SOAP_PT_PROCESS_META_LANGUAGE_TAG_NAME = "language";
	private const XML_SOAP_PT_PROCESS_META_PERSONALDATA_TAG_NAME = "personalData";
	private const XML_SOAP_PT_PROCESS_META_FIRSTNAME_TAG_NAME = "firstname";
	private const XML_SOAP_PT_PROCESS_META_MIDDLENAME_TAG_NAME = "middlename";
	private const XML_SOAP_PT_PROCESS_META_LASTNAME1_TAG_NAME = "lastname1";
	private const XML_SOAP_PT_PROCESS_META_LASTNAME2_TAG_NAME = "lastname2";
	private const XML_SOAP_PT_PROCESS_META_CONTACT_TAG_NAME = "contact";
	private const XML_SOAP_PT_PROCESS_META_UUID_TAG_NAME = "uuid";
	private const XML_SOAP_PT_PROCESS_META_PHONE_TAG_NAME = "phone";
	private const XML_SOAP_PT_PROCESS_META_MOBILE_TAG_NAME = "mobile";
	private const XML_SOAP_PT_PROCESS_META_SUBJECT_TAG_NAME = "subject";
	private const XML_SOAP_PT_PROCESS_META_BODY_TAG_NAME = "body";
	private const XML_SOAP_PT_PROCESS_META_URL_TAG_NAME = "url";
	private const XML_SOAP_PT_PROCESS_META_URLBACK_TAG_NAME = "url_back";
	private const XML_SOAP_PT_PROCESS_META_EMAIL_TAG_NAME = "email";
	private const XML_SOAP_PT_PROCESS_META_ADDITIONALEMAIL_TAG_NAME = "additional_email_info";
	private const XML_SOAP_PT_PROCESS_META_USERDEFINED_TAG_NAME = "userdefined";
	private const XML_SOAP_PT_PROCESS_META_LEGAL_TAG_NAME = "legalIdentity";
	private const XML_SOAP_PT_PROCESS_META_IDENTITYTYPE_TAG_NAME = "type";
	private const XML_SOAP_PT_PROCESS_META_IDENTITYID_TAG_NAME = "id";
	private const XML_SOAP_PT_PROCESS_META_EMAILTEMPLATE_TAG_NAME = "emailTemplate";
	private const XML_SOAP_PT_PROCESS_META_FORMSFLOW_TAG_NAME = "xforms_flow";
	private const XML_SOAP_PT_PROCESS_META_FORM_TAG_NAME = "ptforms:form";
	private const XML_SOAP_PT_PROCESS_META_FORM_ID_PREFIX = "form";
	private const XML_SOAP_PT_PROCESS_META_FORMELEMENTTEXT_TAG_NAME = "ptforms:text";
	private const XML_SOAP_PT_PROCESS_META_FORMELEMENTCHECKBOX_TAG_NAME = "ptforms:checkbox";
	private const XML_SOAP_PT_PROCESS_META_FORMELEMENTLABEL_TAG_NAME = "ptforms:label";
	private const XML_SOAP_PT_PROCESS_META_FORMELEMENTHINT_TAG_NAME = "ptforms:hint";
	private const XML_SOAP_PT_PROCESS_META_METAPROPERTIES_TAG_NAME = "metaProperties";
	private const XML_SOAP_PT_PROCESS_META_PROPERTY_TAG_NAME = "mp:property";
	private const XML_SOAP_PT_PROCESS_META_LOCALIZED_PROPERTIES_TAG_NAME = "mp:localizedProperties";
	private const XML_SOAP_PT_PROCESS_META_LOCALIZED_PROPERTY_TAG_NAME = "mp:localizedProperty";
	private const XML_SOAP_PT_PROCESS_META_LOCALIZEDPROPERTYLABEL_TAG_NAME = "label";
	private const XML_SOAP_PT_PROCESS_META_LOCALIZEDPROPERTYVALUE_TAG_NAME = "value";
	private const XML_SOAP_PT_PROCESS_META_PROPERTYLOCATIONS_TAG_NAME = "mp:locations";
	private const XML_SOAP_PT_PROCESS_META_PROPERTYLOCATION_TAG_NAME = "location";
	private const XML_SOAP_PT_PROCESS_META_NAME_ATTRIBUTE_NAME = "name";
	private const XML_SOAP_PT_PROCESS_META_NAME_ATTRIBUTE_VALUE = "user-id";
	private const XML_SOAP_PT_PROCESS_META_PHP_ATTRIBUTE_VALUE = "PHP";
	private const XML_SOAP_PT_PROCESS_META_NOTICEMETHOD_ATTRIBUTE_NAME = "notice_method";
	private const XML_SOAP_PT_PROCESS_META_FORMID_ATTRIBUTE_NAME = "id";
	private const XML_SOAP_PT_PROCESS_META_FORMLOCALE_ATTRIBUTE_NAME = "locale";
	private const XML_SOAP_PT_PROCESS_META_FORMPLACEMENT_ATTRIBUTE_NAME = "placement";
	private const XML_SOAP_PT_PROCESS_META_FORMSTEP_ATTRIBUTE_NAME = "step";
	private const XML_SOAP_PT_PROCESS_META_FORMELEMENTID_ATTRIBUTE_NAME = "id";
	private const XML_SOAP_PT_PROCESS_META_FORMELEMENTMANDATORY_ATTRIBUTE_NAME = "mandatory";
	private const XML_SOAP_PT_PROCESS_META_FORMELEMENTVALUE_ATTRIBUTE_NAME = "value";
	private const XML_SOAP_PT_PROCESS_META_PROPERTYID_ATTRIBUTE_NAME = "id";
	private const XML_SOAP_PT_PROCESS_META_LOCALIZEDPROPERTYLOCALE_ATTRIBUTE_NAME = "locale";
	private const XML_SOAP_PT_BINARYCONTENTRULE_TAG_NAME = "binarycontentrule";
	private const XML_SOAP_PT_BINARYCONTENTRULE_HIDECANCEL_ATTRIBUTE_NAME = "hide_cancel_button";
	private const XML_SOAP_PT_BINARYCONTENTITEM_TAG_NAME = "binarycontentitem";
	private const XML_SOAP_PT_BINARYCONTENTITEM_FILENAME_ATTRIBUTE_NAME = "file-name";
	private const XML_SOAP_PT_BINARYCONTENTITEM_TYPE_ATTRIBUTE_NAME = "type";

	private const XML_SOAP_PT_RESPONSE_MAIN_TAG_NAME = "main";
	private const XML_SOAP_PT_RESPONSE_GUID_TAG_NAME = "guid";
	private const XML_SAML_RESPONSE_NS = "urn:oasis:names:tc:SAML:2.0:protocol";
	private const XML_SAML_RESPONSE_TAG_NAME = "Response";
	private const XML_SAML_ASSERTION_TAG_NAME = "Assertion";
	private const XML_SAML_NAMEID_TAG_NAME = "NameID";
	private const XML_SAML_SUBJECT_CONFIRMATION_DATA_TAG_NAME = "SubjectConfirmationData";
	private const XML_SAML_AUTHN_STATEMENT_TAG_NAME = "AuthnStatement";
	private const XML_SAML_ATTRIBUTE_TAG_NAME = "Attribute";
	private const XML_SAML_AUTHN_INSTANT_ATTRIBUTE_NAME = "AuthnInstant";
	private const XML_SAML_ATTRIBUTE_ATTRIBUTE_NAME = "Name";
	private const XML_SAML_ATTRIBUTE_GUID_ATTRIBUTE_VALUE = "urn:logalty:schemas:core:1.0:attributes:document_guid";
	private const XML_SAML_ATTRIBUTE_VALUE_TAG_NAME = "AttributeValue";
	private const XML_SAML_ISSUE_INSTANT_ATTRIBUTE_NAME = "IssueInstant";
	private const XML_SAML_ID_ATTRIBUTE_NAME = "ID";
	private const XML_SAML_NOTONORAFTER_ATTRIBUTE_NAME = "NotOnOrAfter";

	private const XML_SOAP_STATE_REQUEST_GUID_TAG_NAME = "guid";
	private const XML_SOAP_STATE_RESPONSE_MAIN_TAG_NAME = "main";
	private const XML_SOAP_STATE_RESPONSE_STATE_TAG_NAME = "state";
	private const XML_SOAP_STATE_RESPONSE_SIGNATURE_TAG_NAME = "signature_state";
	private const XML_SOAP_STATE_RESPONSE_STATE_EXTERNALID_ATTRIBUTE_NAME = "externalId";
	private const XML_SOAP_STATE_RESPONSE_STATE_GUID_ATTRIBUTE_NAME = "guid";
	private const XML_SOAP_STATE_RESPONSE_STATE_SUBSTATE_ATTRIBUTE_NAME = "substate_value";
	private const XML_SOAP_STATE_RESPONSE_STATE_VALUE_ATTRIBUTE_NAME = "value";
	private const XML_SOAP_STATE_RESPONSE_SIGNATURE_SUBSTATE_ATTRIBUTE_NAME = "substate_value";
	private const XML_SOAP_STATE_RESPONSE_SIGNATURE_VALUE_ATTRIBUTE_NAME = "value";
	/* CONF */
	private const INCOMING_SERVICE_URL = 'https://www.demo.logalty.es/lgt/lgtbus/public/IncomingService';
	private const SAML_SERVICE_URL = 'https://www.demo.logalty.es/lgt/lgtfrontend/login/samlLogin';
	private const STATES_SERVICE_URL = 'https://www.demo.logalty.es/lgt/lgtbus/public/DataService';

	private const CERT_FILENAME = 'WIREDANDLINKED_demo.pfx';
	private const CERT_P12_FILENAME = 'WIREDANDLINKED_demo.p12';
	private const CERT_PASS = 'logalty';
	
	private const RESOURCES_FOLDER = 'res';
	private const PT_TEMPLATE_FILENAME = 'request_template.xml';
	private const SAML_TEMPLATE_FILENAME = 'saml_template.xml';
	private const STATE_TEMPLATE_FILENAME = 'state_request_template.xml';
	private const OUTPUT_TMP_DIR = '/tmp';
	/* END CONF*/

	private $incomingServiceUrl;
	private $samlServiceUrl;
	private $statesServiceUrl;
	private $resourcesPath;
	private $certPath;
	private $certPass;
	private $ptRequestTemplate;
	private $samlRequestTemplate;
	private $stateRequestTemplate;
	private $timezone;
	private $outputPath;
	private $transaction;

	function __construct($transaction)
	{
		$this->timezone = LogaltyClient::DEFAULT_TIMEZONE;
		$this->incomingServiceUrl = LogaltyClient::INCOMING_SERVICE_URL;
		$this->samlServiceUrl = LogaltyClient::SAML_SERVICE_URL;
		$this->statesServiceUrl = LogaltyClient::STATES_SERVICE_URL;
		$this->resourcesPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . LogaltyClient::RESOURCES_FOLDER;
		$this->certPath = $this->resourcesPath . DIRECTORY_SEPARATOR . LogaltyClient::CERT_FILENAME;
		$this->certPass = LogaltyClient::CERT_PASS;
		$this->ptRequestTemplate = $this->resourcesPath . DIRECTORY_SEPARATOR . LogaltyClient::PT_TEMPLATE_FILENAME;
		$this->samlRequestTemplate = $this->resourcesPath . DIRECTORY_SEPARATOR . LogaltyClient::SAML_TEMPLATE_FILENAME;
		$this->stateRequestTemplate = $this->resourcesPath . DIRECTORY_SEPARATOR . LogaltyClient::STATE_TEMPLATE_FILENAME;
		$this->outputPath = dirname(__FILE__) . LogaltyClient::OUTPUT_TMP_DIR;
		$this->transaction = $transaction;

		date_default_timezone_set($this->timezone);

		if ( !is_dir($this->resourcesPath) || !is_file($this->certPath) || !is_file($this->ptRequestTemplate) || !is_file($this->samlRequestTemplate) ) {
			throw new Exception("Configuration error", 1);
		}

		if ( !is_dir($this->outputPath) ) {
			mkdir($this->outputPath, 0700);
		}
	}

	public function setIncomingServiceUrl($incomingServiceUrl)
	{
		$this->incomingServiceUrl = $incomingServiceUrl;
	}
	public function setSamlServiceUrl($samlServiceUrl)
	{
		$this->samlServiceUrl = $samlServiceUrl;
	}
	public function setStatesServiceUrl($statesServiceUrl)
	{
		$this->statesServiceUrl = $statesServiceUrl;
	}
	public function setResourcesPath($path)
	{
		$this->resourcesPath = $path;

		if ( !is_dir($this->resourcesPath) ) {
			throw new Exception("Configuration error", 1);
		}
	}
	public function setOutputPath($path)
	{
		$this->outputPath = $path;

		if ( !is_dir($this->outputPath) ) {
			throw new Exception("Configuration error", 2);
		}
	}
	public function setCertPath($path)
	{
		$this->certPath = $path;

		if ( !is_file($this->certPath) ) {
			throw new Exception("Configuration error", 3);
		}
	}
	public function setCertPass($pass)
	{
		$this->certPass = $pass;
	}
	public function setRequestTemplatePath($template)
	{
		$this->ptRequestTemplate = $template;

		if ( !is_file($this->ptRequestTemplate) ) {
			throw new Exception("Configuration error", 4);
		}
	}
	public function setSamlTemplatePath($template)
	{
		$this->samlRequestTemplate = $template;

		if ( !is_file($this->samlRequestTemplate) ) {
			throw new Exception("Configuration error", 5);
		}
	}
	public function setStateTemplatePath($template)
	{
		$this->stateRequestTemplate = $template;

		if ( !is_file($this->stateRequestTemplate) ) {
			throw new Exception("Configuration error", 6);
		}
	}
	public function setTimezone($timezone)
	{
		$this->timezone = $timezone;
		date_default_timezone_set($this->timezone);
	}
	public function setTransaction($transaction)
	{
		$this->transaction = $transaction;
	}


	public function getState($guid = '')
	{
		if ( empty($guid) ) {
			$guid = $this->guid;
		}

		$request = $this->buildStatesRequestFile($guid); //Load request template and load user properties
		
		$output = $this->outputPath . DIRECTORY_SEPARATOR . 'signed_states_request_' . $guid . '.xml';
		$signedRequest = $this->signFile($request, $output);

		$signedSoap = $this->buildSoapRequest($signedRequest);

		$response = $this->post($this->statesServiceUrl, utf8_decode($signedSoap));

		$stateResponse = $this->buildStatesResponse($response);
		if ( !empty($stateResponse) ) {
			unlink($request);
			unlink($signedRequest);
			return $stateResponse;
		}
	}


	private function buildStatesRequestFile($guid)
	{
		$requestTemplate = new DOMDocument();
		$requestTemplate->preserveWhiteSpace = true;
		$requestTemplate->formatOutput = false;
		$requestTemplate->load($this->stateRequestTemplate);
		$requestTemplate->encoding = "UTF-8";

		$this->setNodeValue($requestTemplate, "*", LogaltyClient::XML_SOAP_STATE_REQUEST_GUID_TAG_NAME, $guid);

		$filledFilePath = $this->outputPath . DIRECTORY_SEPARATOR . 'filled_' . $this->transaction->getExternalId() . '_' . LogaltyClient::STATE_TEMPLATE_FILENAME;
			// $requestTemplate->save($filledFilePath);
		// file_put_contents($filledFilePath, $requestTemplate->saveXml($requestTemplate->documentElement));
		$this->saveDoc($filledFilePath, $requestTemplate->saveXml($requestTemplate->documentElement));

		return $filledFilePath;
	}


	public function getUserSyncAcceptanceAccess()
	{
		$this->createTransaction();
		return $this->getTransactionAccess();
	}


	public function createTransaction()
	{
		$request = $this->buildRequestFile(); //Load request template and load user properties
		
		$output = $this->outputPath . DIRECTORY_SEPARATOR . 'signed_request_' . $this->transaction->getExternalId() . '.xml';
		$signedRequest = $this->signFile($request, $output);

		$signedSoap = $this->buildSoapRequest($signedRequest);

		$response = $this->post($this->incomingServiceUrl, utf8_decode($signedSoap));
		
		if ( empty($response) ) {
			throw new Exception("Transaction error: Empty response from incoming service", 2);
		}

		if ( is_array($response) ) {
			throw new Exception("Transaction error: Unexpected response from incoming service", 3);
			// dd($response);
		}

		$transactionResult = $this->buildTransactionResult($response);
		if ( !empty($transactionResult) ) {
			unlink($request);
			// unlink($signedRequest);
			return $transactionResult;
		}
	}

	public function getTransactionAccess($guid = "")
	{
		if ( empty($guid) ) {
			$guid = $this->guid;
		}
		
		$request = $this->buildSamlFile(); //Load saml template and load user properties
		$output = $this->outputPath . DIRECTORY_SEPARATOR . 'signed_saml_' . $guid . '.xml';
		$signedRequest = $this->signFile($request, $output);
		$postData = array(
			"saml_assertion" => urlencode(file_get_contents($signedRequest))
		);
		$response = $this->post($this->samlServiceUrl, $postData);

		$transactionAccess = $this->buildTransactionAccessResponse($guid, $response);
		if ( !empty($transactionAccess) ) {
			unlink($request);
			// unlink($signedRequest);
			return $transactionAccess;
		}
	}


	private function buildRequestFile()
	{
		
		$requestTemplate = new DOMDocument();
		$requestTemplate->preserveWhiteSpace = true;
		$requestTemplate->formatOutput = false;
		$requestTemplate->load($this->ptRequestTemplate);
		$requestTemplate->encoding = "UTF-8";

		$this->setRequestMeta($requestTemplate);
		$this->setProcessMeta($requestTemplate);
		$this->setFileData($requestTemplate);
		
		$filledFilePath = $this->outputPath . DIRECTORY_SEPARATOR . 'filled_' . $this->transaction->getExternalId() . '_' . LogaltyClient::PT_TEMPLATE_FILENAME;
			// $requestTemplate->save($filledFilePath);
		// file_put_contents($filledFilePath, $requestTemplate->saveXml($requestTemplate->documentElement));
		$this->saveDoc($filledFilePath, $requestTemplate->saveXml());

		return $filledFilePath;
	}

	private function setRequestMeta($doc)
	{
		//set service
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_SERVICE_TAG_NAME, $this->transaction->getService());
		//set time2close
		$this->setAttributeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_T2C_TAG_NAME, LogaltyClient::XML_SOAP_PT_REQUEST_META_T2C_VALUE_ATTRIBUTE_NAME, $this->transaction->getTime2Close());
		$this->setAttributeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_T2C_TAG_NAME, LogaltyClient::XML_SOAP_PT_REQUEST_META_T2C_UNIT_ATTRIBUTE_NAME, $this->transaction->getTime2CloseUnit());
		//set time2save
		$this->setAttributeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_T2S_TAG_NAME, LogaltyClient::XML_SOAP_PT_REQUEST_META_T2S_VALUE_ATTRIBUTE_NAME, $this->transaction->getTime2Save());
		$this->setAttributeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_T2S_TAG_NAME, LogaltyClient::XML_SOAP_PT_REQUEST_META_T2S_UNIT_ATTRIBUTE_NAME, $this->transaction->getTime2SaveUnit());
		//set lopd
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_LOPD_TAG_NAME, $this->transaction->getLopdLevel());
		//set sign level
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_SIGNLEVEL_TAG_NAME, $this->transaction->getSignLevel());
		//set retryprotocol
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_RETRY_PROTOCOL_TAG_NAME, $this->transaction->getRetryProtocol());
		//set sync
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_SYNC_TAG_NAME, $this->transaction->getSynchronous());
		//set tsa
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_TSA_TAG_NAME, $this->transaction->getTsa());
		//set external id
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_EXTERNALID_TAG_NAME, $this->transaction->getExternalId());
		//set duplicated keys
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_DUPLICATEDKEYS_TAG_NAME, $this->transaction->getAllowDuplicatedKeys());
		//set PHP version
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_USERDEFINED_TAG_NAME, phpversion(), LogaltyClient::XML_SOAP_PT_PROCESS_META_NAME_ATTRIBUTE_NAME, LogaltyClient::XML_SOAP_PT_PROCESS_META_PHP_ATTRIBUTE_VALUE);
		//set sign method
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_REQUEST_META_NS, LogaltyClient::XML_SOAP_PT_REQUEST_META_SIGNMETHOD_TAG_NAME, $this->transaction->getSignMethod());
	}

	private function setProcessMeta($doc)
	{
		//set generator
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_GENERATOR_TAG_NAME, $this->transaction->getGenerator());

		//set creation date
		$date = date("c");
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_CREATIONDATE_TAG_NAME, $date);

		//set main language
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_LANGUAGE_TAG_NAME, $this->transaction->getLanguage());

		$this->setReceiverData($doc);

		//set subject
		if ( !empty($this->transaction->getSubject()) ) {
			$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_SUBJECT_TAG_NAME, $this->transaction->getSubject());
		}
		
		//set body
		if ( !empty($this->transaction->getBody()) ) {
			$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_BODY_TAG_NAME, $this->transaction->getBody());
		}

		//set url
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_URL_TAG_NAME, $this->transaction->getUrl());
		//set url back
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_URLBACK_TAG_NAME, $this->transaction->getBackUrl());
		//set email
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_EMAIL_TAG_NAME, $this->transaction->getSupportEmail());
		//set additional_email
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_ADDITIONALEMAIL_TAG_NAME, $this->transaction->getEmailInfo());

		//set userdefined
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_USERDEFINED_TAG_NAME, $this->transaction->getLegalIdentityId(), LogaltyClient::XML_SOAP_PT_PROCESS_META_NAME_ATTRIBUTE_NAME, LogaltyClient::XML_SOAP_PT_PROCESS_META_NAME_ATTRIBUTE_VALUE);

		//set email template
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_NS, LogaltyClient::XML_SOAP_PT_PROCESS_META_EMAILTEMPLATE_TAG_NAME, $this->transaction->getTemplate());

		$this->setFormsFlow($doc);
		$this->setMetaProperties($doc);
	}

	private function setReceiverData($doc)
	{
		if ( !empty($this->transaction->getFirstName()) ) {
			$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_PERSONALDATA_TAG_NAME, $this->transaction->getFirstName(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_FIRSTNAME_TAG_NAME);
		}
		if ( !empty($this->transaction->getMiddleName()) ) {
			$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_PERSONALDATA_TAG_NAME, $this->transaction->getMiddleName(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_MIDDLENAME_TAG_NAME);
		}
		if ( !empty($this->transaction->getLastName1()) ) {
			$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_PERSONALDATA_TAG_NAME, $this->transaction->getLastName1(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_LASTNAME1_TAG_NAME);
		}
		if ( !empty($this->transaction->getLastName2()) ) {
			$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_PERSONALDATA_TAG_NAME, $this->transaction->getLastName2(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_LASTNAME2_TAG_NAME);
		}
		if ( !empty($this->transaction->getNoticeMethod()) ) {
			$this->setAttributeValue($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_CONTACT_TAG_NAME, LogaltyClient::XML_SOAP_PT_PROCESS_META_NOTICEMETHOD_ATTRIBUTE_NAME, $this->transaction->getNoticeMethod());
		}
		
		$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_CONTACT_TAG_NAME, $this->getUUID(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_UUID_TAG_NAME);
		
		if ( !empty($this->transaction->getPhone()) ) {
			$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_CONTACT_TAG_NAME, $this->transaction->getPhone(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_PHONE_TAG_NAME);
		}
		if ( !empty($this->transaction->getMobile()) ) {
			$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_CONTACT_TAG_NAME, $this->transaction->getMobile(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_MOBILE_TAG_NAME);
		}
		if ( !empty($this->transaction->getEmail()) ) {
			$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_CONTACT_TAG_NAME, $this->transaction->getEmail(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_EMAIL_TAG_NAME);
		}
		if ( !empty($this->transaction->getLegalIdentityType()) ) {
			$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_LEGAL_TAG_NAME, $this->transaction->getLegalIdentityType(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_IDENTITYTYPE_TAG_NAME);
		}
		if ( !empty($this->transaction->getLegalIdentityId()) ) {
			$this->setNodeValue($doc, "*", LogaltyClient::XML_SOAP_PT_PROCESS_META_LEGAL_TAG_NAME, $this->transaction->getLegalIdentityId(), "", "", LogaltyClient::XML_SOAP_PT_PROCESS_META_IDENTITYID_TAG_NAME);
		}
		if ( !empty($this->transaction->getHideCancelButton()) ) {
			$this->setAttributeValue($doc, LogaltyClient::XML_SOAP_PT_BINARYCONTENTRULE_TAG_NAME, LogaltyClient::XML_SOAP_PT_BINARYCONTENTRULE_HIDECANCEL_ATTRIBUTE_NAME, $this->transaction->getHideCancelButton());
		}
	}

	private function setFormsFlow($doc) {

		$formsFlow = $this->getUniqueNodeByTag($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMSFLOW_TAG_NAME);
		foreach ($this->transaction->getForms() as $form) {
			$fEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORM_TAG_NAME);
			$fEl->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMID_ATTRIBUTE_NAME, LogaltyClient::XML_SOAP_PT_PROCESS_META_FORM_ID_PREFIX . $form->getId());
			$fEl->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMLOCALE_ATTRIBUTE_NAME, $form->getLocale());
			$fEl->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMPLACEMENT_ATTRIBUTE_NAME, $form->getPlacement());
			$fEl->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMSTEP_ATTRIBUTE_NAME, $form->getStep());
			$formsFlow->appendChild($fEl);

			$this->setFormElements($doc, $fEl, $form->getElements());
		}
	}

	private function setFormElements($doc, $form, $elements)
	{
		foreach ($elements as $element) {
			if ( $element->getType() === FormElementDTO::FORM_ELEMENT_TYPE_CHECKBOX ) {
				$tag = LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMELEMENTCHECKBOX_TAG_NAME;
			} else if ( $element->getType() === FormElementDTO::FORM_ELEMENT_TYPE_TEXT ) {
				$tag = LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMELEMENTTEXT_TAG_NAME;
			}
			
			$e = $doc->createElement($tag);
			$e->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMELEMENTID_ATTRIBUTE_NAME, $element->getId());
			if ( $element->getType() !== FormElementDTO::FORM_ELEMENT_TYPE_TEXT ) {
				$e->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMELEMENTMANDATORY_ATTRIBUTE_NAME, $element->getMandatory());
				$e->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMELEMENTVALUE_ATTRIBUTE_NAME, $element->getValue());
			}
			$form->appendChild($e);

			$labelEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMELEMENTLABEL_TAG_NAME);
			$labelEl->appendChild(new DOMText($this->convert($element->getLabel())));
			$e->appendChild($labelEl);

			if ( !empty($element->getHint()) ) {
				$hintEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_FORMELEMENTHINT_TAG_NAME);
				$hintEl->appendChild(new DOMText($this->convert($element->getHint())));
				$e->appendChild($hintEl);
			}
		}
	}

	private function setMetaProperties($doc)
	{
		$metaProp = $this->getUniqueNodeByTag($doc, LogaltyClient::XML_SOAP_PT_PROCESS_META_METAPROPERTIES_TAG_NAME);
		foreach ($this->transaction->getRejectList() as $prop) {
			$pEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_PROPERTY_TAG_NAME);
			$pEl->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_PROPERTYID_ATTRIBUTE_NAME, $prop->getId());
			$localizedPsEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZED_PROPERTIES_TAG_NAME);
			$localizedPEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZED_PROPERTY_TAG_NAME);
			$localizedPEl->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZEDPROPERTYLOCALE_ATTRIBUTE_NAME, $prop->getLocale());
			$lpLabelEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZEDPROPERTYLABEL_TAG_NAME);
			$lpLabelEl->appendChild(new DOMText($this->convert($prop->getLabel())));
			$lpValueEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZEDPROPERTYVALUE_TAG_NAME);
			$lpValueEl->appendChild(new DOMText($prop->getValue()));
			$localizedPEl->appendChild($lpLabelEl);
			$localizedPEl->appendChild($lpValueEl);
			$localizedPsEl->appendChild($localizedPEl);
			$pEl->appendChild($localizedPsEl);

			$locationsEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_PROPERTYLOCATIONS_TAG_NAME);
			$locationEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_PROPERTYLOCATION_TAG_NAME);
			$locationEl->appendChild(new DOMText($prop->getLocation()));
			$locationsEl->appendChild($locationEl);
			$pEl->appendChild($locationsEl);
			
			$metaProp->appendChild($pEl);
		}

		if ( !empty($this->transaction->getMailUrls()) ) {
			$pEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_PROPERTY_TAG_NAME);
			$pEl->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_PROPERTYID_ATTRIBUTE_NAME, PropertyDTO::PROPERTY_ID_URL);
			$localizedPsEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZED_PROPERTIES_TAG_NAME);

			foreach ($this->transaction->getMailUrls() as $localizedP) {
				$localizedPEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZED_PROPERTY_TAG_NAME);
				$localizedPEl->setAttribute(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZEDPROPERTYLOCALE_ATTRIBUTE_NAME, $localizedP->getLocale());
				$lpLabelEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZEDPROPERTYLABEL_TAG_NAME);
				$lpLabelEl->appendChild(new DOMText($localizedP->getLabel()));
				$lpValueEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_LOCALIZEDPROPERTYVALUE_TAG_NAME);
				$lpValueEl->appendChild(new DOMText($localizedP->getValue()));
				$localizedPEl->appendChild($lpLabelEl);
				$localizedPEl->appendChild($lpValueEl);
				$localizedPsEl->appendChild($localizedPEl);
			}
			
			$pEl->appendChild($localizedPsEl);

			$locationsEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_PROPERTYLOCATIONS_TAG_NAME);
			$locationEl = $doc->createElement(LogaltyClient::XML_SOAP_PT_PROCESS_META_PROPERTYLOCATION_TAG_NAME);
			$locationEl->appendChild(new DOMText(PropertyDTO::PROPERTY_LOCATION_EMAIL));
			$locationsEl->appendChild($locationEl);
			$pEl->appendChild($locationsEl);
			
			$metaProp->appendChild($pEl);
		}
	}

	private function setFileData($doc)
	{
		$this->setNodeValue($doc, LogaltyClient::XML_SOAP_PT_NS, LogaltyClient::XML_SOAP_PT_BINARYCONTENTITEM_TAG_NAME, $this->transaction->getDocument());
		$this->setAttributeValue($doc, LogaltyClient::XML_SOAP_PT_BINARYCONTENTITEM_TAG_NAME, LogaltyClient::XML_SOAP_PT_BINARYCONTENTITEM_FILENAME_ATTRIBUTE_NAME, $this->transaction->getFilename());
		$this->setAttributeValue($doc, LogaltyClient::XML_SOAP_PT_BINARYCONTENTITEM_TAG_NAME, LogaltyClient::XML_SOAP_PT_BINARYCONTENTITEM_TYPE_ATTRIBUTE_NAME, $this->transaction->getFileMimeType());
	}


	private function signFile($src, $output) 
	{
		$signCommand = 'xmlsec1 --sign --output ' . $output . ' --pkcs12 ' . $this->certPath . ' --pwd ' . $this->certPass . ' ' . $src;
		
		$r = shell_exec( $signCommand );

		return $output;
	}


	private function buildSamlFile()
	{
		$samlTemplate = new DOMDocument();
		$samlTemplate->preserveWhiteSpace = true;
		$samlTemplate->formatOutput = false;
		$samlTemplate->load($this->samlRequestTemplate);
		$samlTemplate->encoding = "UTF-8";
		

		//set creation date
		$timestamp = time();
		$date = date("c", $timestamp);
		$this->setAttributeValue($samlTemplate, LogaltyClient::XML_SAML_RESPONSE_TAG_NAME, LogaltyClient::XML_SAML_ISSUE_INSTANT_ATTRIBUTE_NAME, $date);
		$this->setAttributeValue($samlTemplate, LogaltyClient::XML_SAML_ASSERTION_TAG_NAME, LogaltyClient::XML_SAML_ISSUE_INSTANT_ATTRIBUTE_NAME, $date);
		$this->setAttributeValue($samlTemplate, LogaltyClient::XML_SAML_AUTHN_STATEMENT_TAG_NAME, LogaltyClient::XML_SAML_AUTHN_INSTANT_ATTRIBUTE_NAME, $date);
		//set ID
		$this->setAttributeValue($samlTemplate, LogaltyClient::XML_SAML_RESPONSE_TAG_NAME, LogaltyClient::XML_SAML_ID_ATTRIBUTE_NAME, md5($this->transaction->getExternalId()));
		$this->setAttributeValue($samlTemplate, LogaltyClient::XML_SAML_ASSERTION_TAG_NAME, LogaltyClient::XML_SAML_ID_ATTRIBUTE_NAME, md5($this->transaction->getExternalId()));
		//set expirationTime
		$expDate = new DateTime();
		$expDate->setTimestamp($timestamp);
		$expDate->add(new DateInterval('PT30M'));
		$this->setAttributeValue($samlTemplate, LogaltyClient::XML_SAML_SUBJECT_CONFIRMATION_DATA_TAG_NAME, LogaltyClient::XML_SAML_NOTONORAFTER_ATTRIBUTE_NAME, $expDate->format("c"));
		//set uuid
		$this->setNodeValue($samlTemplate, "*", LogaltyClient::XML_SAML_NAMEID_TAG_NAME, $this->getUUID());
		//set guid
		$this->setNodeValue($samlTemplate, "*", LogaltyClient::XML_SAML_ATTRIBUTE_TAG_NAME, $this->guid, LogaltyClient::XML_SAML_ATTRIBUTE_ATTRIBUTE_NAME, LogaltyClient::XML_SAML_ATTRIBUTE_GUID_ATTRIBUTE_VALUE, LogaltyClient::XML_SAML_ATTRIBUTE_VALUE_TAG_NAME);
		
		$filledFilePath = $this->outputPath . DIRECTORY_SEPARATOR . 'saml_filled_' . $this->guid . '_' . LogaltyClient::SAML_TEMPLATE_FILENAME;

		// file_put_contents($filledFilePath, $samlTemplate->saveXml($samlTemplate->documentElement));
		// $this->saveDoc($filledFilePath, $samlTemplate->saveXml($samlTemplate->documentElement));
		$this->saveDoc($filledFilePath, $samlTemplate->saveXml());

		return $filledFilePath;
	}


	private function buildSoapRequest($signedRequest)
	{

		$xml = new DOMDocument();
		$xml->preserveWhiteSpace = true;
		$xml->formatOutput = false;

		if ( $this->is_file($signedRequest) ){
			$xml->load($signedRequest);
		} else {
			$xml->loadXml($signedRequest);
		}
		$xml->encoding = "UTF-8";

		$signedRequest = $xml->saveXml($xml->documentElement);

		$header = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body>';
		$footer = '</soapenv:Body></soapenv:Envelope>';
		return $header . $signedRequest . $footer;
	}

	private function buildTransactionResult($response)
	{
		$result = new DOMDocument();
		$result->loadXml($response);
		$result->encoding = "UTF-8";

		$resultCode = $this->getUniqueNodeByTag($result, LogaltyClient::XML_SOAP_PT_RESPONSE_MAIN_TAG_NAME)->textContent;

		if ( $resultCode === "0000" ) {
			$guidNode = $this->getUniqueNodeByTag($result, LogaltyClient::XML_SOAP_PT_RESPONSE_GUID_TAG_NAME);
			$this->guid = $guidNode->textContent;
			return $this->guid;
		} else {
			throw new Exception("Transaction error: " . $this->getUniqueNodeByTag($result, "reason")->textContent . " - " . $this->getUniqueNodeByTag($result, "additionalInfo")->textContent, 1);
		}
	}

	private function buildTransactionAccessResponse($guid, $response)
	{
		if ( !empty($response) ) {
			if ( $response["http_code"] >= 200 && $response["http_code"] < 400 ) {
				return new TransactionResponseDTO($guid, $response["redirect_url"]);
			} else {
				throw new Exception("Transaction error: SAML authentication error" , 3);
			}
		} else {
			throw new Exception("Transaction error: Empty response from SAML service", 4);
		}
	}

	private function buildStatesResponse($response)
	{
		$result = new DOMDocument();
		$result->loadXml($response);
		$result->encoding = "UTF-8";

		$resultCode = $this->getUniqueNodeByTag($result, LogaltyClient::XML_SOAP_STATE_RESPONSE_MAIN_TAG_NAME)->textContent;

		if ( $resultCode === "0000" ) {
			$state = new StateResultDTO();
			$state->setExternalId(
				$this->getUniqueNodeByTag($result, LogaltyClient::XML_SOAP_STATE_RESPONSE_STATE_TAG_NAME)->getAttribute(LogaltyClient::XML_SOAP_STATE_RESPONSE_STATE_EXTERNALID_ATTRIBUTE_NAME)
			);
			$state->setGuid(
				$this->getUniqueNodeByTag($result, LogaltyClient::XML_SOAP_STATE_RESPONSE_STATE_TAG_NAME)->getAttribute(LogaltyClient::XML_SOAP_STATE_RESPONSE_STATE_GUID_ATTRIBUTE_NAME)
			);
			$state->setTransactionState(
				$this->getUniqueNodeByTag($result, LogaltyClient::XML_SOAP_STATE_RESPONSE_STATE_TAG_NAME)->getAttribute(LogaltyClient::XML_SOAP_STATE_RESPONSE_STATE_SUBSTATE_ATTRIBUTE_NAME)
			);
			$state->setTransactionResult(
				$this->getUniqueNodeByTag($result, LogaltyClient::XML_SOAP_STATE_RESPONSE_STATE_TAG_NAME)->getAttribute(LogaltyClient::XML_SOAP_STATE_RESPONSE_STATE_VALUE_ATTRIBUTE_NAME)
			);
			$state->setSignatureState(
				$this->getUniqueNodeByTag($result, LogaltyClient::XML_SOAP_STATE_RESPONSE_SIGNATURE_TAG_NAME)->getAttribute(LogaltyClient::XML_SOAP_STATE_RESPONSE_SIGNATURE_SUBSTATE_ATTRIBUTE_NAME)
			);
			$state->setSignatureResult(
				$this->getUniqueNodeByTag($result, LogaltyClient::XML_SOAP_STATE_RESPONSE_SIGNATURE_TAG_NAME)->getAttribute(LogaltyClient::XML_SOAP_STATE_RESPONSE_SIGNATURE_VALUE_ATTRIBUTE_NAME)
			);

			return $state;
			
		} else {
			throw new Exception("States error: " . $this->getUniqueNodeByTag($result, "reason")->textContent, 1);
		}
	}

	private function post($host, $data)
	{

		$REQUEST='POST';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $host);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $REQUEST);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		$output = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);

		if ( !empty($output) ) {
			return $output;	
		}
		return $info;
	}

	private function loadCertificate()
	{
		$cert = file_get_contents($this->certPath);
		openssl_pkcs12_read($cert, $certs, $this->certPass);
		return $certs;
	}


	private function setNodeValue($doc, $ns, $tagName, $value, $attrName = "", $attrValue = "", $innerTagName = "")
	{

		$nodes = $doc->getElementsByTagNameNS($ns, $tagName);
		foreach ($nodes as $node) {
			if ( !empty($attrName) ) {
				if ( $node->getAttribute($attrName) == $attrValue ) {
					if ( !empty($innerTagName) ) {
						$n = $this->getUniqueNodeByTag($node, $innerTagName);
						if ( !empty($n->firstChild) ) {
							$n->removeChild($n->firstChild);
						}
						$n->appendChild(new DOMText($value));
					} else {
						if ( !empty($node->firstChild) ) {
							$node->removeChild($node->firstChild);
						}
						$node->appendChild(new DOMText($value));				
					}
				}	
			} else {
				if ( !empty($innerTagName) ) {
					$n = $this->getUniqueNodeByTag($node, $innerTagName);
					if ( !empty($n->firstChild) ) {
						$n->removeChild($n->firstChild);
					}
					$n->appendChild(new DOMText($value));
				} else {
					if ( !empty($node->firstChild) ) {
						$node->removeChild($node->firstChild);
					}
					$node->appendChild(new DOMText($value));				
				}
			}	
		}
	}


	private function setAttributeValue($doc, $tagName, $attrName, $attrValue)
	{
		$responseNode = $this->getUniqueNodeByTag($doc, $tagName);
		$responseNode->setAttribute($attrName, $attrValue);
	}


	private function getUniqueNodeByTag($doc, $tagName)
	{
		$nodes = $doc->getElementsByTagName($tagName);
		if ( count($nodes) == 1 ){
			return $nodes->item(0);
		} else if ( count($nodes) > 1 ) {
			throw new Exception("Wrong document. Duplicated node", 1);	
		}
		throw new Exception("Wrong document. Node does not exist", 2);
	}


	private function is_file($f)
	{

		if ( strlen($f) < 255 ) {
			return is_file($f);
		}

		return false;
	}


	private function convert($str)
	{
		// $unwanted_array = array(
		// 	'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
		// 	'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
		// 	'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
		// 	'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
		// 	'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
		// return strtr( $str, $unwanted_array );
		return $str;
	}


	private function getUUID()
	{
		return md5($this->transaction->getFirstName() . ";" . $this->transaction->getLastName1() . ";" . $this->transaction->getEmail() . ";");
	}

	private function saveDoc($path, $data)
	{
		// $encodedData = utf8_encode($data);
		// $encodedData =  iconv(mb_detect_encoding($data, mb_detect_order(), true), "UTF-8", $data);
		file_put_contents($path, $data);
	}
}
?>