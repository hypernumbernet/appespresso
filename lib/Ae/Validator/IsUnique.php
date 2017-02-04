<?php

/**
 * データがDBテーブルでユニークかどうかを調べる。
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * 直後でUPDATE,INSERT操作をする場合、適切なロックを考慮しないと
 * DBサーバーレベルでエラーが発生する可能性が残る。
 */
class Ae_Validator_IsUnique extends Ae_Validator_Field {

	public $record;

	/**
	 * @param PDO $db
	 * @param string $table サニタイズなし
	 * @param string $column
	 * @param int $id UPDATEで自分自身を除きたいときそのid
	 * @param int $type 規定 PDO::PARAM_STR
	 */
	function set_db($db, $table, $column, $id = 0, $type = PDO::PARAM_STR) {
		$this->db = $db;
		$this->table = $table;
		$this->column = $column;
		$this->id = $id;
		$this->type = $type;
	}

	function valid() {
		if (parent::valid()) {
			$s = 'SELECT COUNT(*) FROM ' . $this->table
					. ' WHERE ' . $this->column . ' = ?';
			if ($this->id) {
				$s .= ' AND id != ?';
			}
			$stt = $this->db->prepare($s);
			$stt->bindValue(1, $this->input[$this->field], $this->type);
			if ($this->id) {
				$stt->bindValue(2, (int) $this->id, PDO::PARAM_INT);
			}
			$stt->execute();
			$n = 0;
			if ($r = $stt->fetch(PDO::FETCH_NUM)) {
				$n = (int) $r[0];
			}
			$this->is_valid = ($n == 0);
		}
		return $this->is_valid;
	}

	private $db;
	private $table;
	private $column;
	private $type;
	private $id;

}
