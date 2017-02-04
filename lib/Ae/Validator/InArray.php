<?php

/**
 * 指定の値だけに制限
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_InArray extends Ae_Validator_Field {

	public $allow;

	/**
	 * @param string $field 対象のフィールド名
	 * @param array $allow 許可する文字列
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $allow, $input = null) {
		parent::__construct($field, $input);
		$this->allow = $allow;
	}

	function valid() {
		if (parent::valid()) {
			$w = $this->input[$this->field];
			$this->is_valid = false;
			foreach ($this->allow as $x) {
				if ($w == $x) {
					$this->is_valid = true;
					break;
				}
			}
		}
		return $this->is_valid;
	}

}
