<?php
class BranchCoa extends AppModel {
	var $name = 'BranchCoa';
	var $validate = array(
        'coa_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Coa harap dipilih'
            ),
        ),
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'Coa' => array(
            'className' => 'Coa',
            'foreignKey' => 'coa_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'BranchCoa.status' => 1,
            ),
            'order'=> array(
                'BranchCoa.id' => 'ASC'
            ),
            'fields' => array(),
            'contain' => array(),
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getList($id){
        $values = $this->getData('list', array(
            'conditions' => array(
                'BranchCoa.branch_id' => $id
            ),
            'fields' => array(
                'BranchCoa.id', 'BranchCoa.coa_id',
            ),
        ));

        return $values;
    }

    function doSave ( $branch_id, $coa_id ) {
        $value = $this->Branch->getData('first', array(
            'conditions' => array(
                'Branch.id' => $branch_id,
            ),
        ));
        $coa = $this->Coa->getData('first', array(
            'conditions' => array(
                'Coa.id' => $coa_id,
            ),
        ));

        if( !empty($value) && !empty($coa) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    'BranchCoa.branch_id' => $branch_id,
                    'BranchCoa.coa_id' => $coa_id,
                ),
            ));

            if( !empty($value) ) {
                $id = !empty($value['BranchCoa']['id'])?$value['BranchCoa']['id']:false;
                $this->id = $id;
                $this->set('status', 0);
            } else {
                $this->create();
                $this->set(array(
                    'BranchCoa' => array(
                        'branch_id' => $branch_id,
                        'coa_id' => $coa_id,
                    ),
                ));
            }

            if( $this->save() ) {
                $result = array(
                    'msg' => __('Berhasil menyimpan COA pada cabang'),
                    'status' => 'success',
                );
            } else {
                $result = array(
                    'msg' => __('Gagal menyimpan COA pada cabang'),
                    'status' => 'error',
                );
            }
        } else {
            $result = array(
                'msg' => __('Cabang atau COA tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function doCheckAll ( $branch_id, $parent_id ) {
        $value = $this->Branch->getData('first', array(
            'conditions' => array(
                'Branch.id' => $branch_id,
            ),
        ));
        $coas = $this->Coa->children($parent_id);

        if( !empty($value) && !empty($coas) ) {
            if( !empty($coas) ) {
                $flagSave = true;

                foreach ($coas as $key => $coa) {
                    $coa_id = !empty($coa['Coa']['id'])?$coa['Coa']['id']:false;
                    $level = !empty($coa['Coa']['level'])?$coa['Coa']['level']:false;
                    $status = !empty($coa['Coa']['status'])?$coa['Coa']['status']:false;

                    if( $level == 4 && !empty($status) ) {
                        $value = $this->getData('first', array(
                            'conditions' => array(
                                'BranchCoa.branch_id' => $branch_id,
                                'BranchCoa.coa_id' => $coa_id,
                            ),
                        ));

                        if( !empty($value) ) {
                            $id = !empty($value['BranchCoa']['id'])?$value['BranchCoa']['id']:false;
                            $this->id = $id;
                            $this->set('status', 1);
                        } else {
                            $this->create();
                            $this->set(array(
                                'BranchCoa' => array(
                                    'branch_id' => $branch_id,
                                    'coa_id' => $coa_id,
                                ),
                            ));
                        }
                        
                        if( !$this->save() ) {
                            $flagSave = false;
                        }
                    }
                }

                if( $flagSave ) {
                    $result = array(
                        'msg' => __('Berhasil menyimpan COA pada cabang'),
                        'status' => 'success',
                    );
                } else {
                    $result = array(
                        'msg' => __('Gagal menyimpan COA pada cabang'),
                        'status' => 'error',
                    );
                }
            }
        } else {
            $result = array(
                'msg' => __('Cabang atau COA tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function doUnCheckAll ( $branch_id, $parent_id ) {
        $value = $this->Branch->getData('first', array(
            'conditions' => array(
                'Branch.id' => $branch_id,
            ),
        ));
        $coas = $this->Coa->children($parent_id);

        if( !empty($value) && !empty($coas) ) {
            if( !empty($coas) ) {
                $flagSave = true;

                foreach ($coas as $key => $coa) {
                    $coa_id = !empty($coa['Coa']['id'])?$coa['Coa']['id']:false;
                    $level = !empty($coa['Coa']['level'])?$coa['Coa']['level']:false;
                    $status = !empty($coa['Coa']['status'])?$coa['Coa']['status']:false;

                    if( $level == 4 && !empty($status) ) {
                        $value = $this->getData('first', array(
                            'conditions' => array(
                                'BranchCoa.branch_id' => $branch_id,
                                'BranchCoa.coa_id' => $coa_id,
                            ),
                        ));

                        if( !empty($value) ) {
                            $id = !empty($value['BranchCoa']['id'])?$value['BranchCoa']['id']:false;
                            $this->id = $id;
                            $this->set('status', 0);
                            
                            if( !$this->save() ) {
                                $flagSave = false;
                            }
                        }
                    }
                }

                if( $flagSave ) {
                    $result = array(
                        'msg' => __('Berhasil menyimpan COA pada cabang'),
                        'status' => 'success',
                    );
                } else {
                    $result = array(
                        'msg' => __('Gagal menyimpan COA pada cabang'),
                        'status' => 'error',
                    );
                }
            }
        } else {
            $result = array(
                'msg' => __('Cabang atau COA tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function getCoas ( $fields = false, $is_cash_bank = true, $elements = array() ) {
        $status = $this->filterEmptyField($elements, 'status');

        $conditions = array(
            'BranchCoa.branch_id' => Configure::read('__Site.config_branch_id'),
            'Coa.status' => 1,
        );

        if( empty($fields) ) {
            $fields = array(
                'Coa.id', 'BranchCoa.coa_name'
            );
        }
        if( !empty($is_cash_bank) ) {
            $conditions['Coa.is_cash_bank'] = 1;
        }

        switch ($status) {
            case 'non-cashbank':
                $conditions['Coa.is_cash_bank'] = 0;
                break;
        }

        $this->virtualFields['coa_name'] = 'CASE WHEN Coa.with_parent_code IS NOT NULL AND Coa.with_parent_code <> \'\' THEN CONCAT(Coa.with_parent_code, \' - \', Coa.name) WHEN Coa.code <> \'\' THEN CONCAT(Coa.code, \' - \', Coa.name) ELSE Coa.name END';
        $coas = $this->getData('list', array(
            'conditions' => $conditions,
            'contain' => array(
                'Coa',
            ),
            'fields' => $fields,
        ));

        return $coas;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $name = !empty($data['named']['name'])?urldecode($data['named']['name']):false;
        $code = !empty($data['named']['code'])?urldecode($data['named']['code']):false;

        if(!empty($name)){
            $default_options['conditions']['Coa.name LIKE'] = '%'.$name.'%';
        }
        if(!empty($code)){
            $code = trim($code);
            $default_options['conditions']['OR']['Coa.code LIKE'] = '%'.$code.'%';
            $default_options['conditions']['OR']['Coa.with_parent_code LIKE'] = '%'.$code.'%';
        }
        
        return $default_options;
    }
}
?>