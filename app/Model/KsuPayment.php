<?php
class KsuPayment extends AppModel {
	var $name = 'KsuPayment';
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
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account Kas/Bank harap dipilih'
            ),
        ),
        'type_lku' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe modul harap dipilih'
            ),
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
        'KsuPaymentDetail' => array(
            'className' => 'KsuPaymentDetail',
            'foreignKey' => 'ksu_payment_id',
        )
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'KsuPayment.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'KsuPayment.created' => 'DESC',
                'KsuPayment.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['KsuPayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['KsuPayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['KsuPayment.status'] = 1;
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

    function getKsuPayment($id){
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
                'KsuPayment.id' => $id
            ),
            'contain' => array(
                'CustomerNoType',
                'KsuPaymentDetail'
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
            $default_options['conditions']['KsuPayment.no_doc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(KsuPayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($customer)){
            $default_options['conditions']['KsuPayment.customer_id'] = $customer;
        }
        if( !empty($transaction_status) ) {
            switch ($transaction_status) {
                case 'draft':
                    $default_options['conditions']['KsuPayment.transaction_status'] = 'unposting';
                    $default_options['conditions']['KsuPayment.is_void'] = 0;
                    break;
                case 'commit':
                    $default_options['conditions']['KsuPayment.transaction_status'] = 'posting';
                    $default_options['conditions']['KsuPayment.is_void'] = 0;
                    break;
                case 'void':
                    $default_options['conditions']['KsuPayment.is_void'] = 1;
                    break;
            }
        }
        
        return $default_options;
    }
}
?>