<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Db;

/**
 * DB基底クラス
 *
 * @version 2.1.0
 */
abstract class DbcBase extends \PDO
{

    /** @var string ホスト名 */
    public $host;

    /** @var int ポート番号 */
    public $port;

    /** @var string データベース名 */
    public $database;

    /** @var string ユーザー名 */
    public $user;

    /** @var string パスワード */
    public $pass;

    /** @var string ソケット名。CGIで動かす時に環境によっては必要 */
    public $socket;

    /** @var string 文字セット */
    public $charset;

    /**
     * PDOのコンストラクタを無効化している。
     */
    public function __construct()
    {

    }

    /**
     * データベースに接続する。
     */
    abstract public function open();

    /**
     * テーブルのロックを取得する。
     *
     * @param string|string[] 配列の場合は複数テーブル
     * @param int \Ae\Dbの定数
     */
    abstract public function lockTable($table_name, $mode);

    /**
     * テーブルのロックを解除する。
     */
    abstract public function unlock();

    /**
     * テーブルの列一覧を取得する。
     *
     * @param string テーブル名
     * @return string[]
     */
    abstract public function columns($table);

    /**
     * テーブルの一覧を取得する。
     *
     * @return string[]
     */
    abstract public function tables();

    /**
     * テーブルの存在を調べる。
     *
     * @param string テーブル名。LIKE検索有効
     * @return bool
     */
    abstract public function existsTable($table);

    /**
     * SQL識別子をDB固有の文字で囲う。identifier quote character
     *
     * @param string $s
     * @return string
     */
    abstract public function iqc($s);

    /**
     * 主キーとなっているカラムを取得する。
     * <p>カラム順でソートされている。</p>
     *
     * @param string $table
     * @return string[]
     */
    abstract public function pKeyColumns($table);

    /**
     * カラムの情報を取得する。
     * <p>カラム順でソートされている。</p>
     *
     * @param string $table
     * @return string[]
     */
    abstract public function columnInfo($table);

    /**
     * SQL SELECT 文の組み立て。
     *
     * @param SqlParam $param
     */
    abstract public function sqlSelect($param);

    /**
     * SQL UPDATE 文の組み立て。
     *
     * @param SqlParam $param
     */
    abstract public function sqlUpdate($param);

    /**
     * SQL INSERT 文の組み立て。
     *
     * @param SqlParam $param
     */
    abstract public function sqlInsert($param);

    /**
     * SQL DELETE 文の組み立て。
     *
     * @param SqlParam $param
     */
    abstract public function sqlDelete($param);

    /**
     * DBの型名の解決
     *
     * @param string $type
     * @return int \PDO::PARAM
     */
    abstract public function paramType($type);

    /**
     * レコード数取得のSQL
     *
     * @param SqlParam $param
     * @return string
     */
    public function sqlCount($param)
    {
        return 'SELECT COUNT(*) FROM ' . $this->iqc($param->table) .
                $this->sqlWhere($param->where);
    }

    /**
     * カラム部分のSQLを作成する。
     *
     * @param string|string[] $c 配列は「.」で連結
     * @return string
     */
    protected function sqlColumns($c)
    {
        if (!$c) {
            return '*';
        }
        if (\is_array($c)) {
            foreach ($c as &$v) {
                if (\is_array($v)) {
                    foreach ($v as &$w) {
                        $w = $this->iqc($w);
                    }
                    $v = \implode('.', $v);
                } else {
                    $v = $this->iqc($v);
                }
            }
            return \implode(', ', $c);
        }
        return $this->iqc($c);
    }

    /**
     * WHERE句のSQL
     *
     * @param string|string[] $c 配列はAND展開される。配列の値が配列なら
     * そこだけOR展開される。
     * @return string
     */
    protected function sqlWhere($c)
    {
        if (!$c) {
            return '';
        }
        if (\is_array($c)) {
            foreach ($c as &$v) {
                if (\is_array($v)) {
                    $v = ' ( ' . \implode(' OR ', $v) . ' ) ';
                }
            }
            return ' WHERE ' . \implode(' AND ', $c);
        }
        return ' WHERE ' . $c;
    }

    /**
     * ORDER BY句のSQL
     * <p>ユーザー入力を想定して必ずエスケープをしている。</p>
     *
     * @param string|SqlOrderBy|SqlOrderBy[] $c
     * @return string
     */
    protected function sqlOderBy($c)
    {
        if (!$c) {
            return '';
        }
        if (\is_array($c)) {
            $a = [];
            foreach ($c as $v) {
                $a[] = $this->sqlOneOderBy($v);
            }
            return ' ORDER BY ' . \implode(', ', $a);
        } elseif (\is_string($c)) {
            return ' ORDER BY ' . $this->iqc($c);
        }
        return ' ORDER BY ' . $this->sqlOneOderBy($c);
    }

    /**
     * ORDER BY句のSQLの1カラム
     * <p>ユーザー入力を想定して必ずエスケープをしている。</p>
     *
     * @param SqlOrderBy $c
     * @return string
     */
    protected function sqlOneOderBy($c)
    {
        $s = $c->name;
        if (\is_array($s)) {
            foreach ($s as &$v) {
                $v = $this->iqc($v);
            }
            $s = \implode('.', $s);
        } else {
            $s = $this->iqc($s);
        }
        if ($c->desc) {
            $s .= ' DESC';
        }
        return $s;
    }

    /**
     * LIMIT句のSQL
     *
     * @param int $c
     * @return string
     */
    protected function sqlLimit($c)
    {
        if (\is_null($c) || !\is_numeric($c)) {
            $c = 1;
        }
        return ' LIMIT ' . $c;
    }

    /**
     * OFFSET句のSQL
     *
     * @param int $c
     * @return string
     */
    protected function sqlOffset($c)
    {
        if (\is_null($c) || !\is_numeric($c)) {
            return '';
        }
        return ' OFFSET ' . $c;
    }

    /**
     * UPDATE SET句のSQL
     *
     * @param SqlValue[] $values
     * @return string
     */
    protected function sqlSet($values)
    {
        $a = [];
        foreach ($values as $k => $v) {
            if ($v->direct) {
                $a[] = $k . ' = ' . $v->getValue();
            } else {
                $a[] = $this->iqc($k) . ' = ?';
            }
        }
        return \implode(', ', $a);
    }

    /**
     * INSERT VALUES句のSQL
     *
     * @param SqlParam $param
     * @return string
     */
    protected function sqlValues($param)
    {
        $c = [];
        $a = [];
        $param->binds = [];
        foreach ($param->values as $k => $v) {
            $c[] = $this->iqc($k);
            if ($v->direct) {
                $a[] = $v->getValue();
            } else {
                $a[] = '?';
                $param->binds[] = $v;
            }
        }
        $s = ' ( ' .
                \implode(',', $c) .
                ' ) VALUES ( ' .
                \implode(',', $a) .
                ' ) ';
        return $s;
    }

}
