<?php
class PurchaseOrderDetail extends AppModel {
	var $name = 'PurchaseOrderDetail';

    var $belongsTo = array(
        'PurchaseOrder' => array(
            'className' => 'PurchaseOrder',
            'foreignKey' => 'purchase_order_id',
        ),
        'Product' => array(
            'className' => 'Product',
            'foreignKey' => 'product_id',
        )
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
        'price' => array(
            'emptyFill' => array(
                'rule' => array('emptyFill', 'price'),
                'message' => 'Harga barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga barang harus berupa angka'
            ),
        ),
        'disc' => array(
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'disc harga barang harus berupa angka'
            ),
        ),
        'ppn' => array(
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'PPN harga barang harus berupa angka'
            ),
        ),
        'qty' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Qty harap diisi'
            ),
        ),
	);

    // public function __construct($id = false, $table = NULL, $ds = NULL){
    //     parent::__construct($id, $table, $ds);
    //     $this->virtualFields['total_remain'] = sprintf('%s.qty - IFNULL(%s.total_receipt, 0)', $this->alias, $this->alias);
    // }

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'PurchaseOrderDetail.id' => 'ASC',
            ),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['PurchaseOrderDetail.status'] = 1;
                break;
            case 'unreceipt':
                $default_options['conditions']['PurchaseOrderDetail.status'] = 1;
                $default_options['conditions']['PurchaseOrderDetail.receipt_status <>'] = 'full';
                break;
            case 'unretur':
                $default_options['conditions']['PurchaseOrderDetail.status'] = 1;
                $default_options['conditions']['PurchaseOrderDetail.retur_status <>'] = 'full';
                break;
        }

        if(!empty($options['conditions'])){
            $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
        }
        if(!empty($options['order'])){
            $default_options['order'] = $options['order'];
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
        if(!empty($options['contain'])){
            $default_options['contain'] = $options['contain'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id, $type = false ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'PurchaseOrderDetail.purchase_order_id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $purchase_order_id = $this->filterEmptyField($value, 'PurchaseOrderDetail', 'purchase_order_id');
                $product_id = $this->filterEmptyField($value, 'PurchaseOrderDetail', 'product_id');
                $qty = $this->filterEmptyField($value, 'PurchaseOrderDetail', 'qty');
                
                $value = $this->Product->getMerge($value, $product_id, 'PurchaseOrderDetail', false);

                switch ($type) {
                    case 'ProductReceipt':
                        $receipts = $this->PurchaseOrder->ProductReceipt->getData('list', array(
                            'conditions' => array(
                                'ProductReceipt.document_id' => $purchase_order_id,
                                'ProductReceipt.document_type' => 'po',
                            ),
                            'fields' => array(
                                'ProductReceipt.id',
                            ),
                        ));
                        $value['PurchaseOrderDetail']['total_remain'] = $qty;

                        if( !empty($receipts) ) {
                            $this->Product->ProductReceiptDetail->virtualFields['total_receipt'] = 'SUM(ProductReceiptDetail.qty)';
                            $receiptProducts = $this->Product->ProductReceiptDetail->getData('first', array(
                                'conditions' => array(
                                    'ProductReceiptDetail.product_receipt_id' => $receipts,
                                    'ProductReceiptDetail.product_id' => $product_id,
                                ),
                            ));

                            $qty_receipt = $this->filterEmptyField($receiptProducts, 'ProductReceiptDetail', 'total_receipt');
                            $total_remain = $qty - $qty_receipt;

                            if( $total_remain < 0 ) {
                                $total_remain = 0;
                            }

                            $value['PurchaseOrderDetail']['total_remain'] = $total_remain;
                        }
                        break;
                }

                $values[$key] = $value;
            }

            $data['PurchaseOrderDetail'] = $values;
        }

        return $data;
    }

    function getMergeData( $data, $id, $product_id ){
        $value = $this->getData('first', array(
            'conditions' => array(
                'PurchaseOrderDetail.purchase_order_id' => $id,
                'PurchaseOrderDetail.product_id' => $product_id,
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($value)){
            $qty_retur = $this->Product->ProductReturDetail->getTotalRetur(false, $id, 'po', $product_id);
            $qty = Common::hashEmptyField($value, 'PurchaseOrderDetail.qty');

            $total_qty = $qty - $qty_retur;

            if( $total_qty <= 0 ) {
                $total_qty = 0;
            }

            $value['PurchaseOrderDetail']['qty'] = $total_qty;

            $data = array_merge($data, $value);
        }

        return $data;
    }

    function doSave( $datas, $purchase_order_id, $is_validate = false ) {
        $result = false;
        $msg = __('Gagal menambahkan PO');

        if( !empty($purchase_order_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'PurchaseOrderDetail.purchase_order_id' => $purchase_order_id,
            ));
        }

        if ( !empty($datas['PurchaseOrderDetail']) ) {
            foreach ($datas['PurchaseOrderDetail'] as $key => $data) {
                $this->create();

                if( !empty($purchase_order_id) ) {
                    $data['PurchaseOrderDetail']['purchase_order_id'] = $purchase_order_id;
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
                $msg = __('Berhasil menambahkan PO');
                $result = true;
            }
        }

        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = !empty($data['named']['keyword'])?$data['named']['keyword']:false;
        $code = !empty($data['named']['code'])?$data['named']['code']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $group = !empty($data['named']['group'])?$data['named']['group']:false;

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
        
        return $default_options;
    }

    function _callGrandtotal( $id ){
        $values = $this->getData('all', array(
            'conditions' => array(
                'PurchaseOrderDetail.purchase_order_id' => $id,
            ),
        ), array(
            'status' => 'status',
        ));
        $grandtotal = 0;

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $value = $this->getMergeList($value, array(
                    'contain' => array(
                        'PurchaseOrder',
                    ),
                ));

                $ppn_include = Common::hashEmptyField($value, 'PurchaseOrder.ppn_include');
                $product_id = Common::hashEmptyField($value, 'PurchaseOrderDetail.product_id');
                $qty = Common::hashEmptyField($value, 'PurchaseOrderDetail.qty');
                $price = Common::hashEmptyField($value, 'PurchaseOrderDetail.price');
                $disc = Common::hashEmptyField($value, 'PurchaseOrderDetail.disc');
                $ppn = Common::hashEmptyField($value, 'PurchaseOrderDetail.ppn');

                $qty_retur = $this->Product->ProductReturDetail->getTotalRetur(false, $id, 'po', $product_id);
                $qty -= $qty_retur;

                $total = ($qty*$price) - $disc;

                if( empty($ppn_include) ) {
                    $total += $ppn;
                }
                
                $grandtotal += $total;
            }
        }

        return $grandtotal;
    }
}
?>