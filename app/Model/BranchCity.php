<?php
class BranchCity extends AppModel {
	var $name = 'BranchCity';
	var $validate = array(
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
        'branch_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih tujuan bongkar dari cabang?'
            ),
        ),
	);

    var $belongsTo = array(
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'BranchTtuj' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_city_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'BranchCity.status' => 1,
            ),
            'order'=> array(
                'BranchCity.id' => 'ASC'
            ),
            'fields' => array(),
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

    function getMerge($data, $id, $find = 'all'){
        if(empty($data['BranchCity'])){
            $options = array(
                'conditions' => array(
                    'BranchCity.branch_id' => $id
                )
            );

            if( $find == 'list' ) {
                $options['fields'] = array(
                    'BranchCity.id', 'BranchCity.branch_city_id',
                );
            }

            $value = $this->getData($find, $options);

            if(!empty($value)){
                $data['BranchCity'] = $value;
            }
        }

        return $data;
    }

    function doSave( $data, $validate = true, $branch_id = false ) {
        $result = false;

        if ( !empty($data) ) {
            $this->create();
            $this->set($data);

            if( $this->validates() ) {
                $flagSave = true;

                if( empty($validate) ) {
                    $flagSave = $this->save();
                }

                if( $flagSave ) {
                    if( empty($validate) ) {
                        $id = $this->id;
                    }

                    $result = array(
                        'status' => 'success',
                    );
                } else {
                    $result = array(
                        'status' => 'error',
                    );
                }
            } else {
                $result = array(
                    'status' => 'error',
                );
            }
        }

        return $result;
    }
}
?>