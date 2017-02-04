<?php

/**
 * Validator基底クラス
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * 継承したクラスは、適切なコンストラクタとvalid()を実装すること。
 * valid()では、$is_validに結果を格納し、bool値を返すようにする。
 */
abstract class Ae_Validator_abstract {

	/**
	 * 検査結果を保持。デフォルトtrue
	 * @var bool
	 */
	public $is_valid = true;

	/**
	 * エラーメッセージを出力するためのテンプレートの場所
	 * @var string
	 */
	public $message_tag;

	/**
	 * エラーメッセージ
	 * @var string
	 */
	public $message_str;

	/**
	 * エラーサマリーに出力するメッセージ
	 * @var string
	 */
	public $summary_str;

	/**
	 * $is_validに結果を格納し、bool値を返すように実装して下さい。
	 */
	abstract public function valid();

	/**
	 * エラーメッセージをセット
	 * @param string $tag テンプレートの場所
	 * @param string $str メッセージ
	 */
	public function set_error_message($tag, $str) {
		$this->message_tag = $tag;
		$this->message_str = $str;
	}

	/**
	 * エラーサマリーをセット
	 * @param string $str メッセージ
	 */
	public function set_error_summary($str) {
		$this->summary_str = $str;
	}

}
