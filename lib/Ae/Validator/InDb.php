<?php

/**
 * DBにデータが存在するか調べ、レコードを取得する。
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @tutorial
 * idからデータを引くというようなフォームで使用する。
 */
class Ae_Validator_InDb extends Ae_Validator_Field {

	public $record;

	/**
	 * @param PDO $db
	 * @param string $table サニタイズなし
	 * @param array,string $columns
	 * @param string $where
	 */
	function set_db($db, $table, $columns = '*', $where = 'id = ?') {
		$this->db = $db;
		$this->table = $table;
		$this->columns = $columns;
		$this->where = $where;
	}

	/**
	 * 条件付き判定にセットする
	 * @param string $if_field
	 * @param string $if_value
	 */
	function set_void($if_field, $if_value) {
		$this->if_field = $if_field;
		$this->if_value = $if_value;
	}

	function valid() {
		if (isset($this->if_field) && array_key_exists($this->if_field, $this->input) && $this->input[$this->if_field] == $this->if_value
		) {
			return true;
		}
		if (parent::valid()) {
			$this->record = Ae_Db_Utl::get_record(
							$this->db, $this->table, $this->input[$this->field], $this->columns, $this->where
			);
			if (!count($this->record)) {
				$this->is_valid = false;
			}
		}
		return $this->is_valid;
	}

	private $db;
	private $table;
	private $columns;
	private $where;
	private $if_field;
	private $if_value;

}
