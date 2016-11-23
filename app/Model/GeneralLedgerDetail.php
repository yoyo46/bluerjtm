<?php
class GeneralLedgerDetail extends AppModel {
	var $name = 'GeneralLedgerDetail';

    var $belongsTo = array(
        'GeneralLedger' => array(
            'className' => 'GeneralLedger',
            'foreignKey' => 'general_ledger_id',
        ),
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        )
    );

	var $validate = array(
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA harap dipilih'
            ),
        ),
        'debit' => array(
            'choosen' => array(
                'rule' => array('choosen'),
                'message' => 'Nilai debit atau kredit harap diisi'
            ),
            'choose_one' => array(
                'rule' => array('choose_one'),
                'message' => 'Mohon hanya mengisi nilai debit atau kredit'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty'=> true,
                'message' => 'Debit harus berupa angka'
            ),
        ),
        'credit' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty'=> true,
                'message' => 'Kredit harus berupa angka'
            ),
        ),
	);

    function choosen() {
        $data = $this->data;
        $debit = $this->filterEmptyField($data, 'GeneralLedgerDetail', 'debit', 0);
        $credit = $this->filterEmptyField($data, 'GeneralLedgerDetail', 'credit', 0);

        $debit = intval($debit);
        $credit = intval($credit);

        if( empty($debit) && empty($credit) ) {
            return false;
        } else {
            return true;
        }
    }

    function choose_one() {
        $data = $this->data;
        $debit = $this->filterEmptyField($data, 'GeneralLedgerDetail', 'debit', 0);
        $credit = $this->filterEmptyField($data, 'GeneralLedgerDetail', 'credit', 0);

        $debit = intval($debit);
        $credit = intval($credit);

        if( !empty($debit) && !empty($credit) ) {
            return false;
        } else {
            return true;
        }
    }

	function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'GeneralLedgerDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = $options['order'];
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'GeneralLedgerDetail.general_ledger_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            $data['GeneralLedgerDetail'] = $values;
        }

        return $data;
    }

    function setJournal ( $id, $data, $nodoc = false ) {
        $transaction_date = $this->filterEmptyField($data, 'GeneralLedger', 'transaction_date');
        $transaction_status = $this->filterEmptyField($data, 'GeneralLedger', 'transaction_status');
        $details = $this->filterEmptyField($data, 'GeneralLedgerDetail');

        if( $transaction_status == 'posting' && !empty($details) ) {
            $title = sprintf(__('Jurnal umum #%s '), $nodoc);
            $title = $this->filterEmptyField($data, 'GeneralLedger', 'note', $title);

            $this->GeneralLedger->User->Journal->deleteJournal($id, array(
                'general_ledger',
            ));

            foreach ($details as $key => $detail) {
                $coa_id = $this->filterEmptyField($detail, 'GeneralLedgerDetail', 'coa_id');
                $debit = $this->filterEmptyField($detail, 'GeneralLedgerDetail', 'debit', false, array(
                    'format' => 'number',
                ));
                $credit = $this->filterEmptyField($detail, 'GeneralLedgerDetail', 'credit', false, array(
                    'format' => 'number',
                ));
                $options = array();

                if( !empty($debit) ) {
                    $total = $debit;
                    $options = array(
                        'debit' => $coa_id,
                    );
                } else {
                    $total = $credit;
                    $options = array(
                        'credit' => $coa_id,
                    );
                }

                $this->GeneralLedger->User->Journal->setJournal($total, $options, array(
                    'date' => $transaction_date,
                    'document_id' => $id,
                    'title' => $title,
                    'document_no' => $nodoc,
                    'type' => 'general_ledger',
                ));
            }
        }
    }
}
?>