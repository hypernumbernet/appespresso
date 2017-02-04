<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Input;

/**
 * HTML5で追加された色々なtypeに対応
 * 
 * @version 2.0.0
 */
class FreeType extends Base
{

    /** @var string HTML5対応type */
    private $type;

    public function html()
    {
        return '<input type="' . $this->type . '" name="' . $this->name
                . '" value="' . $this->hs($this->value) . '"' . $this->attr
                . '>';
    }

    /**
     * type設定
     * 
     * @param string $s
     * @return FreeType
     */
    public function type($s)
    {
        $this->type = $s;
        return $this;
    }

}
