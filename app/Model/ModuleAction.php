<?php
class ModuleAction extends AppModel {
	var $name = 'ModuleAction';

	var $belongsTo = array(
		'Module' => array(
			'className' => 'Module',
			'foreignKey' => 'module_id',
		),
	);
}
?>