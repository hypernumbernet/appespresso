<?php

/**
 * メール送信
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * qmailにてヘッダ改行コードを\r\nにするとサーバで二重改行するので避ける。
 * Postfixにてメールサーバが、改行のない長い文字列に対して、
 * 自動改行してしまい、文字化けを引き起こす問題を回避する。
 * 一行文字数を適度に加工してからJISエンコードする。
 */
class Ae_Mail {

	/**
	 * テンプレート
	 * @var Ae_Output
	 */
	private $template;

	/**
	 * テンプレート設定
	 * @param Ae_Output $s
	 * @return Ae_Mail
	 */
	public function template($s) {
		$this->template = $s;
		return $this;
	}

	/**
	 * デフォルトのテンプレート値
	 * @var array
	 */
	public $default = array('REMOTE_ADDR', 'HTTP_USER_AGENT');

	/**
	 * 送信先アドレス
	 * @var string
	 */
	private $to_address = '';

	/**
	 * 送信先アドレス設定
	 * @param string $s
	 * @return Ae_Mail
	 */
	public function to_address($s) {
		$this->to_address = $s;
		return $this;
	}

	/**
	 * 送信者名
	 * @var string
	 */
	private $to_name = '';

	/**
	 * 送信者名設定
	 * @param string $s
	 * @return Ae_Mail
	 */
	public function to_name($s) {
		$this->to_name = $s;
		return $this;
	}

	/**
	 * 送信元アドレス
	 * @var string
	 */
	private $from_address = '';

	/**
	 * 送信元アドレス設定
	 * @param string $s
	 * @return Ae_Mail
	 */
	public function from_address($s) {
		$this->from_address = $s;
		return $this;
	}

	/**
	 * 送信者名
	 * @var string
	 */
	private $from_name = '';

	/**
	 * 送信者名設定
	 * @param string $s
	 * @return Ae_Mail
	 */
	public function from_name($s) {
		$this->from_name = $s;
		return $this;
	}

	/**
	 * 題名
	 * @var string
	 */
	private $subject = '';

	/**
	 * 題名設定
	 * @param string $s
	 * @return Ae_Mail
	 */
	public function subject($s) {
		$this->subject = $s;
		return $this;
	}

	/**
	 * 本文
	 * @var string
	 */
	private $body = '';

	/**
	 * 本文の設定
	 * @param string $s
	 * @return Ae_Mail
	 */
	public function body($s) {
		$this->body = $s;
		return $this;
	}

	/**
	 * 本文の取得。body空文ならテンプレートを使用する。
	 * @return type
	 */
	public function get_body() {
		if ($this->body) {
			return $this->body;
		} else {
			foreach ($this->default as $x) {
				if (isset($_SERVER[$x])) {
					$this->template->add($x, $_SERVER[$x]);
				} else {
					$this->template->add($x, '');
				}
			}
			return $this->template->fetch();
		}
	}

	/**
	 * エラーメッセージ
	 * @var string
	 */
	public $error_msg = 'Error Sending Mail.';

	/**
	 * 指定した文字数ごとに文字を挿入する。再帰呼出使用
	 * @param string $s 対象文字列
	 * @param string $c 文字数
	 * @param string $ins 挿入文字
	 * @return string 変換した文字列
	 */
	private function mb_strins($s, $c, $ins) {
		if (mb_strlen($s) > $c) {
			return mb_substr($s, 0, $c) . $ins
					. $this->mb_strins(mb_substr($s, $c), $c, $ins);
		} else {
			return $s;
		}
	}

	/**
	 * 一行を指定した文字数に制限してエンコード(改行コードCR未対応)
	 * @param string $s 対象となる文字列
	 * @param string $to_encoding 変換する文字コード
	 * @param int $c 1行の文字数
	 * @return string 変換した文字列
	 */
	private function mb_convert_encoding_limit($s, $to_encoding, $c) {
		if ($c > 0) {
			$a = mb_split("\n", $s);
			$a = str_replace("\r", '', $a);
			$s = '';
			foreach ($a as $t) {
				$s .= $this->mb_strins($t, $c, "\n") . "\n";
			}
		}
		$s = mb_convert_encoding($s, $to_encoding);
		return $s;
	}

	/**
	 * 基本ヘッダ
	 * @param string $charset
	 * @param string $encoding
	 * @return string
	 */
	private function basic_header($charset, $encoding) {
		return "MIME-Version: 1.0\n"
				. 'Content-Type: text/plain; charset=' . $charset . "\n"
				. 'Content-Transfer-Encoding: ' . $encoding;
	}

	/**
	 * mail()で使用するアドレス。JIS
	 * @return string 受信者
	 */
	private function mailto_jis() {
		if ($this->to_name) {
			return mb_encode_mimeheader($this->to_name) . " <$this->to_address>";
		} else {
			return $this->to_address;
		}
	}

	/**
	 * 送信者ヘッダ。JIS
	 * @return string ヘッダに追加する
	 */
	private function mailfrom_jis() {
		if ($this->from_name) {
			return 'From: ' . mb_encode_mimeheader($this->from_name)
					. " <$this->from_address>\n";
		} else {
			return "From: $this->from_address\n";
		}
	}

	/**
	 * メールをJISコードで作成して送信する
	 * @throws Exception
	 */
	public function mail_jis() {
		$header = $this->mailfrom_jis()
				. $this->basic_header('ISO-2022-JP', '7bit');
		$err = mail($this->mailto_jis(), mb_encode_mimeheader($this->subject),
				$this->mb_convert_encoding_limit($this->get_body(), 'JIS', 460), $header
		);
		if (!$err) {
			throw new Exception($this->error_msg);
		}
	}

	/**
	 * mail()で使用するアドレス。UTF-8
	 * @return string 受信者
	 */
	private function mailto_utf8() {
		if ($this->to_name) {
			return mb_encode_mimeheader($this->to_name, 'UTF-8', 'B', "\n")
					. " <$this->to_address>";
		} else {
			return $this->to_address;
		}
	}

	/**
	 * 送信者ヘッダ。UTF-8
	 * @return string ヘッダに追加する
	 */
	private function mailfrom_utf8() {
		if ($this->from_name) {
			return "From: "
					. mb_encode_mimeheader($this->from_name, 'UTF-8', 'B', "\n")
					. " <$this->from_address>\n";
		} else {
			return "From: $this->from_address\n";
		}
	}

	/**
	 * メールをUTF-8コードで作成して送信する
	 * @throws Exception
	 */
	public function mail_utf8() {
		$header = $this->mailfrom_utf8()
				. $this->basic_header('UTF-8', 'base64');
		$err = mail($this->mailto_utf8(), mb_encode_mimeheader($this->subject, 'UTF-8', 'B', "\n"),
				chunk_split(base64_encode(
								mb_convert_encoding($this->get_body(), 'UTF-8')), 70, "\n"), $header
		);
		if (!$err) {
			throw new Exception($this->error_msg);
		}
	}

}
