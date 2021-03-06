<?php
class ProductExpenditureDetail extends AppModel {
	var $name = 'ProductExpenditureDetail';

    var $belongsTo = array(
        'ProductExpenditure' => array(
            'className' => 'ProductExpenditure',
            'foreignKey' => 'product_expenditure_id',
        ),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        ),
        'SpkProduct' => array(
            'className' => 'SpkProduct',
            'foreignKey' => 'spk_product_id',
        ),
    );

    var $hasMany = array(
        'ProductExpenditureDetailSerialNumber' => array(
            'className' => 'ProductExpenditureDetailSerialNumber',
            'foreignKey' => 'product_expenditure_detail_id',
            'dependent' => true,
        ),
        'ProductHistory' => array(
            'className' => 'ProductHistory',
            'foreignKey' => 'transaction_id',
            'conditions' => array(
                'ProductHistory.transaction_type' => 'product_expenditures',
            ),
        ),
    );

	var $validate = array(
        'product_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Barang harap dipilih'
            ),
        ),
        'qty' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Qty harap diisi'
            ),
        ),
        'qty_over' => array(
            'validateValue' => array(
                'rule' => array('validateValue', 'qty_over'),
                'message' => 'Tdk boleh lebih dari Qty Out'
            ),
        ),
        'sn_match' => array(
            'validateValue' => array(
                'rule' => array('validateValue', 'sn_match'),
                'message' => 'Serial number keluar tdk sesuai dgn Qty'
            ),
        ),
        'sn_empty' => array(
            'validateValue' => array(
                'rule' => array('validateValue', 'sn_empty'),
                'message' => 'Mohon pilih serial number sesuai Qty'
            ),
        ),
        'out_stock' => array(
            'validateValue' => array(
                'rule' => array('validateValue', 'out_stock'),
                'message' => 'Jml qty melebihi stok barang'
            ),
        ),
        'out_stock_date' => array(
            'validateValue' => array(
                'rule' => array('validateValue', 'out_stock_date'),
                'message' => 'Tgl pengeluaran melebihi tgl transaksi terakhir'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $header = isset($elements['header'])?$elements['header']:false;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'contain'=> array(),
            'order'=> array(
                'ProductExpenditureDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductExpenditureDetail.status'] = 1;
                break;
            case 'unexit':
                $default_options['conditions']['ProductExpenditureDetail.status'] = 1;
                $default_options['conditions']['ProductExpenditureDetail.document_status <>'] = 'full';
                break;
            case 'unreceipt':
                $default_options['conditions']['ProductExpenditureDetail.status'] = 1;
                $default_options['conditions']['ProductExpenditureDetail.receipt_status <>'] = 'full';
                break;
        }

        if( !empty($header) ) {
            // $default_options['conditions']['ProductExpenditure.status'] = 1;
            $default_options['contain'][] = 'ProductExpenditure';
        }

        $default_options = $this->merge_options($default_options, $options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'ProductExpenditureDetail.product_expenditure_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            if( !empty($values) ) {
                foreach ($values as $key => $value) {
                    $product_id = $this->filterEmptyField($value, 'ProductExpenditureDetail', 'product_id');
                    $product_expenditure_id = $this->filterEmptyField($value, 'ProductExpenditureDetail', 'product_expenditure_id');
                    
                    $value = $this->Product->getMerge($value, $product_id);
                    $values[$key] = $value;
                }
            }

            $data['ProductExpenditureDetail'] = $values;
        }

        return $data;
    }

    function doSave( $datas, $product_expenditure_id, $is_validate = false ) {
        $result = false;
        $msg = __('Gagal menyimpan penerimaan barang');

        if( !empty($product_expenditure_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'ProductExpenditureDetail.product_expenditure_id' => $product_expenditure_id,
            ));
        }

        if ( !empty($datas['ProductExpenditureDetail']) ) {
            foreach ($datas['ProductExpenditureDetail'] as $key => $data) {
                $this->create();

                if( !empty($product_Expenditure_id) ) {
                    $data['ProductExpenditureDetail']['product_expenditure_id'] = $product_expenditure_id;
                }

                $this->set($data);

                if( $this->validates() ) {
                    if( $is_validate ) {
                        $flagSave = true;
                    } else {
                        $flagSave = $this->save($data);
                    }

                    if( !$flagSave ) {
                        $result = false;
                    }
                } else {
                    $result = false;
                }
            }

            if( empty($result) ) {
                $msg = __('Berhasil menyimpan penerimaan barang');
                $result = true;
            }
        }

        return $result;
    }

    function getTotalExpenditure( $document_number, $id, $document_id, $product_id ){
        $values = $this->ProductExpenditure->getData('list', array(
            'conditions' => array(
                'ProductExpenditure.document_id' => $document_id,
                'ProductExpenditure.document_number' => $document_number,
            ),
            'fields' => array(
                'ProductExpenditure.id',
            ),
        ));

        $this->virtualFields['total'] = 'SUM(ProductExpenditureDetail.qty)';
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductExpenditureDetail.product_expenditure_id' => $values,
                'ProductExpenditureDetail.product_expenditure_id <>' => $id,
                'ProductExpenditureDetail.product_id' => $product_id,
            ),
        ));
        return $this->filterEmptyField($value, 'ProductExpenditureDetail', 'total', 0);
    }

    function getExpenditureByProduct( $id, $product_id, $spk_product_id, $branch_id ){
        $productExpenditureId = $this->ProductExpenditure->getData('list', array(
            'conditions' => array(
                'ProductExpenditure.document_id' => $id,
                'ProductExpenditure.branch_id' => $branch_id,
            ),
            'fields' => array(
                'ProductExpenditure.id',
            ),
        ), array(
            'branch' => false,
        ));

        $this->ProductExpenditureDetailSerialNumber->virtualFields['total_price'] = 'SUM(ProductExpenditureDetailSerialNumber.price*ProductExpenditureDetailSerialNumber.qty)';
        $this->ProductExpenditureDetailSerialNumber->virtualFields['total_qty'] = 'SUM(ProductExpenditureDetailSerialNumber.qty)';
        return $this->ProductExpenditureDetailSerialNumber->getData('first', array(
            'conditions' => array(
                'ProductExpenditureDetail.product_expenditure_id' => $productExpenditureId,
                'ProductExpenditureDetail.spk_product_id' => $spk_product_id,
                'ProductExpenditureDetail.product_id' => $product_id,
            ),
            'contain' => array(
                'ProductExpenditureDetail',
            ),
        ));
    }

    function getExpenditureByDetail( $id, $type = 'total' ){
        if( !empty($id) ) {
            $this->ProductHistory->virtualFields['grandtotal'] = 'SUM(ProductHistory.qty*ProductHistory.price)';
            $this->ProductHistory->virtualFields['total_qty'] = 'SUM(ProductHistory.qty)';

            $history = $this->ProductHistory->getData('first', array(
                'conditions' => array(
                    'ProductHistory.transaction_type' => 'product_expenditure',
                    'ProductHistory.transaction_id' => $id,
                ),
            ), array(
                'branch' => false,
            ));

            switch ($type) {
                case 'total':
                    return Common::hashEmptyField($history, 'ProductHistory.grandtotal');
                    break;
                
                default:
                    return $history;
                    break;
            }
        } else {
            return false;
        }
    }

    function getExpenditureByDocumentId( $id ){
        if( !empty($id) ) {
            $product_expenditure_detail_id = $this->getData('list', array(
                'conditions' => array(
                    'ProductExpenditureDetail.product_expenditure_id' => $id,
                ),
            ));

            return $this->getExpenditureByDetail($product_expenditure_detail_id);
        } else {
            return false;
        }
    }

    function getExpenditureByProductCategoryId( $id, $branch_id = null, $monthYear = null ){
        $options = array(
            'conditions' => array(
                'Product.product_category_id' => $id,
            ),
            'fields' => array(
                'ProductExpenditureDetail.id',
            ),
            'contain' => array(
                'Product',
                'ProductExpenditure',
            ),
            'group' => array(
                'ProductExpenditureDetail.id',
            ),
        );

        if( !empty($branch_id) ) {
            $options['conditions']['ProductExpenditure.branch_id'] = $branch_id;
        }
        if( !empty($monthYear) ) {
            $options['conditions']['DATE_FORMAT(ProductExpenditure.transaction_date, \'%Y-%m\')'] = $monthYear;
        }

        $options = $this->ProductExpenditure->getData('paginate', $options, array(
            'branch' => false,
            'status' => 'confirm',
        ));
        $product_expenditure_detail_id = $this->getData('list', $options);

        if( !empty($product_expenditure_detail_id) ) {
            return $this->getExpenditureByDetail($product_expenditure_detail_id, 'all');
        } else {
            return false;
        }
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $code = $this->filterEmptyField($data, 'named', 'code');
        $name = $this->filterEmptyField($data, 'named', 'name');
        $group = $this->filterEmptyField($data, 'named', 'group');
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $serial_number = !empty($data['named']['serial_number'])?$data['named']['serial_number']:false;
        $sort = !empty($data['named']['sort'])?$data['named']['sort']:false;
        $direction = !empty($data['named']['direction'])?$data['named']['direction']:false;
        $is_laka = !empty($data['named']['is_laka'])?$data['named']['is_laka']:false;

        if( !empty($code) ) {
            $default_options['conditions']['Product.code LIKE'] = '%'.$code.'%';
            $default_options['contain'][] = 'Product';
        }
        if( !empty($name) ) {
            $default_options['conditions']['Product.name LIKE'] = '%'.$name.'%';
            $default_options['contain'][] = 'Product';
        }
        if( !empty($group) ) {
            $default_options['conditions']['Product.product_category_id'] = $group;
            $default_options['contain'][] = 'Product';
        }
        if( !empty($nopol) ) {
            $default_options['conditions']['Truck.nopol LIKE'] = '%'.$nopol.'%';
            $default_options['contain'][] = 'ProductExpenditure';
            $default_options['contain'][] = 'Spk';
            $default_options['contain'][] = 'Truck';
            $bind = true;
        }
        if( !empty($serial_number) ) {
            $default_options['conditions']['ProductExpenditureDetailSerialNumber.serial_number LIKE'] = '%'.$serial_number.'%';
            $default_options['contain'][] = 'ProductExpenditureDetailSerialNumber';
        }
        if( !empty($is_laka) ) {
            $default_options['conditions'][]['OR'] = array(
                array( 'Spk.laka_id <>' => 0, ),
                array( 'Spk.laka_id <>' => NULL ),
            );

            $this->bindModel(array(
                'hasOne' => array(
                    'Spk' => array(
                        'className' => 'Spk',
                        'foreignKey' => false,
                        'conditions' => array(
                            'Spk.id = ProductExpenditure.document_id',
                            'Spk.status' => 1,
                        ),
                    ),
                )
            ), false);
            $default_options['contain'][] = 'ProductExpenditure';
            $default_options['contain'][] = 'Spk';
        }

        if( !empty($sort) ) {
            $spk = strpos($sort, 'Spk.');
            $truck = strpos($sort, 'Truck.');
            $product = strpos($sort, 'Product.');
            $branch = strpos($sort, 'Branch.');

            if( is_numeric($spk) ) {
                $default_options['contain'][] = 'Spk';
                $bind = true;
            }
            if( is_numeric($truck) ) {
                $default_options['contain'][] = 'Spk';
                $default_options['contain'][] = 'Truck';
                $bind = true;
            }
            if( is_numeric($product) ) {
                $default_options['contain'][] = 'Product';
            }
            if( is_numeric($branch) ) {
                $this->bindModel(array(
                    'hasOne' => array(
                        'Branch' => array(
                            'className' => 'Branch',
                            'foreignKey' => false,
                            'conditions' => array(
                                'ProductExpenditure.branch_id = Branch.id'
                            ),
                        ),
                    )
                ), false);

                $default_options['contain'][] = 'ProductExpenditure';
                $default_options['contain'][] = 'Branch';
            }

            $default_options['order'] = array(
                $sort => $direction,
            );
        }

        if( !empty($bind) ) {
            $this->unBindModel(array(
                'belongsTo' => array(
                    'ProductExpenditure'
                )
            ));
            $this->bindModel(array(
                'hasOne' => array(
                    'ProductExpenditure' => array(
                        'className' => 'ProductExpenditure',
                        'foreignKey' => false,
                        'conditions' => array(
                            'ProductExpenditure.id = ProductExpenditureDetail.product_expenditure_id'
                        ),
                    ),
                    'Spk' => array(
                        'className' => 'Spk',
                        'foreignKey' => false,
                        'conditions' => array(
                            'Spk.id = ProductExpenditure.document_id'
                        ),
                    ),
                    'Truck' => array(
                        'className' => 'Truck',
                        'foreignKey' => false,
                        'conditions' => array(
                            'Truck.id = Spk.truck_id'
                        ),
                    ),
                ),
            ), false);
        }
        
        return $default_options;
    }

    function getMergeData( $data, $id, $product_id ){
        $value = $this->getData('first', array(
            'conditions' => array(
                'ProductExpenditureDetail.product_expenditure_id' => $id,
                'ProductExpenditureDetail.product_id' => $product_id,
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($value)){
            $data = array_merge($data, $value);
        }

        return $data;
    }
}
?>