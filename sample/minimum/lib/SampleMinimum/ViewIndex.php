<?php

namespace SampleMinimum;

class ViewIndex extends ViewBase {

	function __construct() {
		parent::__construct();
		$this->setMain('index.phtml');
		$this->main->add('message', Msg::HELLO, true);
		$this->display();
	}

}
