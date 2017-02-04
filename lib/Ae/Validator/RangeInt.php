<?php

/**
 * 整数かどうか検査し、値の範囲を制限する項目を設定する。
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * キーがない、または、初期値に一致している場合は制限されない。
 */
class Ae_Validator_RangeInt extends Ae_Validator_Field {

	public $min;
	public $max;

	/**
	 * 初期値
	 * @var string
	 */
	public $initial_value = '';

	/**
	 * @param string $field 対象のフィールド名
	 * @param int $min 最小値
	 * @param int $max 最大値
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $min, $max, $input = null) {
		parent::__construct($field, $input);
		$this->min = $min;
		$this->max = $max;
	}

	function valid() {
		if (parent::valid()) {
			$v = $this->input[$this->field];
			if ($v != $this->initial_value) {
				if (preg_match('/^-?\d*$/', $v)) {
					$v = (int) $v;
					$this->is_valid = ($v >= $this->min && $v <= $this->max);
				} else {
					$this->is_valid = false;
				}
			}
		} else {
			$this->is_valid = true;
		}
		return $this->is_valid;
	}

}
