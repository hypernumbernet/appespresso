<?php

/**
 * 特定のフィールドに関わるValidatorの基底クラス
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * キーが存在すれば適正とみなす。
 * checkbox, radioならこのクラスだけで使える。
 */
class Ae_Validator_Field extends Ae_Validator_abstract {

	public $field;
	public $input;

	/**
	 * @param string $field 対象のフィールド名
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $input = null) {
		$this->field = $field;
		if (is_null($input)) {
			$this->input = & $_POST;
		} else {
			$this->input = $input;
		}
	}

	function valid() {
		$this->is_valid = array_key_exists($this->field, $this->input);
		return $this->is_valid;
	}

}
