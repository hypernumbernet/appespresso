<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * データベース接続設定ローダー
 *
 * @version 2.0.0
 */
class Db
{

    const DB_MYSQL = 'DbcMysql';
    const DB_POSTGRESQL = 'DbcPgsql';
    const DB_SQLITE = 'DbcSqlite';
    const LOCK_READ_WRITE = 1;
    const LOCK_WRITE_ONLY = 2;

    /**
     * 接続に使用するアダプター名とインクルードの対応
     */
    private static $adapter = array(
        self::DB_MYSQL,
        self::DB_POSTGRESQL,
        self::DB_SQLITE,
    );

    /**
     * ファクトリーメソッド
     *
     * @param Db\DbcSetting $config 接続設定
     * @return Db\DbcBase
     * @throws Exception
     */
    public static function of($config)
    {
        $adapter = $config->adapter;
        if (!in_array($adapter, self::$adapter)) {
            $msg = '\\Ae\\Msg\\' . \AE_LANG . '\\Db';
            throw new \Exception($msg::ADAPTER . $adapter);
        }
        $class = '\\Ae\\Db\\' . $adapter;
        $db = new $class;
        if (isset($config->host)) {
            $db->host = $config->host;
        }
        if (isset($config->port)) {
            $db->port = $config->port;
        }
        if (isset($config->database)) {
            $db->database = $config->database;
        } elseif (isset($config->user)) {
            $db->database = $config->user;
        }
        if (isset($config->user)) {
            $db->user = $config->user;
        } elseif (isset($config->database)) {
            $db->user = $config->database;
        }
        if (isset($config->pass)) {
            $db->pass = $config->pass;
        }
        if (isset($config->socket)) {
            $db->socket = $config->socket;
        }
        return $db;
    }

}
