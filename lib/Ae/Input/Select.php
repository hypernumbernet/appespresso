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
 * @version 2.1.0
 */
class Select extends Option
{

    /** @var bool selectのmultiple属性 */
    public $multi;

    public function html()
    {
        $s = '';
        if (\is_array($this->value)) {
            $c = $this->value;
        } else {
            $c = array($this->value);
        }
        $x = ' selected';
        $s .= '<select name="' . $this->name;
        if ($this->multi) {
            $s .= '[]';
        }
        $s .= '"' . $this->attr . '>';
        foreach ($this->options as $v) {
            if (isset($v[2])) {
                $s .= $v[2];
            }
            $s .= '<option';
            if (!isset($v[1])) {
                $v[1] = $v[0];
            }
            if (\is_null($v[0])) {
                if (\in_array($v[1], $c)) {
                    $s .= $x;
                }
            } else {
                $s .= ' value="' . $this->hs($v[0]) . '"';
                if (\in_array($v[0], $c)) {
                    $s .= $x;
                }
            }
            $s .= '>' . $v[1];
            if (isset($v[3])) {
                $s .= $v[3];
            }
        }
        $s .= '</select>';
        return $s;
    }

    /**
     * selectのmultiple属性設定
     *
     * @return Select
     */
    public function multi()
    {
        $this->multi = true;
        $this->attr .= ' multiple';
        return $this;
    }

}
