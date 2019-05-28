<?php
App::uses('Sanitize', 'Utility');
class RjDebtComponent extends Component {
	var $components = array(
		'MkCommon'
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
}
?>