<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * Httpユーティリティ
 *
 * @version 2.0.0
 */
class HttpUtl
{

    /**
     * 指定したURLにリダイレクト。URLエンコード必要なし
     *
     * @param string $url
     */
    public static function redirect($url)
    {
        \header('Location: ' . $url);
        exit();
    }

    /**
     * POSTされたか確認する。
     *
     * @return bool
     * @throws Exception
     */
    public static function byPost()
    {
        return \filter_input(\INPUT_SERVER, 'REQUEST_METHOD') === 'POST';
    }

    /**
     * パラメータありでGETされたか確認する。
     *
     * @return bool
     * @throws Exception
     */
    public static function byGet()
    {
        return !\is_null(\filter_input_array(\INPUT_GET));
    }

}
