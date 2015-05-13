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

    function setJournal ( $document_id = false, $coa_name = false, $debit = 0, $credit = 0, $type = false ) {
        $this->CoaSetting = ClassRegistry::init('CoaSetting');
        $coaSetting = $this->CoaSetting->getData('first', array(
            'conditions' => array(
                'CoaSetting.status' => 1
            ),
        ));

        if( !empty($coaSetting['CoaSetting'][$coa_name]) ) {
            $data['Journal'] = array(
                'document_id' => $document_id,
                'coa_id' => $coaSetting['CoaSetting'][$coa_name],
                'debit' => $debit,
                'credit' => $credit,
                'type' => $type,
            );
            $this->create();
            $this->set($data);

            if( $this->save($data) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function deleteJournal ( $document_id = false, $type = false ) {
        $journal = $this->find('first', array(
            'conditions' => array(
                'Journal.document_id' => $document_id,
                'Journal.type' => $type,
                'Journal.status' => 1,
            ),
        ));

        if( !empty($journal) ) {
            if( $this->updateAll(
                array(
                    'Journal.status' => 0
                ),
                array(
                    'Journal.document_id' => $journal['Journal']['document_id'],
                    'Journal.type' => $type,
                )
            ) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
?>