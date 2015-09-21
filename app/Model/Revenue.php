<?php
class Revenue extends AppModel {
	var $name = 'Revenue';
	var $validate = array(
        'branch_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Cabang harap dipilih'
            ),
        ),
        'ttuj_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'TTUJ harap dipilih'
            ),
        ),
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
        'truck_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Truk harap dipilih'
            ),
        ),
        'from_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota asal harap dipilih'
            ),
        ),
        'to_city_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kota tujuan harap dipilih'
            ),
        ),
        'tarif_per_truck' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih muatan'
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

	function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Revenue.created' => 'DESC',
                'Revenue.id' => 'DESC',
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Revenue.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Revenue.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Revenue.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['Revenue.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if(!empty($options) && $is_merge){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
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
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
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

    function getMerge ($data, $invoice_id, $id, $data_type = 'first') {
        if(empty($data['Revenue'])){
            switch ($data_type) {
                case 'all':
                    $data_merge = $this->getData('all', array(
                        'conditions' => array(
                            'Revenue.id' => $id,
                        ),
                        'contain' => array(
                            'RevenueDetail' => array(
                                'conditions' => array(
                                    'RevenueDetail.invoice_id' => $invoice_id,
                                ),
                            ),
                        ),
                    ));

                    if( !empty($data_merge) ) {
                        foreach ($data_merge as $key => $value) {
                            $ttuj_id = !empty($value['Revenue']['ttuj_id'])?$value['Revenue']['ttuj_id']:false;
                            
                            $value = $this->Ttuj->getMerge($value, $ttuj_id);
                            $data_merge[$key] = $value;
                        }
                    }

                    if(!empty($data_merge)){
                        $data['Revenue'] = $data_merge;
                    }
                    break;
                
                default:
                    $data_merge = $this->getData('first', array(
                        'conditions' => array(
                            'Revenue.id' => $id
                        ),
                    ), true, array(
                        'status' => 'all',
                    ));

                    if(!empty($data_merge)){
                        $data = array_merge($data, $data_merge);
                    }
                    break;
            }
        }

        return $data;
    }

    function checkQtyUsed ( $ttuj_id = false, $id = false, $group_motor_id = false, $with_qty_ttuj = true ) {
        $this->Ttuj = ClassRegistry::init('Ttuj');

        $revenue_id = $this->getData('list', array(
            'conditions' => array(
                'Revenue.ttuj_id' => $ttuj_id,
            ),
        ));
        $conditions = array(
            'RevenueDetail.revenue_id' => $revenue_id,
            'RevenueDetail.revenue_id NOT' => $id,
            'RevenueDetail.tarif_angkutan_type' => 'angkut',
        );

        if( !empty($group_motor_id) ) {
            $conditions['RevenueDetail.group_motor_id'] = $group_motor_id;
        }

        $qtyUsed = $this->RevenueDetail->getData('first', array(
            'conditions' => $conditions,
            'fields' => array(
                'SUM(RevenueDetail.qty_unit) as count_qty'
            )
        ));

        if( !empty($with_qty_ttuj) ) {
            $qtyTtuj = $this->Ttuj->TtujTipeMotor->find('first', array(
                'conditions' => array(
                    'TtujTipeMotor.ttuj_id' => $ttuj_id,
                    'TtujTipeMotor.status' => 1,
                ),
                'fields' => array(
                    'SUM(TtujTipeMotor.qty) as count_qty'
                )
            ));
        }

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

    function getPaid ( $data, $ttuj_id, $data_type = false ) {
        $conditions = array(
            'Revenue.ttuj_id' => $ttuj_id,
            'Revenue.transaction_status' => array( 'invoiced', 'half_invoiced' ),
        );

        if( in_array($data_type, array( 'unit', 'invoiced' )) ) {
            $revenues = $this->getData('list', array(
                'conditions' => $conditions,
                'fields' => array(
                    'Revenue.id', 'Revenue.id',
                ),
            ), true, array(
                'branch' => false,
            ));
        }

        switch ($data_type) {
            case 'unit':
                if( !empty($revenues) ) {
                    $revenueDetail = $this->RevenueDetail->getData('first', array(
                        'conditions' => array(
                            'RevenueDetail.revenue_id' => $revenues,
                        ),
                        'fields' => array(
                            'SUM(qty_unit) total_unit',
                        ),
                    ));

                    if( !empty($revenueDetail[0]['total_unit']) ) {
                        $data['unitInvoiced'] = $revenueDetail[0]['total_unit'];
                    }
                }

                return $data;
                break;

            case 'invoiced':
                if( !empty($revenues) ) {
                    $invoice = $this->InvoiceDetail->find('first', array(
                        'conditions' => array(
                            'InvoiceDetail.revenue_id' => $revenues,
                            'InvoiceDetail.status' => 1,
                        ),
                        'contain' => array(
                            'Invoice'
                        ),
                        'order' => array(
                            'Invoice.invoice_date' => 'DESC',
                            'Invoice.id' => 'DESC',
                        ),
                    ));

                    if( !empty($invoice['Invoice']) ) {
                        $data['Invoice'] = $invoice['Invoice'];
                    }
                }

                return $data;
                break;
            
            default:
                $revenue = $this->getData('first', array(
                    'conditions' => $conditions,
                ), true, array(
                    'branch' => false,
                ));

                if( !empty($revenue) ) {
                    $data['Ttuj']['is_invoice'] = true;
                }

                return $data;
                break;
        }
    }

    function getDocumentCashBank () {
        $docs = array();
        $result = array(
            'docs' => array(),
            'docs_type' => false,
        );
        $docTmps = $this->getData('all', array(
            'conditions' => array(
                'Revenue.paid_ppn' => 0,
                'Revenue.transaction_status <>' => 'unposting',
            ),
            'order' => array(
                'Revenue.id' => 'ASC'
            ),
            'contain' => array(
                'CustomerNoType',
            ),
        ));
        
        if( !empty($docTmps) ) {
            foreach ($docTmps as $key => $docTmp) {
                $revenue_id = $docTmp['Revenue']['id'];
                $revenue_name = sprintf('%s - %s', str_pad($docTmp['Revenue']['id'], 5, '0', STR_PAD_LEFT), $docTmp['CustomerNoType']['code']);
                $docs[$revenue_id] = $revenue_name;
            }
        }

        $result = array(
            'docs' => $docs,
            'docs_type' => 'revenue',
        );

        return $result;
    }

    function changeStatusPPNPaid ( $revenue_id = false, $status = 0 ) {
        $this->id = $revenue_id;
        $this->set('paid_ppn', $status);
        return $this->save();
    }

    function getProsesInvoice ( $customer_id, $invoice_id, $action, $tarif_type, $data = false ) {
        $revenueId = array();

        switch ($action) {
            case 'tarif':
                if( !empty($data) ) {
                    foreach ($data as $key => $value_detail) {
                        if( !empty($value_detail['RevenueDetail']['id']) ) {
                            $this->RevenueDetail->id = $value_detail['RevenueDetail']['id'];
                            $this->RevenueDetail->set('invoice_id', $invoice_id);
                            $this->RevenueDetail->save();
                            $revenueId[] = !empty($value_detail['Revenue']['id'])?$value_detail['Revenue']['id']:false;
                        }
                    }
                }
                break;
            
            default:
                $revenueDetails = $this->RevenueDetail->getData('list', array(
                    'conditions' => array(
                        'Revenue.customer_id' => $customer_id,
                        'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
                        'RevenueDetail.tarif_angkutan_type' => $tarif_type,
                        'RevenueDetail.invoice_id' => NULL,
                    ),
                    'contain' => array(
                        'Revenue'
                    ),
                    'fields' => array(
                        'RevenueDetail.id', 'Revenue.id'
                    ),
                ));

                if(!empty($revenueDetails)){
                    foreach ($revenueDetails as $revenue_detail_id => $revenue_id) {
                        $this->InvoiceDetail->create();
                        $this->InvoiceDetail->set(array(
                            'invoice_id' => $invoice_id,
                            'revenue_id' => $revenue_id,
                            'revenue_detail_id' => $revenue_detail_id,
                        ));
                        $this->InvoiceDetail->save();

                        $this->RevenueDetail->id = $revenue_detail_id;
                        $this->RevenueDetail->set('invoice_id', $invoice_id);
                        $this->RevenueDetail->save();
                        $revenueId[] = $revenue_id;
                    }
                }
                break;
        }

        $revenueId = array_unique($revenueId);

        if( !empty($revenueId) ) {
            foreach ($revenueId as $key => $revenue_id) {
                $revenueDetails = $this->RevenueDetail->getData('first', array(
                    'conditions' => array(
                        'RevenueDetail.revenue_id' => $revenue_id,
                        'RevenueDetail.invoice_id' => NULL,
                    ),
                ));

                $this->id = $revenue_id;

                if(empty($revenueDetails)){
                    $this->set('transaction_status', 'invoiced');
                } else {
                    $this->set('transaction_status', 'half_invoiced');
                }

                $this->save();
            }
        }
    }

    function saveRevenue ( $id, $data_local, $data, $controller ) {
        $data['Revenue']['date_sj'] = !empty($data['Revenue']['date_sj']) ? date('Y-m-d', strtotime($data['Revenue']['date_sj'])) : '';
        $data['Revenue']['ppn'] = !empty($data['Revenue']['ppn'])?$data['Revenue']['ppn']:0;
        $data['Revenue']['pph'] = !empty($data['Revenue']['pph'])?$data['Revenue']['pph']:0;
        $data['Revenue']['additional_charge'] = !empty($data['Revenue']['additional_charge'])?$data['Revenue']['additional_charge']:0;
        $ttuj_id = !empty($data['Revenue']['ttuj_id'])?$data['Revenue']['ttuj_id']:false;
        $dataRevenues = array();
        $flagSave = array();
        $dataTtuj = array();
        $checkQty = true;
        $result = array(
            'status' => 'error',
            'msg' => false,
        );

        $dataRevenue = $data;
        $dataRevenuDetail = array();

        if( !empty($dataRevenue['RevenueDetail']['tarif_angkutan_type']) ) {
            $idx = 0;

            foreach ($dataRevenue['RevenueDetail']['tarif_angkutan_type'] as $keyDetail => $revenueDetail) {
                $dataRevenuDetail['RevenueDetail']['city_id'][$idx] = isset($data['RevenueDetail']['city_id'][$keyDetail])?$data['RevenueDetail']['city_id'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['tarif_angkutan_id'][$idx] = isset($data['RevenueDetail']['tarif_angkutan_id'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_id'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['tarif_angkutan_type'][$idx] = isset($data['RevenueDetail']['tarif_angkutan_type'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_type'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['no_do'][$idx] = isset($data['RevenueDetail']['no_do'][$keyDetail])?$data['RevenueDetail']['no_do'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['no_sj'][$idx] = isset($data['RevenueDetail']['no_sj'][$keyDetail])?$data['RevenueDetail']['no_sj'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['group_motor_id'][$idx] = isset($data['RevenueDetail']['group_motor_id'][$keyDetail])?$data['RevenueDetail']['group_motor_id'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['qty_unit'][$idx] = isset($data['RevenueDetail']['qty_unit'][$keyDetail])?$data['RevenueDetail']['qty_unit'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['payment_type'][$idx] = isset($data['RevenueDetail']['payment_type'][$keyDetail])?$data['RevenueDetail']['payment_type'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['is_charge'][$idx] = isset($data['RevenueDetail']['is_charge'][$keyDetail])?$data['RevenueDetail']['is_charge'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['from_ttuj'][$idx] = !empty($data['RevenueDetail']['from_ttuj'][$keyDetail])?true:false;
                $dataRevenuDetail['RevenueDetail']['price_unit'][$idx] = isset($data['RevenueDetail']['price_unit'][$keyDetail])?$data['RevenueDetail']['price_unit'][$keyDetail]:false;
                $dataRevenuDetail['RevenueDetail']['total_price_unit'][$idx] = isset($data['RevenueDetail']['total_price_unit'][$keyDetail])?$data['RevenueDetail']['total_price_unit'][$keyDetail]:false;

                if( $dataRevenuDetail['RevenueDetail']['payment_type'][$idx] == 'per_truck' && empty($dataRevenuDetail['RevenueDetail']['is_charge'][$idx]) ) {
                    $dataRevenuDetail['RevenueDetail']['total_price_unit'][$idx] = 0;
                }
                $idx++;
            }
        }

        unset($dataRevenue['RevenueDetail']);
        $dataRevenue['RevenueDetail'] = !empty($dataRevenuDetail['RevenueDetail'])?$dataRevenuDetail['RevenueDetail']:false;

        $validate_detail = true;
        $validate_qty = true;
        $total_revenue = 0;
        $total_qty = 0;
        $array_ttuj_tipe_motor = array();
        $revenue_id = '';

        if( !empty($dataRevenue['Ttuj']) ) {
            $this->TarifAngkutan = ClassRegistry::init('TarifAngkutan');

            $tarif = $this->TarifAngkutan->findTarif($dataRevenue['Ttuj']['from_city_id'], $dataRevenue['Ttuj']['to_city_id'], $dataRevenue['Revenue']['customer_id'], $dataRevenue['Ttuj']['truck_capacity']);

            if( !empty($tarif['jenis_unit']) && $tarif['jenis_unit'] == 'per_truck' ) {
                $tarifTruck = $tarif;

                if( !empty($dataRevenue['Revenue']['additional_charge']) ) {
                    $tarifTruck['addCharge'] = $dataRevenue['Revenue']['additional_charge'];
                }
            }
        }

        if(!empty($dataRevenue['RevenueDetail'])){
            foreach ($dataRevenue['RevenueDetail']['no_do'] as $keyDetail => $value) {
                $tarif_angkutan_type = !empty($dataRevenue['RevenueDetail']['tarif_angkutan_type'][$keyDetail])?$dataRevenue['RevenueDetail']['tarif_angkutan_type'][$keyDetail]:'angkut';
                $data_detail['RevenueDetail'] = array(
                    'no_do' => $value,
                    'no_sj' => $dataRevenue['RevenueDetail']['no_sj'][$keyDetail],
                    'qty_unit' => !empty($dataRevenue['RevenueDetail']['qty_unit'][$keyDetail])?$dataRevenue['RevenueDetail']['qty_unit'][$keyDetail]:0,
                    'price_unit' => !empty($dataRevenue['RevenueDetail']['price_unit'][$keyDetail])?$dataRevenue['RevenueDetail']['price_unit'][$keyDetail]:0,
                    'total_price_unit' => !empty($dataRevenue['RevenueDetail']['total_price_unit'][$keyDetail])?$dataRevenue['RevenueDetail']['total_price_unit'][$keyDetail]:0,
                    'city_id' => $dataRevenue['RevenueDetail']['city_id'][$keyDetail],
                    'group_motor_id' => $dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail],
                    'tarif_angkutan_id' => $dataRevenue['RevenueDetail']['tarif_angkutan_id'][$keyDetail],
                    'tarif_angkutan_type' => $tarif_angkutan_type,
                    'payment_type' => $dataRevenue['RevenueDetail']['payment_type'][$keyDetail],
                    'is_charge' => !empty($dataRevenue['RevenueDetail']['is_charge'][$keyDetail])?$dataRevenue['RevenueDetail']['is_charge'][$keyDetail]:0,
                    'from_ttuj' => !empty($dataRevenue['RevenueDetail']['from_ttuj'][$keyDetail])?true:false,
                );

                $this->RevenueDetail->set($data_detail);
                if( !$this->RevenueDetail->validates() ){
                    $validate_detail = false;
                }
                
                if( $tarif_angkutan_type == 'angkut' ) {
                    $total_qty += $dataRevenue['RevenueDetail']['qty_unit'][$keyDetail];

                    if( empty($array_ttuj_tipe_motor[$dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail]]) ){
                        $array_ttuj_tipe_motor[$dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail]] = array(
                            'qty' => !empty($data_detail['RevenueDetail']['qty_unit'])?intval($data_detail['RevenueDetail']['qty_unit']):0,
                            'payment_type' => $dataRevenue['RevenueDetail']['payment_type'][$keyDetail]
                        );
                    }else{
                        $array_ttuj_tipe_motor[$dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail]]['qty'] += !empty($data_detail['RevenueDetail']['qty_unit'])?$data_detail['RevenueDetail']['qty_unit']:0;
                        $array_ttuj_tipe_motor[$dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail]]['payment_type'] = $dataRevenue['RevenueDetail']['payment_type'][$keyDetail];
                    }
                }

                if(!empty($dataRevenue['RevenueDetail']['price_unit'][$keyDetail]) && $dataRevenue['RevenueDetail']['qty_unit'][$keyDetail]){
                    if($dataRevenue['RevenueDetail']['payment_type'][$keyDetail] == 'per_truck'){
                        $total_revenue += $dataRevenue['RevenueDetail']['price_unit'][$keyDetail];
                    }else{
                        $total_revenue += $dataRevenue['RevenueDetail']['price_unit'][$keyDetail] * $dataRevenue['RevenueDetail']['qty_unit'][$keyDetail];
                    }
                }
            }
        }

        if( !empty($dataRevenue['Revenue']['revenue_tarif_type']) && $dataRevenue['Revenue']['revenue_tarif_type'] == 'per_truck' ) {
            $total_revenue = $dataRevenue['Revenue']['tarif_per_truck'];
        }

        $totalWithoutTax = $total_revenue;
        if( !empty($dataRevenue['Revenue']['additional_charge']) && $dataRevenue['Revenue']['additional_charge'] > 0 ){
            $total_revenue += $dataRevenue['Revenue']['additional_charge'];
        }

        if( !empty($dataRevenue['Revenue']['pph']) && $dataRevenue['Revenue']['pph'] > 0 ){
            $pph = $total_revenue * ($dataRevenue['Revenue']['pph'] / 100);
        }
        if( !empty($dataRevenue['Revenue']['ppn']) && $dataRevenue['Revenue']['ppn'] > 0 ){
            $ppn = $total_revenue * ($dataRevenue['Revenue']['ppn'] / 100);
        }

        // if( !empty($dataRevenue['Revenue']['pph']) && $dataRevenue['Revenue']['pph'] > 0 ){
        //     $total_revenue -= $pph;
        // }
        if( !empty($dataRevenue['Revenue']['ppn']) && $dataRevenue['Revenue']['ppn'] > 0 ){
            $total_revenue += $ppn;
        }

        $dataRevenue['Revenue']['total'] = $total_revenue;
        $dataRevenue['Revenue']['total_without_tax'] = $totalWithoutTax;
        $dataRevenue['Revenue']['branch_id'] = Configure::read('__Site.config_branch_id');

        $this->set($dataRevenue);
        $validate_qty = true;
        $qtyReview = $this->checkQtyUsed( $ttuj_id, $id );
        $qtyTtuj = !empty($qtyReview['qtyTtuj'])?$qtyReview['qtyTtuj']:0;
        $qtyUse = !empty($qtyReview['qtyUsed'])?$qtyReview['qtyUsed']:0;
        $qtyUse += $total_qty;

        if( !empty($ttuj_id) && $qtyUse > $qtyTtuj ) {
            $validate_qty = false;
        }

        $validate_main = $this->validates($dataRevenue);

        if( $validate_main && $validate_detail && $validate_qty ){
            if( $qtyUse >= $qtyTtuj ) {
                $dataTtuj['Ttuj']['is_revenue'] = 1;
            } else {
                $dataTtuj['Ttuj']['is_revenue'] = 0;
            }
        }else{
            $checkQty = false;
            $text = __('Gagal menyimpan Revenue');

            if( empty($validate_main) ) {
                $text .= ', mohon lengkapi field-field utama yang dibutuhkan';
            } else if( empty($validate_detail) ){
                $text .= ', mohon lengkapi field-field muatan';
            }
            
            if( empty($validate_qty) ){
                $text .= ', jumlah muatan melebihi jumlah maksimum TTUJ';
            }

            $result = array(
                'status' => 'error',
                'msg' => $text,
            );
        }

        if( $checkQty ) {
            if($id && $data_local){
                $this->id = $id;
                $msg = 'merubah';
            }else{
                $this->create();
                $msg = 'membuat';
            }

            if($this->save($dataRevenue)){
                $revenue_id = $this->id;
                $this->TtujTipeMotorUse = ClassRegistry::init('TtujTipeMotorUse');
                $this->Log = ClassRegistry::init('Log');

                if($id && $data_local){
                    $this->RevenueDetail->updateAll(array(
                        'RevenueDetail.status' => 0
                    ), array(
                        'RevenueDetail.revenue_id' => $revenue_id,
                    ));

                    $this->TtujTipeMotorUse->deleteAll(array(
                        'revenue_id' => $revenue_id
                    ));
                }

                foreach ($array_ttuj_tipe_motor as $group_motor_id => $value) {
                    $this->TtujTipeMotorUse->create();
                    $this->TtujTipeMotorUse->set(array(
                        'revenue_id' => $revenue_id,
                        'group_motor_id' => $group_motor_id,
                        'qty' => $value['qty']
                    ));
                    $this->TtujTipeMotorUse->save();
                }

                $getLastReference = intval($this->RevenueDetail->getLastReference())+1;

                if( !empty($dataRevenue['RevenueDetail']) ) {
                    foreach ($dataRevenue['RevenueDetail']['no_do'] as $key => $value) {
                        $this->RevenueDetail->create();
                        $data_detail['RevenueDetail'] = array(
                            'no_do' => $value,
                            'no_sj' => $dataRevenue['RevenueDetail']['no_sj'][$key],
                            'qty_unit' => !empty($dataRevenue['RevenueDetail']['qty_unit'][$key])?$dataRevenue['RevenueDetail']['qty_unit'][$key]:0,
                            'price_unit' => !empty($dataRevenue['RevenueDetail']['price_unit'][$key])?$dataRevenue['RevenueDetail']['price_unit'][$key]:0,
                            'total_price_unit' => !empty($dataRevenue['RevenueDetail']['total_price_unit'][$key])?$dataRevenue['RevenueDetail']['total_price_unit'][$key]:0,
                            'revenue_id' => $revenue_id,
                            'city_id' => $dataRevenue['RevenueDetail']['city_id'][$key],
                            'group_motor_id' => $dataRevenue['RevenueDetail']['group_motor_id'][$key],
                            'tarif_angkutan_id' => $dataRevenue['RevenueDetail']['tarif_angkutan_id'][$key],
                            'tarif_angkutan_type' => $dataRevenue['RevenueDetail']['tarif_angkutan_type'][$key],
                            'no_reference' => str_pad ( $getLastReference++ , 10, "0", STR_PAD_LEFT),
                            'payment_type' => $dataRevenue['RevenueDetail']['payment_type'][$key],
                            'is_charge' => !empty($dataRevenue['RevenueDetail']['is_charge'][$key])?$dataRevenue['RevenueDetail']['is_charge'][$key]:0,
                            'from_ttuj' => !empty($dataRevenue['RevenueDetail']['from_ttuj'][$key])?true:false,
                        );

                        $this->RevenueDetail->set($data_detail);
                        $this->RevenueDetail->save();
                    }
                }

                if( !empty($dataTtuj) && !empty($ttuj_id) ) {
                    $this->Ttuj->id = $ttuj_id;
                    $this->Ttuj->save($dataTtuj);
                }

                if( !empty($ttuj_id) && !empty($data_local) && $data_local['Ttuj']['id'] <> $ttuj_id ) {
                    $this->Ttuj->set('is_revenue', 0);
                    $this->Ttuj->id = $data_local['Ttuj']['id'];
                    $this->Ttuj->save();
                }
                $flagSave[] = true;
            }else{
                $result = array(
                    'status' => 'error',
                    'msg' => sprintf(__('Gagal %s Revenue'), $msg),
                );
                $this->Log->logActivity( sprintf(__('Gagal %s Revenue #%s'), $msg, $id), $controller->user_data, $controller->RequestHandler, $controller->params, 1, false, $id ); 
            }
            
            if( empty($id) ) {
                $msgAlert = sprintf(__('Sukses %s Revenue! No Ref: %s'), $msg, str_pad($revenue_id, 5, '0', STR_PAD_LEFT));
            } else {
                $msgAlert = sprintf(__('Sukses %s Revenue!'), $msg);
            }

            $result = array(
                'status' => 'success',
                'msg' => $msgAlert,
            );

            $this->Log->logActivity( sprintf(__('Sukses %s Revenue #%s'), $msg, $revenue_id), $controller->user_data, $controller->RequestHandler, $controller->params, 0, false, $revenue_id );
        }

        return $result;
    }
}
?>