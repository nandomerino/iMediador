<?php

namespace WL\Http\LogaltyClient;

/**
 * 
 */
class FormDTO
{
	public const PLACEMENT_MIDDLE_MIDDLE = "MIDDLE_MIDDLE";
	public const STEP_AFTERDOWNLOAD = "AFTERDOWNLOAD";

	private $id;
	private $locale;
	private $placement;
	private $step;
	private $elements;

	function __construct()
	{
		$this->placement = FormDTO::PLACEMENT_MIDDLE_MIDDLE;
		$this->step = FormDTO::STEP_AFTERDOWNLOAD;
		$this->elements = array();
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

	public function setLocale($locale)
	{
		$this->locale = $locale;
	}

	public function getPlacement()
	{
		return $this->placement;
	}

	public function setPlacement($p)
	{
		$this->placement = $p;
	}

	public function getStep()
	{
		return $this->step;
	}

	public function setStep($s)
	{
		$this->step = $s;
	}

	public function getElements()
	{
		return $this->elements;
	}

	public function setElements($e)
	{
		$this->elements = $e;
	}

	public function addElement($e)
	{
		$this->elements[] = $e;
	}
}

?>