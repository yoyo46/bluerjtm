<?php
class DebtPayment extends AppModel {
	var $name = 'DebtPayment';
	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Dokumen harap diisi'
            ),
            'checkUnique' => array(
                'rule' => array('checkUnique', 'nodoc'),
                'message' => 'No. Dok sudah terdaftar, silahkan masukan No. Dok lain',
            ),
        ),
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Account Kas/Bank harap dipilih'
            ),
        ),
        'total_payment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Total pembayaran harap diisi'
            ),
        ),
        'date_payment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal pembayaran harap diisi'
            ),
        ),
	);

    var $hasMany = array(
        'DebtPaymentDetail' => array(
            'foreignKey' => 'debt_payment_id',
            'conditions' => array(
                'DebtPaymentDetail.status' => 1,
            ),
        ),
    );

    var $belongsTo = array(
        'Coa' => array(
            'foreignKey' => 'coa_id',
        )
    );

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'DebtPayment.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['DebtPayment.status'] = 1;
                break;

            case 'non-active':
                $default_options['conditions']['DebtPayment.status'] = 0;
                break;
        }

        if( !empty($branch) ) {
                $default_options['conditions']['DebtPayment.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        return $this->full_merge_options($default_options, $options, $find);
    }

    function beforeSave( $options = array() ) {
        $id = $this->id;
        $id = Common::hashEmptyField($this->data, 'DebtPayment.id', $id);

        if( empty($id) ) {
            // $this->data = Hash::insert($this->data, 'DebtPayment.nodoc', $this->generateNoDoc());
        }
    }

    function generateNoDoc(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');
        $format_id = sprintf('PH-%s-%s-', $branch_code, date('y'));

        $last_data = $this->getData('first', array(
            'conditions' => array(
                'DebtPayment.nodoc LIKE' => ''.$format_id.'%',
            ),
            'order' => array(
                'DebtPayment.nodoc' => 'DESC'
            ),
            'fields' => array(
                'DebtPayment.nodoc'
            )
        ));
        $nodoc = $this->filterEmptyField($last_data, 'DebtPayment', 'nodoc');

        if(!empty($nodoc)){
            $str_arr = explode($format_id, $nodoc);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }

        $id = str_pad($default_id, 5,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;

        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;
        $staff_name = !empty($data['named']['staff_name'])?urldecode($data['named']['staff_name']):false;

        $status = !empty($data['named']['status'])?$data['named']['status']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(DebtPayment.date_payment, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(DebtPayment.date_payment, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['DebtPayment.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(DebtPayment.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($note)){
            $default_options['conditions']['DebtPayment.description LIKE'] = '%'.$note.'%';
        }

        if( !empty($staff_name) ) {
            $staffs = $this->DebtPaymentDetail->ViewStaff->getData('list', array(
                'conditions' => array(
                    'ViewStaff.name_code LIKE' => '%'.$staff_name.'%',
                ),
                'fields' => array(
                    'ViewStaff.id',
                    'ViewStaff.id',
                ),
            ));

            $default_options['conditions']['DebtPaymentDetail.employe_id'] = $staffs;
            $default_options['contain'][] = 'DebtPaymentDetail';

            $this->unBindModel(array(
                'hasMany' => array(
                    'DebtPaymentDetail'
                )
            ));

            $this->bindModel(array(
                'hasOne' => array(
                    'DebtPaymentDetail' => array(
                        'className' => 'DebtPaymentDetail',
                        'foreignKey' => 'debt_payment_id',
                    ),
                )
            ), false);
        }

        return $default_options;
    }

    function payment_delete($ttuj_payment_id, $canceled_note, $canceled_date){
        $value = $this->getData('first', array(
            'conditions' => array(
                'DebtPayment.ttuj_payment_id' => $ttuj_payment_id,
            ),
        ));

        if( !empty($value) ){
            $id = Common::hashEmptyField($value, 'DebtPayment.id');
            $value = $this->DebtPaymentDetail->getMerge($value, $id);

            $date_payment = Common::hashEmptyField($value, 'DebtPayment.date_payment');
            $cogs_id = Common::hashEmptyField($value, 'DebtPayment.cogs_id');

            $data['DebtPayment']['canceled_note'] = $canceled_note;
            $data['DebtPayment']['canceled_date'] = $canceled_date;
            $data['DebtPayment']['is_canceled'] = 1;

            $this->id = $id;
            $this->set($data);

            if($this->save()){
                $debt_no = Common::hashEmptyField($value, 'DebtPayment.nodoc');
                $coa_id = Common::hashEmptyField($value, 'DebtPayment.coa_id');

                if( !empty($value['DebtPaymentDetail']) ) {
                    foreach ($value['DebtPaymentDetail'] as $key => $detail) {
                        $debt_id = Common::hashEmptyField($detail, 'DebtPaymentDetail.debt_id');
                        $debt_detail_id = Common::hashEmptyField($detail, 'DebtPaymentDetail.debt_detail_id');
                        $total_dibayar = $this->DebtPaymentDetail->getTotalPayment($debt_detail_id);
                        $flagPaid = 'none';

                        if( !empty($total_dibayar) ) {
                            $flagPaid = 'half';
                        }
                        
                        $debt = $this->DebtPaymentDetail->DebtDetail->findById($debt_detail_id);
                        $debt = $this->DebtPaymentDetail->DebtDetail->getMergeList($debt, array(
                            'contain' => array(
                                'Debt',
                                'ViewStaff',
                            ),
                        ));

                        $debt_date = Common::hashEmptyField($debt, 'Debt.transaction_date');

                        $this->DebtPaymentDetail->DebtDetail->set('paid_status', $flagPaid);
                        $this->DebtPaymentDetail->DebtDetail->id = $debt_detail_id;
                        
                        $this->DebtPaymentDetail->DebtDetail->save();
                    }
                }

                if( !empty($value['DebtPayment']['total_payment']) ) {
                    $titleJournal = __('pembayaran biaya Hutang Karyawan');
                    $titleJournal = sprintf(__('<i>Pembatalan</i> %s'), Common::hashEmptyField($value, 'DebtPayment.description', $titleJournal));
                    $totalPayment = Common::hashEmptyField($value, 'DebtPayment.total_payment');

                    $coaDebt = $this->Coa->CoaSettingDetail->getMerge(array(), 'Debt', 'CoaSettingDetail.label');
                    $debt_coa_id = !empty($coaDebt['CoaSettingDetail']['coa_id'])?$coaDebt['CoaSettingDetail']['coa_id']:false;

                    if( !empty($debt_coa_id) ) {
                        $this->Coa->Journal->setJournal($totalPayment, array(
                            'debit' => $debt_coa_id,
                            'credit' => $coa_id,
                        ), array(
                            'cogs_id' => $cogs_id,
                            'date' => $date_payment,
                            'document_id' => $id,
                            'title' => $titleJournal,
                            'document_no' => $debt_no,
                            'type' => 'debt_payment_void',
                        ));
                    }
                }

                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
?>