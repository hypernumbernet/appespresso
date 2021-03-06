<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */

namespace Ae\Lang\ja;

/**
 * メッセージ集約クラス
 * @version 1.0.0
 */
class Message
{

    /** @var string[] データベースメッセージ */
    public static $DB = [
        'ACCESS' => 'データベース接続に失敗しました。',
        'ADAPTER' => 'サポートされていないデータベースです。',
        'CONNECT' => 'サーバー接続に失敗しました。',
        'DBNAME' => 'データベースが設定されていません。',
        'MODE' => 'サポートされていないロックモードです。',
        'PARAM' => 'パラメーターの数が一致しません。',
        'PASS' => '接続認証のパスワードが設定されていません。',
        'QUERY' => '不正なクエリーです。',
        'SOCKET' => 'ソケット接続はサポートされていません。',
        'USER' => '接続認証のユーザー名が設定されていません。',
        'ZERO' => 'パラメーターが1から開始されていません。',
        'AGO_HOUR' => '時間前',
        'AGO_DAY' => '日前',
        'AGO_MONTH' => 'ヶ月前',
        'AGO_YEAR' => '年前',
    ];

    /** @var string[] HTTPメッセージ */
    public static $HTTP = [
        'ENCODING' => '不正な文字コードを検出しました。',
    ];

    /** @var string[] LOGINメッセージ */
    public static $LOGIN = [
        'AGAIN' => 'ログインして下さい。',
        'BAN' => 'アクセス権限がありません。',
        'COOKIE' => 'Cookieを有効にしてログインしてください。',
        'DUP' => 'ユーザーが重複しています。管理者にお問い合わせ下さい。',
        'PASS' => 'パスワードが間違っています。',
        'ROLE' => 'ロールにトップページが定義されていません。',
        'SALT' => 'システムに保存されたパスワードの形式が間違っています。',
        'SESSION' => 'セッションが開始できませんでした。',
        'TIMEOUT' => '[[time]]秒以上操作をしませんでした。ログインしなおしてください。',
        'USER' => 'ユーザーが登録されていません。',
    ];

    /** @var string[] PAGERメッセージ */
    public static $PAGER = [
        'NEXT' => '次へ',
        'PREV' => '前へ',
    ];

    /** @var string[] バリデータメッセージ */
    public static $VALIDATOR = [
        'ERR_1' => 'ファイルサイズがシステムの制限を超えています。',
        'ERR_2' => 'ファイルサイズがフォームの制限を超えています。',
        'ERR_3' => 'ファイルが一部しかアップロードされませんでした。',
        'ERR_4' => 'ファイルが指定されていません。',
        'ERR_6' => 'テンポラリフォルダがありません。',
        'ERR_7' => 'ディスクへの書き込みに失敗しました。',
        'ERR_8' => 'アップロードが拡張モジュールによって停止しました。',
        'IMG_1' => 'サポートされていない画像ファイル形式です。',
        'IMG_2' => '拡張子と画像形式が一致しません。',
        'IMG_3' => 'アップロードされたファイルは画像ファイルとして認識できませんでした。',
    ];

}
