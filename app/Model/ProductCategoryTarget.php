<?php
class ProductCategoryTarget extends AppModel {
	var $name = 'ProductCategoryTarget';
	var $validate = array(
        'product_category_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Grup Barang harap diisi'
            ),
            'checkUniq' => array(
                'rule' => array('checkUniq'),
                'message' => 'Grup Barang telah terdaftar',
            ),
        ),
        'target' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Target harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'ProductCategory' => array(
            'className' => 'ProductCategory',
            'foreignKey' => 'product_category_id',
        ),
    );

    function checkUniq () {
        $id = !empty($this->data['ProductCategoryTarget']['id'])?$this->data['ProductCategoryTarget']['id']:false;
        $product_category_id = !empty($this->data['ProductCategoryTarget']['product_category_id'])?$this->data['ProductCategoryTarget']['product_category_id']:false;
        $value = $this->getData('count', array(
            'conditions' => array(
                'ProductCategoryTarget.product_category_id' => $product_category_id,
                'ProductCategoryTarget.id NOT' => $id,
            ),
        ));
        
        if( !empty($value) ) {
            return false;
        } else {
            return true;
        }
    }

	function getData( $find, $options = false ){
        $default_options = array(
            'conditions'=> array(
                'ProductCategoryTarget.status' => 1,
            ),
            'order'=> array(
                'ProductCategoryTarget.id' => 'ASC'
            ),
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

    public function _callRefineParams( $data = '', $default_options = false ) {
        $group = $this->filterEmptyField($data, 'named', 'group');
        $sort = !empty($data['named']['sort'])?$data['named']['sort']:false;
        $direction = !empty($data['named']['direction'])?$data['named']['direction']:false;

        if( !empty($group) ) {
            $default_options['conditions']['ProductCategoryTarget.product_category_id'] = $group;
        }

        $productCategory = strpos($sort, 'ProductCategory.');

        if( !empty($sort) ) {
            if( is_numeric($productCategory) ) {
                $default_options['contain'][] = 'ProductCategory';
            }

            $default_options['order'] = array(
                $sort => $direction,
            );
        }

        return $default_options;
    }

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('target grup barang');

        if ( !empty($data) ) {
            $branch_id = Configure::read('__Site.config_branch_id');

            if( empty($id) ) {
                $defaul_msg = sprintf(__('menambah %s'), $defaul_msg);
            } else {
                $data['ProductCategoryTarget']['id'] = $id;
                $defaul_msg = sprintf(__('mengubah %s'), $defaul_msg);
            }

            $flag = $this->saveAll($data, array(
                'validate' => 'only',
            ));

            if( !empty($flag) ) {
                $this->saveAll($data);

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
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }
}
?>