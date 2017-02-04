<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * 共通と固有のテンプレートを扱う
 *
 * @version 2.0.0
 */
class View
{

    /** @var string 標準メッセージ出力変数名 */
    const VAR_OK = 'ok';

    /** @var string エラーメッセージ出力変数名 */
    const VAR_ERR = 'err';

    /** @var string 固有テンプレート出力変数名 */
    const VAR_MAIN = 'main';

    /** @var string システムメール文字コード */
    const MAIL_ENC = 'JIS';

    /** @var Output 共通テンプレート */
    public $layout;

    /** @var Output 固有テンプレート */
    public $main;

    /** @var string システムメッセージ受信アドレス */
    public $mail;

    /** @var string 標準エラーメッセージ */
    public $errGeneral;

    /** @var bool アプリケージョン全体がデバッグモードで動いているか */
    public $debug = false;

    /**
     * @param string $mail
     * @param string $err_general
     */
    public function __construct($mail, $err_general)
    {
        $this->mail = $mail;
        $this->errGeneral = $err_general;
    }

    /**
     * 共通テンプレートをセット
     *
     * @param string $file
     */
    public function setLayout($file)
    {
        $this->layout = new Output($file);
    }

    /**
     * メインテンプレートをセット
     *
     * @param string $file
     */
    public function setMain($file)
    {
        $this->main = new Output($file);
    }

    /**
     * 出力
     */
    public function display()
    {
        if (isset($this->main)) {
            $this->layout->add(self::VAR_MAIN, $this->main->fetch(), false);
        }
        $this->layout->display();
    }

    /**
     * tryでcatchしたエラーを記録しメール送信する。
     *
     * @param \Exception $e
     */
    public function log($e)
    {
        \error_log($e->getMessage(), 0);
        if ($this->mail) {
            \error_log(\mb_convert_encoding(\print_r($e, true), self::MAIL_ENC), 1, $this->mail);
        }
    }

    /**
     * テンプレートの標準出力
     *
     * @param string $msg メッセージ
     * @param bool $hs HTMLエンティティ変換
     * @param string $sep メッセージの区切り文字列
     */
    public function ok($msg, $hs = true, $sep = '<br>')
    {
        if (isset($this->main)) {
            $tpl = $this->main;
        } else {
            $tpl = $this->layout;
        }
        $p = $tpl->item[self::VAR_OK];
        if ($p) {
            $p .= $sep;
        }
        if ($hs) {
            $this->hs($msg);
        }
        $tpl->add(self::VAR_OK, $p . $msg, false);
    }

    /**
     * テンプレートの標準エラー出力
     *
     * @param string|\Exception $msg エラーメッセージ
     * @param bool $hs HTMLエンティティ変換
     * @param string $sep メッセージの区切り文字列
     */
    public function err($msg, $hs = true, $sep = '<br>')
    {
        if (isset($this->main)) {
            $tpl = $this->main;
        } else {
            $tpl = $this->layout;
        }
        $p = $tpl->item[self::VAR_ERR];
        if ($p) {
            $p .= $sep;
        }
        if (\is_object($msg) && \method_exists($msg, 'getMessage')) {
            $s = $msg->getMessage();
            if (!\mb_check_encoding($s, 'UTF-8')) {
                $enc = \mb_detect_encoding($s);
                if ($enc) {
                    $s = \mb_convert_encoding($s, 'UTF-8', $enc);
                }
            }
            $msg = $this->errGeneral . $s;
        }
        if ($hs) {
            $this->hs($msg);
        }
        $tpl->add(self::VAR_ERR, $p . $msg, false);
    }

    /**
     * エラー出力の制御
     *
     * @param string $log_file
     */
    public function setDisplayErrors($log_file = '')
    {
        if ($this->debug) {
            \error_reporting(\E_ALL | \E_STRICT);
            \ini_set('display_errors', 1);
        } else {
            \ini_set('display_errors', 0);
            if ($log_file) {
                \ini_set('log_errors', 1);
                \ini_set('error_log', $log_file);
            }
        }
    }

    /**
     * HTMLエンティティ変換
     *
     * @param mixed &$val
     */
    private function hs(&$val)
    {
        $val = \htmlspecialchars($val, \ENT_QUOTES, \mb_internal_encoding());
    }

}
