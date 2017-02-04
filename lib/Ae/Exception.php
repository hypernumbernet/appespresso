<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */

namespace Ae;

/**
 * 多言語対応例外メッセージ
 * @version 1.0.0
 */
class Exception extends \Exception
{

    /**
     * メッセージID変換とエラー詳細
     *
     * @param string $group メッセージグループ名
     * @param string $name メッセージ名称
     * @param string $code エラーコード
     */
    public function __construct($group, $name, $code = 0)
    {
        $class = '\\Ae\\Lang\\' . \AE_LANG . '\\Message';
        $m = $class::$$group[$name] . "\n" .
                $this->getFile() . '(' . $this->getLine() . ")\n" .
                $this->getTraceAsString();
        parent::__construct($m, $code);
    }

}
