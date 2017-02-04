<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae\Db;

/**
 * 汎用的 Data Handling Object
 *
 * @version 2.0.0
 */
class Dho
{

    /** @var int 返すレコードの形式 */
    public $fetchStyle = \PDO::FETCH_ASSOC;

    /** @var DbcBase */
    private $db;

    /**
     * @param DbcBase $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * 指定されたレコードを1件だけ取得する。
     *
     * @param SqlParam $param
     * @return array 取得したレコード。取得されない場合は空配列
     */
    public function record($param)
    {
        if (\is_null($param->binds)) {
            return [];
        }
        $s = $this->db->sqlSelect($param);
        $stt = $this->db->prepare($s);
        $this->bind($stt, $param->binds);
        $stt->execute();
        $r = $stt->fetchAll($this->fetchStyle);
        if (\count($r)) {
            return $r[0];
        } else {
            return [];
        }
    }

    /**
     * 指定された範囲のレコードを取得する。
     *
     * @param SqlParam $param
     * @return array 取得したレコード。取得されない場合は空配列
     */
    public function records($param)
    {
        $s = $this->db->sqlSelect($param);
        $stt = $this->db->prepare($s);
        $this->bind($stt, $param->binds);
        $stt->execute();
        return $stt->fetchAll($this->fetchStyle);
    }

    /**
     * 条件にあうデータ件数を取得
     *
     * @param SqlParam $param
     * @return int
     */
    public function count($param)
    {
        $s = $this->db->sqlCount($param);
        $stt = $this->db->prepare($s);
        $this->bind($stt, $param->binds);
        $stt->execute();
        $r = $stt->fetch(\PDO::FETCH_NUM);
        if ($r) {
            return (int) $r[0];
        } else {
            return 0;
        }
    }

    /**
     * レコードを追加する。
     *
     * @param SqlParam $param
     * @param bool|string $wantsId
     * @return int 付加された自動番号
     */
    public function insert($param, $wantsId = true)
    {
        $s = $this->db->sqlInsert($param);
        $stt = $this->db->prepare($s);
        $this->bind($stt, $param->binds);
        $stt->execute();
        if (\is_string($wantsId)) {
            return $this->db->lastInsertId($wantsId);
        }
        if ($wantsId) {
            return $this->db->lastInsertId();
        }
    }

    /**
     * レコードを更新する。
     *
     * @param SqlParam $param
     * @return int 件数
     */
    public function update($param)
    {
        $s = $this->db->sqlUpdate($param);
        $stt = $this->db->prepare($s);
        $n = $this->bind($stt, $param->values);
        $this->bind($stt, $param->binds, $n);
        $stt->execute();
        return $stt->rowCount();
    }

    /**
     * レコードを更新する。
     *
     * @param SqlParam $param
     * @return int 件数
     */
    public function delete($param)
    {
        $s = $this->db->sqlDelete($param);
        $stt = $this->db->prepare($s);
        $this->bind($stt, $param->binds);
        $stt->execute();
        return $stt->rowCount();
    }

    /**
     * PDOステートメントに値を設定
     *
     * @param \PDOStatement $stt PDOステートメント
     * @param int|SqlValue[] $v 配列でないと単一int値となる
     * @param int $number
     * @return int bind number
     */
    private function bind($stt, $v, $number = 1)
    {
        if (\is_array($v)) {
            foreach ($v as $x) {
                if (!$x->direct) {
                    $stt->bindValue($number++, $x->getValue(), $x->type);
                }
            }
        } else {
            $stt->bindValue(1, $v, \PDO::PARAM_INT);
        }
        return $number;
    }

}
