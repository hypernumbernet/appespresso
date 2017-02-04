<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * 文字列操作など便利な関数
 *
 * @version 1.1.0
 */
class Utl
{

    /**
     * 文字列をHTMLへ適切に変換します。文字幅整形機能付き
     *
     * @param string|string[] $str
     * @param int $len カットする文字列長
     * @return string
     */
    public static function textToHtml($str, $len = 0)
    {
        $br = '<br>';
        if (\is_array($str)) {
            $str = \implode(',', $str);
        }
        if ($len) {
            $str = \mb_strimwidth($str, 0, $len, '...');
            $br = '';
        }
        $str = \htmlspecialchars($str, \ENT_QUOTES);
        $f = 'return \' \'.str_repeat(\'&nbsp;\',strlen($m[0])-1);';
        $str = \preg_replace_callback('/ {2,}/', \create_function('$m', $f),
                $str);
        $str = \str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $str);
        $str = \str_replace("\r\n", $br, $str);
        $str = \str_replace("\n", $br, $str);
        $str = \str_replace("\r", $br, $str);
        return $str;
    }

    /**
     * 文字列をバイナリ数値文字列化
     *
     * @param string $str
     * @return string
     */
    public static function strToHex($str)
    {
        $hex = '';
        for ($i = 0; $i < \strlen($str); ++$i) {
            $hex .= \dechex(\ord($str[$i]));
        }
        return $hex;
    }

    /**
     * 配列の任意の位置へ一つの要素を挿入する
     *
     * @param array& $input 挿入される配列 (参照渡し)
     * @param mixed $insert 挿入する値 (型変換なし)
     * @param string $offset 挿入位置 (先頭は0)
     */
    public static function arrayInsert(& $input, $insert, $offset = 0)
    {
        $tail = \array_splice($input, $offset);
        $input[] = $insert;
        $input = \array_merge($input, $tail);
    }

    /**
     * URLのqueryをデコードして配列へ格納
     *
     * @param string $s
     * @return array
     */
    public static function queryDecode($s)
    {
        $r = array();
        foreach (\explode('&', $s) as $v) {
            if (!$v) {
                continue;
            }
            $a = \explode('=', $v);
            $n = \count($a);
            if ($n == 1) {
                $r[\urldecode($a[0])] = null;
            } elseif ($n == 2) {
                $r[\urldecode($a[0])] = \urldecode($a[1]);
            }
        }
        return $r;
    }

    /**
     * 指定ファイルに日時付きでロギング
     *
     * @param string $log
     * @param string $file
     */
    public static function log($log, $file)
    {
        $t = new \DateTime();
        \error_log($t->format('Y-m-d H:i:s ') . $log . "\n", 3, $file);
    }

    /**
     * 配列から値を配列とキーの存在を確かめてから取り出す。
     *
     * @param mixed $var
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public static function arrayVal($var, $key, $default = null)
    {
        if (!\is_array($var)) {
            return $default;
        }
        return \array_key_exists($key, $var) ? $var[$key] : $default;
    }

    /**
     * Noticeエラーを回避してarrayから値を取得する。
     * <p>PHP 7 以上ではnull合体演算子「??」が利用可能</p>
     *
     * @param array $var
     * @param mixed $default
     * @return mixed
     */
    public static function get(&$var, $default = null)
    {
        return isset($var) ? $var : $default;
    }

}
