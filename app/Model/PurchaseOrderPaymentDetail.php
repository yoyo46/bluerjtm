<?php
class PurchaseOrderPaymentDetail extends AppModel {
	var $name = 'PurchaseOrderPaymentDetail';

    var $belongsTo = array(
        'PurchaseOrder' => array(
            'className' => 'PurchaseOrder',
            'foreignKey' => 'document_id',
        ),
        'Spk' => array(
            'className' => 'Spk',
            'foreignKey' => 'document_id',
        ),
        'PurchaseOrderPayment' => array(
            'className' => 'PurchaseOrderPayment',
            'foreignKey' => 'purchase_order_payment_id',
        ),
    );

	var $validate = array(
        'document_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Dokumen harap dipilih'
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
            case 'paid-posting':
                $default_options['conditions']['PurchaseOrderPaymentDetail.status'] = 1;
                $default_options['conditions']['PurchaseOrderPayment.transaction_status'] = array( 'posting' );
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
            $document_type = $this->filterEmptyField($data, 'PurchaseOrderPayment', 'document_type');

            foreach ($values as $key => $value) {
                $document_id = $this->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'document_id');
                $price = $this->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'price');

                switch ($document_type) {
                    case 'spk':
                        $value = $this->Spk->getMerge($value, $document_id);
                        $grandtotal = $this->Spk->SpkProduct->_callGrandtotal($document_id);
                        $paid = $this->_callPaidSpk($document_id, $id);
                        $modelName = 'Spk';
                        break;
                    
                    default:
                        $value = $this->PurchaseOrder->getMerge($value, $document_id);
                        $grandtotal = $this->PurchaseOrder->PurchaseOrderDetail->_callGrandtotal($document_id);
                        $paid = $this->_callPaidPO($document_id, $id);
                        $modelName = 'PurchaseOrder';
                        break;
                }

                $value[$modelName]['total_remain'] = $grandtotal - $paid;
                $value[$modelName]['total_paid'] = $paid;
                $value[$modelName]['grandtotal'] = $grandtotal;
                $values[$key] = $value;
            }
            $data['PurchaseOrderPaymentDetail'] = $values;
        }

        return $data;
    }

    function _callPaidPO( $id = false, $payment_id = false, $status = 'paid' ){
        $this->virtualFields['total_paid'] = 'SUM(PurchaseOrderPaymentDetail.price)';
        $result = $this->getData('first', array(
            'conditions'=> array(
                'PurchaseOrderPaymentDetail.document_id' => $id,
                'PurchaseOrderPaymentDetail.purchase_order_payment_id <>' => $payment_id,
                'PurchaseOrderPayment.document_type' => 'po',
            ),
            'contain' => array(
                'PurchaseOrderPayment',
            ),
        ), array(
            'status' => $status,
        ));
        $result = $this->PurchaseOrder->getMerge($result, $id, 'PurchaseOrder.id', 'unpaid');

        $total_paid = $this->filterEmptyField($result, 'PurchaseOrderPaymentDetail', 'total_paid', 0);
        $total_po = $this->filterEmptyField($result, 'PurchaseOrder', 'grandtotal', 0);

        return $total_paid;
    }

    function _callPaidSpk( $id = false, $payment_id = false, $status = 'paid' ){
        $this->virtualFields['total_paid'] = 'SUM(PurchaseOrderPaymentDetail.price)';
        $result = $this->getData('first', array(
            'conditions'=> array(
                'PurchaseOrderPaymentDetail.document_id' => $id,
                'PurchaseOrderPaymentDetail.purchase_order_payment_id <>' => $payment_id,
                'PurchaseOrderPayment.document_type' => 'spk',
            ),
            'contain' => array(
                'PurchaseOrderPayment',
            ),
        ), array(
            'status' => $status,
        ));
        $result = $this->Spk->getMerge($result, $id, 'Spk.id', 'unpaid');

        $total_paid = $this->filterEmptyField($result, 'PurchaseOrderPaymentDetail', 'total_paid', 0);
        $total_spk = $this->filterEmptyField($result, 'Spk', 'grandtotal', 0);

        return $total_paid;
    }
}
?>