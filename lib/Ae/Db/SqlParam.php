<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Db;

/**
 * SQLビルダー用パラメータクラス
 * <pre>
 * SELECT文生成のために必要な値を集約する。
 * 集約したら各アダプターにSQL文字列化を依頼する。
 * </pre>
 * @version 1.0.0
 */
class SqlParam
{

    /** @var string|string[] テーブル */
    public $table;

    /** @var string|string[] カラムリスト */
    public $columns;

    /** @var SqlValue[] UPDATE,INSERT時のカラム値 key:カラム名 */
    public $values;

    /** @var int|SqlValue[] prepare文埋め込み値リスト */
    public $binds;

    /** @var string|string[] 検索条件 */
    public $where;

    /** @var string|SqlOrderBy[] 並び替え */
    public $order;

    /** @var int 件数制限 */
    public $limit;

    /** @var int 取得位置 */
    public $offset;

    /** @var string|string[] 結合テーブル */
    public $join;

    /**
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * 単純配列からvalues値を一括セット
     *
     * @param array $values 2次元配列。
     * <pre>
     * [
     * key => [値, タイプ(省略時\PDO::PARAM_STR、trueでダイレクト指定],
     * key => 値(配列でない場合タイプ省略と同等),
     * ...
     * ]
     * </pre>
     * @return \Ae\Db\SqlParam
     */
    public function value($values)
    {
        $r = [];
        foreach ($values as $key => $value) {
            $r[$key] = $this->getSqlValue($value);
        }
        $this->values = $r;
        return $this;
    }

    /**
     * 単純配列からbinds値を一括セット
     *
     * @param array $binds 2次元配列。
     * <pre>
     * [
     * [値, タイプ(省略時\PDO::PARAM_STR、trueでダイレクト指定],
     * 値(配列でない場合タイプ省略と同等),
     * ...
     * ]
     * </pre>
     * @return \Ae\Db\SqlParam
     */
    public function bind($binds)
    {
        $r = [];
        foreach ($binds as $value) {
            $r[] = $this->getSqlValue($value);
        }
        $this->binds = $r;
        return $this;
    }

    private function getSqlValue($value)
    {
        if (!\is_array($value)) {
            return new \Ae\Db\SqlValue($value);
        }
        return new \Ae\Db\SqlValue($value[0], $value[1]);
    }

}
