<?php
class ReportQueue extends AppModel {
	var $name = 'ReportQueue';

	public $belongsTo = array(
		'Report' => array(
			'foreignKey' => 'report_id',
		),
	);
	
	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'conditions'=> array(
				'ReportQueue.status' => 1,
			),
			'order'=> array(
				'ReportQueue.created' => 'DESC',
				'ReportQueue.id' => 'DESC',
			),
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);

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

		if( $find == 'paginate' ) {
			if( empty($default_options['limit']) ) {
				$default_options['limit'] = Configure::read('__Site.config_admin_pagination');
			}
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}
}
?>