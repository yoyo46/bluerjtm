<?php
class Asset extends AppModel {
	var $name = 'Asset';
    var $belongsTo = array(
        'AssetGroup' => array(
            'className' => 'AssetGroup',
            'foreignKey' => 'asset_group_id',
        ),
    );
}
?>