<?php
class ProductAdjustment extends AppModel {
	var $name = 'ProductAdjustment';

    var $belongsTo = array(
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
    );

    var $hasMany = array(
        'ProductAdjustmentDetail' => array(
            'className' => 'ProductAdjustmentDetail',
            'foreignKey' => 'product_adjustment_id',
        ),
    );

	var $validate = array(
        'nodoc' => array(
            'checkUniq' => array(
                'rule' => array('checkUniq'),
                'message' => 'No. Dokumen telah terdaftar',
            ),
        ),
        'transaction_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl harap dipilih'
            ),
        ),
	);

    function beforeSave( $options = array() ) {
        $this->data = Hash::insert($this->data, 'ProductAdjustment.nodoc', $this->generateNoDoc());
    }

    function generateNoDoc(){
        $default_id = 1;
        $format_id = sprintf('PA-%s-%s-', date('Y'), date('m'));

        $last_data = $this->getData('first', array(
            'order' => array(
                'ProductAdjustment.nodoc' => 'DESC'
            ),
            'fields' => array(
                'ProductAdjustment.nodoc'
            )
        ), array(
            'branch' => false,
        ));

        if(!empty($last_data['ProductAdjustment']['nodoc'])){
            $str_arr = explode('-', $last_data['ProductAdjustment']['nodoc']);
            $last_arr = count($str_arr)-1;
            $default_id = intval($str_arr[$last_arr]+1);
        }

        $id = str_pad($default_id, 4,'0',STR_PAD_LEFT);
        $format_id .= $id;
        
        return $format_id;
    }

	function getData( $find, $options = false, $elements = false ){
        $branch = isset($elements['branch'])?$elements['branch']:true;
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'ProductAdjustment.status' => 'DESC',
                'ProductAdjustment.created' => 'DESC',
                'ProductAdjustment.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['ProductAdjustment.status'] = 1;
                break;
            case 'non-active':
                $default_options['conditions']['ProductAdjustment.status'] = 0;
                break;
            default:
                $default_options['conditions']['ProductAdjustment.status'] = array( 0, 1 );
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['ProductAdjustment.branch_id'] = Configure::read('__Site.config_branch_id');
        }

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

    function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $defaul_msg = __('Qty Adjustment');

        if ( !empty($data) ) {
            $nodoc = $this->filterEmptyField($data, 'ProductAdjustment', 'nodoc');
            $transaction_status = $this->filterEmptyField($data, 'ProductAdjustment', 'transaction_status');

            $data['ProductAdjustment']['branch_id'] = Configure::read('__Site.config_branch_id');

            if( !empty($nodoc) ) {
                $defaul_msg = sprintf(__('%s #%s'), $defaul_msg, $nodoc);
            }

            if( empty($id) ) {
                $this->create();
                $defaul_msg = sprintf(__('menambah %s'), $defaul_msg);
            } else {
                $this->id = $id;
                $defaul_msg = sprintf(__('mengubah %s'), $defaul_msg);
            }

            $flag = $this->saveAll($data, array(
                'validate' => 'only',
                'deep' => true,
            ));
                // debug($data);
                // debug($this->validationErrors);die();

            if( !empty($flag) ) {
                $session_id = $this->filterEmptyField($data, 'ProductAdjustment', 'session_id');
                $this->ProductAdjustmentDetail->ProductAdjustmentDetailSerialNumber->deleteAll(array(
                    'ProductAdjustmentDetailSerialNumber.session_id' => $session_id,
                ));
                
                $flag = $this->saveAll($data, array(
                    'deep' => true,
                ));

                if( !empty($flag) ) {
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
                        'data' => $data,
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
                        'data' => $data,
                    );
                }
            } else {
                $defaul_msg = sprintf(__('Gagal %s'), $defaul_msg);
                $result = array(
                    'msg' => $defaul_msg,
                    'status' => 'error',
                    'data' => $data,
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }
}
?>