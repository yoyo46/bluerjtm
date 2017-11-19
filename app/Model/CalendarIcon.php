<?php
class CalendarIcon extends AppModel {
	var $name = 'CalendarIcon';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Judul Icon harap diisi'
			),
		),
        'photo' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Icon Kalender harap diisi'
            ),
        )
	);

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'CalendarIcon.status' => 1,
            ),
            'order'=> array(
                'CalendarIcon.name' => 'ASC'
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