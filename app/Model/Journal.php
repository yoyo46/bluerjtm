<?php
class Journal extends AppModel {
	var $name = 'Journal';
	var $validate = array(
		'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'COA harap dipilih'
            ),
		),
        'debit' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Debit harus berupa angka',
            ),
        ),
        'credit' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Kredit harus berupa angka',
            ),
        )
	);

    var $belongsTo = array(
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
            'fields' => '',
            'order' => ''
        )
    );

    function setJournal ( $coa_id = false, $debit = 0, $credit = 0 ) {
        $data['Journal'] = array(
            'coa_id' => $coa_id,
            'debit' => $debit,
            'credit' => $credit,
        );
        $this->Journal->create();
        $this->Journal->set($data);

        if( $this->Journal->save($data) ) {
            return false;
        } else {
            return false;
        }
    }
}
?>