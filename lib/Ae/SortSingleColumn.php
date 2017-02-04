<?php

/**
 * データテーブルの単項並び替えのユーティリティクラス
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * $_GETから並び替え情報を読み取り、テンプレートにリンクを埋め込んだり、
 * データベース問い合わせ用のSQLを生成したりする。
 * あらかじめ、二次元配列に[{(表示文字列), (データベース項目名)}]を用意してセットする。
 * テンプレートには、[SORT_(表示文字列)]をプレースホルダとして埋め込んでおく。
 * buttonプロパティをセットすることで、画像を使用することもできる。
 */
class Ae_SortSingleColumn {

	const QUERY_KEY = 'sort';
	const TAG_HEADER = 'SORT_';

	public $null_button = '';
	public $desc_button = '▼';
	public $asc_button = '▲';
	public $query_key;
	public $query_str;
	public $sort_array = array();

	/**
	 * @param Ae_QueryString $query_str 他のクエリ文字列を保持する。
	 * @param array $sort_array 二次元配列[{(表示文字列), (データベース項目名)}]
	 * @param string $key GETクエリ文字列に使用する変数名
	 */
	function __construct($query_str, $sort_array = array(), $key = self::QUERY_KEY) {
		$this->query_str = $query_str;
		$this->sort_array = $sort_array;
		$this->query_key = $key;
	}

	/**
	 * デフォルトで並び替える項目を設定する。
	 * @param int,string $index ゼロスタートの番号、または項目表示文字列
	 * @param bool $desc 降順指定
	 */
	function set_default($index, $desc = false) {
		$i = 0;
		if (is_string($index)) {
			foreach ($this->sort_array as $k => $v) {
				if ($v[0] == $index) {
					$i = $k;
					break;
				}
			}
		} else {
			$i = (int) $index;
		}
		if (!array_key_exists($this->query_key, $_GET)) {
			$_GET[$this->query_key] = ($desc ? '-' : '') . $i;
		}
	}

	/**
	 * Ae_Templateの SORT_(db column) タグにリンクを埋め込む。
	 * @param Ae_Template $tpl
	 */
	function set_sort_link($tpl) {
		$now_sort = '';
		if (array_key_exists($this->query_key, $_GET)) {
			$now_sort = $_GET[$this->query_key];
		}
		foreach ($this->sort_array as $k => $v) {
			$k = (string) $k;
			$button = '';
			$index = str_replace('-', '', $now_sort);
			if ($index == $k) {
				if (strlen($now_sort) > 0 && $now_sort[0] == '-') {
					$button = $this->desc_button;
				} else {
					$k = '-' . $k;
					$button = $this->asc_button;
				}
			} else {
				$button = $this->null_button;
			}
			$q = clone $this->query_str;
			$q->add($this->query_key, $k);
			$tpl->add(
					self::TAG_HEADER . $v[1],
					'<a href="' . $q->get_query_string() . '">' . $v[0] . $button . '</a>'
			);
		}
	}

	/**
	 * データベース問い合わせ用のSQLを返す。
	 * @param array,string $other_sort 暗黙で並び替えられる項目。優先順位低
	 * @return string
	 */
	function get_order_by($other_sort = array()) {
		$now_sort = '';
		if (array_key_exists($this->query_key, $_GET)) {
			$now_sort = $_GET[$this->query_key];
		}
		$o = array();
		foreach ($this->sort_array as $k => $v) {
			$k = (string) $k;
			$index = str_replace('-', '', $now_sort);
			if ($index == $k) {
				if (strlen($now_sort) > 0 && $now_sort[0] == '-') {
					$o[] = $v[1] . ' DESC';
					break;
				} else {
					$o[] = $v[1];
					break;
				}
			}
		}
		if (is_string($other_sort)) {
			$other_sort = array($other_sort);
		}
		$o = array_merge($o, $other_sort);
		if (count($o) == 0) {
			return '';
		}
		return ' ORDER BY ' . implode(',', $o) . ' ';
	}

}
