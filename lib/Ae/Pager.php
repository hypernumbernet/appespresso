<?php

/**
 * HTMLのページングサポート
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Pager {

	/**
	 * ベースURL
	 * @var string
	 */
	public $base_url;

	/**
	 * 表示件数
	 * @var int
	 */
	public $limit_count;

	/**
	 * データ件数
	 * @var int
	 */
	public $data_count;

	/**
	 * GETに使用する変数名
	 * @var string
	 */
	public $get_key;

	/**
	 * 現在のページを保持する
	 * @var int
	 */
	public $cur_page = 0;

	/**
	 * 算出されたページ数を保持する
	 * @var int
	 */
	public $max_page;

	/**
	 * ページ数が多くなった場合に表示するページリンクの範囲。7以上
	 * @var int
	 */
	public $display_range;

	/**
	 * 最初のページへ飛ぶリンクの文字
	 * @var string
	 */
	public $txt_first = '|&lt;&lt;';

	/**
	 * 最後のページへ飛ぶリンクの文字
	 * @var string
	 */
	public $txt_last = '&gt;&gt;|';

	/**
	 * 最初のページが表示されていない時に出力される文字
	 * @var string
	 */
	public $txt_no_first = '';

	/**
	 * 最後のページが表示されていない時に出力される文字
	 * @var string
	 */
	public $txt_no_last = '';

	/**
	 * 最初のページが表示されている時に出力される文字
	 * @var string
	 */
	public $txt_yes_first = '';

	/**
	 * 最後のページが表示されている時に出力される文字
	 * @var string
	 */
	public $txt_yes_last = '';

	/**
	 * 最初のページが表示されているか表すフラグ。リンクではない文字を表示したい時用
	 * @var bool
	 */
	public $flag_first = true;

	/**
	 * 最後のページが表示されているか表すフラグ。リンクではない文字を表示したい時用
	 * @var bool
	 */
	public $flag_last = true;

	/**
	 * 前のページへのリンクの文字
	 * @var string
	 */
	public $txt_prev;

	/**
	 * 次のページへのリンクの文字
	 * @var string
	 */
	public $txt_next;

	/**
	 * 最初のページの時に「前へ」に表示される文字
	 * @var string
	 */
	public $txt_no_prev = '';

	/**
	 * 最後のページの時に「次へ」に表示される文字
	 * @var string
	 */
	public $txt_no_next = '';

	/**
	 * 現在のページの文字の前に付加する文字
	 * @var string
	 */
	public $txt_now_head = '<span>[';

	/**
	 * 現在のページの文字の後に付加する文字
	 * @var string
	 */
	public $txt_now_tail = ']</span>';

	/**
	 * 生成するリンクの間に挿入する文字
	 * @var string
	 */
	public $txt_gap = '';

	/**
	 * @param string $base_url 生成するリンクの基本になるURL。GET変数を含めてもOK
	 * @param int $data_count データ件数
	 * @param int $limit_count 表示件数
	 * @param string $get_key GETに使用する変数名
	 * @param int $display_range 表示するページリンクの範囲
	 */
	function __construct(
	$base_url, $data_count, $limit_count = 16, $get_key = 'p', $display_range = 16) {
		$this->base_url = $base_url;
		$this->data_count = $data_count;
		$this->limit_count = $limit_count;
		$this->get_key = $get_key;
		//現在表示しているページをGETから取得
		if (array_key_exists($get_key, $_GET)) {
			$this->cur_page = (int) $_GET[$get_key];
		}
		$this->max_page = ceil($data_count / $limit_count);
		$this->display_range = $display_range;
		$msg = 'Ae_Msg_' . AE_LANG . '_Pager';
		$this->txt_prev = $msg::PREV;
		$this->txt_next = $msg::NEXT;
	}

	/**
	 * HTML出力
	 * @param Ae_Output $tpl 埋め込み先のテンプレートインスタンス
	 * @return string ページリンク本体
	 */
	function set_pager($tpl) {
		$s = '';
		$p = '';
		if ($this->max_page > 1) {
			$p = strpos($this->base_url, '?') === false ? '?' : '&amp;';
			$p .= $this->get_key . '=';
			if ($this->max_page <= $this->display_range) {
				$b = 0;
				$e = $this->max_page;
			} else {
				$r = $this->display_range / 2;
				$b = $this->cur_page - $r + 1; //偶数の場合に後半を多くするための + 1
				$e = $this->cur_page + $r + 1;
				if ($b < 0) {
					$e -= $b;
					$b = 0;
				}
				if ($e > $this->max_page) {
					$b -= $e - $this->max_page;
					$e = $this->max_page;
				}
				$b = (int) $b;
				$e = (int) $e;
				if ($b > 0) {
					$this->flag_first = false;
				}
				if ($e < $this->max_page) {
					$this->flag_last = false;
				}
			}
			$s = $this->link($b, $e, $p);
		}
		$this->put($tpl, $s, $p);
		return $s;
	}

	/**
	 * データ開始位置を0スタートで返す。データベース問い合わせ用
	 * @return int
	 */
	function get_start() {
		return $this->cur_page * $this->limit_count;
	}

	/**
	 * データ終了位置を1スタートで返す。表示用
	 * @return int
	 */
	function get_end() {
		$r = $this->cur_page * $this->limit_count + $this->limit_count;
		return min($r, $this->data_count);
	}

	/**
	 * テンプレートに書き出す
	 * @param Ae_Output $tpl
	 * @param string $s
	 * @param string $p
	 */
	private function put($tpl, $s, $p) {
		if (isset($tpl)) {
			$tpl->add('PAGER_PAGE', $s);
			$tpl->add('PAGER_START', $this->get_start() + 1);
			$tpl->add('PAGER_END', $this->get_end());
			$tpl->add('PAGER_COUNT', $this->data_count);
			if ($this->cur_page > 0) {
				$tpl->add('PAGER_PREV',
						'<a href="' . $this->base_url . $p
						. ($this->cur_page - 1) . '">' . $this->txt_prev . '</a>');
			} else {
				$tpl->add('PAGER_PREV', $this->txt_no_prev);
			}
			if ($this->cur_page < $this->max_page - 1) {
				$tpl->add('PAGER_NEXT',
						'<a href="' . $this->base_url . $p
						. ($this->cur_page + 1) . '">' . $this->txt_next . '</a>');
			} else {
				$tpl->add('PAGER_NEXT', $this->txt_no_next);
			}
			if ($this->flag_first) {
				$tpl->add('PAGER_FIRST', $this->txt_yes_first);
				$tpl->add('PAGER_NO_FIRST', '');
			} else {
				$tpl->add('PAGER_FIRST',
						'<a href="' . $this->base_url . '">'
						. $this->txt_first . '</a>');
				$tpl->add('PAGER_NO_FIRST', $this->txt_no_first);
			}
			if ($this->flag_last) {
				$tpl->add('PAGER_LAST', $this->txt_yes_last);
				$tpl->add('PAGER_NO_LAST', '');
			} else {
				$tpl->add('PAGER_LAST',
						'<a href="' . $this->base_url . $p
						. ($this->max_page - 1) . '">' . $this->txt_last . '</a>');
				$tpl->add('PAGER_NO_LAST', $this->txt_no_last);
			}
		}
	}

	/**
	 * リンク生成
	 * @param int $start
	 * @param int $end
	 * @param string $url
	 * @return string
	 */
	private function link($start, $end, $url) {
		$a = array();
		for ($i = $start; $i < $end; ++$i) {
			if ($i == $this->cur_page) {
				$a[] = $this->txt_now_head . ($i + 1) . $this->txt_now_tail;
			} else
			if ($i == 0) {
				$a[] = '<a href="' . $this->base_url . '">'
						. ($i + 1) . '</a>';
			} else {
				$a[] = '<a href="' . $this->base_url . $url . $i . '">'
						. ($i + 1) . '</a>';
			}
		}
		return implode($this->txt_gap, $a);
	}

}
