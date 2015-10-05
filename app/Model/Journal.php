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
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        )
    );

    function setJournal ( $document_id = false, $document_no = false, $coas, $type = false ) {
        if( !empty($coas) && is_array($coas) ) {
            foreach ($coas as $coa_name => $total) {
                $coaSetting = $this->User->CoaSetting->getData('first', array(
                    'conditions' => array(
                        'CoaSetting.status' => 1
                    ),
                ));

                if( !empty($coaSetting['CoaSetting'][$coa_name]) ) {
                    $coa_id = $coaSetting['CoaSetting'][$coa_name];
                } else {
                    $coa_id = false;
                }

                $user_id = Configure::read('__Site.config_user_id');
                $coa = $this->Coa->getData('first', array(
                    'conditions' => array(
                        'Coa.id' => $coa_id,
                    ),
                ));
                $saldo_awal = !empty($coa['Coa']['balance'])?$coa['Coa']['balance']:false;

                $data['Journal'] = array(
                    'user_id' => $user_id,
                    'coa_id' => $coa_id,
                    'document_id' => $document_id,
                    'document_no' => $document_no,
                    'saldo_awal' => $saldo_awal,
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

    function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'Journal.status' => 1,
            ),
            'order'=> array(
                'Journal.created' => 'DESC',
                'Journal.document_id' => 'ASC',
                'Journal.type' => 'ASC',
                'Journal.id' => 'DESC',
            ),
            'fields' => array(),
            'group' => array(),
            'contain' => array(
                'Coa'
            ),
        );

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }
}
?>