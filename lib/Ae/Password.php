<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * パスワード関連のユーティリティ
 * 
 * @version 2.0.0
 */
class Password
{

    const FULL = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';
    const ROMAN_NUMBER = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    const ROMAN = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const NUMBER = '0123456789';
    const ROMAN_LOWER = 'abcdefghijklmnopqrstuvwxyz';
    const ROMAN_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /** 誤読しがちな文字なし */
    const EASY_LOWER = 'abcdefghijkmnprstuvwxy345678';

    /** 誤読しがちな文字なし */
    const EASY_UPPER = 'ABCDEFGHJKLMNPRSTUVWXY345678';

    /** 誤読しがちな文字なし */
    const EASY_ROMAN = 'ABCDEFGHJKLMNPRSTUVWXYabcdefghijkmnprstuvwxy345678';

    /**
     * 英数字からランダムな文字列を生成
     * 
     * @param int $length
     * @param string $str
     * @return string
     */
    static function make($length = 8, $str = self::ROMAN_NUMBER)
    {
        $len = strlen($str) - 1;
        $password = '';
        for ($i = 0; $i < $length; ++$i) {
            $password .= $str[mt_rand(0, $len)];
        }
        return $password;
    }

}
