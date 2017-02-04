<?php
/**
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 */

namespace Ae;

/**
 * ログイン認証クラス
 *
 * <pre>
 * [ テンプレートの要件 ]
 * formのmethod: "post"
 * formのaction: ""
 * ユーザー名入力項目名: "user"
 * パスワード入力項目名: "pass"
 * エラーメッセージ出力: "err"
 * </pre>
 *
 * @version 3.0.0
 */
class Login
{

    const SESSION_ROLE = 'ROLE';
    const SESSION_USER = 'USER';
    const SESSION_ID = 'ID';
    const SESSION_ACCESS = 'ACCESS';
    const ERROR_GET_KEY = 'loginfail';

    /** @var int エラーコード:セッション異常時 */
    const ERROR_SESSION = 1;

    /** @var int エラーコード:ロール情報が読み取り不可 */
    const ERROR_ROLE_VOID = 2;

    /** @var int エラーコード:ロール情報が一致しない */
    const ERROR_ROLE_BAN = 3;

    /** @var int エラーコード:タイムアウト時 */
    const ERROR_TIME_OUT = 4;

    /** @var int エラーコード:Cookieが未発行 */
    const ERROR_COOKIE = 5;

    const POST_USER = 'user';
    const POST_PASS = 'pass';
    const SALT_SEP = '#';
    const STRETCH = 10000;

    /** @var string セッションのCookieに割り当てられる名前 */
    public $session_name;

    /** @var string ユーザーロール名 */
    public $role_name;
    public $form;
    public $wait = 1;
    public $hash;

    /** @var string パスワード暗号化のソルト部分のセパレータ */
    public $salt_sep;

    /** @var string パスワード暗号化のハッシュ関数適用回数 */
    public $stretch;
    public $err_panel = 'err';

    /** @var string DB認証テーブル名 */
    public $db_table = 'users';

    /** @var string DB認証テーブル：ユーザー名のカラム名 */
    public $db_user = 'uname';

    /** @var string DB認証テーブル：パスワードのカラム名 */
    public $db_pass = 'pass';

    /** @var string DB認証テーブル：ロールのカラム名 */
    public $db_role = 'urole';

    /** @var string DB認証テーブル：主キーのカラム名 */
    public $db_id = 'id';

    /**
     * @param string $session_name 設定すると独自のセッション名を使用する。
     * @param string $role_name 設定するとDBのロール名を使用しない。
     */
    public function __construct($session_name = '', $role_name = '')
    {
        if ($session_name) {
            $this->session_name = $session_name;
        } else {
            $this->session_name = \session_name();
        }
        $this->role_name = $role_name;
        $this->salt_sep = self::SALT_SEP;
        $this->stretch = self::STRETCH;
    }

    /**
     * ログイン認証をして指定されたページへ移動する。
     *
     * @param Form $form
     * @param Db\DbcBase $db
     * @param string,string[] $login_top DBのroleによってログイン先を分けたい場合配列
     */
    public function login($form, $db, $login_top = '')
    {
        $this->form = $form;
        if (Ae_IsSubmitted::by_post()) {
            if ($form->valid()) {
                $a = $this->select($db);
                $c = \count($a);
                $msg = 'Ae_Msg_' . \AE_LANG . '_Login';
                if ($c == 1) {
                    $r = $a[0];
                    $pass = $this->val($_POST, self::POST_PASS);
                    if ($r[$this->db_pass] && $this->hash) {
                        $this->passHash($pass, $r[$this->db_pass]);
                    }
                    if ($r[$this->db_pass] == $pass) {
                        $this->loginOk($login_top, $r);
                    } else {
                        $this->err($msg::PASS);
                    }
                } elseif ($c > 1) {
                    $this->err($msg::DUP);
                } else {
                    $this->err($msg::USER);
                }
            } else {
                $this->err();
            }
        } else {
            $this->loginIni();
        }
    }

    /**
     * ログイン認証をする。
     *
     * @param HttpInput $input
     * @param Db\DbcBase $db
     * @return string 正常時:空文字, 異常時:エラーメッセージ
     */
    public function loginAjax($input, $db)
    {
        $a = $this->select($db, $input->get(self::POST_USER));
        $c = \count($a);
        $msg = '\\Ae\\Msg\\' . \AE_LANG . '\\Login';
        if ($c == 1) {
            $r = $a[0];
            $pass = $input->get(self::POST_PASS);
            if (\password_verify($pass, $r[$this->db_pass])) {
                \session_name($this->session_name);
                $ok = \session_start();
                if ($ok === false) {
                    return $msg::SESSION;
                }
                if ($this->role_name) {
                    $_SESSION[self::SESSION_ROLE] = $this->role_name;
                } else {
                    $_SESSION[self::SESSION_ROLE] = $r[$this->db_role];
                }
                $_SESSION[self::SESSION_USER] = $r[$this->db_user];
                $_SESSION[self::SESSION_ID] = $r[$this->db_id];
                $_SESSION[self::SESSION_ACCESS] = \time();
                return '';
            } else {
                \sleep($this->wait);
                return $msg::PASS;
            }
        } elseif ($c > 1) { // TODO この条件は前提として削除予定 SQLにLIMIT
            return $msg::DUP;
        } else {
            \sleep($this->wait);
            return $msg::USER;
        }
    }

    /**
     * パスワード合致後の処理
     */
    private function loginOk($login_top, $r)
    {
        if (\is_array($login_top)) {
            if (!array_key_exists($r[$this->db_role], $login_top)) {
                $msg = 'Ae_Msg_' . \AE_LANG . '_Login';
                $this->err($msg::ROLE);
                return;
            }
            $url = $login_top[$r[$this->db_role]];
        } else {
            if ($login_top) {
                $url = $login_top;
            } else {
                $url = $this->val($_SERVER, 'SCRIPT_NAME');
            }
        }
        \session_name($this->session_name);
        \session_start();
        if (!$this->cookieEnable()) {
            if (\ini_get('session.use_trans_sid')) {
                if (\SID) {
                    $url .= (strpos($url, '?') !== false ? '&' : '?') .
                            \htmlspecialchars(SID);
                }
            } else {
                $msg = 'Ae_Msg_' . \AE_LANG . '_Login';
                $this->err($msg::COOKIE);
                return;
            }
        }
        if ($this->role_name) {
            $_SESSION[self::SESSION_ROLE] = $this->role_name;
        } else {
            $_SESSION[self::SESSION_ROLE] = $r[$this->db_role];
        }
        $_SESSION[self::SESSION_USER] = $r[$this->db_user];
        $_SESSION[self::SESSION_ID] = $r[$this->db_id];
        $_SESSION[self::SESSION_ACCESS] = \time();
        \header('Location: ' . $url);
        exit();
    }

    /**
     * POSTなしでアクセスした時の処理
     */
    private function loginIni()
    {
        $this->cookieTest();
        if (isset($_GET[self::ERROR_GET_KEY])) {
            $msg = 'Ae_Msg_' . \AE_LANG . '_Login';
            switch ($_GET[self::ERROR_GET_KEY]) {
                case self::ERROR_SESSION:
                    $this->form->template->add($this->err_panel, $msg::SESSION);
                    break;
                case self::ERROR_ROLE_VOID:
                    $this->form->template->add($this->err_panel, $msg::AGAIN);
                    break;
                case self::ERROR_ROLE_BAN:
                    $this->form->template->add($this->err_panel, $msg::BAN);
                    break;
                case self::ERROR_TIME_OUT:
                    $this->form->template->add($this->err_panel,
                            \str_replace('[[time]]', $this->val($_GET, 'time'),
                                    $msg::TIMEOUT));
                    break;
                default:
                    $this->form->template->add($this->err_panel, $msg::AGAIN);
            }
        }
    }

    /**
     * エラー処理
     * @param string $msg エラーメッセージ
     */
    private function err($msg = '')
    {
        if ($msg) {
            $this->form->template->add($this->err_panel, $msg);
        }
        $this->form->value(self::POST_USER, $this->val($_POST, self::POST_USER));
        \sleep($this->wait);
    }

    /**
     * Cookieが有効かどうかテストする。
     */
    private function cookieTest()
    {
        \setcookie($this->session_name . 'TEST', '1', \pow(2, 31) - 1);
    }

    /**
     * Cookieテストの結果を得る。
     */
    private function cookieEnable()
    {
        return isset($_COOKIE[$this->session_name . 'TEST']);
    }

    /**
     * ユーザーの存在を確認
     *
     * @param Db\DbcBase $db
     */
    private function select($db, $user)
    {
        $q = "SELECT $this->db_user, $this->db_pass, $this->db_id ";
        if (!$this->role_name) {
            $q .= ', ' . $this->db_role;
        }
        $q .= " FROM $this->db_table WHERE $this->db_user = ? ";
        $t = $db->prepare($q);
        $t->bindValue(1, $user);
        $t->execute();
        return $t->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 指定された方式に従ってハッシュを算出
     * @param string& $pass
     * @param string $hash
     * @throws Exception
     */
    private function passHash(& $pass, $hash)
    {
        if ($this->salt_sep) {
            $b = \explode($this->salt_sep, $hash);
            if (\count($b) > 1) {
                if ($this->stretch) {
                    for ($i = 0; $i < $this->stretch; $i++) {
                        $pass = \hash($this->hash, $b[0] . $pass);
                    }
                } else {
                    $pass = \hash($this->hash, $b[0] . $pass);
                }
                $pass = $b[0] . $this->salt_sep . $pass;
            } else {
                $msg = '\\Ae\\Msg\\' . \AE_LANG . '\\Login';
                throw new \Exception($msg::SALT);
            }
        } else {
            if ($this->stretch) {
                for ($i = 0; $i < $this->stretch; $i++) {
                    $pass = \hash($this->hash, $pass);
                }
            } else {
                $pass = \hash($this->hash, $pass);
            }
        }
    }

    /**
     * エラー無しで値取得。キーがない場合は空文字
     * @param array $a
     * @param string $k
     */
    private function val($a, $k)
    {
        return isset($a[$k]) ? $a[$k] : '';
    }

    /**
     * 認証が必要なAjax apiの先頭で呼び出す。
     *
     * @param string|string[] $role アクセスを許可するロール名
     * @param string $session_name Cookieのキー名(省略可)
     * @param int $time_out タイムアウト。規定値(18000秒 = 5時間)
     * @return int 認証時:0, エラー時:エラーコード
     */
    public static function authOrErrCode($role, $session_name = '',
            $time_out = 18000)
    {
        if ($session_name) {
            \session_name($session_name);
        }
        $cookie = new HttpInput(\INPUT_COOKIE);
        if (!$cookie->get(\session_name())) {
            return self::ERROR_COOKIE;
        }
        if (!\session_start()) {
            return self::ERROR_SESSION;
        }
        if ($time_out &&
                isset($_SESSION[self::SESSION_ACCESS]) &&
                $_SESSION[self::SESSION_ACCESS] < \time() - $time_out
        ) {
            return self::ERROR_TIME_OUT;
        }
        if (!isset($_SESSION[self::SESSION_ROLE])) {
            return self::ERROR_ROLE_VOID;
        }
        if (\is_string($role)) {
            if (!$role == '*' && $_SESSION[self::SESSION_ROLE] != $role) {
                return self::ERROR_ROLE_BAN;
            }
        } else {
            if (!\in_array($_SESSION[self::SESSION_ROLE], $role)) {
                return self::ERROR_ROLE_BAN;
            }
        }
        $_SESSION[self::SESSION_ACCESS] = \time();
        return 0;
    }

    /**
     * 認証が必要なページの先頭で呼び出す。
     *
     * @param string $login_url
     * @param string,string[] $roles
     * @param string $session_name = ''
     * @param int $time_out = 5 hour
     */
    public static function auth($login_url, $roles, $session_name = '',
            $time_out = 18000)
    {
        if ($session_name) {
            \session_name($session_name);
        }
        $h = 'Location: ' . $login_url . (\strpos($login_url, '?') ? '&' : '?')
                . self::ERROR_GET_KEY . '=';
        if (!\session_start()) {
            \header($h . self::ERROR_SESSION);
            exit;
        }
        if ($time_out &&
                isset($_SESSION[self::SESSION_ACCESS]) &&
                $_SESSION[self::SESSION_ACCESS] < \time() - $time_out
        ) {
            \header($h . self::ERROR_TIME_OUT . '&time=' . $time_out);
            exit;
        }
        if (!\array_key_exists(self::SESSION_ROLE, $_SESSION)) {
            \header($h . self::ERROR_ROLE_VOID);
            exit();
        }
        if (\is_string($roles)) {
            if ($_SESSION[self::SESSION_ROLE] != $roles) {
                \header($h . self::ERROR_ROLE_BAN);
                exit();
            }
        } else {
            if (!\in_array($_SESSION[self::SESSION_ROLE], $roles)) {
                \header($h . self::ERROR_ROLE_BAN);
                exit();
            }
        }
        $_SESSION[self::SESSION_ACCESS] = \time();
    }

    /**
     * セッションを消去してログアウトする。
     * @param string $session_name
     */
    public static function logout($session_name = '')
    {
        if ($session_name) {
            \session_name($session_name);
        }
        \session_start();
        $_SESSION = array();
        if (isset($_COOKIE[\session_name()])) {
            \setcookie(\session_name(), '', 0, '/');
        }
        \session_destroy();
    }

    /**
     * ログインステータスを表示する。
     * @param Ae_Output $tpl
     * @param string $session_name
     * @return int 正常終了0
     * @tutorial
     * [Template]
     * login_user: ログインユーザー;
     * login_not: ログインしていない時に表示;
     */
    public static function status($tpl, $session_name = '')
    {
        if ($session_name) {
            \session_name($session_name);
        }
        if (!\session_start()) {
            return 1;
        }
        if (isset($_SESSION) &&
                \array_key_exists(self::SESSION_USER, $_SESSION)
        ) {
            $tpl->add('login_user', $_SESSION[self::SESSION_USER]);
        } else {
            $tpl->add('login_not');
        }
        return 0;
    }

    /**
     * ログインステータスを取得する。
     * @param string $session_name
     * @return int
     * @tutorial
     * 0: ログアウト
     * 1: ログイン
     * -1: エラー
     */
    public static function getStatus($session_name = '', $time_out = 18000)
    {
        if ($session_name) {
            \session_name($session_name);
        }
        if (!\session_start()) {
            return -1;
        }
        if (isset($_SESSION) &&
                \array_key_exists(self::SESSION_USER, $_SESSION)
        ) {
            if ($time_out &&
                    isset($_SESSION[self::SESSION_ACCESS]) &&
                    $_SESSION[self::SESSION_ACCESS] < \time() - $time_out
            ) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }

    /**
     * パスワードにhashを適用し保存形式にします。
     * @param string $pass
     * @param string $algo
     * @param string $salt
     * @param string $salt_sep
     * @param int $stretch
     */
    public static function newPass($pass, $algo, $salt,
            $salt_sep = self::SALT_SEP, $stretch = self::STRETCH)
    {
        for ($i = 0; $i < $stretch; ++$i) {
            $pass = \hash($algo, $salt . $pass);
    }
        return $salt . $salt_sep . $pass;
    }

}
