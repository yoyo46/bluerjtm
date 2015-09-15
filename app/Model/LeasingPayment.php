<?php
class LeasingPayment extends AppModel {
	var $name = 'LeasingPayment';
	var $validate = array(
        'no_doc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No Dokumen telah terdaftar',
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account Kas/Bank harap dipilih'
            ),
        ),
        'payment_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl bayar harap dipilih'
            ),
        ),
        'vendor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Vendor harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Vendor' => array(
            'className' => 'Vendor',
            'foreignKey' => 'vendor_id',
        ),
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'leasing_id',
        ),
    );

    var $hasMany = array(
        'LeasingPaymentDetail' => array(
            'className' => 'LeasingPaymentDetail',
            'foreignKey' => 'leasing_payment_id',
        )
    );

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(
                'LeasingPayment.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'LeasingPayment.created' => 'DESC',
                'LeasingPayment.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['LeasingPayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['LeasingPayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['LeasingPayment.status'] = 1;
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

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $default_msg = __('melakukan pembayaran leasing');

        if ( !empty($data) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
            }

            $data['LeasingPayment']['user_id'] = Configure::read('__Site.config_user_id');
            $dataDetail = $this->LeasingPaymentDetail->getDataModel($data);

            $this->set($data);
            $mainValidate = $this->validates();

            $detailValidates = $this->LeasingPaymentDetail->doSave($dataDetail, false, false, false, true);
            $statusDetail = !empty($detailValidates['status'])?$detailValidates['status']:false;

            if( $mainValidate && $statusDetail == 'success' ) {
                $flagSave = $this->save();
                $id = $this->id;

                if( !empty($flagSave) ) {
                    $this->LeasingPaymentDetail->doSave($dataDetail, false, false, $id);
                }

                if( !empty($flagSave) ) {
                    $result = array(
                        'msg' => sprintf(__('Berhasil %s'), $default_msg),
                        'status' => 'success',
                    );
                } else {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                    );
                }
            } else {
                $result = array(
                    'msg' => sprintf(__('Gagal %s'), $default_msg),
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }
}
?>