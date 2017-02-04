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
abstract class Base
{

    /** @var string name属性。テンプレート変数名にもなる。 */
    public $name;

    /** @var string value属性。null初期値。htmlエンコードなし */
    public $value;

    /** @var string 追加属性。html文そのまま */
    protected $attr = '';

    /** @var string 表示名 */
    public $caption = '';

    /** @var string Postにキーがない場合の表示 */
    public $capnull = '';

    /** @var int データベース連動時タイプ */
    public $db_type;

    /** @var string データベース連動時カラム名 */
    public $column;

    /**
     * htmlエンコーディング（属性名対応）
     *
     * @param string $s
     */
    protected function hs($s)
    {
        $s = \htmlspecialchars($s, \ENT_QUOTES, \mb_internal_encoding());
        $s = \str_replace('=', '&#61;', $s);
        return $s;
    }

    /**
     * @param string $name
     * @param string $caption
     * @param int $db_type
     * @param string $column
     */
    public function __construct($name, $caption = null, $db_type = null, $column = null)
    {
        $this->name = $name;
        $this->caption = \is_null($caption) ? $this->name : $caption;
        $this->db_type = \is_null($db_type) ? \PDO::PARAM_STR : $db_type;
        $this->column = \is_null($column) ? $name : $column;
    }

    /**
     * ファクトリーメソッド
     *
     * @param string $name
     * @param string $caption
     * @return $this
     */
    public static function of($name, $caption = null)
    {
        return new static($name, $caption);
    }

    /**
     * フォーム部品HTMLを出力
     */
    public abstract function html();

    /**
     * type=hiddenとしてフォーム部品を出力
     *
     * @return string
     */
    public function hidden()
    {
        if (\is_array($this->value)) {
            $s = '';
            foreach ($this->value as $v) {
                $s .= '<input type="hidden" name="' . $this->name
                        . '[]" value="' . $v . '">';
            }
            return $s;
        }
        return '<input type="hidden" name="' . $this->name
                . '" value="' . $this->value . '">';
    }

    /**
     * カスタム属性追加
     *
     * @param string $name
     * @param string $value
     * @return Base
     */
    public function attr($name, $value = null)
    {
        if (\is_null($value)) {
            $this->attr .= ' ' . $this->hs($name);
        } else {
            $this->attr .= ' ' . $this->hs($name) . '="'
                    . $this->hs($value) . '"';
        }
        return $this;
    }

    /**
     * CSS属性追加
     *
     * @param string $name
     * @param string $value
     * @return Base
     */
    public function css($value)
    {
        $this->attr .= ' style="'
                . $this->hs($value) . '"';
        return $this;
    }

    /**
     * DB連動設定
     *
     * @param string $s カラム名。省略でname属性を使用
     * @param int $t カラムタイプ。PDO::PARAM定数を指定。省略でPARAM_STR
     * @return Base
     */
    public function db($s = null, $t = null)
    {
        $this->column = \is_null($s) ? $this->name : $s;
        $this->db_type = \is_null($t) ? \PDO::PARAM_STR : $t;
        return $this;
    }

    /**
     * 値を設定、または取得
     *
     * @param string|string[] $val
     * @return $this|string
     */
    public function value($val = null)
    {
        if (\is_null($val)) {
            return $this->value;
        } elseif (\is_array($val)) {
            $this->value = isset($val[$this->name]) ? $val[$this->name] : '';
        } else {
            $this->value = $val;
        }
        return $this;
    }

    /**
     * 確認表示用の値を出力。表示名に変換
     *
     * @return string
     */
    public function caption($esc = true)
    {
        if (\is_null($this->value)) {
            return $this->capnull;
        }
        if ($esc) {
            return $this->hs($this->value);
        } else {
            return $this->value;
        }
    }

    /**
     * capnullチェーン
     *
     * @param string $s
     * @return Base
     */
    public function capnull($s)
    {
        $this->capnull = $s;
        return $this;
    }

}
