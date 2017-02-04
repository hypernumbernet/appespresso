<?php

/**
 * 値が同じなら適正
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * 配列に含まれる値がすべて同じかどうか検査する。
 */
class Ae_Validator_Same extends Ae_Validator_abstract {

	public $fields;
	public $input;

	/**
	 * @param array $fields 対象のフィールド名
	 * @param array $input 初期値：$_POST
	 */
	function __construct($fields, $input = null) {
		$this->fields = $fields;
		if (is_null($input)) {
			$this->input = & $_POST;
		} else {
			$this->input = $input;
		}
	}

	function valid() {
		$v = null;
		foreach ($this->fields as $s) {
			if (array_key_exists($s, $this->input)) {
				if (is_null($v)) {
					$v = $this->input[$s];
				}
				if ($v != $this->input[$s]) {
					$this->is_valid = false;
				}
			}
		}
		return $this->is_valid;
	}

}
