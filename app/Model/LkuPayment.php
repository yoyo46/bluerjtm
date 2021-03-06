<?php
class LkuPayment extends AppModel {
	var $name = 'LkuPayment';
	var $validate = array(
        'no_doc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen harap diisi'
            ),
            // 'isUnique' => array(
            //     'rule' => array('isUnique'),
            //     'message' => 'No Dokumen telah terdaftar',
            // ),
        ),
        'tgl_bayar' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl bayar harap dipilih'
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'grandtotal' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account Kas/Bank harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
        'Cogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'cogs_id',
        ),
    );

    var $hasMany = array(
        'LkuPaymentDetail' => array(
            'className' => 'LkuPaymentDetail',
            'foreignKey' => 'lku_payment_id',
        )
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'LkuPayment.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'LkuPayment.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['LkuPayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['LkuPayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['LkuPayment.status'] = 1;
                break;
        }

        if(!empty($options) && $is_merge){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }else{
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getLkuPayment($id){
        $this->bindModel(array(
            'belongsTo' => array(
                'CustomerNoType' => array(
                    'className' => 'CustomerNoType',
                    'foreignKey' => 'customer_id',
                )
            )
        ));
        
        return $this->getData('first', array(
            'conditions' => array(
                'LkuPayment.id' => $id
            ),
            'contain' => array(
                'CustomerNoType',
                'LkuPaymentDetail'
            )
        ), true, array(
            'status' => 'all',
        ));
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $customer = !empty($data['named']['customer'])?$data['named']['customer']:false;
        $transaction_status = !empty($data['named']['transaction_status'])?urldecode($data['named']['transaction_status']):false;

        if(!empty($nodoc)){
            $default_options['conditions']['LkuPayment.no_doc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(LkuPayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($customer)){
            $default_options['conditions']['LkuPayment.customer_id'] = $customer;
        }
        if( !empty($transaction_status) ) {
            switch ($transaction_status) {
                case 'draft':
                    $default_options['conditions']['LkuPayment.transaction_status'] = 'unposting';
                    $default_options['conditions']['LkuPayment.is_void'] = 0;
                    break;
                case 'commit':
                    $default_options['conditions']['LkuPayment.transaction_status'] = 'posting';
                    $default_options['conditions']['LkuPayment.is_void'] = 0;
                    break;
                case 'void':
                    $default_options['conditions']['LkuPayment.is_void'] = 1;
                    break;
            }
        }
        
        return $default_options;
    }
}
?>