<?php

namespace WL\Http\LogaltyClient;

/**
 * 
 */
class FormElementDTO
{
	public const FORM_ELEMENT_TYPE_CHECKBOX = "C";
	public const FORM_ELEMENT_TYPE_TEXT = "T";

	private $id;
	private $mandatory;
	private $value;
	private $label;
	private $hint;
	private $type;

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

	public function getMandatory()
	{
		return $this->mandatory;
	}

	public function setMandatory($m)
	{
		$this->mandatory = $m;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setValue($v)
	{
		$this->value = $v;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function setLabel($l)
	{
		$this->label = $l;
	}

	public function getHint()
	{
		return $this->hint;
	}

	public function setHint($h)
	{
		$this->hint = $h;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($t)
	{
		$this->type = $t;
	}
}