<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * HTMLフォームに関する総合的な各種操作
 *
 * @version 2.0.0
 */
class Form
{

    /** @var string DB直結時の配列保存形式 */
    const PREFIX_CSV = '[CSV]';

    /** @var string PREFIX_CSVの文字長 */
    const PREFIX_LEN = 5;

    /** @var Output 関連付けるテンプレート */
    public $template;

    /** @var Input\Base[] フォーム入力フィールド定義 */
    public $inputs = [];

    /** @var Validator\Base[] Validatorクラス配列 */
    public $validators = [];

    /** @var string メッセージサマリーの後ろに付け加えるHTML */
    public $end_html = '<br>';

    /** @var string メッセージサマリーの変数名 */
    public $panel_var = 'err';

    /** @var string データベースのテーブル名 */
    public $table;

    /** @var string[] null出力マスク */
    private $nulls = [];

    /**
     * HTMLフォーム
     *
     * @param Output $tpl テンプレート
     * @param string $table DBテーブル
     */
    public function __construct($tpl = null, $table = '')
    {
        $this->template = $tpl;
        $this->table = $table;
    }

    /**
     * テンプレートにフォーム部品を書き出す。
     */
    public function put()
    {
        foreach ($this->inputs as $k => $v) {
            $this->template->add($k, $v->html(), false);
        }
    }

    /**
     * 入力フィールドの定義を追加する。
     *
     * @param Input\Base $input 入力フィールド
     * @return $this
     */
    public function add($input)
    {
        $this->inputs[$input->name] = $input;
        return $this;
    }

    /**
     * テンプレートにフォームの値を書き出す。
     *
     * @param bool $hidden 値と同時にhidden部品を出力
     */
    public function putValues($hidden = false)
    {
        if ($this->f_put) {
            return;
        }
        $this->f_put = true;
        foreach ($this->inputs as $k => $v) {
            $this->template->add($k, $v->caption() . ($hidden ? $v->hidden() : ''), false);
        }
    }

    /**
     * inputの値を設定、または取得
     *
     * @param string $k
     * @param string $v
     * @return Form
     */
    public function value($k, $v = null)
    {
        if (\is_null($v)) {
            return \array_key_exists($k, $this->inputs) ?
                    $this->inputs[$k]->value : '';
        }
        if (\array_key_exists($k, $this->inputs)) {
            $this->inputs[$k]->value = $v;
        }
        return $this;
    }

    /**
     * 入力値からテンプレート内formフィールドへ展開する。
     *
     * @param int INPUT_ システム定数
     */
    public function fromInput($a = \INPUT_POST)
    {
        $p = new \Ae\HttpInput($a);
        foreach ($this->inputs as $k => $v) {
            $opt = null;
            if (($v instanceof Input\Checkbox && \count($v->options) > 1) || ($v instanceof Input\Select && $v->multi)) {
                $opt = \FILTER_REQUIRE_ARRAY;
            }
            $v->value = $p->get($k, $opt);
        }
    }

    /**
     * データベースからテンプレートへデータを埋め込む。
     *
     * @param PDO $db
     * @param int $id
     * @param string $id_column
     * @return array 結果レコード
     * @tutorial
     * チェックボックスでプレフィックスが適合する場合配列として処理する。
     * この場合エスケープ処理省略のためデータにカンマが使用できない。
     */
    public function fromDb($db, $id, $id_column = 'id')
    {
        $stt = $db->prepare('SELECT * FROM ' . $this->table
                . ' WHERE ' . $id_column . ' = ? ');
        $stt->bindValue(1, $id, PDO::PARAM_INT);
        $stt->execute();
        $r = $stt->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            foreach ($this->inputs as $x) {
                if (!isset($r[$x->column])) {
                    continue;
                }
                $x->value = $r[$x->column];
                if (\get_class($x) == 'Input\Checkbox' ||
                        \get_class($x) == 'Input\Select') {
                    $s1 = \substr($r[$x->column], 0, self::PREFIX_LEN);
                    if ($s1 == self::PREFIX_CSV) {
                        $s2 = \substr($r[$x->column], self::PREFIX_LEN);
                        $x->value = \explode(',', $s2);
                    }
                }
            }
        }
        return $r;
    }

    /**
     * INSERTする。
     *
     * @param PDO $db
     * @param array $columns 追加のカラム(key:カラム, val:値(array[]型でbind))
     * @param string $serial = 'id' 自動番号のカラム名
     * @return int 付加された自動番号
     */
    public function insert($db, $columns = array(), $serial = 'id')
    {
        $a1 = array();
        $a2 = array();
        $in = array();
        foreach ($this->inputs as $v) {
            if ($v->column) {
                $a1[] = $v->column;
                if ($this->asNull($v, $_POST)) {
                    $a2[] = 'NULL';
                } else {
                    $a2[] = '?';
                }
            }
        }
        foreach ($columns as $k => $v) {
            $a1[] = $k;
            if (is_array($v)) {
                $a2[] = '?';
                $in[] = $v;
            } else {
                $a2[] = $v;
            }
        }
        $s = 'INSERT INTO ' . $this->table . ' ( '
                . \implode(',', $a1) . ' ) VALUES ( '
                . \implode(',', $a2)
                . ' ) ';
        $stt = $db->prepare($s);
        $n = $this->bindValues($stt, $_POST);
        foreach ($in as $x) {
            if (isset($x[1])) {
                $stt->bindValue($n++, $x[0], $x[1]);
            } else {
                $stt->bindValue($n++, $x[0]);
            }
        }
        $stt->execute();
        if ($serial) {
            $q = $this->table . '_' . $serial . '_seq';
            return $db->lastInsertId($q);
        }
    }

    /**
     * UPDATEする。
     *
     * @param PDO $db
     * @param int,array[][2] $id id値またはwhere条件値。配列の順序はwhereと合致すること
     * @param array $columns 追加のカラム(key:カラム, val:値(array[]型でbind))
     * @param string $where
     */
    public function update($db, $id, $columns = array(), $where = 'id = ?')
    {
        $a1 = array();
        foreach ($this->inputs as $v) {
            if ($v->column) {
                if ($this->asNull($v, $_POST)) {
                    $a1[] = $v->column . ' = NULL ';
                } else {
                    $a1[] = $v->column . ' = ? ';
                }
            }
        }
        $in = array();
        foreach ($columns as $k => $v) {
            if (\is_array($v)) {
                $a1[] = $k . ' = ? ';
                $in[] = $v;
            } else {
                $a1[] = $k . ' = ' . $v;
            }
        }
        $sql = 'UPDATE ' . $this->table . ' SET '
                . \implode(',', $a1) . ' WHERE ' . $where;
        $stt = $db->prepare($sql);
        $n = $this->bindValues($stt, $_POST);
        foreach ($in as $x) {
            if (isset($x[1])) {
                $stt->bindValue($n++, $x[0], $x[1]);
            } else {
                $stt->bindValue($n++, $x[0]);
            }
        }
        if (\is_array($id)) {
            foreach ($id as $x) {
                if (isset($x[1])) {
                    $stt->bindValue($n++, $x[0], $x[1]);
                } else {
                    $stt->bindValue($n++, $x[0]);
                }
            }
        } else {
            $stt->bindValue($n, $id, PDO::PARAM_INT);
        }
        $stt->execute();
    }

    /**
     * Validator追加
     *
     * @param Validator\Base $vld
     * @param string $msg
     * @return Form
     */
    public function validator($vld, $msg)
    {
        $vld->set_error_summary($msg);
        $this->validators[] = $vld;
        return $this;
    }

    /**
     * PDO::PARAM_INTのものを一括して半角数字に制限するValidatorを作成
     *
     * @param string $smr サマリーメッセージ
     * @param string $msg 個別メッセージ
     */
    public function setIntValidators($smr, $msg = '*')
    {
        foreach ($this->inputs as $k => $x) {
            if ($x->db_type == PDO::PARAM_INT) {
                $v = new Validator\Regex($k, '|^[0-9]+$|');
                $v->set_error_summary($smr);
                $v->set_error_message('ERR_' . $k, $msg);
                $this->validators[] = $v;
            }
        }
    }

    /**
     * 入力必須項目の一括設定
     *
     * @param array $targets
     * @param string $smr サマリーメッセージ
     * @param string $msg 個別メッセージ
     */
    public function setRequires($targets, $smr, $msg = '*')
    {
        foreach ($targets as $x) {
            $v = new Validator\Required($x);
            $v->set_error_summary($smr);
            $v->set_error_message('ERR_' . $x, $msg);
            $this->validators[] = $v;
        }
    }

    /**
     * 最大文字数制限の一括設定
     *
     * @param array $targets
     * @param int $max
     * @param string $smr サマリーメッセージ
     * @param string $msg 個別メッセージ
     */
    public function setMaxValidators($targets, $max, $smr, $msg = '*')
    {
        foreach ($targets as $x) {
            $v = new Validator\Max($x, $max);
            $v->set_error_summary($smr);
            $v->set_error_message('ERR_' . $x, $msg);
            $this->validators[] = $v;
        }
    }

    /**
     * バリデータークラス配列をすべて検査し、不正の場合はエラーメッセージとエラーサマリーを付加する。
     *
     * @return bool 入力が１つでも不正ならfalse
     */
    public function valid()
    {
        $f = true;
        $s = '';
        foreach ($this->validators as $v) {
            if (!$v->valid()) {
                $f = false;
                if ($v->summary_str) {
                    $s .= $this->setName($v, $v->summary_str)
                            . $this->end_html;
                }
                if ($v->message_tag) {
                    $this->template->add($v->message_tag, $this->setName($v, $v->message_str), false);
                }
            }
        }
        if ($s) {
            $this->template->add($this->panel_var, $s, false);
        }
        return $f;
    }

    /**
     * 配列からフィールド名のキーのデータを抽出しバインドする。
     *
     * @param PDOStatement $statement
     * @param array $array $_POSTなど
     * @return int
     * @tutorial
     * チェックボックスが配列の場合CSV文字列で格納する。
     * この場合エスケープ処理省略のためデータにカンマが使用できない。
     */
    private function bindValues($statement, $array)
    {
        $i = 1;
        foreach ($this->inputs as $k => $x) {
            if ($x->column) {
                if ($this->asNull($x, $array)) {
                    continue;
                }
                $v = '';
                if ($x->db_type == \PDO::PARAM_INT) {
                    $v = 0;
                }
                if (\array_key_exists($k, $array)) {
                    $v = $array[$k];
                    if (\is_array($v)) {
                        $v = self::PREFIX_CSV . \implode(',', $v);
                    } else {
                        $v = \trim($v);
                    }
                }
                $statement->bindValue($i++, $v, $x->db_type);
            }
        }
        return $i;
    }

    /**
     * エラーメッセージに項目名を埋め込む
     *
     * @param object $v
     * @param string $msg
     * @return string
     */
    private function setName($v, $msg)
    {
        if (\property_exists($v, 'field') && \array_key_exists($v->field, $this->inputs)
        ) {
            return \str_replace('[[NAME]]', $this->inputs[$v->field]->caption, $msg);
        }
        return $msg;
    }

    /**
     * nullとみなす条件
     *
     * @param Input\Base $input
     * @param array $a
     * @return boolean
     */
    private function asNull($input, $a)
    {
        if ($input->db_type == \PDO::PARAM_INT && isset($a[$input->name]) && $a[$input->name] == ''
        ) {
            return true;
        }
        if (\in_array($input->name, $this->nulls)) {
            return true;
        }
        return false;
    }

    /**
     * nullでDB出力したい入力キーを指定
     *
     * @param string,array $k
     */
    public function setNull($k)
    {
        if (\is_array($k)) {
            $this->nulls = $k;
        } else {
            $this->nulls[] = $k;
        }
    }

}
