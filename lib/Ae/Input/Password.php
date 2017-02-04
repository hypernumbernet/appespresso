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
class Password extends Base
{

    function html()
    {
        return '<input type="password" name="' . $this->name
                . '" value="' . $this->hs($this->value) . '"' . $this->attr
                . '>';
    }

}
