<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Input;

/**
 * HTMLフォームの入力定義
 * 
 * @version 2.0.0
 */
abstract class Option extends Base
{

    /**
     * @var array select,radio,checkboxが使用
     * 
     * [[value, cap, before, after, info],...]
     * 0: value
     * 1: cap
     * 2: before
     * 3: after
     * 4: info
     */
    public $options = array();

    function caption($esc = true)
    {
        $ret = '';
        if (is_null($this->value)) {
            $ret = $this->capnull;
        }
        if (is_array($this->value)) {
            $a = array();
            foreach ($this->options as $v) {
                if (in_array($v[0], $this->value)) {
                    $a[] = $v[1];
                }
            }
            $ret = implode(', ', $a);
        } else {
            foreach ($this->options as $v) {
                if ($this->value == $v[0]) {
                    $ret = isset($v[4]) ? $v[4] : $v[1];
                }
            }
        }
        if ($esc) {
            return $this->hs($ret);
        } else {
            return $ret;
        }
    }

    /**
     * option追加
     * 
     * @param string $value value属性値
     * @param string $caption 表示名
     * @param string $before 項目の前に出力する特別なHTML
     * @param string $after 項目の後に出力する特別なHTML
     * @param string $info 確認表示へ出力する別の言葉
     * @return Option
     */
    function add($value, $caption = null, $before = null, $after = null, $info = null)
    {
        if (is_null($caption)) {
            $caption = $value;
        }
        $this->options[] = array($value, $caption, $before, $after, $info);
        return $this;
    }

    /**
     * optionを置き換え
     * 
     * [[値, 表示名,
     *   (項目の前に出力する特別なHTML),
     *   (項目の後に出力する特別なHTML),
     *   (確認表示へ出力する別の言葉)
     * ], ...]
     * 
     * @param array $a
     * @return Option
     */
    function options($a)
    {
        $this->options = $a;
        return $this;
    }

}
