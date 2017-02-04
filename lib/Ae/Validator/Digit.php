<?php

/**
 * すべての文字を10進数字に制限
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_Digit extends Ae_Validator_Field {

	/**
	 * 初期値
	 * @var string
	 */
	public $init = '';

	/**
	 * @param string $field 対象のフィールド名
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $input = null) {
		parent::__construct($field, $input);
	}

	function valid() {
		if (parent::valid()) {
			if ($this->input[$this->field] != $this->init) {
				$this->is_valid = ctype_digit($this->input[$this->field]);
			}
		} else {
			$this->is_valid = true;
		}
		return $this->is_valid;
	}

}
