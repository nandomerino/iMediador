<?php

namespace WL\Http\LogaltyClient;

/**
 * 
 */
class StateResultDTO
{
	private $externalId;
	private $guid;
	private $transactionState;
	private $transactionResult;
	private $signatureState;
	private $signatureResult;

	function __construct()
	{
	}

	public function getExternalId()
	{
		return $this->externalId;
	}

	public function setExternalId($e)
	{
		$this->externalId = $e;
	}

	public function getGuid()
	{
		return $this->guid;
	}

	public function setGuid($guid)
	{
		$this->guid = $guid;
	}

	public function getTransactionState()
	{
		return $this->transactionState;
	}

	public function setTransactionState($s)
	{
		$this->transactionState = $s;
	}

	public function getTransactionResult()
	{
		return $this->transactionResult;
	}

	public function setTransactionResult($r)
	{
		$this->transactionResult = $r;
	}

	public function getSignatureState()
	{
		return $this->signatureState;
	}

	public function setSignatureState($s)
	{
		$this->signatureState = $s;
	}

	public function getSignatureResult()
	{
		return $this->signatureResult;
	}

	public function setSignatureResult($r)
	{
		$this->signatureResult = $r;
	}
}

?>