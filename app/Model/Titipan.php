<?php
class Titipan extends AppModel {
	var $validate = array(
        // 'nodoc' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'No Dokumen name harap diisi'
        //     ),
        // ),
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
        'User' => array(
            'foreignKey' => 'user_id',
        ),
        'Branch' => array(
            'foreignKey' => 'branch_id',
        ),
        'TtujPayment' => array(
            'foreignKey' => 'ttuj_payment_id',
        ),
        'Coa' => array(
            'foreignKey' => 'coa_id',
        ),
	);

    var $hasMany = array(
        'TitipanDetail' => array(
            'foreignKey' => 'titipan_id',
        ),
    );

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:false;
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(
                'Titipan.status' => 1,
            ),
            'order'=> array(
                'Titipan.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($branch) ) {
            $default_options['conditions']['Titipan.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($status) ) {
            $default_options['conditions']['Titipan.transaction_status'] = $status;
        }

        return $this->full_merge_options($default_options, $options, $find);
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $note = !empty($data['named']['note'])?$data['named']['note']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $transaction_status = !empty($data['named']['transaction_status'])?urldecode($data['named']['transaction_status']):false;
        
        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Titipan.transaction_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Titipan.transaction_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Titipan.nodoc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($note)){
            $default_options['conditions']['Titipan.note LIKE'] = '%'.$note.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(Titipan.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        if( !empty($transaction_status) ) {
            $default_options['conditions']['Titipan.transaction_status'] = $transaction_status;
        }
        
        return $default_options;
    }

    function beforeSave( $options = array() ) {
        $id = $this->id;
        $id = Common::hashEmptyField($this->data, 'Titipan.id', $id);
        $nodoc = Common::hashEmptyField($this->data, 'Titipan.nodoc');

        if( empty($id) ) {
            $this->data = Hash::insert($this->data, 'Titipan.branch_id', Configure::read('__Site.config_branch_id'));
            $this->data = Hash::insert($this->data, 'Titipan.user_id', Configure::read('__Site.config_user_id'));

            if( empty($nodoc) ) {
                $this->data = Hash::insert($this->data, 'Titipan.nodoc', $this->generateNoDoc());
            }
        }
    }

    function generateNoDoc(){
        $default_id = 1;
        $branch_code = Configure::read('__Site.Branch.code');

        $data = $this->data;
        $format_id = sprintf('%s/%s/%s/', $branch_code, date('Y'), date('m'));
        $last_data = $this->getData('first', array(
            'conditions' => array(
                'Titipan.nodoc LIKE' => ''.$format_id.'%',
            ),
            'order' => array(
                'Titipan.nodoc' => 'DESC'
            ),
            'fields' => array(
                'Titipan.nodoc'
            )
        ), array(
            'branch' => false,
        ));
        $nodoc = Common::hashEmptyField($last_data, 'Titipan.nodoc');

        if(!empty($nodoc)){
            $str_arr = explode($format_id, $nodoc);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }

        $id = str_pad($default_id, 3,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }
}
?>