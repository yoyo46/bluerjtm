<?php
class InvoiceDetail extends AppModel {
	var $name = 'InvoiceDetail';
	var $validate = array(
        'invoice_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Invoice tidak diketahui'
            ),
        ),
        'revenue_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Revenue tidak diketahui'
            ),
        ),
	);

	var $belongsTo = array(
        'Revenue' => array(
            'className' => 'Revenue',
            'foreignKey' => 'revenue_id',
        ),
        'Invoice' => array(
            'className' => 'Invoice',
            'foreignKey' => 'invoice_id',
        ),
	);
}
?>