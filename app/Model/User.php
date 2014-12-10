<?php
class User extends AppModel {
	var $name = 'User';
    var $actsAs = array('Acl' => array('type' => 'requester'));
    var $belongsTo = array('Group');
    
    var $validate = array(
        'first_name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'nama depan harap diisi'
            ),
        ),
        'username' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'username harap diisi'
            ),
        ),
        'email' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'email harap diisi'
            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'format emil tidak valid'
            ),
        ),
        'gender' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'jenis kelamin harap diisi'
            ),
        ),
        'birthdate' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tanggal lahir harap diisi'
            ),
        ),
    );

    function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['group_id'])) {
        $groupId = $this->data['User']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
        return null;
        } else {
            return array('Group' => array('id' => $groupId));
        }
    }
}
?>