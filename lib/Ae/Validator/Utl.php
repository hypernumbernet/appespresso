<?php

/**
 * Validatorインスタンス作成など
 * @copyright Copyright (C) 2009-2017 Tomohito Inoue
 * @license MIT
 * @author Tomohito Inoue <hypernumbernet@users.noreply.github.com>
 */
class Ae_Validator_Utl {

	/**
	 * 入力必須項目の一括設定
	 * @param Ae_Form $form
	 * @param array $targets
	 * @param string $msg エラーメッセージ
	 * @param array $input
	 */
	static function requires($form, $targets, $msg, $input = null) {
		foreach ($targets as $x) {
			$v = new Ae_Validator_Require($x, $input);
			$v->set_error_summary($msg);
			$v->set_error_message('ERR_' . $x, $msg);
			$form->validators[] = $v;
		}
	}

}
