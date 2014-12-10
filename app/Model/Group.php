<?php
class Group extends AppModel {
	var $actsAs = array('Acl' => array('type' => 'requester'));

    function parentNode() {
        return null;
    }

    var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Nama group harap diisi'
			),
		)
	);
}
?>