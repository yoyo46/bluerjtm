<?php
class Approval extends AppModel {
	var $name = 'Approval';
	var $validate = array(
        'approval_module_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Modul harap dipilih'
            ),
            'checkUniq' => array(
                'rule' => array('checkUniq'),
                'message' => 'Pengaturan approval sudah terdaftar'
            ),
        ),
        'group_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Posisi yg Mengajukan dipilih'
            ),
        ),
	);

	var $belongsTo = array(
		'ApprovalModule' => array(
			'className' => 'ApprovalModule',
			'foreignKey' => 'approval_module_id',
		),
        'Group' => array(
            'className' => 'Group',
            'foreignKey' => 'group_id',
        ),
	);

    var $hasMany = array(
        'ApprovalDetail' => array(
            'className' => 'ApprovalDetail',
            'foreignKey' => 'approval_id',
        ),
    );

    function checkUniq() {
        $id = $this->id;
        $approval_module_id = !empty($this->data['Approval']['approval_module_id'])?trim($this->data['Approval']['approval_module_id']):false;
        $group_id = !empty($this->data['Approval']['group_id'])?trim($this->data['Approval']['group_id']):false;
        $check = $this->getData('first', array(
            'conditions' => array(
                'Approval.id <>' => $id,
                'Approval.approval_module_id' => $approval_module_id,
                'Approval.group_id' => $group_id,
            ),
            'contain' => false,
        ));

        if( !empty($check) ) {
            return false;
        } else {
            return true; 
        }
    }

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'contain' => array(
                'ApprovalModule',
                'Group',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Approval.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Approval.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Approval.status'] = 1;
                break;
        }

        if( !empty($options) && $is_merge ){
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
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

    function getUserOtorisasiApproval ( $modul, $group_id, $grand_total, $cash_bank_id = false ) {
        $result = false;
        $data = $this->getData('first', array(
            'conditions' => array(
                'ApprovalModule.slug' => $modul,
                'Approval.group_id' => $group_id,
            ),
        ));
        
        if( !empty($data) ) {
            $approval_id = !empty($data['Approval']['id'])?$data['Approval']['id']:false;
            $approval_detail_id = $this->ApprovalDetail->getData('list', array(
                'conditions' => array(
                    'ApprovalDetail.approval_id' => $approval_id,
                    'OR' => array(
                        array(
                            'ApprovalDetail.min_amount <=' => $grand_total,
                            'ApprovalDetail.max_amount >=' => $grand_total,
                        ),
                        array(
                            'ApprovalDetail.min_amount <=' => $grand_total,
                            'ApprovalDetail.max_amount' => 0,
                        ),
                        array(
                            'ApprovalDetail.min_amount' => 0,
                            'ApprovalDetail.max_amount' => 0,
                        ),
                    ),
                ),
                'fields' => array(
                    'ApprovalDetail.id', 'ApprovalDetail.id'
                ),
            ));

            if( !empty($approval_detail_id) ) {
                $result = $this->ApprovalDetail->ApprovalDetailPosition->getData('all', array(
                    'conditions' => array(
                        'ApprovalDetailPosition.approval_detail_id' => $approval_detail_id,
                    ),
                ));

                if( !empty($result) ) {
                    $this->CashBankAuth = ClassRegistry::init('CashBankAuth');

                    foreach ($result as $key => $value) {
                        $approval_detail_position_id = !empty($value['ApprovalDetailPosition']['id'])?$value['ApprovalDetailPosition']['id']:false;
                        $cashBankAuth = $this->CashBankAuth->getData('first', array(
                            'conditions' => array(
                                'CashBankAuth.approval_detail_position_id' => $approval_detail_position_id,
                                'CashBankAuth.cash_bank_id' => $cash_bank_id,
                            ),
                        ));
                        $value = array_merge($value, $cashBankAuth);
                        $result[$key] = $value;
                    }
                }
            }
        }

        return $result;
    }

    function getPositionPriority ( $data ) {
        $result = array();

        if( !empty($data) ) {
            foreach ($data as $key => $value) {
                if( !empty($value['ApprovalDetailPosition']['is_priority']) ) {
                    $result['Priority'][] = $value['ApprovalDetailPosition']['group_id'];
                } else {
                    $result['Normal'][] = $value['ApprovalDetailPosition']['group_id'];
                }
            }
        }

        return $result;
    }
}
?>