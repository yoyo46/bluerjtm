<?php
class KsuPayment extends AppModel {
	var $name = 'KsuPayment';
	var $validate = array(
        'no_doc' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'No Dokumen harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'No Dokumen telah terdaftar',
            ),
        ),
        'type_lku' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe modul harap dipilih'
            ),
        ),
        'tgl_bayar' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl bayar harap dipilih'
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'grandtotal' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
	);

    var $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
    );

    var $hasMany = array(
        'KsuPaymentDetail' => array(
            'className' => 'KsuPaymentDetail',
            'foreignKey' => 'ksu_payment_id',
        )
    );

	function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'conditions'=> array(
                'KsuPayment.status' => 1,
            ),
            'order'=> array(
                'KsuPayment.created' => 'DESC',
                'KsuPayment.id' => 'DESC',
            ),
            'fields' => array(),
            'contain' => array()
        );

        if(!empty($options) && $is_merge){
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
        }else{
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getKsuPayment($id){
        $this->bindModel(array(
            'belongsTo' => array(
                'CustomerNoType' => array(
                    'className' => 'CustomerNoType',
                    'foreignKey' => 'customer_id',
                )
            )
        ));
        
        return $this->find('first', array(
            'conditions' => array(
                'KsuPayment.id' => $id
            ),
            'contain' => array(
                'CustomerNoType',
                'KsuPaymentDetail'
            )
        ));
    }
}
?>