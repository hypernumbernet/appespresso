<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Db;

/**
 * Db Utility
 *
 * DBに関しての典型的な操作が集められている。
 *
 * 疑問符プレースホルダを使用する方式のみ利用している。
 *
 * @version 3.0.0
 * @deprecated Dho に移行中
 */
class Utl
{

    /**
     * 条件にあうデータ件数を取得
     *
     * $bindsが配列でない場合、int変換され数値としてバインドされ、
     * $where条件の?に埋め込まれる。
     *
     * $bindsが配列の場合、$where条件の?に埋め込まれる複数の値となる。
     *
     * $bindsの配列の値は、[値, 型(PDO::PARAM定数)]で指定し、
     * 型が省略されるとPDO::PARAM_STRになる。
     *
     * $whereには、適切な数の?がなければならない。
     *
     * @param DbcBase $db
     * @param string $table
     * @param mixed|array $binds
     * @param string $where
     * @return int
     */
    public static function count($db, $table, $binds = array(), $where = '')
    {
        $s = 'SELECT COUNT(*) FROM ' . $table;
        if ($where) {
            $s .= ' WHERE ' . $where;
        }
        $stt = $db->prepare($s);
        if (is_array($binds)) {
            $i = 1;
            foreach ($binds as $x) {
                if (isset($x[1])) {
                    $stt->bindValue($i ++, $x[0], $x[1]);
                } else {
                    $stt->bindValue($i ++, $x[0]);
                }
            }
        } else {
            $stt->bindValue(1, (int) $binds, PDO::PARAM_INT);
        }
        $stt->execute();
        $r = $stt->fetch(PDO::FETCH_NUM);
        if ($r) {
            return (int) $r[0];
        } else {
            return 0;
        }
    }

    /**
     * 指定されたidのレコードを一件だけ取得
     *
     * $bindsが配列の場合、$where条件の?に埋め込まれる複数の値となる。
     *
     * $bindsの配列の値は、[値, 型(PDO::PARAM定数)]で指定し、
     * 型が省略されるとPDO::PARAM_STRになる。
     *
     * $bindsがnullの場合、値を取得しに行かない。
     *
     * $bindsがいずれでもない場合、int変換され数値としてバインドされ、
     * $where条件の?に埋め込まれる。
     *
     * $whereには、適切な数の?がなければならない。
     *
     * 複数レコードが取得された場合、不定でその内の一つが返される。
     *
     * @param DbcBase $db
     * @param string $table サニタイズなし
     * @param mixed|array $binds idまたは値リスト
     * @param string $where
     * @param string|array $columns
     * @return array 取得したレコード。取得されない場合は空配列
     */
    public static function getRecord($db, $table, $binds, $where = 'id = ?', $columns = '*')
    {
        if (is_null($binds)) {
            return array();
        }
        $s = 'SELECT ';
        if (is_array($columns)) {
            $s .= implode(' , ', $columns);
        } else {
            $s .= $columns;
        }
        $s .= ' FROM ' . $table;
        if ($where) {
            $s .= ' WHERE ' . $where;
        }
        $stt = $db->prepare($s);
        if (is_array($binds)) {
            $i = 1;
            foreach ($binds as $x) {
                if (isset($x[1])) {
                    $stt->bindValue($i ++, $x[0], $x[1]);
                } else {
                    $stt->bindValue($i ++, $x[0]);
                }
            }
        } else {
            $stt->bindValue(1, (int) $binds, PDO::PARAM_INT);
        }
        $stt->execute();
        $r = $stt->fetchAll(PDO::FETCH_ASSOC);
        if (count($r)) {
            return $r[0];
        } else {
            return array();
        }
    }

    /**
     * テーブルからレコードを削除
     *
     * $bindsが配列でない場合、int変換され数値としてバインドされ、
     * $where条件の?に埋め込まれる。
     *
     * 配列の場合、$where条件の?に埋め込まれる複数の値となる。
     *
     * その配列の値は、[値, 型(PDO::PARAM定数)]で指定し、
     * 型が省略されるとPDO::PARAM_STRになる。
     *
     * $whereには、適切な数の?がなければならない。
     *
     * @param DbcBase $db
     * @param string $table
     * @param mixed|array $binds
     * @param string $where
     * @return int 削除件数
     */
    public static function delete($db, $table, $binds, $where = 'id = ?')
    {
        $s = 'DELETE FROM ' . $table;
        if ($where) {
            $s .= ' WHERE ' . $where;
        }
        $stt = $db->prepare($s);
        if (is_array($binds)) {
            $i = 1;
            foreach ($binds as $x) {
                if (isset($x[1])) {
                    $stt->bindValue($i ++, $x[0], $x[1]);
                } else {
                    $stt->bindValue($i ++, $x[0]);
                }
            }
        } else {
            $stt->bindValue(1, (int) $binds, PDO::PARAM_INT);
        }
        $stt->execute();
        return $stt->rowCount();
    }

    /**
     * レコードを更新
     *
     * $values配列の値が配列でない場合、生SQL文になる。
     *
     * $values配列の値が配列の場合、無害化処理がされる。
     *
     * その配列の値は、[値, 型(PDO::PARAM定数)]で指定し、
     * 型が省略されるとPDO::PARAM_STRになる。
     *
     * 値をnullにすると、update文から省かれる。
     *
     * $bindsが配列の場合、$where条件の?に埋め込まれる複数の値となる。
     *
     * その配列の値は、[値, 型(PDO::PARAM定数)]で指定し、
     * 型が省略されるとPDO::PARAM_STRになる。
     *
     * $bindsがnullの場合、$where条件が無視される。
     *
     * $bindsがいずれでもない場合、int変換され数値としてバインドされ、
     * $where条件の?に埋め込まれる。
     *
     * $whereには、適切な数の?がなければならない。
     *
     * @param DbcBase $db
     * @param string $table
     * @param array $values key:カラム, val:値(array[]型でbind)
     * @param mixed|array $binds
     * @param string $where
     * @return int 件数
     */
    public static function update($db, $table, $values, $binds, $where = 'id = ?')
    {
        $s = 'UPDATE ' . $table . ' SET ';
        $a = array();
        $w = array();
        foreach ($values as $k => $v) {
            if (is_array($v)) {
                if (!is_null($v[0])) {
                    $a[] = $k . ' = ?';
                    $w[] = $v;
                }
            } else {
                $a[] = $k . ' = ' . $v;
            }
        }
        $s .= implode(' , ', $a);
        if (!is_null($binds)) {
            $s .= ' WHERE ' . $where;
            if (is_array($binds)) {
                foreach ($binds as $v) {
                    $w[] = $v;
                }
            } else {
                $w[] = array((int) $binds, PDO::PARAM_INT);
            }
        }
        $stt = $db->prepare($s);
        $n = 1;
        foreach ($w as $x) {
            if (isset($x[1])) {
                $stt->bindValue($n ++, $x[0], $x[1]);
            } else {
                $stt->bindValue($n ++, $x[0]);
            }
        }
        $stt->execute();
        return $stt->rowCount();
    }

    /**
     * INSERTする。
     *
     * $values配列の値が配列でない場合、生SQL文になる。
     *
     * $values配列の値が配列の場合、無害化処理がされる。
     *
     * その配列は、[値, 型(PDO::PARAM定数)]で指定し、
     * 型が省略されるとPDO::PARAM_STRになる。
     *
     * 値をnullにすると、insert文から省かれる。
     *
     * $serialは、PostgreSQLで利用される。
     *
     * @param DbcBase $db
     * @param string $table
     * @param array $values key:カラム, val:値(array[]型でbind)
     * @param string $serial = 'id' 自動番号の列名
     * @return int 付加された自動番号
     */
    public static function insert($db, $table, $values = array(), $serial = 'id')
    {
        $a1 = array();
        $a2 = array();
        $vl = array();
        foreach ($values as $k => $v) {
            $a1[] = $k;
            if (is_array($v)) {
                if (!is_null($v[0])) {
                    $a2[] = '?';
                    $vl[] = $v;
                }
            } else {
                $a2[] = $v;
            }
        }
        $s = 'INSERT INTO ' . $table . ' ( ' .
                implode(',', $a1) .
                ' ) VALUES ( ' .
                implode(',', $a2) .
                ' ) ';
        $stt = $db->prepare($s);
        $n = 1;
        foreach ($vl as $x) {
            if (isset($x[1])) {
                $stt->bindValue($n ++, $x[0], $x[1]);
            } else {
                $stt->bindValue($n ++, $x[0]);
            }
        }
        $stt->execute();
        if ($serial) {
            $q = $table . '_' . $serial . '_seq';
            return $db->lastInsertId($q);
        }
    }

    /**
     * DB時刻と現在時刻の差を時で返す。
     *
     * @param string $dbtime
     * @return float
     */
    public static function getHourPast($dbtime)
    {
        $hour = (int) substr($dbtime, 11, 2); // hour
        $minute = (int) substr($dbtime, 14, 2); // minute
        $second = (int) substr($dbtime, 17, 2); // second
        $month = (int) substr($dbtime, 5, 2); // month
        $day = (int) substr($dbtime, 8, 2); // day
        $year = (int) substr($dbtime, 0, 4); // year
        $now = time();
        $dbt = mktime($hour, $minute, $second, $month, $day, $year);
        return floor(($now - $dbt) / 3600);
    }

    /**
     * 時間数から経過時間を案内する文字列を返す。
     *
     * @param int $hour
     * @return string
     */
    public static function getPastCaption($hour)
    {
        $hpd = 24;
        $hpm = $hpd * 30.5;
        $hpy = $hpd * 365;
        $msg = 'Ae_Msg_' . AE_LANG . '_Db';
        if ($hour < $hpd) {
            return $hour . $msg::AGO_HOUR;
        } elseif ($hour < $hpm) {
            return floor($hour / $hpd) . $msg::AGO_DAY;
        } elseif ($hour < $hpy) {
            return floor($hour / $hpm) . $msg::AGO_MONTH;
        } else {
            return floor($hour / $hpy) . $msg::AGO_YEAR;
        }
    }

    /**
     * LIKE句に使用するワイルドカードをエスケープする。
     *
     * ESCAPE を付加し、prepare()で埋め込む。
     *
     * %記号が連続しているデータの抽出がMySQLの仕様でできない。
     *
     * @param string $param
     * @param string $escape
     * @return string
     */
    public static function escapeLike($param, $escape = '!')
    {
        $param = str_replace($escape, $escape . $escape, $param);
        $param = preg_replace('/(' . $escape . '+)/u', $escape . '$1', $param);
        $param = str_replace('%', $escape . '%', $param);
        $param = str_replace('_', $escape . '_', $param);
        return $param;
    }

    /**
     * テーブルから全レコードを取得する。
     *
     * @param DbcBase $db
     * @param string $table
     * @param string $order
     * @return array
     */
    public static function selectAll($db, $table, $order = null)
    {
        $s = 'SELECT * FROM ' . $table;
        if (!empty($order)) {
            $s .= ' ORDER BY ' . $order;
        }
        $t = $db->query($s);
        return $t->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * テーブルから条件付きでレコードを取得する。
     *
     * $bindsの配列の値は、[値, 型(PDO::PARAM定数)]で指定し、
     * 型が省略されるとPDO::PARAM_STRになる。
     *
     * $whereには、適切な数の?がなければならない。
     *
     * @param DbcBase $db
     * @param string $table
     * @param array $binds
     * @param string $where
     * @param array|string $columns
     * @return array
     */
    public static function selectWhere($db, $table, $binds = array(), $where = '', $columns = '*')
    {
        $s = 'SELECT ';
        if (is_array($columns)) {
            $s .= implode(' , ', $columns);
        } else {
            $s .= $columns;
        }
        $s .= ' FROM ' . $table;
        if ($where) {
            $s .= ' WHERE ' . $where;
        }
        $stt = $db->prepare($s);
        $n = 1;
        foreach ($binds as $x) {
            if (isset($x[1])) {
                $stt->bindValue($n ++, $x[0], $x[1]);
            } else {
                $stt->bindValue($n ++, $x[0]);
            }
        }
        $stt->execute();
        return $stt->fetchAll(PDO::FETCH_ASSOC);
    }

}
