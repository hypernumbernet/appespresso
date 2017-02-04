<?php

/**
 * 正規表現にマッチしないと×　未入力で○
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * キーがない、または、初期値に一致している場合は制限されない。
 */
class Ae_Validator_Regex extends Ae_Validator_Field {

	const MAIL = '/^.+@.+\..+$/';
	const HIRA_KATA = '/^[ぁ-んァ-ヶー]+$/u';

	public $regex = '';

	/**
	 * 初期値
	 * @var string
	 */
	public $initial_value = '';

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
			if ($this->input[$this->field] != $this->initial_value) {
				$this->is_valid = preg_match($this->regex, $this->input[$this->field]);
			}
		} else {
			$this->is_valid = true;
		}
		return $this->is_valid;
	}

}
