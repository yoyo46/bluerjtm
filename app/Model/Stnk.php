<?php
class Stnk extends AppModel {
	var $name = 'Stnk';
	var $validate = array(
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap diisi'
            ),
        ),
        'tgl_bayar' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tanggal bayar Stnk harap diisi'
            ),
        ),
        'tgl_berakhir' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tanggal berakhir Stnk harap diisi'
            ),
            'validDateStnk' => array(
                'rule' => array('validDateStnk'),
                'message' => 'tanggal berakhir harus lebih besar dari tanggal bayar'
            ) 
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga perpanjang Stnk harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga harus berupa angka'
            ),
            'validPrice' => array(
                'rule' => array('validPrice'),
                'message' => 'Harga harus lebih besar dari 0'
            ),
        ),
        'from_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Berakhir STNK harap diisi pada data Truk'
            ),
        ),
        'to_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Berakhir STNK harap diisi pada data Truk'
            ),
        ),
	);

    var $belongsTo = array(
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        )
    );

    function validDateStnk($data){
        $result = false;
        if(strtotime($data['tgl_berakhir']) > strtotime($this->data['Stnk']['tgl_bayar'])){
            $result = true;
        }
        return $result;
    }

    function validPrice($data){
        $result = false;
        $key = key($data);
        if($data[$key] > 0){
            $result = true;
        }
        return $result;
    }

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $default_options = array(
            'conditions'=> array(
                'Stnk.group_branch_id' => Configure::read('__Site.config_branch_id'),
            ),
            'order'=> array(
                'Stnk.created' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Stnk.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Stnk.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Stnk.status'] = 1;
                break;
        }

        if(!empty($options) && $is_merge){
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
        } else {
            $default_options = $options;
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