<?php
class Notification extends AppModel {
	var $name = 'Notification';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Notification name harap diisi'
            ),
        ),
	);
    var $approvalAction = array( 'Kas/Bank', 'Penawaran Supplier' );

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);

	function getData( $find, $options = false, $element = true ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Notification.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( isset($element['read']) ) {
            $default_options['conditions']['Notification.read'] = $element['read'];
        }

        if( !empty($options) ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function saveData($user_id, $data = array()){
        if(!empty($user_id)){
            if(is_array($user_id)){
                foreach ($user_id as $key => $value) {
                    $this->create();
                    $data['user_id'] = $value;
                    $this->set($data);
                    $this->save();
                }
            }else{
                $this->create();
                $this->set($data);
                if($this->save()){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
    }

    function notifCheck($user_id, $id){
        $notification = $this->find('first', array(
            'conditions' => array(
                'Notification.user_id' => $user_id,
                'Notification.id' => $id,
                'Notification.read' => 1
            )
        ));

        if( empty($notification) ){
            $this->id = $id;
            $this->set(array('read' => 1));
            $this->save();
        }
    }

    function doSave ( $data ) {
        $this->create();

        if($this->save($data)) {
            return true;    
        } else {
            return false;
        }
    }

    function doSaveMany( $data = NULL ){
        if( !empty($data) ) {
            $flag = true;
            $users = !empty($data['Notification']['user_id'])?$data['Notification']['user_id']:false;

            if( !is_array($users) ) {         
                $users = array(
                    $users,
                );
            }

            if( !empty($data['Notification']['link']) ) {
                $data['Notification']['link'] = serialize($data['Notification']['link']);
                $data['Notification']['url'] = $data['Notification']['link'];
            }

            if( !empty($users) ) {
                foreach ($users as $key => $user_id) {
                    $this->create();
                    $data['Notification']['user_id'] = $user_id;

                    if( !$this->save($data) ) {
                        $flag = false;
                    }
                }
            }

            return $flag;
        }
    }

    function doRead ( $id ) {
        $this->set('read', 1);
        $this->id = $id;

        if($this->save()) {
            return true;    
        } else {
            return false;
        }
    }

    function _callNotifications ( $user_id = false ) {
        if( empty($user_id) ) {
            $user_id = Configure::read('__Site.config_user_id');
        }

        $notifications = $this->getData('all', array(
            'conditions' => array(
                'Notification.user_id' => $user_id,
                'Notification.action <>' => $this->approvalAction,
            ),
            'limit' => 10,
        ));
        $cnt = $this->getData('count', array(
            'conditions' => array(
                'Notification.user_id' => $user_id,
                'Notification.action <>' => $this->approvalAction,
            )
        ), array(
            'read' => false,
        ));

        return array(
            'notifications' => $notifications,
            'cnt' => $cnt,
        );
    }

    function _callApprovalNotifs ( $user_id = false ) {
        if( empty($user_id) ) {
            $user_id = Configure::read('__Site.config_user_id');
        }

        $notifications = $this->getData('all', array(
            'conditions' => array(
                'Notification.user_id' => $user_id,
                'Notification.action' => $this->approvalAction,
            ),
            'limit' => 10,
        ));
        $cnt = $this->getData('count', array(
            'conditions' => array(
                'Notification.user_id' => $user_id,
                'Notification.action' => $this->approvalAction,
            )
        ), array(
            'read' => false,
        ));

        return array(
            'notifications' => $notifications,
            'cnt' => $cnt,
        );
    }
}
?>