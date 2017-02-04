<?php

/**
 * 最大文字数制限
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * キーがない場合も制限される。
 */
class Ae_Validator_Max extends Ae_Validator_Field {

	public $max;
	public $place_len = '[[LEN]]';
	public $place_max = '[[MAX]]';

	/**
	 * @param string $field 対象のフィールド名
	 * @param int $max_length 最大文字長
	 * @param array $input 初期値：$_POST
	 */
	function __construct($field, $max_length, $input = null) {
		parent::__construct($field, $input);
		$this->max = $max_length;
	}

	function valid() {
		if (parent::valid()) {
			$l = mb_strlen($this->input[$this->field]);
			$this->is_valid = ($l <= $this->max);
			if (!$this->is_valid) {
				$this->message_str = str_replace(
						$this->place_len, $l, $this->message_str);
				$this->summary_str = str_replace(
						$this->place_len, $l, $this->summary_str);
				$this->message_str = str_replace(
						$this->place_max, $this->max, $this->message_str);
				$this->summary_str = str_replace(
						$this->place_max, $this->max, $this->summary_str);
			}
		}
		return $this->is_valid;
	}

}
