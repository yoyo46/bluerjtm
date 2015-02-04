<?php
class Invoice extends AppModel {
	var $name = 'Invoice';
	var $validate = array(
        'no_invoice' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode Invoice harap diisi'
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'message' => 'Kode Invoice telah terdaftar',
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'period_from' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Periode awal tidak boleh kosong'
            ),
        ),
        'period_to' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Periode akhir tidak boleh kosong'
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
        'InvoiceDetail' => array(
            'className' => 'InvoiceDetail',
            'foreignKey' => 'invoice_id',
        ),
    );

	function getData( $find, $options = false, $is_merge = true ){
        $default_options = array(
            'conditions'=> array(
                'Invoice.status' => 1,
            ),
            'order'=> array(
                'Invoice.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = array_merge($default_options['order'], $options['order']);
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge($data, $id){
        if(empty($data['Invoice'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'Invoice.id' => $id
                ),
                'contain' => array(
                    'InvoiceType',
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getNoInvoice($action = 'tarif'){
        $last_invoice = $this->find('first', array(
            'conditions' => array(
                'type_invoice' => $action,
                'status' => 1
            ),
            'order' => array(
                'id' => 'DESC'
            )
        ));

        if(!empty($last_invoice)){
            $arr_explode = explode('/', $last_invoice['Invoice']['no_invoice']);
            if($arr_explode[2] == date('Y')){
                $number = intval($arr_explode[0]);
                $id = str_pad ( ++$number , 3, "0", STR_PAD_LEFT);
            }else{
                $id = '001';
            }
            
            $invoice = sprintf('%s/INV/%s/%s', $id, date('Y'), date('m'));
        }else{
            $invoice = sprintf('001/INV/%s/%s', date('Y'), date('m'));
        }

        return $invoice;
    }
}
?>