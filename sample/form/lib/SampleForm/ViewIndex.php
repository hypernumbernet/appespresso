<?php

namespace SampleForm;

class ViewIndex extends ViewBase
{

    /** @var \Ae\Form HTMLフォーム */
    private $form;

    public function __construct()
    {
        parent::__construct();
        $this->setMain('index.phtml');
        $this->main->html('err')->html('ok');
        $pt = new \Ae\PageToken(\INPUT_POST);
        $this->main->add('token', $pt->value);
        $this->main->add('token_valid', $pt->valid ? 'True' : 'False');
        $this->main->add('token_key', $pt->val_key);
        $this->buildForm();
        $this->display();
    }

    public function display()
    {
        try {
            if (\Ae\HttpUtl::byGet()) {
                $this->ok('OK.GET');
            }
            if (\Ae\HttpUtl::byPost()) {
                $this->ok('OK.POST');
                $this->form->fromInput();
            }
        } catch (\Exception $e) {
            $this->err($e);
            $this->log($e);
        }
        $this->form->put();
        parent::display();
    }

    private function buildForm()
    {
        $this->form = new \Ae\Form($this->main);
        $this->form
                ->add(\Ae\Input\Text::of('created1', 'お申込み年')
                        ->css('width:40px'))
                ->add(\Ae\Input\Text::of('created2', 'お申込み月')
                        ->css('width:40px;'))
                ->add(\Ae\Input\Text::of('created3', 'お申込み日')
                        ->css('width:40px;'))
                ->add(\Ae\Input\Text::of('name1k', '申込者名')
                        ->css('width:180px;'))
                ->add(\Ae\Input\Text::of('name1h', '申込者フリガナ')
                        ->css('width:180px;'))
                ->add(\Ae\Input\Text::of('tel1', '電話番号1')
                        ->css('width:80px;'))
                ->add(\Ae\Input\Text::of('tel2', '電話番号2')
                        ->css('width:80px;'))
                ->add(\Ae\Input\Text::of('tel3', '電話番号3')
                        ->css('width:80px;'))
                ->add(\Ae\Input\Radio::of('gender', '性別')
                        ->options([['1', '男性'], ['2', '女性']]))
                ->add(\Ae\Input\Checkbox::of('mailmag', 'メールマガジン購読')
                        ->options([['1', '購読する']])
                        ->value('1'))
                ->add(\Ae\Input\Password::of('pass1', 'パスワード')
                        ->css('width:80px;'))
                ->add(\Ae\Input\Password::of('pass2', 'パスワード確認入力')
                        ->css('width:80px;'))
                ->add(\Ae\Input\Select::of('age', 'ご年齢')
                        ->options([
                            [null, ''],
                            ['10', '19歳以下'],
                            ['20', '20代'],
                            ['30', '30代'],
                            ['40', '40代'],
                            ['50', '50代'],
                            ['60', '60代'],
                            ['70', '70歳以上'],
                ]))
                ->add(\Ae\Input\FreeType::of('email', 'メールアドレス')
                        ->type('email')
                        ->css('width:300px;'))
                ->add(\Ae\Input\FreeType::of('homepage', 'ホームページ')
                        ->type('url')
                        ->css('width:300px;'))
                ->add(\Ae\Input\Checkbox::of('interest', '興味のある分野')
                        ->options([
                            ['1', '政治'],
                            ['2', '経済'],
                            ['3', '芸能'],
                            ['4', '科学技術'],
                ]))
                ->add((new \Ae\Input\Select('foods', '好きな料理')) //newも使用できる
                        ->multi()
                        ->attr('size', '6')
                        ->options([
                            ['1', 'カレー'],
                            ['2', 'そば'],
                            ['3', 'うどん'],
                            ['4', 'ハンバーグ'],
                            ['5', 'ピザ'],
                            ['6', 'スパゲティ'],
                            ['7', 'ステーキ'],
                ]))
                ->add(\Ae\Input\Textarea::of('request', 'ご要望')
                        ->css('width:400px;height:100px;'))
        ;
    }

}
