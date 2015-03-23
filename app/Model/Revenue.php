<?php
class Revenue extends AppModel {
	var $name = 'Revenue';
	var $validate = array(
        'ttuj_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih'
            ),
        ),
        // 'no_doc' => array(
        //     'isUnique' => array(
        //         'rule' => array('isUnique'),
        //         'allowEmpty'=> true,
        //         'message' => 'No Dokumen telah terdaftar',
        //     ),
        //     // 'notempty' => array(
        //     //     'rule' => array('notempty'),
        //     //     'message' => 'No Dokumen harap dipilih'
        //     // ),
        // ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'date_revenue' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal revenue harap diisi'
            ),
        ),
	);

    var $belongsTo = array(
        'Ttuj' => array(
            'className' => 'Ttuj',
            'foreignKey' => 'ttuj_id',
        ),
        'CustomerNoType' => array(
            'className' => 'CustomerNoType',
            'foreignKey' => 'customer_id',
        ),
    );

    var $hasMany = array(
        'RevenueDetail' => array(
            'className' => 'RevenueDetail',
            'foreignKey' => 'revenue_id',
        ),
    );

    var $hasOne = array(
        'InvoiceDetail' => array(
            'className' => 'InvoiceDetail',
            'foreignKey' => 'revenue_id',
            'dependent' => true,
        ),
    );

	function getData($find, $options = false, $is_merge = true){
        $default_options = array(
            'conditions'=> array(
                'Revenue.status' => 1,
            ),
            'order'=> array(
                'Revenue.created' => 'DESC',
                'Revenue.id' => 'DESC',
            ),
            'contain' => array(
                'Ttuj'
            ),
            'fields' => array(),
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
        if(empty($data['Revenue'])){
            $data_merge = $this->find('first', array(
                'conditions' => array(
                    'id' => $id
                )
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function checkQtyUsed ( $ttuj_id = false, $id = false ) {
        $this->TtujTipeMotorUse = ClassRegistry::init('TtujTipeMotorUse');
        $this->Ttuj = ClassRegistry::init('Ttuj');

        $revenue_id = $this->find('list', array(
            'conditions' => array(
                'Revenue.ttuj_id' => $ttuj_id,
                'Revenue.status' => 1,
            ),
        ));
        $qtyUsed = $this->TtujTipeMotorUse->find('first', array(
            'conditions' => array(
                'TtujTipeMotorUse.revenue_id' => $revenue_id,
                'TtujTipeMotorUse.revenue_id <>' => $id,
            ),
            'fields' => array(
                'SUM(TtujTipeMotorUse.qty) as count_qty'
            )
        ));
        $qtyTtuj = $this->Ttuj->TtujTipeMotor->find('first', array(
            'conditions' => array(
                'TtujTipeMotor.ttuj_id' => $ttuj_id,
                'TtujTipeMotor.status' => 1,
            ),
            'fields' => array(
                'SUM(TtujTipeMotor.qty) as count_qty'
            )
        ));

        if( !empty($qtyUsed[0]['count_qty']) ) {
            $qtyUsed = $qtyUsed[0]['count_qty'];
        } else {
            $qtyUsed = 0;
        }
        if( !empty($qtyTtuj[0]['count_qty']) ) {
            $qtyTtuj = $qtyTtuj[0]['count_qty'];
        } else {
            $qtyTtuj = 0;
        }

        return array(
            'qtyUsed' => $qtyUsed,
            'qtyTtuj' => $qtyTtuj,
        );
    }

    function getPaid ( $data, $ttuj_id ) {
        $revenue = $this->getData('first', array(
            'conditions' => array(
                'Revenue.ttuj_id' => $ttuj_id,
                'Revenue.status' => 1,
                'Revenue.transaction_status' => 'invoiced',
            ),
        ));

        if( !empty($revenue) ) {
            $data['Ttuj']['is_invoice'] = true;
        }

        return $data;
    }
}
?>