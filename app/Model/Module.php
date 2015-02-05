<?php
class Module extends AppModel {
	var $name = 'Module';

	var $hasMany = array(
		'ModuleAction' => array(
			'className' => 'ModuleAction',
			'foreignKey' => 'module_id',
		),
	);
}
?>