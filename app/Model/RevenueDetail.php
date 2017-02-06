<?php
class RevenueDetail extends AppModel {
	var $name = 'RevenueDetail';
	var $validate = array(
        'city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota Tujuan harap dipilih'
            ),
        ),
        'qty_unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Qty harap diisi'
            ),
        ),
        'price_unit' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tarif harap diisi'
            ),
            'validNumber' => array(
                'rule' => array('validNumber', 'price_unit'),
                'message' => 'Tarif harap diisi'
            ),
        ),
        'group_motor_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group Motor harap diisi'
            ),
        ),
        'tarif_angkutan_id' => array(
            'checkTarif' => array(
                'rule' => array('checkTarif'),
                'message' => 'Tarif angkutan tidak ditemukan'
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
        'TarifAngkutan' => array(
            'className' => 'TarifAngkutan',
            'foreignKey' => 'tarif_angkutan_id',
        ),
    );

    function checkTarif( $data ) {
        $action_type = $this->filterEmptyField($this->Revenue->data, 'Revenue', 'action_type');
        $tarif_angkutan_id = $this->filterEmptyField($this->data, 'RevenueDetail', 'tarif_angkutan_id');
        
        if( $action_type != 'manual' && empty($tarif_angkutan_id) ) {
            return false;
        } else {
            return true;
        }
    }

    function validNumber( $data, $field ) {
        if( !empty($data[$field]) ) {
            return true;
        } else {
            return false;   
        }
    }

	function getData( $find, $options = false, $elements = array(), $is_merge = true ){
        $active = isset($elements['active'])?$elements['active']:true;
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(),
            'group'=> array(),
            'contain' => array(
                'Revenue',
            ),
            'fields' => array(),
        );
        if( !empty($branch) ) {
            $default_options['conditions']['Revenue.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($active) ) {
            $options['conditions']['RevenueDetail.status'] = 1;
        }

        if( !empty($options) && !empty($is_merge) ){
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
        } else if( !empty($options) && empty($is_merge) ) {
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
        if(empty($data['RevenueDetail'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'id' => $id,
                ),
            ), array(
                'active' => false,
            ), false);

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    function getMergeAll($data, $revenue_id){
        if(empty($data['RevenueDetail'])){
            $this->virtualFields['qty_unit'] = 'SUM(RevenueDetail.qty_unit)';
            $this->virtualFields['total_price_unit'] = 'SUM(RevenueDetail.total_price_unit)';
            $data_merge = $this->getData('all', array(
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
                    'RevenueDetail.is_charge',
                    'RevenueDetail.no_do',
                    'RevenueDetail.no_sj',
                    'RevenueDetail.group_motor_id',
                    'RevenueDetail.city_id',
                ),
            ), array(
                'active' => true,
            ));

            if(!empty($data_merge)){
                $data['RevenueDetail'] = $data_merge;
            }
        }

        return $data;
    }

    function getLastReference(){
        return $this->getData('first', array(
            'conditions' => array(
                'RevenueDetail.no_reference <>' => ''
            ),
            'fields' => array(
                'RevenueDetail.no_reference'
            ),
            'order' => array(
                'RevenueDetail.id' => 'DESC'
            )
        ), false, false);
    }

    function getPreviewInvoice ( $id = false, $invoice_type = 'angkut', $action = false, $data_action = false, $revenue_detail_id = false ) {
        $result = array();
        $options = array();
        $head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = false;
        
        if( in_array($data_action, array( 'date', 'hso-smg', 'sa' )) ) {
            $options['contain'][] = 'Invoice';
        }
        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

        if( !empty($revenue_detail_id) ) {
            $options['conditions'] = array(
                'RevenueDetail.id' => $revenue_detail_id,
            );
            $elementRevenue['active'] = false;
        } else if( in_array($data_action, array( 'invoice', 'date', 'hso-smg', 'sa' )) ) {
            $options['conditions'] = array(
                'RevenueDetail.invoice_id' => $id,
            );
        } else {
            $options['conditions'] = array(
                'RevenueDetail.revenue_id' => $id,
                'Revenue.status' => 1,
                'RevenueDetail.invoice_id' => NULL,
            );
        }

        if( !empty($invoice_type) ) {
            $options['conditions']['RevenueDetail.tarif_angkutan_type'] = $invoice_type;
        }

        if( in_array($action, array( 'tarif', 'tarif_name' )) && in_array($data_action, array( 'invoice', 'preview' )) ){
                $options['order'] = array(
                    'Revenue.date_revenue' => 'ASC',
                    'Revenue.id' => 'ASC',
                    'RevenueDetail.id' => 'ASC',
                );

            if( $action == 'tarif_name' ) {
                $options['contain'][] = 'TarifAngkutan';
                $options['order'] = array_merge(array(
                    'TarifAngkutan.name_tarif' => 'ASC',
                ), $options['order']);
            } else {
                $options['order'] = array_merge(array(
                    'RevenueDetail.price_unit' => 'ASC',
                ), $options['order']);
            }
        }else{
            $options['order'] = array(
                'Revenue.date_revenue' => 'ASC',
                'Revenue.id' => 'ASC',
                'RevenueDetail.id' => 'ASC',
            );
        }

        $revenue_detail = $this->getData('all', $options, $elementRevenue);

        if( !empty($revenue_detail) ) {
            $this->City = ClassRegistry::init('City');

            foreach ($revenue_detail as $key => $value) {
                if(!empty($value['RevenueDetail'])){
                    $date_revenue = !empty($value['Revenue']['date_revenue'])?$value['Revenue']['date_revenue']:false;
                    // $from_city_id = !empty($value['Revenue']['Ttuj']['from_city_id'])?$value['Revenue']['Ttuj']['from_city_id']:false;
                    $truck_id = !empty($value['Revenue']['truck_id'])?$value['Revenue']['truck_id']:false;

                    // $fromCity = $this->City->getMerge($value, $from_city_id);
                    // $value['FromCity'] = !empty($fromCity['City'])?$fromCity['City']:false;
                    
                    $value = $this->Revenue->Ttuj->TtujTipeMotor->TipeMotor->getMerge($value, $value['RevenueDetail']['group_motor_id']);
                    $value = $this->City->getMerge($value, $value['RevenueDetail']['city_id']);
                    $value = $this->TarifAngkutan->getMerge($value, $value['RevenueDetail']['tarif_angkutan_id']);
                    $value = $this->Revenue->Truck->getMerge($value, $truck_id);

                    $ttuj = $this->Revenue->Ttuj->getMerge($value, $value['Revenue']['ttuj_id']);

                    if( !empty($ttuj['Ttuj']) ) {
                        $value['Revenue']['Ttuj'] = $ttuj['Ttuj'];
                    } else {
                        $value['Revenue']['Ttuj'] = array();;
                    }

                    // if( empty($value['Revenue']['ttuj_id']) ) {
                    //     $value = $this->Revenue->Ttuj->Truck->getMerge($value, $value['Revenue']['truck_id']);
                    // }

                    if(in_array($action, array( 'tarif', 'tarif_name' )) && in_array($data_action, array( 'invoice', 'preview' ))){
                        if( $action == 'tarif_name' ) {
                            $result[$value['TarifAngkutan']['name_tarif']][] = $value;
                        } else {
                            $result[$value['RevenueDetail']['price_unit']][] = $value;
                        }
                    } else if( in_array($data_action, array( 'date', 'sa' )) && !empty($value['Revenue']['date_revenue']) ) {
                        $result[0][] = $value;
                    } else {
                        if( $value['Revenue']['revenue_tarif_type'] == 'per_truck' ) {
                            $result[$value['Revenue']['no_doc']][] = $value;
                        } else if( $data_action == 'hso-smg' ) {
                            $result[$date_revenue][] = $value;
                        } else {
                            $result[$value['RevenueDetail']['city_id']][] = $value;
                        }
                    }
                }
            }
        }

        return $result;
    }

    function getSumUnit($data, $id, $data_action = 'invoice', $fieldName = 'RevenueDetail.invoice_id'){
        $this->virtualFields['qty_unit'] = 'SUM(RevenueDetail.qty_unit)';
        $options = array();
        $elementRevenue = array(
            'branch' => false,
        );

        switch ($data_action) {
            case 'revenue':
                $options['conditions'] = array(
                    $fieldName => $id,
                );
                $options['group'] = array(
                    'RevenueDetail.revenue_id',
                );

                $data_merge = $this->getData('first', $options, $elementRevenue);

                if(!empty($data_merge['RevenueDetail']['qty_unit'])){
                    $data['qty_unit'] = $data_merge['RevenueDetail']['qty_unit'];
                }
                break;

            case 'revenue_price':
                $this->virtualFields['total_price'] = 'SUM(RevenueDetail.price_unit*RevenueDetail.qty_unit)';
                $options = array(
                    'conditions' => array(
                        $fieldName => $id,
                    ),
                    'group' => array(
                        'RevenueDetail.revenue_id',
                    ),
                );

                $data_merge = $this->getData('first', $options, $elementRevenue);

                if(!empty($data_merge['RevenueDetail']['total_price'])){
                    $data['total_price'] = $data_merge['RevenueDetail']['total_price'];
                }
                break;
            
            default:
                $options = array(
                    'contain' => array(
                        'Revenue',
                    ),
                    'conditions' => array(
                        $fieldName => $id,
                    ),
                    'group' => array(
                        $fieldName,
                    ),
                );

                // $this->virtualFields['sum_pph'] = 'SUM(Revenue.total * (Revenue.pph/100))';
                $data_merge = $this->getData('first', $options, $elementRevenue);

                if(!empty($data_merge['RevenueDetail']['qty_unit'])){
                    $data['qty_unit'] = $data_merge['RevenueDetail']['qty_unit'];
                }
                // if(!empty($data_merge['RevenueDetail']['sum_pph'])){
                //     $data['total_pph'] = $data_merge['RevenueDetail']['sum_pph'];
                // }
                break;
        }

        return $data;
    }

    function getToCity($data, $ttuj_id){
        $revenueDetails = $this->getData('list', array(
            'conditions' => array(
                'Revenue.ttuj_id' => $ttuj_id,
                'Revenue.status' => 1,
            ),
            'order' => array(
                'City.name' => 'ASC',
            ),
            'contain' => array(
                'City',
            ),
            'fields' => array(
                'RevenueDetail.id', 'City.name',
            ),
            'group' => array(
                'RevenueDetail.city_id'
            ),
        ), array(
            'active' => true,
        ));

        if(!empty($revenueDetails)){
            $revenueDetails = array_unique($revenueDetails);
            $data['city_name'] = implode(', ', $revenueDetails);
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $nopol = !empty($data['named']['nopol'])?$data['named']['nopol']:false;
        $type = !empty($data['named']['type'])?$data['named']['type']:1;
        $customer = !empty($data['named']['customer'])?$data['named']['customer']:false;
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;
        $nottuj = !empty($data['named']['nottuj'])?$data['named']['nottuj']:false;
        $status = !empty($data['named']['status'])?$data['named']['status']:false;
        $fromcity = !empty($data['named']['fromcity'])?$data['named']['fromcity']:false;
        $tocity = !empty($data['named']['tocity'])?$data['named']['tocity']:false;
        $tocityDetail = !empty($data['named']['tocityDetail'])?$data['named']['tocityDetail']:false;

        if(!empty($fromcity) || !empty($tocity) || $nopol){
            $this->bindModel(array(
                'hasOne' => array(
                    'Ttuj' => array(
                        'className' => 'Ttuj',
                        'foreignKey' => false,
                        'conditions' => array(
                            'Revenue.ttuj_id = Ttuj.id',
                        ),
                    ),
                ),
            ), false);
            
            $default_options['contain'][] = 'Ttuj';
        }

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nopol)){
            if( $type == 2 ) {
                $conditionsNopol = array(
                    'Truck.id' => $nopol,
                );
            } else {
                $conditionsNopol = array(
                    'Truck.nopol LIKE' => '%'.$nopol.'%',
                );
            }

            $truckSearch = $this->Revenue->Truck->getData('list', array(
                'conditions' => $conditionsNopol,
                'fields' => array(
                    'Truck.id', 'Truck.id',
                ),
            ), true, array(
                'status' => 'all',
                'branch' => false,
            ));

            $default_options['conditions'][0]['OR']['Ttuj.truck_id'] = $truckSearch;
            $default_options['conditions'][0]['OR']['Revenue.truck_id'] = $truckSearch;
        }
        if(!empty($customer)){
            $default_options['conditions']['Revenue.customer_id'] = $customer;
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Revenue.no_doc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($nottuj)){
            $ttuj = $this->Revenue->Ttuj->getData('list', array(
                'conditions' => array(
                    'Ttuj.no_ttuj LIKE' => '%'.$nottuj.'%',
                ),
            ), true, array(
                'branch' => false,
            ));
            $default_options['conditions']['Revenue.ttuj_id'] = $ttuj;
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(Revenue.id, 5, 0) LIKE'] = '%'.$noref.'%';
        }
        if(!empty($status)){
            $default_options['conditions']['Revenue.transaction_status'] = $status;
        }
            
        if(!empty($fromcity)){
            $default_options['conditions'][1]['OR']['Ttuj.from_city_id'] = $fromcity;
            $default_options['conditions'][1]['OR']['Revenue.from_city_id'] = $fromcity;
        }
        if(!empty($tocity)){
            $default_options['conditions'][2]['OR']['Ttuj.to_city_id'] = $tocity;
            $default_options['conditions'][2]['OR']['Revenue.to_city_id'] = $tocity;
        }
        if(!empty($tocityDetail)){
            $default_options['conditions']['RevenueDetail.city_id'] = $tocityDetail;
        }
        
        return $default_options;
    }
}
?>