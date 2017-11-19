<?php
class ViewStock extends AppModel {
	public $belongsTo = array(
		'Product' => array(
			'foreignKey' => 'product_id',
		),
		'Branch' => array(
			'foreignKey' => 'branch_id',
		),
	);

	function getData( $find, $options = false, $elements = false ){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'fields' => array(),
            'contain' => array(),
        );

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }
}
?>