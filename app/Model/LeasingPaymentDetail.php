<?php
class LeasingPaymentDetail extends AppModel {
	var $name = 'LeasingPaymentDetail';
	var $validate = array(
        'leasing_payment_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Pembayaran leasing tidak ditemukan'
            ),
        ),
        'leasing_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No kontrak harap dipilih'
            ),
        ),
        'leasing_installment_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No kontrak harap dipilih'
            ),
        ),
        'expired_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl jatuh tempo harap dipilih'
            ),
        ),
        'installment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Pokok harap diisi'
            ),
            'numeric' => array(
                'rule' => array('notempty'),
                'message' => 'Pokok harap diisi dan berupa angka'
            ),
            'validateMax' => array(
                'rule' => array('validateMax', 'installment'),
                'message' => 'Pembayaran pokok tidak boleh lebih besar dari %s pokok cicilan'
            ),
        ),
        'installment_rate' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bunga harap diisi'
            ),
            'numeric' => array(
                'rule' => array('notempty'),
                'message' => 'Bunga harap diisi dan berupa angka'
            ),
            'validateMax' => array(
                'rule' => array('validateMax', 'installment_rate'),
                'message' => 'Pembayaran bunga tidak boleh lebih besar dari bunga cicilan'
            ),
        ),
	);

    var $belongsTo = array(
        'Leasing' => array(
            'className' => 'Leasing',
            'foreignKey' => 'leasing_id',
        ),
        'LeasingInstallment' => array(
            'className' => 'LeasingInstallment',
            'foreignKey' => 'leasing_installment_id',
        ),
        'LeasingPayment' => array(
            'className' => 'LeasingPayment',
            'foreignKey' => 'leasing_payment_id'
        ),
    );

    function validateMax ( $data, $fieldName ) {
        if( $fieldName == 'installment_rate' ) {
            $default = !empty($this->data['Leasing'][$fieldName])?$this->data['Leasing'][$fieldName]:0;
        } else {
            $default = !empty($this->data['LeasingInstallment'][$fieldName])?$this->data['LeasingInstallment'][$fieldName]:0;
        }
        
        $paid = !empty($this->data['LeasingPaymentDetail'][$fieldName])?$this->data['LeasingPaymentDetail'][$fieldName]:0;

        if( $paid > $default ) {
            return false;
        } else {
            return true;
        }
    }

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'LeasingPaymentDetail.status' => 1,
            ),
            'order'=> array(),
            'fields' => array(),
            'contain' => array()
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function _callCicilanLeasing ( $leasing_installment_id, $type ) {
        switch ($type) {
            case 'paid':
                $total = $this->getData('first', array(
                    'conditions' => array(
                        'LeasingPaymentDetail.leasing_installment_id' => $leasing_installment_id,
                        'LeasingPayment.status' => 1,
                        'LeasingPayment.rejected' => 0,
                    ),
                    'contain' => array(
                        'LeasingPayment'
                    ),
                    'fields' => array(
                        'SUM(LeasingPaymentDetail.installment+LeasingPaymentDetail.installment_rate) AS Total',
                    ),
                ));

                return !empty($total[0]['Total'])?$total[0]['Total']:0;
                break;

            case 'cicilan':
                $total = $this->LeasingInstallment->getData('first', array(
                    'conditions' => array(
                        'LeasingInstallment.id' => $leasing_installment_id,
                    ),
                    'contain' => array(
                        'Leasing'
                    ),
                    'fields' => array(
                        'SUM(LeasingInstallment.installment+Leasing.installment_rate) AS Total',
                    ),
                ));
                return !empty($total[0]['Total'])?$total[0]['Total']:0;
                break;
        }
    }

    function getDataModel ( $data ) {
        $dataSave = array();
        $total_installment = 0;
        $total_installment_rate = 0;
        $total_denda = 0;
        $grandtotal = 0;

        if( !empty($data['LeasingPaymentDetail']['leasing_installment_id']) ) {
            $values = array_filter($data['LeasingPaymentDetail']['leasing_installment_id']);
            $dataPayment = $data['LeasingPaymentDetail'];

            foreach ($values as $key => $value) {
                $leasing_id = !empty($dataPayment['leasing_id'][$key])?$dataPayment['leasing_id'][$key]:false;
                $installment = !empty($dataPayment['installment'][$key])?trim(str_replace(array(',', ','), array('', ''), $dataPayment['installment'][$key])):false;
                $installment_rate = !empty($dataPayment['installment_rate'][$key])?trim(str_replace(array(',', ','), array('', ''), $dataPayment['installment_rate'][$key])):false;
                $denda = !empty($dataPayment['denda'][$key])?trim(str_replace(array(',', ','), array('', ''), $dataPayment['denda'][$key])):0;
                $expired_date = !empty($dataPayment['expired_date'][$key])?$dataPayment['expired_date'][$key]:false;
                $total = $installment+$installment_rate+$denda;

                $total_installment += $installment;
                $total_installment_rate += $installment_rate;
                $total_denda += $denda;
                $grandtotal += $total;

                $installment = !empty($installment)?$installment:false;
                $installment_rate = !empty($installment_rate)?$installment_rate:false;
                $denda = !empty($denda)?$denda:false;

                $detail['LeasingPaymentDetail'] = array(
                    'leasing_installment_id' => $value,
                    'leasing_id' => $leasing_id,
                    'expired_date' => $expired_date,
                    'installment' => $installment,
                    'installment_rate' => $installment_rate,
                    'denda' => $denda,
                    'total' => $total,
                );
                $detail = $this->LeasingInstallment->getMerge($detail, $value);

                $dataSave[] = $detail;
            }
        }

        return array(
            'data' => $dataSave,
            'total_installment' => $total_installment,
            'total_installment_rate' => $total_installment_rate,
            'total_denda' => $total_denda,
            'grandtotal' => $grandtotal,
        );
    }

    function doSave( $datas, $value = false, $id = false, $leasing_payment_id, $is_validate = false ) {
        $result = false;
        $coa_id = !empty($value['LeasingPayment']['coa_id'])?$value['LeasingPayment']['coa_id']:false;
        $payment_date = !empty($value['LeasingPayment']['payment_date'])?$value['LeasingPayment']['payment_date']:false;
        $vendor_id = !empty($value['LeasingPayment']['vendor_id'])?$value['LeasingPayment']['vendor_id']:false;
        $no_doc = !empty($value['LeasingPayment']['no_doc'])?$value['LeasingPayment']['no_doc']:false;
        
        $vendor = $this->Leasing->Vendor->getMerge(array(), $vendor_id);
        $vendor_name = !empty($vendor['Vendor']['name'])?$vendor['Vendor']['name']:false;
        $title = sprintf(__('Pembayaran Leasing #%s kepada vendor %s'), $no_doc, $vendor_name);

        if( !empty($leasing_payment_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'LeasingPaymentDetail.leasing_payment_id' => $leasing_payment_id,
            ));
        }


        if ( !empty($datas) ) {
            $installment = 0;
            $installment_rate = 0;
            $denda = 0;

            foreach ($datas as $key => $data) {
                $this->create();

                $installment += !empty($data['LeasingPaymentDetail']['installment'])?$data['LeasingPaymentDetail']['installment']:0;
                $installment_rate += !empty($data['LeasingPaymentDetail']['installment_rate'])?$data['LeasingPaymentDetail']['installment_rate']:0;
                $denda += !empty($data['LeasingPaymentDetail']['denda'])?$data['LeasingPaymentDetail']['denda']:0;

                if( !empty($leasing_payment_id) ) {
                    $data['LeasingPaymentDetail']['leasing_payment_id'] = $leasing_payment_id;
                }

                $this->set($data);

                if( $this->validates() ) {
                    if( $is_validate ) {
                        $flagSave = true;
                    } else {
                        $flagSave = $this->save($data);
                        $leasing_installment_id = !empty($data['LeasingPaymentDetail']['leasing_installment_id'])?$data['LeasingPaymentDetail']['leasing_installment_id']:false;
                        $leasing_id = !empty($data['LeasingPaymentDetail']['leasing_id'])?$data['LeasingPaymentDetail']['leasing_id']:false;

                        $hasPaid = $this->_callCicilanLeasing($leasing_installment_id, 'paid');
                        $totalMustBePaid = $this->_callCicilanLeasing($leasing_installment_id, 'cicilan');

                        if( $hasPaid >= $totalMustBePaid ) {
                            $status = 'paid';
                        } else {
                            $status = 'half_paid';
                        }

                        $this->LeasingInstallment->id = $leasing_installment_id;
                        $this->LeasingInstallment->set('payment_status', $status);
                        $this->LeasingInstallment->save();

                        $installmentPaid = $this->LeasingInstallment->getData('count', array(
                            'conditions' => array(
                                'LeasingInstallment.leasing_id' => $leasing_id,
                                'LeasingInstallment.payment_status' => array( 'unpaid', 'half_paid' ),
                            ),
                        ));

                        if( empty($installmentPaid) ) {
                            $statusPayment = 'paid';
                        } else {
                            $statusPayment = 'half_paid';
                        }

                        $this->Leasing->id = $leasing_id;
                        $this->Leasing->set('payment_status', $statusPayment);
                        $this->Leasing->save();
                    }

                    if( !$flagSave ) {
                        $result = array(
                            'msg' => __('Gagal menyimpan pembayaran leasing'),
                            'status' => 'error',
                        );
                    }
                } else {
                    $result = array(
                        'msg' => __('Gagal menambahkan pembayaran leasing'),
                        'status' => 'error',
                        'validationErrors' => $this->validationErrors,
                    );
                }
            }

            if( !$is_validate ) {
                $this->Journal = ClassRegistry::init('Journal');

                if( !empty($installment) ) {
                    $this->Journal->setJournal($installment, array(
                        'credit' => $coa_id,
                        'debit' => 'leasing_installment_coa_id',
                    ), array(
                        'date' => $payment_date,
                        'document_id' => $leasing_payment_id,
                        'title' => $title,
                        'document_no' => $no_doc,
                        'type' => 'leasing_payment',
                    ));
                }
                if( !empty($installment_rate) ) {
                    $this->Journal->setJournal($installment_rate, array(
                        'credit' => $coa_id,
                        'debit' => 'leasing_installment_rate_coa_id',
                    ), array(
                        'date' => $payment_date,
                        'document_id' => $leasing_payment_id,
                        'title' => $title,
                        'document_no' => $no_doc,
                        'type' => 'leasing_payment',
                    ));
                }
                if( !empty($denda) ) {
                    $this->Journal->setJournal($denda, array(
                        'credit' => $coa_id,
                        'debit' => 'leasing_denda_coa_id',
                    ), array(
                        'date' => $payment_date,
                        'document_id' => $leasing_payment_id,
                        'title' => $title,
                        'document_no' => $no_doc,
                        'type' => 'leasing_payment',
                    ));
                }
            }

            if( empty($result) ) {
                $result = array(
                    'msg' => __('Berhasil menambahkan pembayaran leasing'),
                    'status' => 'success',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function getMerge( $data, $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'LeasingPaymentDetail.leasing_payment_id' => $id,
            ),
        ));

        if( !empty($values) ) {
            $data['LeasingPaymentDetail'] = $values;
        }

        return $data;
    }
}
?>