<?php
class CoaClosingQueue extends AppModel {
	var $name = 'CoaClosingQueue';

    function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:false;

        $default_options = array(
            'conditions'=> array(
                'CoaClosingQueue.status' => 1,
            ),
            'order'=> array(
                'CoaClosingQueue.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        switch ($status) {
            case 'pending':
                $default_options['conditions']['CoaClosingQueue.transaction_status'] = array( 'pending', 'progress' );
                break;
            case 'completed':
                $default_options['conditions']['CoaClosingQueue.transaction_status'] = 'completed';
                break;
        }

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
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function doSave ( $data ) {
        $msg = __('Gagal melakukan proses Closing');

        if( !empty($data) ) {
            $periode = $this->filterEmptyField($data, 'CoaClosingQueue', 'periode');
            $value = $this->getData('first', array(
                'conditions' => array(
                    'CoaClosingQueue.periode >=' => $periode,
                ),
            ), array(
                'status' => 'completed',
            ));

            $flag = $this->saveAll($data, array(
                'validate' => 'only',
            ));
            
            if( !empty($flag) ) {
                if( !empty($value) ) {
                    $msg = __('Closing sedang diproses, telah terdapat closing di atas periode tersebut. Secara otomatis semua closing diatas periode tersebut akan di Cancel. Segera batalkan apabila Anda merasa tidak perlu.');
                } else {
                    $msg = __('Closing sedang diproses, harap menunggu..');
                }

                $this->saveAll($data);
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                    ),
                );
            } else {
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result['data'] = $value;
        }

        return $result;
    }

    function doDelete( $id ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'CoaClosingQueue.id' => $id,
                'CoaClosingQueue.transaction_status' => 'pending',
            ),
        ));

        if ( !empty($value) ) {
            $default_msg = __('membatalkan closing.');

            $this->id = $id;
            $this->set('status', 0);

            if( $this->save() ) {
                $msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                    ),
                );
            } else {
                $msg = sprintf(__('Gagal %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result = array(
                'msg' => __('Gagal membatalkan data closing. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }
}
?>