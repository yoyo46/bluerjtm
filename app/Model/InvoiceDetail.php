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

    function getInvoicedRevenue($data, $revenue_id){
        if(empty($data['Invoice'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'InvoiceDetail.revenue_id' => $revenue_id,
                    'Invoice.status' => 1,
                ),
                'contain' => array(
                    'Invoice',
                ),
            ));

            if(!empty($data_merge['Invoice'])){
                $data['Invoice'] = $data_merge['Invoice'];
            }
        }

        return $data;
    }

    function getInvoicedRevenueList($revenue_id){
        $revenues = $this->find('list', array(
            'conditions' => array(
                'InvoiceDetail.revenue_id' => $revenue_id,
                'Invoice.status' => 1,
                'Invoice.complete_paid' => 1,
            ),
            'contain' => array(
                'Invoice',
            ),
            'fields' => array(
                'InvoiceDetail.id', 'InvoiceDetail.revenue_id'
            ),
        ));

        return $revenues;
    }
}
?>