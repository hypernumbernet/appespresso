<?php

/**
 * 複数のフィールドのどれかが入力されていれば適正
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_RequireSome extends Ae_Validator_abstract {

	/**
	 * 検査するフィールド
	 * @var array
	 */
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
		$this->is_valid = false;
		foreach ($this->fields as $v) {
			if (array_key_exists($v, $this->input) && $this->input[$v] != '') {
				$this->is_valid = true;
			}
		}
		return $this->is_valid;
	}

}
