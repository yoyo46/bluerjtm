<?php
class GroupMotor extends AppModel {
	var $name = 'GroupMotor';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Grup motor harap diisi'
			),
		)
	);

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['lower_name'] = sprintf('LOWER(%s.name)', $this->alias);
    }

    function getData( $find, $options = false, $status = 'active' ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'GroupMotor.name' => 'ASC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['GroupMotor.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['GroupMotor.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['GroupMotor.status'] = 1;
                break;
        }

        if(!empty($options)){
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

    function getMerge($data, $id){
        if(empty($data['GroupMotor'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'id' => $id
                )
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

}
?>