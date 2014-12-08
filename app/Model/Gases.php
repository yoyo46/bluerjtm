<?php
class Gases extends AppModel {
	var $name = 'Gases';
	var $validate = array(
        'premium' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'harga premium harap diisi.'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'harga premium harus berupa angka.'
            ),
        ),
        'solar' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'harga solar harap diisi.'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'harga solar harus berupa angka.'
            ),
        ),
	);
}
?>