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

	function loggedIn() {
		$logged_in = false;
		if($this->controller->Auth->user()) {
			$logged_in = true;
		}
		return $logged_in;
	}

	function getDateSelectbox ( $tgl ) {
		$result = false;

		if( !empty($tgl['day']) && !empty($tgl['month']) && !empty($tgl['year']) ) {
			$result = sprintf('%s-%s-%s', $tgl['year'], $tgl['month'], $tgl['day']);
		}

		return $result;
	}

	function getFilePhoto ( $filePhoto ) {
		$result = $filePhoto;

		if( empty($filePhoto['name']) ) {
			$result = false;
		}

		return $result;
	}
}
?>