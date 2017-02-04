<?php

/**
 * 数値が和暦の範囲にあるかどうか検査します。
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_JapaneseEra extends Ae_Validator_Field {

	public $c_upper = 2100;
	public $c_lower = 1800;
	public $era_field = '';

	/**
	 * @param string $field 対象のフィールド名
	 * @param string $era_field 年号のフィールド名
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $era_field, $input = null) {
		parent::__construct($field, $input);
		$this->era_field = $era_field;
	}

	function valid() {
		if (parent::valid()) {
			$v = $this->input[$this->field];
			if ($v == '') {
				return true;
			}
			if (ctype_digit($v)) {
				$v = (int) $v;
				if (array_key_exists($this->era_field, $this->input)) {
					switch ($this->input[$this->era_field]) {
						case '平成':
							$this->is_valid = ($v > 0 && $v < 99);
							break;
						case '昭和':
							$this->is_valid = ($v > 0 && $v < 65);
							break;
						case '大正':
							$this->is_valid = ($v > 0 && $v < 16);
							break;
						case '明治':
							$this->is_valid = ($v > 0 && $v < 46);
							break;
						default:
							$this->is_valid = ($v > $this->c_lower && $v < $this->c_upper);
					}
				} else {
					$this->is_valid = ($v > $this->c_lower && $v < $this->c_upper);
				}
			} else {
				$this->is_valid = false;
			}
		}
		return $this->is_valid;
	}

}
