<?php

/**
 * アップロードが適切に行われたか検査します。
 * 「@@」にエラーメッセージを埋め込みます。
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_Upload extends Ae_Validator_abstract {

	public $field;
	public $error_code;

	/**
	 * アップロードファイルが指定されなかった場合の検証結果
	 * @var bool
	 */
	public $valid_if_none = true;
	public $error_message;
	public $uploaded = false;

	/**
	 * コンストラクタ
	 * @param string $field 対象のフィールド名
	 */
	function __construct($field) {
		$this->field = $field;
		$msg = 'Ae_Msg_' . AE_LANG . '_Validator';
		$this->error_message = array(
			0 => '',
			1 => $msg::ERR_1,
			2 => $msg::ERR_2,
			3 => $msg::ERR_3,
			4 => $msg::ERR_4,
			6 => $msg::ERR_6,
			7 => $msg::ERR_7,
			8 => $msg::ERR_8
		);
	}

	function valid() {
		if (array_key_exists($this->field, $_FILES)) {
			$this->error_code = $_FILES[$this->field]['error'];
			if ($this->error_code == 4) {
				$this->is_valid = $this->valid_if_none;
			} elseif ($this->error_code != 0) {
				$this->is_valid = false;
			} else {
				$this->uploaded = true;
			}
		} else {
			$this->is_valid = $this->valid_if_none;
			$this->error_code = 4;
		}
		if (!$this->is_valid) {
			$this->message_str = str_replace('@@', $this->error_message[$this->error_code],
					$this->message_str);
			$this->summary_str = str_replace('@@', $this->error_message[$this->error_code],
					$this->summary_str);
		}
		return $this->is_valid;
	}

}
