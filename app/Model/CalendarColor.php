<?php
class CalendarColor extends AppModel {
	var $name = 'CalendarColor';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Warna harap diisi'
			),
		),
        'hex' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode Warna harap diisi'
            ),
        )
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'CalendarColor.status' => 1,
            ),
            'order'=> array(
                'CalendarColor.name' => 'ASC'
            ),
            'contain' => array(),
        );

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
}
?>