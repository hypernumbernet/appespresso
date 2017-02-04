<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Db;

/**
 * SQLビルダー用値保持クラス
 * @version 2.0.0
 */
class SqlValue
{

    /** @var string */
    private $value;

    /** @var int */
    public $type;

    /** @var bool 直接SQLに埋め込むスイッチ */
    public $direct = false;

    /**
     * @param mixed $value
     * @param int|bool $type
     */
    public function __construct($value = null, $type = null)
    {
        $this->value = $value;
        if (isset($type)) {
            if ($type === true) {
                $this->direct = true;
            } else {
                $this->type = $type;
            }
        } else {
            $this->type = \PDO::PARAM_STR;
        }
    }

    /**
     * 数値指定時に数値変換する。
     *
     * @return mixed
     */
    public function getValue()
    {
        if ($this->type === \PDO::PARAM_INT) {
            return (int) $this->value;
        }
        return $this->value;
    }

}
