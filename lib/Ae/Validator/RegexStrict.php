<?php

/**
 * 単純な正規表現による制限。キーがない場合も制限されない。
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_RegexStrict extends Ae_Validator_Field {

	public $regex = '';

	/**
	 * @param string $field 対象のフィールド名
	 * @param string $regular_expression preg使用
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $regular_expression, $input = null) {
		parent::__construct($field, $input);
		$this->regex = $regular_expression;
	}

	function valid() {
		if (parent::valid()) {
			$this->is_valid = preg_match($this->regex, $this->input[$this->field]);
		}
		return $this->is_valid;
	}

}
