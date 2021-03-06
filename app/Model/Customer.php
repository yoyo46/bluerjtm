<?php
class Customer extends AppModel {
	var $name = 'Customer';
	var $validate = array(
        'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode Customer harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Kode Customer telah terdaftar',
            ),
        ),
        'customer_type_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe Customer harap dipilih'
            ),
        ),
        'customer_group_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Grup Customer harap dipilih'
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer name harap diisi'
            ),
        ),
        'address' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Address harap diisi'
            ),
        ),
        'phone_number' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Phone harap diisi'
            ),
        ),
        'target_rit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Taget Rit / bln harap diisi'
            ),
        ),
        'term_of_payment' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Term Of Payment harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
        'CustomerType' => array(
            'className' => 'CustomerType',
            'foreignKey' => 'customer_type_id',
        ),
        'CustomerGroup' => array(
            'className' => 'CustomerGroup',
            'foreignKey' => 'customer_group_id',
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
	);

    var $hasMany = array(
        'CustomerTargetUnit' => array(
            'className' => 'CustomerTargetUnit',
            'foreignKey' => 'customer_id',
        ),
        'UangKuli' => array(
            'className' => 'UangKuli',
            'foreignKey' => 'customer_id',
        ),
        'Invoice' => array(
            'className' => 'Invoice',
            'foreignKey' => 'customer_id',
        ),
        'InvoicePayment' => array(
            'className' => 'InvoicePayment',
            'foreignKey' => 'customer_id',
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['customer_code'] = sprintf('CONCAT(%s.name, \' - \', %s.code)', $this->alias, $this->alias);
        $this->virtualFields['customer_name_code'] = sprintf('CONCAT(%s.code, \' - \', %s.name, \' ( \', CustomerType.name, \' )\')', $this->alias, $this->alias);
        $this->virtualFields['customer_name'] = sprintf('CONCAT(%s.name, \' ( \', CustomerType.name, \' )\')', $this->alias);
        $this->virtualFields['order_sort'] = sprintf('CASE WHEN %s.order IS NULL THEN 1 ELSE 0 END', $this->alias);
    }

	function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $plant = isset($elements['plant'])?$elements['plant']:true;
        $customer_type = isset($elements['customer_type'])?$elements['customer_type']:true;
        
        $branch_is_plant = Configure::read('__Site.config_branch_plant');
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Customer.status' => 'DESC',
                'Customer.order_sort' => 'ASC',
                'Customer.order' => 'ASC',
                'Customer.name' => 'ASC',
            ),
            'contain' => array(
            ),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($customer_type) ) {
            $default_options['contain'][] = 'CustomerType';
        } else {
            unset($this->virtualFields['customer_name']);
            unset($this->virtualFields['customer_name_code']);
        }

        switch ($status) {
            case 'all':
                $default_options['conditions']['Customer.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Customer.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Customer.status'] = 1;
                break;
        }

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(isset($options['order'])){
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

    function getMerge( $data, $id, $with_contain = false, $fieldName = 'Customer.id' ){
        if(empty($data['Customer'])){
            $this->bindModel(array(
                'belongsTo' => array(
                    'Bank' => array(
                        'className' => 'Bank',
                        'foreignKey' => 'bank_id',
                    )
                )
            ), false);
        
            $contain = array(
                'CustomerType',
            );

            if( $with_contain ) {
                $contain[] = 'Bank';
            }

            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    $fieldName => $id
                ),
                'contain' => $contain,
            ), true, array(
                'status' => 'all',
                'branch' => false,
                'plant' => false,
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getInclude ( $customerConditions, $customer_id = false ) {
        $branch_is_plant = Configure::read('__Site.config_branch_plant');

        if( !empty($branch_is_plant) ) {
            $customerConditions['OR'] = array(
                'Customer.branch_id' => Configure::read('__Site.Branch.Plant.id'),
                'Customer.id' => $customer_id,
            );
        } else {
            $customerConditions['OR'] = array(
                'Customer.branch_id' => Configure::read('__Site.config_branch_id'),
                'Customer.id' => $customer_id,
            );
        }

        return $this->getData('list', array(
            'conditions' => $customerConditions,
            'fields' => array(
                'Customer.id', 'Customer.customer_name_code'
            )
        ), true, array(
            'plant' => false,
            'branch' => false,
        ));
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $customer_id = !empty($data['named']['customer_id'])?$data['named']['customer_id']:false;

        if(!empty($customer_id)){
            $default_options['conditions']['Customer.id'] = $customer_id;
        }
        
        return $default_options;
    }
}
?>