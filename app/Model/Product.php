<?php
class Product extends AppModel {
	var $name = 'Product';

    var $belongsTo = array(
        'ProductUnit' => array(
            'className' => 'ProductUnit',
            'foreignKey' => 'product_unit_id',
        ),
        'ProductCategory' => array(
            'className' => 'ProductCategory',
            'foreignKey' => 'product_category_id',
        )
    );

	var $validate = array(
        'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode barang harap diisi'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'Kode barang sudah terdaftar, mohon masukkan kode lain.'
            ),
        ),
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama barang harap diisi'
            ),
        ),
        'product_unit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Satuan barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Satuan barang harap dipilih'
            ),
        ),
        'product_category_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Grup barang harap dipilih'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Grup barang harap dipilih'
            ),
        ),
	);

	function getData( $find, $options = false, $elements = false ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Product.name' => 'ASC'
            ),
            'fields' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['Product.status'] = 1;
                break;
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
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge( $data, $id ){
        $data_merge = $this->getData('first', array(
            'conditions' => array(
                'Product.id' => $id
            ),
        ), array(
            'status' => 'all',
        ));

        if(!empty($data_merge)){
            $data = array_merge($data, $data_merge);
        }

        return $data;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('barang');

        if ( !empty($data) ) {
            if( empty($id) ) {
                $this->create();
                $defaul_msg = sprintf(__('menambah %s'), $defaul_msg);
            } else {
                $this->id = $id;
                $defaul_msg = sprintf(__('mengubah %s'), $defaul_msg);
            }

            $this->set($data);
            $flagValidates = $this->validates();
            $code = !empty($data['Product']['code'])?$data['Product']['code']:false;
            $name = !empty($data['Product']['name'])?$data['Product']['name']:false;

            if( !empty($code) && !empty($name) ) {
                $defaul_msg = sprintf(__('%s (%s) %s'), $defaul_msg, $code, $name);
            }

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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = !empty($data['named']['keyword'])?$data['named']['keyword']:false;

        if( !empty($keyword) ) {
            $default_options['conditions']['OR'] = array(
                'Product.code LIKE' => '%'.$keyword.'%',
                'Product.name LIKE' => '%'.$keyword.'%',
            );
        }
        
        return $default_options;
    }
}
?>