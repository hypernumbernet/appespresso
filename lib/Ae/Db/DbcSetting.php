<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Db;

/**
 * DB接続設定クラス
 * 
 * @version 1.0.1
 */
class DbcSetting
{

    /** @var string DB種別。\Ae\Db::DB_ */
    public $adapter;

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

    /** @var string クライアントの文字コード(utf8固定のため無効) */
    public $charset;

}
