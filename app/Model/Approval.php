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
        'employe_position_id' => array(
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
        'EmployePosition' => array(
            'className' => 'EmployePosition',
            'foreignKey' => 'employe_position_id',
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
        $employe_position_id = !empty($this->data['Approval']['employe_position_id'])?trim($this->data['Approval']['employe_position_id']):false;
        $check = $this->getData('first', array(
            'conditions' => array(
                'Approval.id <>' => $id,
                'Approval.approval_module_id' => $approval_module_id,
                'Approval.employe_position_id' => $employe_position_id,
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
            'order'=> array(
                'Approval.created' => 'DESC',
                'Approval.id' => 'DESC',
            ),
            'contain' => array(
                'ApprovalModule',
                'EmployePosition',
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

    function getMerge ( $data = false, $id = false ) {
        if( empty($data['Approval']) ) {
            $default_options = array(
                'conditions' => array(
                    'Approval.id'=> $id,
                ),
                'contain' => false,
            );

            $value = $this->getData('first', $default_options);
            $data = array_merge($data, $value);
        }

        return $data;
    }

    function _callApprovalId ($modul, $user_position_id) {
        $userData = Configure::read('__Site.config_user_data');
        $approval_position_id = $this->filterEmptyField($userData, 'Employe', 'employe_position_id');
        $data = $this->getData('first', array(
            'conditions' => array(
                'ApprovalModule.slug' => $modul,
                'Approval.employe_position_id' => $user_position_id,
            ),
        ));

        $approval_id = $this->filterEmptyField($data, 'Approval', 'id');
        $approval_name = $this->filterEmptyField($data, 'ApprovalModule', 'name');
        
        $data = $this->ApprovalDetail->ApprovalDetailPosition->getData('first', array(
            'conditions' => array(
                'ApprovalDetail.status' => 1,
                'ApprovalDetail.approval_id' => $approval_id,
                'ApprovalDetailPosition.employe_position_id' => $approval_position_id,
            ),
            'contain' => array(
                'ApprovalDetail',
            ),
        ));
        $approval_detail_id = $this->filterEmptyField($data, 'ApprovalDetail', 'id');
        $approval_detail_position_id = $this->filterEmptyField($data, 'ApprovalDetailPosition', 'id');

        return array(
            'DocumentAuth' => array(
                'approval_id' => $approval_id,
                'approval_detail_id' => $approval_detail_id,
                'approval_detail_position_id' => $approval_detail_position_id,
                'approval_name' => $approval_name,
            ),
        );
    }

    function getUserOtorisasiApproval ( $modul, $employe_position_id, $grand_total, $document_id = false ) {
        $result = false;
        $data = $this->getData('first', array(
            'conditions' => array(
                'ApprovalModule.slug' => $modul,
                'Approval.employe_position_id' => $employe_position_id,
            ),
        ));

        if( !empty($data) ) {
            $approval_id = !empty($data['Approval']['id'])?$data['Approval']['id']:false;
            $conditions = array(
                'ApprovalDetail.approval_id' => $approval_id,
            );

            if( !empty($grand_total) ) {
                $conditions['OR'] = array(
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
                );
            }

            $approval_detail_id = $this->ApprovalDetail->getData('list', array(
                'conditions' => $conditions,
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
                    $this->DocumentAuth = ClassRegistry::init('DocumentAuth');

                    foreach ($result as $key => $value) {
                        $approval_detail_position_id = !empty($value['ApprovalDetailPosition']['id'])?$value['ApprovalDetailPosition']['id']:false;
                        $documentAuth = $this->DocumentAuth->getData('first', array(
                            'conditions' => array(
                                'DocumentAuth.approval_detail_position_id' => $approval_detail_position_id,
                                'DocumentAuth.document_id' => $document_id,
                            ),
                        ));
                        $value = array_merge($value, $documentAuth);
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
                    $result['Priority'][] = $value['ApprovalDetailPosition']['employe_position_id'];
                } else {
                    $result['Normal'][] = $value['ApprovalDetailPosition']['employe_position_id'];
                }
            }
        }

        return $result;
    }

    function _callNeedApproval ( $module, $total ) {
        $employe_position_id = Configure::read('__Site.User.employe_position_id');
        $users = false;
        $approval_module = $this->ApprovalModule->getMerge(array(), $module, 'ApprovalModule.slug');
        $module_id = $this->filterEmptyField($approval_module, 'ApprovalModule', 'id');

        $values = $this->ApprovalDetail->getData('all', array(
            'conditions' => array(
                'Approval.status' => 1,
                'Approval.approval_module_id' => $module_id,
                'Approval.employe_position_id' => $employe_position_id,
                'OR' => array(
                    array(
                        'ApprovalDetail.min_amount <=' => $total,
                        'ApprovalDetail.max_amount >=' => $total,
                    ),
                    array(
                        'ApprovalDetail.min_amount <=' => $total,
                        'ApprovalDetail.max_amount' => 0,
                    ),
                    array(
                        'ApprovalDetail.min_amount' => 0,
                        'ApprovalDetail.max_amount' => 0,
                    ),
                ),
            ),
            'contain' => array(
                'Approval',
            ),
        ));

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = !empty($value['ApprovalDetail']['id'])?$value['ApprovalDetail']['id']:false;

                $value = $this->ApprovalDetail->ApprovalDetailPosition->getMerge($value, $id);
                $values[$key] = $value;
            }

            $approverEmployeePositions = Set::extract('/ApprovalDetailPosition/ApprovalDetailPosition/employe_position_id', $values);
            $users = $this->ApprovalDetail->ApprovalDetailPosition->EmployePosition->Employe->User->getData('list', array(
                'conditions' => array(
                    'Employe.employe_position_id' => $approverEmployeePositions,
                ),
                'fields' => array(
                    'User.id', 'User.id',
                ),
            ));

            if( !empty($users) ) {
                $users = array_unique($users);
            }
        }

        return $users;
    }

    function _callGetDataToApprove ( $module_slug ) {
        $conditions = array();
        $employe_position_id = Configure::read('__Site.User.employe_position_id');
        $approval_module = $this->ApprovalModule->find('first', array(
            'conditions' => array(
                'ApprovalModule.slug' => $module_slug,
            ),
        ));

        switch ($module_slug) {
            case 'supplier_quotation':
                $modelName = 'SupplierQuotation';
                break;
            
            default:
                $modelName = 'CashBank';
                break;
        }
        
        if( !empty($approval_module) ) {
            $approval_module_id = !empty($approval_module['ApprovalModule']['id'])?$approval_module['ApprovalModule']['id']:false;
            $approvals = $this->getData('list', array(
                'conditions' => array(
                    'Approval.approval_module_id' => $approval_module_id,
                ),
                'contain' => false,
                'fields' => array(
                    'Approval.id', 'Approval.id',
                ),
            ));

            $values = $this->ApprovalDetail->ApprovalDetailPosition->getData('all', array(
                'conditions' => array(
                    'ApprovalDetailPosition.employe_position_id' => $employe_position_id,
                    'ApprovalDetail.approval_id' => $approvals,
                    'ApprovalDetail.status' => 1,
                ),
                'contain' => array(
                    'ApprovalDetail',
                ),
                'group' => false,
            ));
            $conditions = array(
                'OR' => array(
                    $modelName.'.branch_id' => Configure::read('__Site.config_branch_id'),
                ),
            );

            if( !empty($values) ) {
                foreach ($values as $key => $value) {
                    $approval_id = !empty($value['ApprovalDetail']['approval_id'])?$value['ApprovalDetail']['approval_id']:false;

                    $value = $this->getMerge($value, $approval_id);
                    $employe_position_id = !empty($value['Approval']['employe_position_id'])?$value['Approval']['employe_position_id']:false;
                    $min_amount = !empty($value['ApprovalDetail']['min_amount'])?$value['ApprovalDetail']['min_amount']:false;
                    $max_amount = !empty($value['ApprovalDetail']['max_amount'])?$value['ApprovalDetail']['max_amount']:false;

                    $employes = $this->ApprovalDetail->ApprovalDetailPosition->EmployePosition->Employe->getListByPosition($employe_position_id);
                    $users = $this->ApprovalDetail->ApprovalDetailPosition->EmployePosition->Employe->User->getData('list', array(
                        'conditions' => array(
                            'User.employe_id' => $employes,
                        ),
                        'fields' => array(
                            'User.id', 'User.id',
                        ),
                    ));

                    $conditions['OR'][$key][$modelName.'.user_id'] = $users;

                    if( !empty($min_amount) ) {
                        $conditions['OR'][$key][$modelName.'.grand_total >='] = $min_amount;
                    }
                    if( !empty($max_amount) ) {
                        $conditions['OR'][$key][$modelName.'.grand_total <='] = $max_amount;
                    }

                    $values[$key] = $value;
                }
            }
        }

        return $conditions;
    }

    function _callAuthApproval ( $data ) {
        $userData = Configure::read('__Site.config_user_data');
        $user_position_id = $this->filterEmptyField($userData, 'Employe', 'employe_position_id');

        if( !empty($data) ) {
            $approvals = Set::extract('/EmployePosition/id', $data);
        } else {
            $approvals = array();
        }

        $key = array_search($user_position_id, $approvals);

        if( is_numeric($key) && empty($data[$key]['DocumentAuth']) ) {
            return true;
        } else {
            return false;
        }
    }
}
?>