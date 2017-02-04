<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * テンプレートマネジメントクラス。Output Buffering を使用
 *
 * @version 2.0.0
 */
class Output
{

    /** @var bool 改行、タブ、行頭半角スペースを除去するスイッチ */
    public $removeCrlf = false;

    /** @var bool HTMLコメントを除去するスイッチ */
    public $removeComment = false;

    /** @var mixed[] 埋め込むデータ。キーが変数名になる */
    public $item = [];

    /** @var string テンプレートのファイル名 */
    public $filename;

    /**
     * @param string $filename ファイル名
     */
    public function __construct($filename = '')
    {
        $this->filename = $filename;
    }

    /**
     * HTMLエンコーディング
     *
     * @param string &$val
     */
    private function hs(&$val)
    {
        $val = \htmlspecialchars($val, \ENT_QUOTES, \mb_internal_encoding());
    }

    /**
     * 置換データの追加
     *
     * @param string $key
     * @param mixed $val
     * @param bool $hs HTMLエンコーディング
     * @return Output
     */
    public function add($key, $val = 1, $hs = true)
    {
        if ($hs) {
            if (\is_string($val)) {
                $this->hs($val);
            } elseif (\is_array($val)) {
                \array_walk_recursive($val, array($this, 'hs'));
            }
        }
        $this->item[$key] = $val;
        return $this;
    }

    /**
     * データ展開を実行
     *
     * @return string 結果文字列
     */
    public function fetch()
    {
        if (!\ob_start()) {
            return '';
        }
        \extract($this->item);
        require $this->filename;
        $a = \ob_get_clean();
        if ($this->removeCrlf) {
            $a = \preg_replace('|^ +|m', '', $a);
            $a = \preg_replace("|[\r\n\t]|", '', $a);
        }
        if ($this->removeComment) {
            $a = \preg_replace('|<!--.*?-->|s', '', $a);
        }
        return $a;
    }

    /**
     * データ展開を実行し標準出力へ
     */
    public function display()
    {
        echo $this->fetch();
    }

    /**
     * 変数が存在するか
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        return \array_key_exists($key, $this->item);
    }

    /**
     * 変数の値を取得
     *
     * @param string $key
     * @return string|array
     */
    public function value($key)
    {
        if ($this->exists($key)) {
            return $this->item[$key];
        }
        return '';
    }

    /**
     * 変数が存在するならhtmlタグで囲う。ないなら変数を定義する。
     *
     * @param string $key 変数名
     * @param string $tag HTMLタグ
     * @param string $attr 属性を「id="i00" class="c00"」のように指定
     * @return Output
     */
    public function html($key, $tag = 'p', $attr = null)
    {
        if ($this->exists($key)) {
            $this->add($key,
                    '<' . $tag
                    . (empty($attr) ? '' : ' ' . $attr)
                    . '>' . $this->item[$key]
                    . '</' . $tag . '>');
        } else {
            $this->add($key, '');
        }
        return $this;
    }

    /**
     * 変数の削除
     *
     * @param string $key
     * @return Output
     */
    public function remove($key)
    {
        unset($this->item[$key]);
        return $this;
    }

}
