<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Db;

/**
 * データベース操作クラス。PDO MySQL を使用
 *
 * PHP 5.3.6以降対応。dsnでcharsetを使用している。
 *
 * @version 2.0.0
 */
class DbcMysql extends DbcBase
{

    public function __construct()
    {
        $this->host = 'localhost';
        $this->port = '3306';
        $this->charset = 'utf8';
    }

    public function open()
    {
        if (!isset($this->database)) {
            $msg = '\\Ae\\Msg\\' . AE_LANG . '\\Db';
            throw new \Exception($msg::DBNAME . get_class($this));
        }
        if (!isset($this->user)) {
            $msg = '\\Ae\\Msg\\' . AE_LANG . '\\Db';
            throw new \Exception($msg::USER . get_class($this));
        }
        if (!isset($this->pass)) {
            $msg = '\\Ae\\Msg\\' . AE_LANG . '\\Db';
            throw new \Exception($msg::PASS . get_class($this));
        }
        if (isset($this->socket)) {
            $dsn = 'mysql:unix_socket=' . $this->socket . ';dbname='
                    . $this->database;
        } else {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->database
                    . ';port=' . $this->port;
        }
        \PDO::__construct(
                $dsn . ';charset=' . $this->charset, $this->user, $this->pass,
                array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)
        );
    }

    public function lockTable($table_name, $mode = \Ae\Db::LOCK_WRITE_ONLY)
    {
        if ($mode == \Ae\Db::LOCK_READ_WRITE) {
            $t = ' READ';
        } elseif ($mode == \Ae\Db::LOCK_WRITE_ONLY) {
            $t = ' WRITE';
        } else {
            $msg = '\\Ae\\Msg\\' . AE_LANG . '\\Db';
            throw new \Exception($msg::MODE . 'class: ' . get_class($this)
            . '; mode: ' . $mode);
        }
        if (is_array($table_name)) {
            $a = array();
            foreach ($table_name as $v) {
                $a[] = $v . $t;
            }
            $s = implode(',', $a);
        } else {
            $s = $table_name . $t;
        }
        parent::exec('LOCK TABLES ' . $s);
    }

    public function unlock()
    {
        parent::exec('UNLOCK TABLES');
    }

    public function columns($table)
    {
        $stt = parent::prepare('SHOW COLUMNS FROM ' . $table);
        $res = array();
        if ($stt->execute()) {
            $r = $stt->fetchAll();
            foreach ($r as $a) {
                foreach ($a as $i => $v) {
                    if ($i === 'Field') {
                        $res[] = $v;
                        break;
                    }
                }
            }
        }
        return $res;
    }

    public function tables()
    {
        $r = array();
        $stt = parent::prepare('SHOW TABLES');
        if ($stt->execute()) {
            foreach ($stt->fetchAll(\PDO::FETCH_NUM) as $v) {
                $r[] = $v[0];
            }
        }
        return $r;
    }

    public function existsTable($table)
    {
        $stt = parent::prepare("SHOW TABLES LIKE '" . $table . "'");
        if ($stt->execute()) {
            $r = $stt->fetchAll(\PDO::FETCH_NUM);
            return count($r) ? true : false;
        }
        return false;
    }

    public function iqc($s)
    {
        return '`' . str_replace('`', '``', $s) . '`';
    }

}
