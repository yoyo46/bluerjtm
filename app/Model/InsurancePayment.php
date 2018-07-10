<?php
class InsurancePayment extends AppModel {
	var $name = 'InsurancePayment';
	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen harap diisi'
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
        'item' => array(
            'checkDetail' => array(
                'rule' => array('checkDetail'),
                'message' => 'Mohon pilih pembayaran terlebih dahulu'
            ),
        ),
	);

    var $belongsTo = array(
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'Insurance' => array(
            'className' => 'Insurance',
            'foreignKey' => 'insurance_id',
        ),
        'Cogs' => array(
            'className' => 'Cogs',
            'foreignKey' => 'cogs_id',
        ),
    );

    var $hasMany = array(
        'InsurancePaymentDetail' => array(
            'className' => 'InsurancePaymentDetail',
            'foreignKey' => 'insurance_payment_id',
        )
    );

    function checkDetail () {
        $data = $this->data;
        $details = Common::hashEmptyField($data, 'InsurancePayment.item');

        if( !empty($details) ) {
            return true;
        } else {
            return false;
        }
    }

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'InsurancePayment.rejected' => 'ASC',
                'InsurancePayment.created' => 'DESC',
                'InsurancePayment.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['InsurancePayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['InsurancePayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['InsurancePayment.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['InsurancePayment.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) ){
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
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function beforeSave( $options = array() ) {
        $id = $this->id;
        $id = Common::hashEmptyField($this->data, 'InsurancePayment.id', $id);

        if( empty($id) ) {
            $this->data = Hash::insert($this->data, 'InsurancePayment.branch_id', Configure::read('__Site.config_branch_id'));
            $this->data = Hash::insert($this->data, 'InsurancePayment.user_id', Configure::read('__Site.config_user_id'));
        }
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $default_msg = __('melakukan pembayaran asuransi');

        if ( !empty($data) ) {
            $data = Hash::insert($data, 'InsurancePayment.id', $id);
            $data = Hash::insert($data, 'InsurancePayment.item', Common::hashEmptyField($data, 'InsurancePaymentDetail'));
            $data = $this->InsurancePaymentDetail->getDataModel($data, $id);

            $flagSave = $this->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));

            if( $flagSave ) {
                $this->saveAll($data, array(
                    'deep' => true,
                ));

                $id = $this->id;
                $this->InsurancePaymentDetail->doSave($data, $id);

                if( !empty($flagSave) ) {
                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                    $result = array(
                        'msg' => sprintf(__('Berhasil %s #%s'), $default_msg, $noref),
                        'status' => 'success',
                    );
                } else {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                        'data' => $data,
                    );
                }
            } else {
                $msg = sprintf(__('Gagal %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'data' => $data,
                    'validationErrors' => !empty($detailValidates['validationErrors'])?$detailValidates['validationErrors']:false,
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function _callLastPayment ($insurance_id) {
        $value = $this->getData('first', array(
            
        ));
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $vendor_id = !empty($data['named']['vendor_id'])?$data['named']['vendor_id']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(InsurancePayment.payment_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(InsurancePayment.payment_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($nodoc) ) {
            $default_options['conditions']['InsurancePayment.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(InsurancePayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        
        return $default_options;
    }

    function getPayment ( $data, $id = null ) {
        $default_options = array(
            'conditions' => array(
                'InsurancePayment.status' => 1,
                'InsurancePayment.rejected' => 0,
            ),
            'contain' => array(
                'InsurancePayment',
            ),
        );

        if( !empty($id) ) {
            $default_options['conditions']['InsurancePaymentDetail.insurance_id'] = $id;
        }

        $this->virtualFields['grandtotal'] = 'SUM(total)';
        $value = $this->InsurancePaymentDetail->getData('first', $default_options);

        if( !empty($value) ) {
            $data = array_merge($data, $value);
        }

        return $data;
    }
}
?>