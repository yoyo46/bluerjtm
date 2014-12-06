<?php
App::uses('Sanitize', 'Utility');
class MkCommonComponent extends Component {
	var $components = array(
		'RequestHandler', 'Email', 'Session'
	); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	function setCustomFlash($message, $type = 'success', $params=array(), $ajaxMsg = false) {
		$flashType = $type;

		if( !$ajaxMsg ) {
			$this->Session->setFlash($message, $flashType, $params, $type);

			$this->controller->set('msg', $message);

			if( $type == 'success' ) {
				$status = 1;
			} else {
				$status = 0;
			}
			$this->controller->set('status', $status);
		}
	}
}
?>