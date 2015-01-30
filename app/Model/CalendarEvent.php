<?php
class CalendarEvent extends AppModel {
	var $name = 'CalendarEvent';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Judul harap diisi'
            ),
        ),
        'note' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Keterangan harap diisi'
            ),
        )
	);

	var $belongsTo = array(
		'CalendarIcon' => array(
			'className' => 'CalendarIcon',
			'foreignKey' => 'icon_id',
		),
        'CalendarColor' => array(
            'className' => 'CalendarColor',
            'foreignKey' => 'color_id',
        )
	);

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'CalendarEvent.status' => 1,
            ),
            'order'=> array(
                'CalendarEvent.name' => 'ASC'
            ),
            'contain' => array(
                'CalendarIcon',
                'CalendarColor'
            ),
            'fields' => array(),
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