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
class Checkbox extends Option
{

    function html()
    {
        $s = '';
        $x = ' checked';
        $s1 = '<label><input type="checkbox" name="' . $this->name;
        if (\count($this->options) > 1) {
            $s1 .= '[]';
        }
        $s1 .= '" value="';
        $s2 = '"' . $this->attr;
        if (\is_array($this->value)) {
            $c = $this->value;
        } else {
            $c = array($this->value);
        }
        foreach ($this->options as $v) {
            if (isset($v[2])) {
                $s .= $v[2];
            }
            $s .= $s1 . $this->hs($v[0]) . $s2;
            if (\in_array($v[0], $c)) {
                $s .= $x;
            }
            $s .= '>' . $v[1] . '</label>';
            if (isset($v[3])) {
                $s .= $v[3];
            }
        }
        return $s;
    }

}
