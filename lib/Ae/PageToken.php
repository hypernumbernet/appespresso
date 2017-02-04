<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * 生成したページにユニークなIDを付け多重動作を防止する。
 * 
 * @version 2.0.0
 */
class PageToken {
	
	/** @var boolean 正当なトークンか？ */
	public $valid;

	/** @var string 現在のトークン値 */
	public $value;

	/** @var string Cookieで使用するキー */
	public $val_key = 'AE_TOKEN';

	/** @var string 評価対象配列のキー */
	public $get_key = 'token';

	/** @var boolean クッキーが発行されてない時の評価 */
	public $allow_keyless = false;

	/** @var int 発行するトークンの長さ */
	public $length = 16;

	/**
	 * 実体化と同時に値を評価する。
     * 
	 * @param int $type
	 */
	public function __construct($type = null) {
		if (is_null($type)) {
			$type = INPUT_GET;
		}
		$a = new HttpInput($type);
		$c = new HttpInput(INPUT_COOKIE);
		$this->valid = $this->allow_keyless;
		if ($c->get($this->val_key)) {
			$this->valid = ($c->get($this->val_key) === $a->get($this->get_key));
		}
		$token = Password::make($this->length);
		setcookie($this->val_key, $token);
		$this->value = $token;
	}

	/**
	 * リンクURLに付加する文字列
     * 
	 * @return string
	 */
	public function urlParam() {
		return $this->get_key . '=' . $this->value;
	}

}
