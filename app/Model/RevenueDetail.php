<?php
class RevenueDetail extends AppModel {
	var $name = 'RevenueDetail';
	var $validate = array(
        // 'no_do' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'No DO harap diisi'
        //     ),
        // ),
        // 'no_sj' => array(
        //     'notempty' => array(
        //         'rule' => array('notempty'),
        //         'message' => 'No SJ harap diisi'
        //     ),
        // ),
        'qty_unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Quantity harap diisi'
            ),
        ),
        'price_unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Quantity harap diisi'
            ),
        ),
        'group_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group Motor harap diisi'
            ),
        ),
        'tarif_angkutan_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'tarif angkutan tidak ditemukan'
            ),
        ),
        'tarif_angkutan_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tipe tarif angkutan tidak ditemukan'
            ),
        ),
	);

    var $belongsTo = array(
        'Revenue' => array(
            'className' => 'Revenue',
            'foreignKey' => 'revenue_id',
        ),
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city_id',
        ),
        'GroupMotor' => array(
            'className' => 'GroupMotor',
            'foreignKey' => 'group_motor_id',
        ),
        'Invoice' => array(
            'className' => 'Invoice',
            'foreignKey' => 'invoice_id',
        ),
    );

	function getData($find, $options = false){
        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'contain' => array(
                'Revenue',
            ),
            'fields' => array(),
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
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

    function getMerge($data, $id){
        if(empty($data['RevenueDetail'])){
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

    function getMergeAll($data, $revenue_id){
        if(empty($data['RevenueDetail'])){
            $data_merge = $this->find('all', array(
                'conditions' => array(
                    'RevenueDetail.revenue_id' => $revenue_id,
                ),
                'contain' => array(
                    'City',
                ),
                'order' => array(
                    'RevenueDetail.id' => 'ASC',
                ),
                'group' => array(
                    'RevenueDetail.no_do',
                    'RevenueDetail.no_sj',
                    'RevenueDetail.group_motor_id',
                    'RevenueDetail.city_id',
                ),
                'fields' => array(
                    'RevenueDetail.id', 'RevenueDetail.group_motor_id',
                    'SUM(RevenueDetail.qty_unit) AS qty_unit', 'RevenueDetail.no_do',
                    'RevenueDetail.no_sj', 'City.name',
                    'RevenueDetail.payment_type', 'RevenueDetail.is_charge',
                    'RevenueDetail.price_unit', 'RevenueDetail.tarif_angkutan_id',
                    'RevenueDetail.total_price_unit', 'RevenueDetail.city_id',
                    'SUM(RevenueDetail.total_price_unit) AS total_price_unit',
                    'RevenueDetail.tarif_angkutan_type', 'MAX(RevenueDetail.from_ttuj) from_ttuj',
                    // 'RevenueDetail.note', 
                ),
            ));

            if(!empty($data_merge)){
                $data['RevenueDetail'] = $data_merge;
            }
        }

        return $data;
    }

    function getLastReference(){
        return $this->find('first', array(
            'conditions' => array(
                'RevenueDetail.no_reference <>' => ''
            ),
            'fields' => array(
                'RevenueDetail.no_reference'
            ),
            'order' => array(
                'RevenueDetail.id' => 'no_reference',
                'RevenueDetail.id' => 'DESC'
            )
        ));
    }

    function getPreviewInvoice ( $id = false, $invoice_type = 'angkut', $action = false, $data_action = false, $revenue_detail_id = false ) {
        $this->TipeMotor = ClassRegistry::init('TipeMotor');
        $this->City = ClassRegistry::init('City');
        $this->TarifAngkutan = ClassRegistry::init('TarifAngkutan');
        $this->Ttuj = ClassRegistry::init('Ttuj');
        $contains = array(
            'Revenue' => array(
                'Ttuj'
            ),
            'GroupMotor'
        );
        
        if( $data_action == 'date' ) {
            $contains[] = 'Invoice';
        }

        if( !empty($revenue_detail_id) ) {
            $conditions = array(
                'RevenueDetail.id' => $revenue_detail_id,
                'Revenue.status' => 1,
            );
        } else if( in_array($data_action, array( 'invoice', 'date' )) ) {
            $conditions = array(
                'RevenueDetail.invoice_id' => $id,
            );
        } else {
            $conditions = array(
                'RevenueDetail.revenue_id' => $id,
                'Revenue.status' => 1,
            );
            $conditions['RevenueDetail.invoice_id'] = NULL;
        }

        if( !empty($invoice_type) ) {
            $conditions['RevenueDetail.tarif_angkutan_type'] = $invoice_type;
            // $conditions['Revenue.type'] = $invoice_type;
        }

        if( $action == 'tarif' && $data_action == 'invoice' ){
            $revenue_detail = $this->getData('all', array(
                'conditions' => $conditions,
                'order' => array(
                    'RevenueDetail.price_unit' => 'DESC'
                ),
                'contain' => $contains,
            ));
        }else{
            $revenue_detail = $this->getData('all', array(
                'conditions' => $conditions,
                'order' => array(
                    'Revenue.date_revenue' => 'ASC',
                    'Revenue.id' => 'ASC',
                ),
                'contain' => $contains,
            ));
        }

        if( !empty($revenue_detail) ) {
            foreach ($revenue_detail as $key => $value) {
                $this->Truck = ClassRegistry::init('Truck');

                if(!empty($value['RevenueDetail'])){
                    $from_city_id = !empty($value['Revenue']['Ttuj']['from_city_id'])?$value['Revenue']['Ttuj']['from_city_id']:false;
                    $fromCity = $this->City->getMerge($value, $from_city_id);
                    $value['FromCity'] = !empty($fromCity['City'])?$fromCity['City']:false;
                    $value = $this->TipeMotor->getMerge($value, $value['RevenueDetail']['group_motor_id']);
                    $value = $this->City->getMerge($value, $value['RevenueDetail']['city_id']);
                    $value = $this->TarifAngkutan->getMerge($value, $value['RevenueDetail']['tarif_angkutan_id']);

                    if( empty($value['Revenue']['ttuj_id']) ) {
                        $value = $this->Truck->getMerge($value, $value['Revenue']['truck_id']);
                    }

                    $revenue_detail[$key] = $value;
                }
            }
        }

        if($action == 'tarif' && $data_action == 'invoice'){
            $result = array();
            foreach ($revenue_detail as $key => $value) {
                $result[$value['RevenueDetail']['price_unit']][] = $value;
            }
            $revenue_detail = $result;
        }else{
            $result = array();

            foreach ($revenue_detail as $key => $value) {
                if( $data_action == 'date' && !empty($value['Revenue']['date_revenue']) ) {
                    // $date_revenue = date('d/m/Y', strtotime($value['Revenue']['date_revenue']));
                    // $result[$date_revenue][] = $value;
                    $result[0][] = $value;
                } else {
                    if( $value['Revenue']['revenue_tarif_type'] == 'per_truck' ) {
                        $result[$value['Revenue']['no_doc']][] = $value;
                    } else {
                        $result[$value['RevenueDetail']['city_id']][] = $value;
                    }
                }
            }
            $revenue_detail = $result;
        }

        return $revenue_detail;
    }

    function getSumUnit($data, $id, $data_action = 'invoice'){
        $options = array(
            'fields' => array(
                'SUM(RevenueDetail.qty_unit) AS qty_unit',
            ),
        );

        switch ($data_action) {
            case 'revenue':
                $options['conditions'] = array(
                    'RevenueDetail.revenue_id' => $id,
                );
                $options['group'] = array(
                    'RevenueDetail.revenue_id',
                );

                $data_merge = $this->find('first', $options);

                if(!empty($data_merge[0])){
                    $data['qty_unit'] = $data_merge[0]['qty_unit'];
                }
                break;

            case 'revenue_price':
                $options = array(
                    'conditions' => array(
                        'RevenueDetail.revenue_id' => $id,
                    ),
                    'group' => array(
                        'RevenueDetail.revenue_id',
                    ),
                    'fields' => array(
                        'SUM(RevenueDetail.price_unit*RevenueDetail.qty_unit) AS total_price',
                    ),
                );

                $data_merge = $this->find('first', $options);

                if(!empty($data_merge[0])){
                    $data['total_price'] = $data_merge[0]['total_price'];
                }
                break;
            
            default:
                $options['conditions'] = array(
                    'RevenueDetail.invoice_id' => $id,
                );
                $options['group'] = array(
                    'RevenueDetail.invoice_id',
                );

                $data_merge = $this->find('first', $options);

                if(!empty($data_merge[0])){
                    $data['RevenueDetail']['qty_unit'] = $data_merge[0]['qty_unit'];
                }
                break;
        }

        return $data;
    }

    function getToCity($data, $ttuj_id){
        $revenueDetails = $this->find('list', array(
            'conditions' => array(
                'Revenue.ttuj_id' => $ttuj_id,
                'Revenue.status' => 1,
            ),
            'order' => array(
                'City.name' => 'ASC',
            ),
            'contain' => array(
                'City',
                'Revenue',
            ),
            'fields' => array(
                'RevenueDetail.id', 'City.name',
            ),
            'group' => array(
                'RevenueDetail.city_id'
            ),
        ));

        if(!empty($revenueDetails)){
            $data['city_name'] = implode(', ', $revenueDetails);
        }

        return $data;
    }
}
?>