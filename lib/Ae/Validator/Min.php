<?php

/**
 * 最小文字数制限
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * キーがない場合も制限される。
 * キーがあって空文字の場合は制限されない。
 */
class Ae_Validator_Min extends Ae_Validator_Field {

	public $min;
	public $place_len = '[[LEN]]';
	public $place_min = '[[MIN]]';

	/**
	 * @param string $field 対象のフィールド名
	 * @param int $min_length 最大文字長
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $min_length, $input = null) {
		parent::__construct($field, $input);
		$this->min = $min_length;
	}

	function valid() {
		if (parent::valid()) {
			$l = mb_strlen($this->input[$this->field]);
			$this->is_valid = ($l >= $this->min || $l == 0);
			if (!$this->is_valid) {
				$this->message_str = str_replace(
						$this->place_len, $l, $this->message_str);
				$this->summary_str = str_replace(
						$this->place_len, $l, $this->summary_str);
				$this->message_str = str_replace(
						$this->place_min, $this->min, $this->message_str);
				$this->summary_str = str_replace(
						$this->place_min, $this->min, $this->summary_str);
			}
		}
		return $this->is_valid;
	}

}
