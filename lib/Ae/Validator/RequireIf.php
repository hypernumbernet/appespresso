<?php

/**
 * 条件付。$if_fieldが$if_valueならば検査する。
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_RequireIf extends Ae_Validator_Require {

	public $if_field = '';
	public $if_value = '';

	/**
	 * @param string $field 対象のフィールド名
	 * @param string $if_field 条件のフィールド名
	 * @param string $if_value 条件値
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $if_field, $if_value, $input = null) {
		parent::__construct($field, $input);
		$this->if_field = $if_field;
		$this->if_value = $if_value;
	}

	function valid() {
		if (array_key_exists($this->if_field, $this->input) && $this->input[$this->if_field] == $this->if_value
		) {
			parent::valid();
		}
		return $this->is_valid;
	}

}
