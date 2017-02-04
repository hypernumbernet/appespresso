<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Db;

/**
 * データベース操作クラス。PDO PostgreSQL を使用
 * <p>PHP 5.3.6以降対応。dsnでcharsetを使用している。</p>
 *
 * @version 2.3.0
 */
class DbcPgsql extends DbcBase
{

    public function __construct()
    {
        $this->host = 'localhost';
        $this->port = '5432';
        $this->charset = 'UTF8';
    }

    public function open()
    {
        if (!isset($this->database)) {
            throw new \Ae\Exception('DB', 'DBNAME');
        }
        if (!isset($this->user)) {
            throw new \Ae\Exception('DB', 'USER');
        }
        if (!isset($this->pass)) {
            throw new \Ae\Exception('DB', 'PASS');
        }
        if (isset($this->socket)) {
            $dsn = 'pgsql:unix_socket=' . $this->socket;
        } else {
            $dsn = 'pgsql:host=' . $this->host . ';port=' . $this->port;
        }
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => true,
        ];
        \PDO::__construct(
                $dsn . ';dbname=' . $this->database .
                ";options='--client_encoding=" . $this->charset . "'",
                $this->user, $this->pass, $options
        );
    }

    public function lockTable($table_name, $mode = \Ae\Db::LOCK_WRITE_ONLY)
    {

    }

    public function unlock()
    {

    }

    public function columns($table)
    {

    }

    public function tables()
    {
        $r = [];
        $stt = parent::prepare('SELECT relname FROM pg_stat_user_tables');
        $stt->execute();
        foreach ($stt->fetchAll(\PDO::FETCH_NUM) as $v) {
            $r[] = $v[0];
        }
        return $r;
    }

    public function existsTable($table)
    {

    }

    public function iqc($s)
    {
        return '"' . \str_replace('"', '""', $s) . '"';
    }

    public function pKeyColumns($table)
    {
        $sql = 'SELECT a.attname FROM pg_index i ' .
                'JOIN pg_attribute a ' .
                'ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey) ' .
                'WHERE i.indrelid = \'' . $this->iqc($table) .
                '\'::regclass AND i.indisprimary ' .
                'ORDER BY a.attnum';
        $stt = parent::prepare($sql);
        $stt->execute();
        $r = [];
        foreach ($stt->fetchAll(\PDO::FETCH_NUM) as $v) {
            $r[] = $v[0];
        }
        return $r;
    }

    /**
     * PDO標準をオーバーライド。LASTVALを使用して、シーケンスidを不要に。
     *
     * @param string $seqname 省略時、テーブルのシーケンスが一つである必要あり
     * @return int セッション内で最後に発行されたシーケンス
     */
    public function lastInsertId($seqname = null)
    {
        if ($seqname) {
            return parent::lastInsertId($seqname);
        }
        $sql = 'SELECT LASTVAL()';
        $stt = $this->query($sql);
        return $stt->fetchColumn();
    }

    public function columnInfo($table)
    {
        $sql = 'SELECT column_name, column_default, ' .
                'is_nullable, data_type, character_maximum_length, ' .
                'numeric_precision, numeric_precision_radix, ' .
                'numeric_scale, datetime_precision, udt_name ' .
                'FROM information_schema.columns ' .
                'WHERE table_name = ? ' .
                'ORDER BY ordinal_position';
        $stt = parent::prepare($sql);
        $stt->bindValue(1, $table, \PDO::PARAM_STR);
        $stt->execute();
        return $stt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * @param SqlParam $param
     */
    public function sqlSelect($param)
    {
        return 'SELECT ' .
                $this->sqlColumns($param->columns) .
                ' FROM ' . $this->iqc($param->table) .
                $this->sqlWhere($param->where) .
                $this->sqlOderBy($param->order) .
                $this->sqlLimit($param->limit) .
                $this->sqlOffset($param->offset);
    }

    /**
     * @param SqlParam $param
     */
    public function sqlUpdate($param)
    {
        return 'UPDATE ' .
                $this->iqc($param->table) .
                ' SET ' .
                $this->sqlSet($param->values) .
                $this->sqlWhere($param->where);
    }

    /**
     * @param SqlParam $param
     */
    public function sqlInsert($param)
    {
        return 'INSERT INTO ' .
                $this->iqc($param->table) .
                $this->sqlValues($param);
    }

    /**
     * @param SqlParam $param
     */
    public function sqlDelete($param)
    {
        return 'DELETE FROM ' .
                $this->iqc($param->table) .
                $this->sqlWhere($param->where);
    }

    public function paramType($type)
    {
        $i = [
            'smallint' => \PDO::PARAM_INT,
            'integer' => \PDO::PARAM_INT,
            'bigint' => \PDO::PARAM_INT,
            'bytea' => \PDO::PARAM_LOB,
        ];
        return isset($i[$type]) ? $i[$type] : \PDO::PARAM_STR;
    }

}
