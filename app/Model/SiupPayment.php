<?php
class SiupPayment extends AppModel {
	var $name = 'SiupPayment';
	var $validate = array(
        'siup_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No. Pol Truk harap dipilih'
            ),
        ),
        'user_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Anda tidak memiliki otoritas pada halaman ini'
            ),
        ),
        'siup_payment_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl dibayar harap dipilih'
            ),
        ),
	);

	var $belongsTo = array(
		'Siup' => array(
			'className' => 'Siup',
			'foreignKey' => 'siup_id',
		)
	);

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'SiupPayment.branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'SiupPayment.created' => 'DESC',
                'SiupPayment.id' => 'DESC',
            ),
            'contain' => array(
                'Siup'
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['SiupPayment.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['SiupPayment.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['SiupPayment.status'] = 1;
                break;
        }

        if(!empty($options) && $is_merge){
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
        }else{
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