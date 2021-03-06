<?php
class Debt extends AppModel {
	var $validate = array(
        'nodoc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen name harap diisi'
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal Transaksi harap diisi'
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
		'Coa' => array(
			'foreignKey' => 'coa_id',
		),
        'User' => array(
            'foreignKey' => 'user_id',
        ),
        'Cogs' => array(
            'foreignKey' => 'cogs_id',
        ),
	);

    var $hasMany = array(
        'DebtDetail' => array(
            'foreignKey' => 'debt_id',
        ),
        'DebtPaymentDetail' => array(
            'foreignKey' => 'debt_id',
        ),
        'ViewDebtCard' => array(
            'foreignKey' => 'debt_id',
        ),
    );

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $transaction_status = isset($elements['transaction_status'])?$elements['transaction_status']:false;
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Debt.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($branch) ) {
            $default_options['conditions']['Debt.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($status) ) {
            switch ($status) {
                case 'active':
                    $default_options['conditions']['Debt.status'] = 1;
                    break;

                case 'non-active':
                    $default_options['conditions']['Debt.status'] = 0;
                    break;
            }
        }

        if( !empty($transaction_status) ) {
            $default_options['conditions']['Debt.transaction_status'] = $transaction_status;
        }

        return $this->full_merge_options($default_options, $options, $find);
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $total = !empty($data['named']['total'])?$data['named']['total']:false;
        $transaction_status = !empty($data['named']['transaction_status'])?urldecode($data['named']['transaction_status']):false;
        $coa = !empty($data['named']['coa'])?urldecode($data['named']['coa']):false;
        $staff_name = !empty($data['named']['staff_name'])?urldecode($data['named']['staff_name']):false;
        
        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Debt.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Debt.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Debt.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($note)){
            $default_options['conditions']['Debt.note LIKE'] = '%'.$note.'%';
        }
        if(!empty($total)){
            $default_options['conditions']['Debt.total LIKE'] = '%'.$total.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(Debt.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if( !empty($transaction_status) ) {
            $default_options['conditions']['Debt.transaction_status'] = $transaction_status;
        }
        if( !empty($coa) ) {
            $default_options['conditions']['Debt.coa_id'] = $coa;
        }

        if( !empty($staff_name) ) {
            $staffs = $this->DebtDetail->ViewStaff->getData('list', array(
                'conditions' => array(
                    'ViewStaff.name_code LIKE' => '%'.$staff_name.'%',
                ),
                'fields' => array(
                    'ViewStaff.id',
                    'ViewStaff.id',
                ),
            ));

            $default_options['conditions']['DebtDetail.employe_id'] = $staffs;
            $default_options['contain'][] = 'DebtDetail';

            $this->unBindModel(array(
                'hasMany' => array(
                    'DebtDetail'
                )
            ));

            $this->bindModel(array(
                'hasOne' => array(
                    'DebtDetail' => array(
                        'className' => 'DebtDetail',
                        'foreignKey' => 'debt_id',
                    ),
                )
            ), false);
        }
        
        return $default_options;
    }

    function generateNoDoc(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');

        $data = $this->data;
        $format_id = sprintf('%s/%s/%s/', $branch_code, date('Y'), date('m'));
        $last_data = $this->getData('first', array(
            'conditions' => array(
                'Debt.nodoc LIKE' => ''.$format_id.'%',
            ),
            'order' => array(
                'Debt.nodoc' => 'DESC'
            ),
            'fields' => array(
                'Debt.nodoc'
            )
        ), array(
            'branch' => false,
        ));
        $nodoc = Common::hashEmptyField($last_data, 'Debt.nodoc');

        if(!empty($nodoc)){
            $str_arr = explode($format_id, $nodoc);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }

        $id = str_pad($default_id, 3,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

    function get_total_debt( $employe_id, $type ){
        $this->ViewDebtCard->virtualFields['total_credit'] = 'SUM(ViewDebtCard.credit)';
        $this->ViewDebtCard->virtualFields['total_debit'] = 'SUM(ViewDebtCard.debit)';

        $value =  $this->ViewDebtCard->getData('first', array(
            'conditions' => array(
                'ViewDebtCard.employe_id' => $employe_id,
                'ViewDebtCard.type' => $type,
            ),
        ));
        $total_credit = Common::hashEmptyField($value, 'ViewDebtCard.total_credit');
        $total_debit = Common::hashEmptyField($value, 'ViewDebtCard.total_debit');

        return $total_credit - $total_debit;
    }

    function get_debt( $options = array() ){
        $options =  $this->getData('paginate', $options, array(
            'transaction_status' => 'posting',
        ));
        $value =  $this->DebtDetail->getData('first', $options, array(
            'status' => 'unpaid',
        ));

        if( !empty($value) ) {
            $document_id = Common::hashEmptyField($value, 'DebtDetail.id');
            $total = Common::hashEmptyField($value, 'DebtDetail.total');

            $last_paid = $this->DebtPaymentDetail->getTotalPayment($document_id);

            $value['DebtDetail']['total_debt'] = $total - $last_paid;
        }

        return $value;
    }

    function beforeSave( $options = array() ) {
        $id = $this->id;
        $id = Common::hashEmptyField($this->data, 'Debt.id', $id);
        $nodoc = Common::hashEmptyField($this->data, 'Debt.nodoc');
        $import = Common::hashEmptyField($this->data, 'Debt.import');
        $note = Common::hashEmptyField($this->data, 'Debt.note');

        if( empty($id) && empty($nodoc) ) {
            $nodoc = $this->generateNoDoc();
            $this->data = Hash::insert($this->data, 'Debt.nodoc', $nodoc);
            $this->data = Hash::insert($this->data, 'Debt.user_id', Configure::read('__Site.config_user_id'));

            if( !empty($import) && empty($note) ) {
                $this->data = Hash::insert($this->data, 'Debt.note', __('Import Saldo Hutang Karyawan %s No. %s', date('d.m.y'), $nodoc));
            }
        }
    }
}
?>