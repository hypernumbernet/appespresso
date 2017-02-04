<?php

/**
 * GETクエリ文字列を生成。「?」「&」などを自動付加してくれる。
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_QueryString {

	/**
	 * キーと値を=で結合した文字列の配列
	 * @var array
	 */
	public $query_ar = array();

	/**
	 * ベースになるURL
	 * @var string
	 */
	public $url;

	/**
	 * コンストラクタ
	 * @param string $url
	 */
	function __construct($url = null) {
		if (is_null($url)) {
			$this->url = basename($_SERVER['SCRIPT_NAME']);
		} else {
			$this->url = $url;
		}
	}

	/**
	 * 単純にキーを追加する。
	 * @param string $key
	 * @param string $value
	 */
	function add($key, $value = '') {
		$this->query_ar[] = $key . '=' . rawurlencode($value);
	}

	/**
	 * もしGETクエリにキーがあればそれを追加する。なければ追加しない。
	 * @param string $key
	 */
	function add_if_exists($key) {
		if (array_key_exists($key, $_GET)) {
			$this->query_ar[] = $key . '=' . rawurlencode($_GET[$key]);
		}
	}

	/**
	 * 指定したキー以外のキーをQUERY_STRINGから追加
	 * @param string $key
	 */
	function add_except($key) {
		$a = explode('&', $_SERVER['QUERY_STRING']);
		if (count($a) == 1 && $a[0] == "") {
			$a = array();
		}
		foreach ($a as $v) {
			$b = explode('=', $v);
			if ($b[0] != $key) {
				$this->query_ar[] = $v;
			}
		}
	}

	/**
	 * URLを出力
	 * @param string $amp
	 * @return string
	 */
	function get_query_string($amp = '&amp;') {
		if (count($this->query_ar)) {
			return $this->url . (strpos($this->url, '?') ? $amp : '?')
					. implode($amp, $this->query_ar);
		}
		return $this->url;
	}

}
