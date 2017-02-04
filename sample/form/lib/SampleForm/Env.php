<?php

namespace SampleForm;

/**
 * 動作環境に依存する設定
 */
class Env {

	/**
	 * デバッグモードで動作
	 * @var bool
	 */
	const DEBUG = true;

	/**
	 * エラーメール送信先
	 * @var string
	 */
	const ERRMAIL = '';

	/**
	 * エラーログ記録用ファイル名
	 * @var string
	 */
	const LOGFILE = '';

}
