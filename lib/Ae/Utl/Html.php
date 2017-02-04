<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Utl;

/**
 * HTMLに関する便利関数
 * 
 * @version 2.0.0
 */
class Html
{

    /**
     * cssインポートhtml生成。キャッシュを無効にする。
     * 
     * @param string $file cssファイル名
     * @return string
     */
    static function loadCss($file)
    {
        if (file_exists($file)) {
            return '<link rel="stylesheet" href="' . $file . '?'
                    . date('YmdHis', filemtime($file)) . '">';
        } else {
            return '';
        }
    }

    /**
     * javascriptインポートhtml生成。キャッシュを無効にする。
     * 
     * @param string $file javascriptファイル名
     * @return string
     */
    static function loadJs($file)
    {
        if (file_exists($file)) {
            return '<script src="' . $file . '?'
                    . date('YmdHis', filemtime($file)) . '"></script>';
        } else {
            return '';
        }
    }

}
