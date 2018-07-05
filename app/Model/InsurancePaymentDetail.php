<?php
class InsurancePaymentDetail extends AppModel {
	var $name = 'InsurancePaymentDetail';
	var $validate = array(
        'insurance_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Polis harap dipilih'
            ),
        ),
        'total' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Total premi harap diisi'
            ),
            'emptyFill' => array(
                'rule' => array('emptyFill', 'total'),
                'message' => 'Total premi harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'Insurance' => array(
            'className' => 'Insurance',
            'foreignKey' => 'insurance_id',
        ),
        'InsurancePayment' => array(
            'className' => 'InsurancePayment',
            'foreignKey' => 'insurance_payment_id'
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'InsurancePaymentDetail.status' => 1,
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

    function _callCicilanInsurance ( $insurance_id, $type ) {
        switch ($type) {
            case 'paid':
                $total = $this->getData('first', array(
                    'conditions' => array(
                        'InsurancePaymentDetail.insurance_id' => $insurance_id,
                        'InsurancePayment.status' => 1,
                        'InsurancePayment.rejected' => 0,
                    ),
                    'contain' => array(
                        'InsurancePayment'
                    ),
                    'fields' => array(
                        'SUM(InsurancePaymentDetail.total) AS Total',
                    ),
                ));

                return !empty($total[0]['Total'])?$total[0]['Total']:0;
                break;

            case 'cicilan':
                $total = $this->Insurance->getData('first', array(
                    'conditions' => array(
                        'Insurance.id' => $insurance_id,
                    ),
                    'fields' => array(
                        'SUM(Insurance.grandtotal) AS Total',
                    ),
                ));
                return !empty($total[0]['Total'])?$total[0]['Total']:0;
                break;
        }
    }

    function getDataModel ( $data, $id = false ) {
        $dataSave = array();
        $grandtotal = 0;

        if( !empty($data['InsurancePaymentDetail']['total']) ) {
            $values = array_filter($data['InsurancePaymentDetail']['total']);
            $dataPayment = $data['InsurancePaymentDetail'];

            foreach ($values as $insurance_id => $total) {
                $total = Common::_callPriceConverter($total);
                $grandtotal += $total;

                $detail['InsurancePaymentDetail'] = array(
                    'insurance_id' => $insurance_id,
                    'total' => $total,
                );
                $dataSave[] = $detail;
            }

            $data = Hash::insert($data, 'InsurancePaymentDetail', $dataSave);
            $data = Hash::insert($data, 'InsurancePayment.grandtotal', $grandtotal);
        }

        return $data;
    }

    function doSave( $data, $id = false ) {
        $coa_id = Common::hashEmptyField($data, 'InsurancePayment.coa_id');
        $payment_date = Common::hashEmptyField($data, 'InsurancePayment.payment_date');
        $nodoc = Common::hashEmptyField($data, 'InsurancePayment.nodoc');
        $note = Common::hashEmptyField($data, 'InsurancePayment.note');
        $details = Common::hashEmptyField($data, 'InsurancePaymentDetail');
        
        if( !empty($note) ) {
            $title = $note;
        } else {
            $title = sprintf(__('Pembayaran Asuransi #%s'), $nodoc);
        }

        $grandtotal = 0;

        if( !empty($details) ) {
            foreach ($details as $key => $detail) {
                $total = Common::hashEmptyField($detail, 'InsurancePaymentDetail.total', 0);
                $insurance_id = Common::hashEmptyField($detail, 'InsurancePaymentDetail.insurance_id');
                $grandtotal += $total;

                $hasPaid = $this->_callCicilanInsurance($insurance_id, 'paid');
                $totalMustBePaid = $this->_callCicilanInsurance($insurance_id, 'cicilan');

                if( $hasPaid >= $totalMustBePaid ) {
                    $status = 'paid';
                } else {
                    $status = 'half_paid';
                }

                $this->Insurance->id = $insurance_id;
                $this->Insurance->set('transaction_status', $status);
                $this->Insurance->save();
            }

            if( !empty($grandtotal) ) {
                $coaAsuransi = $this->InsurancePayment->Coa->CoaSettingDetail->getMerge(array(), 'Asuransi', 'CoaSettingDetail.label');
                $insurance_coa_id = Common::hashEmptyField($coaAsuransi, 'CoaSettingDetail.coa_id');

                $this->InsurancePayment->Coa->Journal->setJournal($grandtotal, array(
                    'credit' => $coa_id,
                    'debit' => $insurance_coa_id,
                ), array(
                    'date' => $payment_date,
                    'document_id' => $id,
                    'title' => $title,
                    'document_no' => $nodoc,
                    'type' => 'insurance_payment',
                ));
            }
        }
        
        return array(
            'msg' => __('Berhasil melakukan pembayaran asuransi'),
            'status' => 'success',
        );
    }

    function getMerge( $data, $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'InsurancePaymentDetail.insurance_payment_id' => $id,
            ),
        ));

        if( !empty($values) ) {
            $data['InsurancePaymentDetail'] = $values;
        }

        return $data;
    }
}
?>