<?php

namespace SampleForm;

/**
 * プリケーションView基底クラス
 */
class ViewBase extends \Ae\View {

	function __construct() {
		parent::__construct(Env::ERRMAIL, Msg::ERR_GENERAL);
		$this->setLayout(\APPHOME . 'template/_layout.phtml');
		$this->debug = Env::DEBUG;
		$this->setDisplayErrors(Env::LOGFILE);
	}

	public function setMain($file) {
		parent::setMain(\APPHOME . 'template/' . $file);
	}

}
