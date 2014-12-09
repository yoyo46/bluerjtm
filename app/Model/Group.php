<?php
class Group extends AppModel {
	var $actsAs = array('Acl' => array('type' => 'requester'));

    function parentNode() {
        return null;
    }
}
?>