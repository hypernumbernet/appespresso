<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * $_POSTや$_GETなどのユーザー入力値の妥当性を検証する。
 * 標準でこのクラスを通してアクセスするようにする。
 *
 * @version 2.0.0
 */
class HttpInput
{

    /** @var string[] 入力値のチェック済みの値を保持するキャッシュ */
    private $store = [];

    /** @var int filter_input()のtype */
    private $type;

    /**
     * $_POSTや$_GETなどの値を取得する。
     *
     * @param int $type filter_input()のtypeを指定する。
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * 妥当性検証を行って値を取得する。
     *
     * @param string $key
     * @param mixed $options
     * @return string
     * @throws \Exception
     */
    public function get($key, $options = null)
    {
        if (isset($this->store[$key])) {
            return $this->store[$key];
        }
        $x = \filter_input($this->type, $key, \FILTER_UNSAFE_RAW, $options);
        if (\is_null($x) || $x === false) {
            $this->store[$key] = '';
            return '';
        }
        $this->checkEncoding($x, '', \mb_internal_encoding());
        $this->store[$key] = $x;
        return $x;
    }

    /**
     * 値を設定する。
     *
     * @param string $key
     * @param string $val
     * @return $this
     */
    public function value($key, $val)
    {
        $this->store[$key] = $val;
        return $this;
    }

    /**
     * 配列内のエンコーディグをチェックする。
     *
     * @param string|array $val
     * @param string $key
     * @param string $encoding
     * @throws \Exception
     */
    private function checkEncoding($val, $key, $encoding)
    {
        if (\is_array($val)) {
            \array_walk($val, 'self::checkEncoding', $encoding);
        } else {
            if (!\mb_check_encoding($val, $encoding)) {
                $this->quit();
            }
        }
        if (!\mb_check_encoding($key, $encoding)) {
            $this->quit();
        }
    }

    /**
     * 例外を投げて終了
     *
     * @throws \Exception
     */
    private function quit()
    {
        $msg = '\\Ae\\Msg\\' . \AE_LANG . '\\Http';
        throw new \Exception($msg::ENCODING . \get_class($this));
    }

}
