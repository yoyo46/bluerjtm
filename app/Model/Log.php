<?php
class Log extends AppModel {
	var $name = 'Log';
	var $validate = array(
		'admin' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'error' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'status' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	* 	@param string $info - info log
	* 	@param array $user - data user
	* 	@param object $requestHandler - event ketika function ini di jalankan
	* 	@param array $params - parameter tambahan
	* 	@param boolean $error - penanda event error
	* 	@param array $options - options
	* 	@return boolean result
	*/
	function logActivity( $info = NULL, $user = false, $requestHandler = false, $params = fase, $error = 0, $options = false, $transaction_id = false, $change_action = '' ){
		$log = array();

		if( !empty($options) ) {
			$log = array_merge($log, $options);
		}

		if( !empty($user['id']) ) {
			$log['Log']['user_id'] = $user['id'];
		}

		if( !empty($user['User']['email']) ) {
			$info = sprintf('( %s ) %s', $user['User']['email'], $info);
		}
		
		$log['Log']['transaction_id'] = $transaction_id;
		$log['Log']['name'] = $info;
		$log['Log']['model'] = $params['controller'];

		if( !empty($change_action) ) {
			$log['Log']['action'] = $change_action;
			$log['Log']['real_action'] = $params['action'];
		} else {
			$log['Log']['action'] = $params['action'];
		}

		if( !empty($requestHandler) ) {
			$ip_address = $requestHandler->getClientIP();
			$log['Log']['ip'] = $ip_address;

			$log['Log']['user_agent'] = env('HTTP_USER_AGENT');
			
			if( !empty($log['Log']['user_agent']) ) {
				// $user_agents = get_browser($log['Log']['user_agent'], true);
				$log['Log']['browser'] = !empty($user_agents['browser'])?implode(' ', array($user_agents['browser'], $user_agents['version'])):'';
				$log['Log']['os'] = !empty($user_agents['platform'])?$user_agents['platform']:'';
			} else {
				$user_agents = '';
				$log['Log']['browser'] = '';
				$log['Log']['os'] = '';
			}
			$log['Log']['from'] = $requestHandler->getReferer();
		}

		if( !empty($params['old_data']) ) {
			$log['Log']['old_data'] = serialize( $params['old_data'] );
		}

		$log['Log']['data'] = serialize( $params['data'] );
		$log['Log']['named'] = serialize( $params['named'] );
		$log['Log']['admin'] = !empty($params['admin'])?1:0;
		$log['Log']['error'] = $error;

		$admin_id = Configure::read('Auth.Admin.id');
		if( !empty($admin_id) ) {
			$log['Log']['admin_id'] = $admin_id;
		}
		
		$this->create();
		if($this->save($log)) {
			return true;	
		} else {
			return false;
		}
	}

	/**
	* Get Data Log
	*
	* @param string $find - all, list, paginate, count
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	*		string paginate - Pick opsi query Bantuan
	*		string count - Pick jumlah data yang ditampilkan
	* @param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query Bantuan
	* @return array - hasil query atau opsi Bantuan
	*/
	function getData( $find = 'all', $options = array() ){
		$default_options = array(
			'conditions'=> array(
				'Log.status'=> 1, 
			),
			'order'=> array(
				'Log.created' => 'DESC',
			),
		);

		if(!empty($options)){
			$default_options = array_merge($default_options, $options);
		}

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getLogs( $paramController, $paramAction, $id ) {
		$logs = $this->getData('paginate', array(
			'conditions' => array(
				'Log.transaction_id' => $id,
				'Log.model' => $paramController,
				'Log.action' => $paramAction,
			),
			'order' => array(
				'Log.created' => 'DESC',
				'Log.id' => 'DESC',
			),
			'limit' => 50,
		));

		return $logs;
	}

	function doSave ( $data ) {
		$this->create();

		if($this->save($data)) {
			return true;	
		} else {
			return false;
		}
	}
}
?>