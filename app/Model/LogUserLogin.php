<?php
class LogUserLogin extends AppModel {
	var $name = 'LogUserLogin';
	var $validate = array(
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

	function logLogin( $user = false, $requestHandler = false ){
		$log = array();

		if( !empty($user['id']) ) {
			$log['LogUserLogin']['user_id'] = $user['id'];
		}

		if( !empty($requestHandler) ) {
			$ip_address = $requestHandler->getClientIP();
			$log['LogUserLogin']['ip'] = $ip_address;
			$log['LogUserLogin']['user_agent'] = env('HTTP_USER_AGENT');

			$user_agents = @get_browser(null, true);
			
			if( !empty($log['LogUserLogin']['user_agent']) ) {
				$log['LogUserLogin']['browser'] = !empty($user_agents['browser'])?implode(' ', array($user_agents['browser'], $user_agents['version'])):'';
				$log['LogUserLogin']['os'] = !empty($user_agents['platform'])?$user_agents['platform']:'';
			} else {
				$user_agents = '';
				$log['LogUserLogin']['browser'] = '';
				$log['LogUserLogin']['os'] = '';
			}

			$log['LogUserLogin']['from'] = $requestHandler->getReferer();
		}

		$this->create();

		if($this->save($log)) {
			if( !empty($user['id']) ) {
				$this->User->updateAll( array(
                    'User.last_login' => "'".date('Y-m-d H:i:s')."'",
                ), array(
                    'User.id' => $user['id'],
                ));
			}

			return true;	
		} else {
			return false;
		}
	}

	function getData( $find = 'all', $options = array() ){
		$default_options = array(
			'conditions'=> array(
				'LogUserLogin.status'=> 1, 
			),
			'order'=> array(
				'LogUserLogin.created' => 'DESC',
			),
		);

		if( !empty($options) ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(isset($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(LogUserLogin.created, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(LogUserLogin.created, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($name)){
        	$this->bindModel(array(
                'hasOne' => array(
                    'Employe' => array(
                        'foreignKey' => false,
                        'conditions' => array(
                            'Employe.id = User.employe_id',
                        ),
                    ),
                )
            ), false);

            $default_options['contain'][] = 'User';
            $default_options['contain'][] = 'Employe';
            $default_options['conditions']['CONCAT(Employe.first_name,\' \',Employe.last_name) LIKE'] = '%'.$name.'%';
        }
        
        return $default_options;
    }
}
?>