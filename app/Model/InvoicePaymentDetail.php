<?php
class InvoicePaymentDetail extends AppModel {
	var $name = 'InvoicePaymentDetail';
	var $validate = array(
        'invoice_payment_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Invoice payment tidak di ketahui'
            ),
        ),
        'invoice_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Invoice tidak di ketahui'
            ),
        ),
        'price_pay' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jumlah pembayaran harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
        'InvoicePayment' => array(
            'className' => 'InvoicePayment',
            'foreignKey' => 'invoice_payment_id',
        ),
        'Invoice' => array(
            'className' => 'Invoice',
            'foreignKey' => 'invoice_id',
        ),
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'InvoicePaymentDetail.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
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

    function getMerge($data, $id){
        if(empty($data['InvoicePaymentDetail'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'InvoicePaymentDetail.id' => $id
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getMergeAll($data, $id){
        if(empty($data['InvoicePaymentDetail'])){
            $data_merge = $this->find('all', array(
                'conditions' => array(
                    'InvoicePaymentDetail.invoice_payment_id' => $id,
                    'InvoicePaymentDetail.status' => 1,
                ),
                'order'=> array(
                    'InvoicePaymentDetail.id' => 'ASC',
                ),
            ));

            if(!empty($data_merge)){
                $data['InvoicePaymentDetail'] = $data_merge;
            }
        }

        return $data;
    }
}
?>