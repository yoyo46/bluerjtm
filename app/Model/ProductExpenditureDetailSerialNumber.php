<?php
class ProductExpenditureDetailSerialNumber extends AppModel {
	var $name = 'ProductExpenditureDetailSerialNumber';

    var $belongsTo = array(
        'ProductExpenditureDetail' => array(
            'className' => 'ProductExpenditureDetail',
            'foreignKey' => 'product_expenditure_detail_id',
        ),
    );

	function getData( $find, $options = false, $elements = null ){
        $status = Common::hashEmptyField($elements, 'status');

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductExpenditureDetailSerialNumber.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'available':
                $this->virtualFields['total_qty'] = 'ProductExpenditureDetailSerialNumber.qty - ProductExpenditureDetailSerialNumber.qty_use';
                $default_options['conditions']['total_qty >'] = 0;
                break;
        }

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMergeAll ( $data, $type = 'all', $product_id, $relation_id, $fieldName = 'ProductExpenditureDetailSerialNumber.product_receipt_id' ) {
        $values = $this->getData($type, array(
            'conditions' => array(
                'ProductExpenditureDetailSerialNumber.product_id' => $product_id,
                $fieldName => $relation_id,
            ),
        ), array(
            'status' => 'available',
        ));

        if( !empty($values) ) {
            $data['ProductExpenditureDetailSerialNumber'] = $values;
        }

        return $data;
    }
}
?>