<?php

/**
 * フィールドに指定の文字列が含まれないように制限
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_BanWords extends Ae_Validator_Field {

	public $words;

	/**
	 * @param string $field 対象のフィールド名
	 * @param array $words 禁止語
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $words, $input = null) {
		parent::__construct($field, $input);
		$this->words = $words;
	}

	function valid() {
		if (parent::valid()) {
			$w = $this->input[$this->field];
			foreach ($this->words as $x) {
				if (strstr($w, $x)) {
					$this->is_valid = false;
					break;
				}
			}
		}
		return $this->is_valid;
	}

}
