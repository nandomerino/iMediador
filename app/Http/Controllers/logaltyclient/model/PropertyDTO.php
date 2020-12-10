<?php 

namespace WL\Http\LogaltyClient;

/**
 * 
 */
class PropertyDTO
{
	public const PROPERTY_LOCATION_EMAIL = "EMAIL";
	public const PROPERTY_LOCATION_CANCEL = "CANCEL_OPTIONS";

	public const PROPERTY_ID_URL = "URL";
	
	private $id;
	private $locale;
	private $label;
	private $value;
	private $location;

	function __construct()
	{
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setLocale($l)
	{
		$this->locale = $l;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function setLabel($l)
	{
		$this->label = $l;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setValue($v)
	{
		$this->value = $v;
	}

	public function getLocation()
	{
		return $this->location;
	}

	public function setLocation($l)
	{
		$this->location = $l;
	}
}

?>