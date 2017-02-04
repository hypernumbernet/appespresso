<?php

/**
 * DBからデータの読み込み
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Db_Select {

	const KEY = 'p';

	public $columns = '*';
	public $repeater;
	public $pager;

	/**
	 * @param PDO $db
	 * @param string $table
	 * @param string $url
	 */
	function __construct($db, $table, $url = null) {
		$this->db = $db;
		$this->table = $table;
		$this->url = $url;
	}

	/**
	 * 検索条件設定
	 * @param string $phrase SQL句
	 * @param array[][1-2] $values {{値, PDO::PARAM_}, ...}
	 */
	function set_where($phrase, $values = array()) {
		$this->where = $phrase;
		$this->values = $values;
	}

	/**
	 * 並び替え機能を付加する。
	 * @param array[][2] $order {{表示文字列, DB項目名},...}
	 * @param int,string $default ゼロスタートの番号、または項目表示文字列
	 * @param bool $desc 降順指定
	 * @param array,string $shadow 陰で必ず並び替えられる項目
	 */
	function set_order_by(
	$order, $default = null, $desc = false, $shadow = array()) {
		$this->order = $order;
		$this->default = $default;
		$this->desc = $desc;
		$this->shadow = $shadow;
	}

	/**
	 * ページングの設定
	 * @param $limit
	 * @param $range
	 */
	function set_paging($limit, $range = 16) {
		$this->limit = $limit;
		$this->range = $range;
	}

	/**
	 * 設定された条件に合わせてデータを取得
	 */
	function get_records() {
		if (isset($this->order) || isset($this->limit)) {
			if (is_object($this->url)) {
				$qs = $this->url;
			} else {
				$qs = new Ae_QueryString($this->url);
			}
		}
		if (isset($this->order)) {
			$q2 = clone $qs;
			$q2->add_except(Ae_SortSingleColumn::QUERY_KEY);
			$this->ssc = new Ae_SortSingleColumn($q2, $this->order);
			if (isset($this->default)) {
				$this->ssc->set_default($this->default, $this->desc);
			}
		}
		if (isset($this->limit)) {
			$qs->add_except(self::KEY);
			$this->pager = new Ae_Pager(
					$qs->get_query_string(),
					Ae_Db_Utl::count(
							$this->db, $this->table, $this->where, $this->values), $this->limit, self::KEY, $this->range
			);
		}
		$sql = 'SELECT ' . $this->columns . ' FROM ' . $this->table;
		if (isset($this->where)) {
			$sql .= ' WHERE ' . $this->where;
		}
		if (isset($this->ssc)) {
			$sql .= $this->ssc->get_order_by($this->shadow);
		}
		if (isset($this->pager)) {
			$sql .= ' LIMIT ? OFFSET ? ';
		}
		$stt = $this->db->prepare($sql);
		$i = 1;
		foreach ($this->values as $x) {
			if (is_array($x)) {
				$stt->bindValue($i ++, $x[0], $x[1]);
			} else {
				$stt->bindValue($i ++, $x);
			}
		}
		if (isset($this->pager)) {
			$stt->bindValue($i ++, $this->limit, PDO::PARAM_INT);
			$stt->bindValue($i ++, $this->pager->get_start(), PDO::PARAM_INT);
		}
		$stt->execute();
		return $this->result = $stt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * 加工されたデータを組み込んでテンプレートに出力
	 * @param Ae_Output $tpl
	 * @param array[][] $shaped 加工部分のみのデータ
	 */
	function put($tpl, $shaped = null) {
		if (is_null($this->result)) {
			$this->get_records();
		}
		if (isset($this->ssc)) {
			$this->ssc->set_sort_link($tpl);
		}
		if (isset($this->pager)) {
			$this->pager->set_pager($tpl);
		}
		$d = array();
		if (is_null($shaped)) {
			$d = $this->result;
		} else {
			$i = 0;
			foreach ($shaped as $r) {
				if (isset($this->result[$i])) {
					$w = array();
					foreach ($this->result[$i] as $k => $x) {
						if (isset($shaped[$i][$k])) {
							$w[$k] = $shaped[$i][$k];
						} else {
							$w[$k] = $x;
						}
					}
					$d[] = $w;
				} else {
					$d[] = $r;
				}
				++$i;
			}
		}
		if (isset($this->repeater)) {
			$tpl->add($this->repeater, $d, false);
		} else {
			$tpl->add($this->table, $d, false);
		}
	}

	private $values = array();
	private $db;
	private $table;
	private $where;
	private $order;
	private $default;
	private $desc;
	private $shadow;
	private $limit;
	private $range;
	private $ssc;
	private $result;
	private $url;

}
