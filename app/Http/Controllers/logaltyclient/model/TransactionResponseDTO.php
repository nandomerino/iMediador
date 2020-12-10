<?php

namespace WL\Http\LogaltyClient;

/**
 * 
 */
class TransactionResponseDTO
{
	private $guid;
	private $url;

	function __construct($guid, $url)
	{
		$this->guid = $guid;
		$this->url = $url;
	}

	public function getGuid()
	{
		return $this->guid;
	}
	
	public function getUrl()
	{
		return $this->url;
	}
}
?>