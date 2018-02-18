<?php
class ProductStock extends AppModel {
	var $name = 'ProductStock';

    var $belongsTo = array(
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        ),
        'ProductHistory' => array(
            'className' => 'ProductHistory',
            'foreignKey' => 'product_id',
        ),
        // 'ProductReceiptDetail' => array(
        //     'className' => 'ProductReceiptDetail',
        //     'foreignKey' => 'transaction_id',
        //     'conditions' => array(
        //         'ProductStock.transaction_type' => 'product_receipts',
        //     ),
        // ),
        // 'ProductExpenditureDetail' => array(
        //     'className' => 'ProductExpenditureDetail',
        //     'foreignKey' => 'transaction_id',
        //     'conditions' => array(
        //         'ProductStock.transaction_type' => 'product_expenditure',
        //     ),
        // ),
    );
    
    public function __construct($id = false, $table = NULL, $ds = NULL){
        parent::__construct($id, $table, $ds);
        $this->virtualFields['qty_total'] = __('%s.qty - %s.qty_use', $this->alias, $this->alias);
    }

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $sort = isset($elements['sort'])?$elements['sort']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductStock.id' => 'ASC'
            ),
            'fields' => array(),
            'group' => array(),
            'contain' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductStock.status'] = 1;
                break;
            case 'in_stock':
                $default_options['conditions']['ProductStock.qty_total <>'] = 0;
                $default_options['conditions']['ProductStock.status'] = 1;
                // $default_options['conditions']['ProductStock.type'] = 'default';
                break;
            case 'FIFO':
                $default_options['conditions']['ProductStock.qty_total <>'] = 0;
                $default_options['conditions']['ProductStock.status'] = 1;
                $default_options['conditions']['ProductStock.type'] = 'default';
                $default_options['order'] = array(
                    'ProductStock.transaction_date' => 'ASC',
                    'ProductStock.id' => 'ASC',
                );
                break;
            case 'barang_jadi':
                $default_options['conditions']['ProductStock.status'] = 1;
                $default_options['conditions']['ProductStock.type'] = 'default';
                break;
        }

        switch ($sort) {
            case 'fifo':
                $default_options['order'] = array(
                    'ProductStock.transaction_date' => 'ASC',
                    'ProductStock.id' => 'ASC',
                );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['ProductStock.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = array_merge($default_options['order'], $options['order']);
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if( isset($options['contain']) && empty($options['contain']) ) {
            $default_options['contain'] = false;
        } else if(!empty($options['contain'])){
            $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $serial_number = !empty($data['named']['serial_number'])?$data['named']['serial_number']:false;

        if( !empty($serial_number) ) {
            $default_options['conditions']['ProductStock.serial_number LIKE'] = '%'.$serial_number.'%';
        }
        
        return $default_options;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('stok barang');

        if ( !empty($data) ) {
            $this->create();
            $defaul_msg = sprintf(__('menambah %s'), $defaul_msg);

            $this->set($data);
            $flagValidates = $this->validates();

            if( $flagValidates ) {
                if( $this->save($data) ) {
                    $id = $this->id;
                    $defaul_msg = sprintf(__('Berhasil %s'), $defaul_msg);

                    $result = array(
                        'msg' => $defaul_msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $defaul_msg,
                            'old_data' => $value,
                            'document_id' => $id,
                        ),
                    );
                } else {
                    $defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
                    $result = array(
                        'msg' => $defaul_msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $defaul_msg,
                            'old_data' => $value,
                            'document_id' => $id,
                            'error' => 1,
                        ),
                    );
                }
            } else {
                $defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
                $result = array(
                    'msg' => $defaul_msg,
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function _callStock ( $product_id, $branch_id = false, $transaction_date = null ) {
        $this->virtualFields['qty_cnt'] = 'SUM(ProductStock.qty - ProductStock.qty_use)';
        $options = array(
            'conditions' => array(
                'ProductStock.product_id' => $product_id,
            ),
        );

        if( !empty($branch_id) ) {
            $options['conditions']['ProductStock.branch_id'] = $branch_id;
        }

        if( !empty($transaction_date) ) {
            $options['conditions']['ProductStock.transaction_date <='] = $transaction_date;
        }

        $value = $this->getData('first', $options, array(
            'status' => 'in_stock',
        ));
        $total_stock = $this->filterEmptyField($value, 'ProductStock', 'qty_cnt');

        return $total_stock;
    }

    public function afterSave($created, $options = array()){
        $product_id = $this->filterEmptyField($this->data, 'ProductStock', 'product_id');

        $qty = $this->_callStock($product_id);

        $this->Product->id = $product_id;
        $this->Product->set('product_stock_cnt', $qty);
        $this->Product->save();
    }

    function _callSerialNumbers ( $product_id, $transaction_id = false ) {
        $values = $this->getData('all', array(
            'conditions' => array(
                'ProductStock.product_id' => $product_id,
            ),
            // 'fields' => array(
            //     'ProductStock.serial_number', 'ProductStock.serial_number',
            // ),
            'group' => array(
                'ProductStock.serial_number',
            ),
        ), array(
            'status' => 'in_stock',
        ));
        $result = array();

        if( !empty($values) ) {
            if( !empty($transaction_id) ) {
                $product_expenditure_detail_id = $this->Product->ProductExpenditureDetail->getData('list', array(
                    'conditions' => array(
                        'ProductExpenditureDetail.product_expenditure_id' => $transaction_id,
                    ),
                    'fields' => array(
                        'ProductExpenditureDetail.id', 'ProductExpenditureDetail.id',
                    ),
                ));
            } else {
                $product_expenditure_detail_id = false;
            }

            foreach ($values as $key => $value) {
                $product_id = $this->filterEmptyField($value, 'ProductStock', 'product_id');
                $serial_number = $this->filterEmptyField($value, 'ProductStock', 'serial_number');
                $qty_total = $this->filterEmptyField($value, 'ProductStock', 'qty_total');

                $usage = $this->Product->ProductExpenditureDetailSerialNumber->getData('count', array(
                    'conditions' => array(
                        'ProductExpenditureDetailSerialNumber.product_id' => $product_id,
                        'ProductExpenditureDetailSerialNumber.serial_number' => $serial_number,
                        'ProductExpenditureDetailSerialNumber.product_expenditure_detail_id NOT' => $product_expenditure_detail_id,
                    ),
                    'group' => array(
                        'ProductExpenditureDetailSerialNumber.serial_number',
                    ),
                ));

                $qty_total -= $usage;

                if( !empty($qty_total) ) {
                    $result[$serial_number] = $serial_number;
                }
            }
        }

        return $result;
    }
}
?>