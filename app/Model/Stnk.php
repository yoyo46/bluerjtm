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

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(
                'Stnk.status' => 1,
            ),
            'order'=> array(
                'Stnk.created' => 'DESC'
            ),
            'contain' => array(),
        );

        if(!empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
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