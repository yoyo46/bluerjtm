<?php
class AssetDepreciation extends AppModel {
	var $name = 'AssetDepreciation';
    
    var $belongsTo = array(
        'Asset' => array(
            'className' => 'Asset',
            'foreignKey' => 'asset_id',
        ),
    );
    var $hasMany = array(
        'Journal' => array(
            'className' => 'Journal',
            'foreignKey' => 'document_id',
        ),
    );

    public function getData( $find = 'all', $options = array(), $elements = array()  ) {
        $status = isset($elements['status']) ? $elements['status']:'active';

        $default_options = array(
            'conditions'=> array(
                'AssetDepreciation.status' => 1,
            ),
            'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
                'AssetDepreciation.periode' => 'DESC',
            ),
        );

        if( !empty($options) ) {
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