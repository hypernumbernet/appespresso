<?php

/**
 * アップロードされた画像を検査します。
 * 「@@」にエラーメッセージを埋め込みます。
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_UploadImage extends Ae_Validator_Upload {

	public $extension = '';
	public $error_code2 = 0;
	public $error_message2;

	/**
	 * コンストラクタ
	 * @param string $field 対象のフィールド名
	 */
	function __construct($field) {
		parent::__construct($field);
		$msg = 'Ae_Msg_' . AE_LANG . '_Validator';
		$this->error_message2 = array(
			0 => '',
			1 => $msg::IMG_1,
			2 => $msg::IMG_2,
			3 => $msg::IMG_3,
		);
	}

	function valid() {
		if (parent::valid()) {
			if ($this->uploaded) {
				$size = getimagesize($_FILES[$this->field]['tmp_name']);
				if ($size) {
					switch ($size[2]) {
						case '1':
							$this->extension = 'gif';
							break;
						case '2':
							$this->extension = 'jpg';
							break;
						case '3':
							$this->extension = 'png';
							break;
						default:
							$this->is_valid = false;
							$this->error_code2 = 1;
					}
					if ($this->is_valid) {
						$info = pathinfo($_FILES[$this->field]['name']);
						if (strtolower($info['extension']) != $this->extension) {
							$this->is_valid = false;
							$this->error_code2 = 2;
						}
					}
				} else {
					$this->is_valid = false;
					$this->error_code2 = 3;
				}
			}
			if (!$this->is_valid) {
				$this->message_str = str_replace('@@', $this->error_message2[$this->error_code2],
						$this->message_str);
				$this->summary_str = str_replace('@@', $this->error_message2[$this->error_code2],
						$this->summary_str);
			}
		}
		return $this->is_valid;
	}

}
