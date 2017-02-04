<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Db;

/**
 * SQLビルダー用 ORDER BY 情報保持クラス
 * @version 1.0.0
 */
class SqlOrderBy
{

    /** @var string|string[] テーブル名。配列で「.」で連結 */
    public $name;

    /** @var bool 降順フラグ */
    public $desc = false;

    public function __construct($name)
    {
        $this->name = $name;
    }

}
