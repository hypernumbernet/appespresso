<?php

/**
 * 設定されている初期値と同じならば×
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * selectなどで、デフォルト値からずれている場合に入力されたとみなす。
 */
class Ae_Validator_Require extends Ae_Validator_Field {

	/**
	 * 初期値
	 * @var string
	 */
	public $initial_value = '';

	function valid() {
		if (parent::valid()) {
			$this->is_valid = ($this->input[$this->field] != $this->initial_value);
		}
		return $this->is_valid;
	}

}
