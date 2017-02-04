<?php

/**
 * 有効な日付かどうか日本の年号付きで調べる
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_DateJpEra extends Ae_Validator_abstract {

	public $era;
	public $year;
	public $month;
	public $day;
	public $input;
	public $c_year; //Christian era

	/**
	 * @param string $year
	 * @param string $month
	 * @param string $day
	 * @param string $era = ''
	 * @param array $input = & $_POST
	 */

	function __construct($year, $month, $day, $era = '', $input = null) {
		$this->era = $era;
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
		if (is_null($input)) {
			$this->input = & $_POST;
		} else {
			$this->input = $input;
		}
	}

	function valid() {
		if (!array_key_exists($this->year, $this->input) ||
				!array_key_exists($this->month, $this->input) ||
				!array_key_exists($this->day, $this->input)
		) {
			return true;
		}
		if ($this->input[$this->year] == '') {
			return true;
		}
		if (!ctype_digit($this->input[$this->year]
						. $this->input[$this->month]
						. $this->input[$this->day])
		) {
			return $this->is_valid = false;
		}
		$y = (int) $this->input[$this->year];
		if ($y == 0) {
			return $this->is_valid = false;
		}
		$m = (int) $this->input[$this->month];
		$d = (int) $this->input[$this->day];
		if (array_key_exists($this->era, $this->input)) {
			switch ($this->input[$this->era]) {
				case '平成':
					$y += 1988;
					break;
				case '昭和':
					$y += 1925;
					break;
				case '大正':
					$y += 1910;
					break;
				case '明治':
					$y += 1867;
			}
		}
		$this->c_year = (string) $y;
		return $this->is_valid = checkdate($m, $d, $y);
	}

	/**
	 * 西暦に補正した日付文字列を返す。
	 * @return string
	 */
	function get_db_date() {
		if (is_null($this->c_year)) {
			return null;
		}
		return $this->c_year . '-' . $this->input[$this->month]
				. '-' . $this->input[$this->day];
	}

}
