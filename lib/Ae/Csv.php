<?php

/**
 * CSVを扱う関数
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Csv {

	/**
	 * レコードを末尾に追加。
	 * @param string $csv_text
	 * @param array $new_record
	 * @param string $crlf 改行正規化
	 * @return string
	 */
	static function add_record($csv_text, $new_record, $crlf = '') {
		$new_fields = array();
		foreach ($new_record as $f) {
			$f = str_replace('"', '""', $f);
			if (preg_match('/[,"\r\n]/', $f)) {
				$f = '"' . $f . '"';
			}
			$new_fields[] = $f;
		}
		if ($crlf) {
			// 改行を\nに統一
			$o = str_replace("\r\n", "\n", $csv_text);
			$o = str_replace("\r", "\n", $o);
			// 前後の\nを削除
			$o = preg_replace('/^\n+/s', '', $o);
			$o = preg_replace('/\n+$/s', '', $o);
			// 指定された改行コードへ
			if ($crlf != "\n") {
				$o = str_replace("\n", $crlf, $o);
			}
		} else {
			// 末尾の改行を削除
			$o = preg_replace('/[\r\n]+$/s', '', $csv_text);
		}
		if ($o) {
			$o .= "\r\n";
		}
		return $o . implode(',', $new_fields);
	}

	/**
	 * 2次元配列からCSV文字列を作成
	 * @param array $records
	 * @return string
	 */
	static function from_array($records) {
		$new_records = array();
		foreach ($records as $r) {
			$new_fields = array();
			foreach ($r as $f) {
				if (preg_match('/[,"\r\n]/', $f)) {
					$f = str_replace('"', '""', $f);
					$f = '"' . $f . '"';
				}
				$new_fields[] = $f;
			}
			$new_records[] = implode(',', $new_fields);
		}
		return implode("\r\n", $new_records);
	}

	/**
	 * CSV文字列からデータを抽出
	 * @param string $csv_text
	 * @param string $sep
	 * @return array
	 * @tutorial 空白行読み飛ばし。
	 *           カンマ直後に引用符がない場合でも有効。
	 *           単一フィールド内で複数の引用符囲みが有効。
	 */
	static function get_array($csv_text, $sep = ',') {
		$records = array();
		$fields = array();
		$ff = false; // 引用符内フラグ
		$s1 = ''; // 文字レジスタ
		$ss = ''; // 列データストア
		$ln = strlen($csv_text) - 1;
		if ($ln < 0) {
			return array();
		}
		for ($i = 0; $i < $ln; ++$i) {
			$s1 = $csv_text[$i];
			if ($ff) {
				if ($s1 == '"') {
					if ($csv_text[$i + 1] == '"') {
						$ss .= '"';
						++$i;
						if ($i >= $ln) {
							$fields[] = $ss;
							$records[] = $fields;
							return $records;
						}
					} else {
						$ff = false;
					}
				} else {
					$ss .= $s1;
				}
			} else {
				if ($s1 == '"') {
					$ff = true;
				} elseif ($s1 == $sep) {
					$fields[] = $ss;
					$ss = '';
				} elseif ($s1 == chr(13) || $s1 == chr(10)) {
					$fields[] = $ss;
					if (count($fields) > 1 || $fields[0] != '') {
						$records[] = $fields;
					}
					if ($csv_text[$i + 1] == chr(10)) {
						++$i;
						if ($i >= $ln) {
							return $records;
						}
					}
					$ss = '';
					$fields = array();
				} else {
					$ss .= $s1;
				}
			}
		}
		$s1 = $csv_text[$i];
		if ($ff) {
			if ($s1 != '"') {
				$ss .= $s1;
			}
			$fields[] = $ss;
			$records[] = $fields;
		} else {
			if ($s1 == $sep) {
				$fields[] = $ss;
				$fields[] = '';
				$records[] = $fields;
			} elseif ($s1 == chr(13) || $s1 == chr(10)) {
				$fields[] = $ss;
				if (count($fields) > 1 || $fields[0] != '') {
					$records[] = $fields;
				}
			} else {
				$ss .= $s1;
				$fields[] = $ss;
				$records[] = $fields;
			}
		}
		return $records;
	}

}
