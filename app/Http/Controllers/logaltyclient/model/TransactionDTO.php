<?php

namespace WL\Http\LogaltyClient;

/**
 * 
 */
class TransactionDTO
{
	private $service;
	private $time2Close;
	private $time2CloseUnit;
	private $time2Save;
	private $time2SaveUnit;
	private $lopdLevel;
	private $signLevel;
	private $retryProtocol;
	private $synchronous;
	private $tsa;
	private $signMethod;
	private $hideCancelButton;
	private $generator;
	private $subject;
	private $body;
	private $supportEmail;
	private $emailInfo;
	private $url;
	private $backUrl;
	private $template;
	private $noticeMethod;
	private $legalIdentityType;
	private $legalIdentityId;
	private $allowDuplicatedKeys;
	private $externalId;
	private $document;
	private $filename;
	private $fileMimeType;
	private $firstName;
	private $middleName;
	private $lastName1;
	private $lastName2;
	private $phone;
	private $mobile;
	private $email;
	private $language;
	private $forms;
	private $rejectList;
	private $mailUrls;

	function __construct()
	{
		$this->service = "PT0005_ACCEPTANCEXPRESS";
		$this->time2Close = "10";
		$this->time2CloseUnit = "d";
		$this->time2Save = "1825";
		$this->time2SaveUnit = "d";
		$this->lopdLevel = "3";
		$this->signLevel = "0";
		$this->retryProtocol = "3";
		$this->synchronous = true;
		$this->tsa = "1";
		$this->signMethod = "SMS";
		$this->hideCancelButton = true;
		$this->generator = "";
		$this->subject = "";
		$this->body = "";
		$this->supportEmail = "";
		$this->emailInfo = "CERT";
		$this->url = "LOGALTY_DIRECT_ACCESS_DOC_IN_FRAME";
		$this->backUrl = "";
		$this->template = "";
		$this->noticeMethod = "EMAIL";
		$this->legalIdentityType = "";
		$this->legalIdentityId = "";
		$this->allowDuplicatedKeys = false;
		$this->forms = array();
		$this->rejectList = array();
		$this->mailUrls = array();
	}

	public function getService()
	{
		return $this->service;
	}

	public function setService($service)
	{
		$this->service = $service;
	}

	public function getTime2Close()
	{
		return $this->time2Close;
	}

	public function setTime2Close($t)
	{
		$this->time2Close = $t;
	}

	public function getTime2CloseUnit()
	{
		return $this->time2CloseUnit;
	}

	public function setTime2CloseUnit($u)
	{
		$this->time2CloseUnit = $u;
	}

	public function getTime2Save()
	{
		return $this->time2Save;
	}

	public function setTime2Save($t)
	{
		$this->time2Save = $t;
	}

	public function getTime2SaveUnit()
	{
		return $this->time2SaveUnit;
	}

	public function setTime2SaveUnit($u)
	{
		$this->time2SaveUnit = $u;
	}

	public function getLopdLevel()
	{
		return $this->lopdLevel;
	}

	public function setLopdLevel($l)
	{
		$this->lopdLevel = $l;
	}

	public function getSignLevel()
	{
		return $this->signLevel;
	}

	public function setSignLevel($l)
	{
		$this->signLevel = $l;
	}

	public function getRetryProtocol()
	{
		return $this->retryProtocol;
	}

	public function setRetryProtocol($p)
	{
		$this->retryProtocol = $p;
	}

	public function getSynchronous()
	{
		return $this->synchronous;
	}

	public function setSynchronous($s)
	{
		$this->synchronous = $s;
	}

	public function getTsa()
	{
		return $this->tsa;
	}

	public function setTsa($t)
	{
		$this->tsa = $t;
	}

	public function getSignMethod()
	{
		return $this->signMethod;
	}

	public function setSignMethod($m)
	{
		$this->signMethod = $m;
	}

	public function getHideCancelButton()
	{
		return $this->hideCancelButton;
	}

	public function setHideCancelButton($h)
	{
		$this->hideCancelButton = $h;
	}

	public function getGenerator()
	{
		return $this->generator;
	}

	public function setGenerator($g)
	{
		$this->generator = $g;
	}

	public function getSubject()
	{
		return $this->subject;
	}

	public function setSubject($s)
	{
		$this->subject = $s;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function setBody($b)
	{
		$this->body = $b;
	}

	public function getSupportEmail()
	{
		return $this->supportEmail;
	}

	public function setSupportEmail($email)
	{
		$this->supportEmail = $email;
	}

	public function getEmailInfo()
	{
		return $this->emailInfo;
	}

	public function setEmailInfo($info)
	{
		$this->emailInfo = $info;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getBackUrl()
	{
		return $this->backUrl;
	}

	public function setBackUrl($url)
	{
		$this->backUrl = $url;
	}

	public function getTemplate()
	{
		return $this->template;
	}

	public function setTemplate($t)
	{
		$this->template = $t;
	}

	public function getNoticeMethod()
	{
		return $this->noticeMethod;
	}

	public function setNoticeMethod($m)
	{
		$this->noticeMethod = $m;
	}

	public function getLegalIdentityType()
	{
		return $this->legalIdentityType;
	}

	public function setLegalIdentityType($type)
	{
		$this->legalIdentityType = $type;
	}

	public function getLegalIdentityId()
	{
		return $this->legalIdentityId;
	}

	public function setLegalIdentityId($id)
	{
		$this->legalIdentityId = $id;
	}

	public function getExternalId()
	{
		return $this->externalId;
	}

	public function setExternalId($id)
	{
		$this->externalId = $id;
	}
	
	public function getDocument()
	{
		return $this->document;
	}

	public function setDocument($doc)
	{
		$this->document = $doc;
	}
	
	public function getFilename()
	{
		return $this->filename;
	}

	public function setFilename($f)
	{
		$this->filename = $f;
	}
	
	public function getFileMimeType()
	{
		return $this->fileMimeType;
	}

	public function setFileMimeType($mimeType)
	{
		$this->fileMimeType = $mimeType;
	}
	
	public function getFirstName()
	{
		return $this->firstName;
	}

	public function setFirstName($name)
	{
		$this->firstName = $name;
	}
	
	public function getMiddleName()
	{
		return $this->middleName;
	}

	public function setMiddleName($middlename)
	{
		$this->middleName = $middlename;
	}
	
	public function getLastName1()
	{
		return $this->lastName1;
	}

	public function setLastName1($lastname)
	{
		$this->lastName1 = $lastname;
	}
	
	public function getLastName2()
	{
		return $this->lastName2;
	}

	public function setLastName2($lastname)
	{
		$this->lastName2 = $lastname;
	}
	
	public function getPhone()
	{
		return $this->phone;
	}

	public function setPhone($phone)
	{
		$this->phone = $phone;
	}

	public function getMobile()
	{
		return $this->mobile;
	}

	public function setMobile($phone)
	{
		$this->mobile = $phone;
	}
	
	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function getLanguage()
	{
		return $this->language;
	}

	public function setLanguage($lang)
	{
		$this->language = $lang;
	}

	public function getAllowDuplicatedKeys()
	{
		return $this->allowDuplicatedKeys;
	}

	public function setAllowDuplicatedKeys($b)
	{
		$this->allowDuplicatedKeys = $b;
	}

	public function getForms()
	{
		return $this->forms;
	}

	public function setForms($f)
	{
		$this->forms = $f;
	}

	public function getRejectList()
	{
		return $this->rejectList;
	}

	public function setRejectList($r)
	{
		$this->rejectList = $r;
	}

	public function getMailUrls()
	{
		return $this->mailUrls;
	}

	public function setMailUrls($u)
	{
		$this->mailUrls = $u;
	}
}
?>