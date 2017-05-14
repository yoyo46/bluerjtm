<?php
class PurchaseOrderPaymentDetail extends AppModel {
	var $name = 'PurchaseOrderPaymentDetail';

    var $belongsTo = array(
        'PurchaseOrder' => array(
            'className' => 'PurchaseOrder',
            'foreignKey' => 'purchase_order_id',
        ),
        'PurchaseOrderPayment' => array(
            'className' => 'PurchaseOrderPayment',
            'foreignKey' => 'purchase_order_payment_id',
        ),
    );

	var $validate = array(
        'purchase_order_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'PO harap dipilih'
            ),
        ),
        'price' => array(
            'emptyFill' => array(
                'rule' => array('emptyFill', 'price'),
                'message' => 'Total dibayar harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Total dibayar harus berupa angka'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(
                'PurchaseOrderPaymentDetail.status' => 1,
            ),
            'order'=> array(
                'PurchaseOrderPaymentDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['PurchaseOrderPaymentDetail.status'] = 1;
                break;
            case 'paid':
                $default_options['conditions']['PurchaseOrderPaymentDetail.status'] = 1;
                $default_options['conditions']['PurchaseOrderPayment.transaction_status'] = array( 'posting', 'unposting' );
                $default_options['conditions']['PurchaseOrderPayment.status'] = 1;
                $default_options['contain'][] = 'PurchaseOrderPayment';
                break;
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = $options['order'];
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

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'PurchaseOrderPaymentDetail.purchase_order_payment_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $purchase_order_id = $this->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'purchase_order_id');
                $price = $this->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'price');

                $value = $this->PurchaseOrder->getMerge($value, $purchase_order_id);

                $grandtotal = $this->PurchaseOrder->PurchaseOrderDetail->_callGrandtotal($purchase_order_id);
                // $grandtotal = $this->filterEmptyField($value, 'PurchaseOrder', 'grandtotal');

                $paid = $this->_callPaidPO($purchase_order_id, $id);
                $value['PurchaseOrder']['total_remain'] = $grandtotal - $paid;
                $value['PurchaseOrder']['total_paid'] = $paid;
                $value['PurchaseOrder']['grandtotal'] = $grandtotal;
                $values[$key] = $value;
            }
            $data['PurchaseOrderPaymentDetail'] = $values;
        }

        return $data;
    }

    function _callPaidPO( $id = false, $payment_id = false ){
        $this->virtualFields['total_paid'] = 'SUM(PurchaseOrderPaymentDetail.price)';
        $result = $this->getData('first', array(
            'conditions'=> array(
                'PurchaseOrderPaymentDetail.purchase_order_id' => $id,
                'PurchaseOrderPaymentDetail.purchase_order_payment_id <>' => $payment_id,
            ),
        ), array(
            'status' => 'paid',
        ));
        $result = $this->PurchaseOrder->getMerge($result, $id, 'PurchaseOrder.id', 'unpaid');

        $total_paid = $this->filterEmptyField($result, 'PurchaseOrderPaymentDetail', 'total_paid', 0);
        $total_po = $this->filterEmptyField($result, 'PurchaseOrder', 'grandtotal', 0);

        return $total_paid;
    }
}
?>