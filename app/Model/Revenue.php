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
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
        'Branch' => array(
            'className' => 'Branch',
            'foreignKey' => 'branch_id',
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
            'conditions'=> array(
                // 'Revenue.import_code' => 0,
            ),
            'order'=> array(
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

            case 'commit':
                $default_options['conditions']['Revenue.status'] = 1;
                $default_options['conditions']['Revenue.transaction_status'] = array( 'posting', 'half_invoiced', 'paid' );
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
            if(isset($options['order'])){
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
            if(!empty($options['offset'])){
                $default_options['offset'] = $options['offset'];
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

    function getMerge ($data, $invoice_id, $id, $data_type = 'first', $revenue_detail_id = false) {
        if(empty($data['Revenue'])){
            switch ($data_type) {
                case 'all':
                    if( !empty($revenue_detail_id) ) {
                        $conditions = array(
                            'RevenueDetail.id' => $revenue_detail_id,
                        );
                    } else {
                        $conditions = array(
                            'RevenueDetail.invoice_id' => $invoice_id,
                        );
                    }

                    $data_merge = $this->getData('all', array(
                        'conditions' => array(
                            'Revenue.id' => $id,
                        ),
                        'contain' => array(
                            'RevenueDetail' => array(
                                'conditions' => $conditions,
                                'order' => array(
                                    'RevenueDetail.id' => 'ASC',
                                ),
                            ),
                        ),
                        'order' => array(
                            'Revenue.date_revenue' => 'ASC',
                            'Revenue.id' => 'ASC',
                        ),
                    ), true, array(
                        'branch' => false,
                        'status' => 'all',
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
                        'branch' => false,
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
        ), true, array(
            'branch' => false,
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

    function checkQtyUsedNonGroup ( $ttuj_id = false, $group_motor_id = false ) {
        $conditions = array(
            'Revenue.ttuj_id' => $ttuj_id,
            'RevenueDetail.tarif_angkutan_type' => 'angkut',
            'RevenueDetail.group_motor_id NOT' => $group_motor_id,
        );

        $this->RevenueDetail->virtualFields['count_qty'] = 'SUM(RevenueDetail.qty_unit)';
        $qtyUsed = $this->RevenueDetail->getData('first', array(
            'conditions' => $conditions,
        ));

        return $this->filterEmptyField($qtyUsed, 'RevenueDetail', 'count_qty');
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
                'order' => false,
            ), true, array(
                'branch' => false,
            ));
        }

        switch ($data_type) {
            case 'unit':
                if( !empty($revenues) ) {
                    $revenueDetail = $this->RevenueDetail->find('first', array(
                        'conditions' => array(
                            'RevenueDetail.revenue_id' => $revenues,
                            'RevenueDetail.status' => 1,
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
                            'Invoice.status' => 1,
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
                    'order' => false,
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
                'Revenue.ppn <>' => 0,
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

    function getProsesInvoice ( $customer_id, $invoice_id, $action, $tarif_type, $data = false, $journalData = false ) {
        $revenueId = array();
        $head_office = Configure::read('__Site.config_branch_head_office');
        $elementRevenue = false;

        if( !empty($head_office) ) {
            $elementRevenue = array(
                'branch' => false,
            );
        }

        if( in_array($action, array( 'tarif', 'tarif_name' )) ){
            if( !empty($data) ) {
                $total_price = 0;

                foreach ($data as $key => $value_detail) {
                    if( !empty($value_detail['RevenueDetail']['id']) ) {
                        $revenue_id = !empty($value_detail['Revenue']['id'])?$value_detail['Revenue']['id']:false;
                        $revenue_detail_id = !empty($value_detail['RevenueDetail']['id'])?$value_detail['RevenueDetail']['id']:false;
                        $total_price_unit = !empty($value_detail['RevenueDetail']['total_price_unit'])?$value_detail['RevenueDetail']['total_price_unit']:0;
                        
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
                        $total_price += $total_price_unit;
                    }
                }

                $this->InvoiceDetail->Invoice->updateAll(array(
                    'Invoice.total' => $total_price,
                ), array(
                    'Invoice.id' => $invoice_id,
                ));

                if( !empty($journalData) ) {
                    $this->Journal = ClassRegistry::init('Journal');
                    $this->Journal->setJournal($total_price, array(
                        'credit' => 'invoice_coa_credit_id',
                        'debit' => 'invoice_coa_debit_id',
                    ), $journalData);
                    $this->Journal->setJournal($total_price, array(
                        'credit' => 'invoice_coa_2_credit_id',
                        'debit' => 'invoice_coa_2_debit_id',
                    ), $journalData);
                }
            }
        } else {
            $revenueDetails = $this->RevenueDetail->getData('list', array(
                'conditions' => array(
                    'Revenue.status' => 1,
                    'RevenueDetail.status' => 1,
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
                'order' => array(
                    'Revenue.date_revenue' => 'ASC',
                    'Revenue.id' => 'ASC',
                    'RevenueDetail.id' => 'ASC',
                ),
            ), $elementRevenue);

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
        }

        $revenueId = array_unique($revenueId);

        if( !empty($revenueId) ) {
            foreach ($revenueId as $key => $revenue_id) {
                $revenueDetails = $this->RevenueDetail->getData('first', array(
                    'conditions' => array(
                        'RevenueDetail.revenue_id' => $revenue_id,
                        'RevenueDetail.invoice_id' => NULL,
                    ),
                ), $elementRevenue);

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

    function _callSetJournal ( $revenue_id, $data ) {
        $this->Journal = ClassRegistry::init('Journal');

        $truck_id = !empty($data['Revenue']['truck_id'])?$data['Revenue']['truck_id']:false;
        $customer_id = !empty($data['Revenue']['customer_id'])?$data['Revenue']['customer_id']:false;
        $total_revenue = !empty($data['Revenue']['total'])?$data['Revenue']['total']:0;
        $date_revenue = !empty($data['Revenue']['date_revenue'])?$data['Revenue']['date_revenue']:false;
        $cogs_id = !empty($data['Revenue']['cogs_id'])?$data['Revenue']['cogs_id']:false;
        $no_doc = str_pad($revenue_id, 5, '0', STR_PAD_LEFT);

        $dataCustomer = $this->Ttuj->Customer->getMerge(array(), $customer_id);
        $customer_name = !empty($dataCustomer['Customer']['customer_name_code'])?$dataCustomer['Customer']['customer_name_code']:false;
        $titleJournal = sprintf(__('Revenue customer %s '), $customer_name);

        $this->Journal->deleteJournal($revenue_id, array(
            'revenue',
        ));

        if( !empty($total_revenue) ) {
            $this->Journal->setJournal($total_revenue, array(
                'credit' => 'revenue_coa_credit_id',
                'debit' => 'revenue_coa_debit_id',
            ), array(
                'truck_id' => $truck_id,
                'cogs_id' => $cogs_id,
                'date' => $date_revenue,
                'document_id' => $revenue_id,
                'title' => $titleJournal,
                'document_no' => $no_doc,
                'type' => 'revenue',
            ));
        }
    }

    function saveRevenue ( $id, $data_local, $data, $controller, $is_import = null ) {
        $data['Revenue']['date_sj'] = !empty($data['Revenue']['date_sj']) ? date('Y-m-d', strtotime($data['Revenue']['date_sj'])) : '';
        $data['Revenue']['ppn'] = $ppn = $this->filterEmptyField($data, 'Revenue', 'ppn', 0);
        $data['Revenue']['pph'] = $pph = $this->filterEmptyField($data, 'Revenue', 'pph', 0);
        $data['Revenue']['ppn_total'] = $ppn_total = $this->filterEmptyField($data, 'Revenue', 'ppn_total', 0);
        $data['Revenue']['pph_total'] = $pph_total = $this->filterEmptyField($data, 'Revenue', 'pph_total', 0);

        if( !empty($data['Ttuj']['from_city_id']) ) {
            $data['Revenue']['from_city_id'] = !empty($data['Ttuj']['from_city_id'])?$data['Ttuj']['from_city_id']:0;
            $data['Revenue']['to_city_id'] = !empty($data['Ttuj']['to_city_id'])?$data['Ttuj']['to_city_id']:0;
        }
        if( !empty($data['Ttuj']['truck_id']) ) {
            $data['Revenue']['truck_id'] = $data['Ttuj']['truck_id'];
        }

        $from_city_id = $this->filterEmptyField($data, 'Revenue', 'from_city_id');
        $to_city_id = $this->filterEmptyField($data, 'Revenue', 'to_city_id');
        $customer_id = $this->filterEmptyField($data, 'Revenue', 'customer_id');
        $truck_id = $this->filterEmptyField($data, 'Revenue', 'truck_id');

        $truck = $this->Ttuj->Truck->getMerge(array(), $truck_id);
        $capacity = $this->filterEmptyField($truck, 'Truck', 'capacity');

        $ttuj_id = !empty($data['Revenue']['ttuj_id'])?$data['Revenue']['ttuj_id']:false;
        $total_muatan = $this->Ttuj->TtujTipeMotor->getTotalMuatan($ttuj_id);

        $tarif = $this->RevenueDetail->TarifAngkutan->findTarif($from_city_id, $to_city_id, $customer_id, $capacity, false, $total_muatan);
        $jenis_unit = $this->filterEmptyField($tarif, 'jenis_unit', false, 'per_unit');

        $data['Revenue']['revenue_tarif_type'] = $jenis_unit;
        $data['Revenue']['nopol'] = Common::hashEmptyField($truck, 'Truck.nopol');

        $dataRevenues = array();
        $dataTtuj = array();
        $checkQty = true;
        $result = array(
            'status' => 'error',
            'msg' => false,
        );

        $dataRevenue = $data;
        $revenuDetail = !empty($data['RevenueDetail'])?$data['RevenueDetail']:false;
        $total_revenue = 0;
        $total_qty = 0;

        if( isset($dataRevenue['RevenueDetail']) ) {
            unset($dataRevenue['RevenueDetail']);
        }
        if( isset($dataRevenue['Ttuj']) ) {
            unset($dataRevenue['Ttuj']);
        }

        if( !empty($revenuDetail['city_id']) ) {
            $idx = 0;

            foreach ($revenuDetail['city_id'] as $keyDetail => $detail) {
                $payment_type = isset($data['RevenueDetail']['payment_type'][$keyDetail])?$data['RevenueDetail']['payment_type'][$keyDetail]:0;
                $price_unit = isset($data['RevenueDetail']['price_unit'][$keyDetail])?$this->convertPriceToString($data['RevenueDetail']['price_unit'][$keyDetail], 0):0;
                $qty_unit = !empty($data['RevenueDetail']['qty_unit'][$keyDetail])?$data['RevenueDetail']['qty_unit'][$keyDetail]:0;
                $is_charge = isset($data['RevenueDetail']['is_charge'][$keyDetail])?$data['RevenueDetail']['is_charge'][$keyDetail]:false;
                $tarif_angkutan_type = isset($data['RevenueDetail']['tarif_angkutan_type'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_type'][$keyDetail]:false;
                $total_price_unit = isset($data['RevenueDetail']['total_price_unit'][$keyDetail])?$this->convertPriceToString($data['RevenueDetail']['total_price_unit'][$keyDetail], 0):0;

                $dataRevenue['RevenueDetail'][] = array(
                    'RevenueDetail' => array(
                        'city_id' => isset($data['RevenueDetail']['city_id'][$keyDetail])?$data['RevenueDetail']['city_id'][$keyDetail]:false,
                        'tarif_angkutan_id' => isset($data['RevenueDetail']['tarif_angkutan_id'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_id'][$keyDetail]:false,
                        'tarif_angkutan_type' => $tarif_angkutan_type,
                        'no_do' => isset($data['RevenueDetail']['no_do'][$keyDetail])?$data['RevenueDetail']['no_do'][$keyDetail]:false,
                        'no_sj' => isset($data['RevenueDetail']['no_sj'][$keyDetail])?$data['RevenueDetail']['no_sj'][$keyDetail]:false,
                        'group_motor_id' => isset($data['RevenueDetail']['group_motor_id'][$keyDetail])?$data['RevenueDetail']['group_motor_id'][$keyDetail]:false,
                        'qty_unit' => $qty_unit,
                        'payment_type' => $payment_type,
                        'is_charge' => $is_charge,
                        'price_unit' => isset($data['RevenueDetail']['price_unit'][$keyDetail])?$this->convertPriceToString($data['RevenueDetail']['price_unit'][$keyDetail], 0):0,
                        'total_price_unit' => $total_price_unit,
                    ),
                );
                $total_revenue += $total_price_unit;

                if( $tarif_angkutan_type == 'angkut' ) {
                    $total_qty += $qty_unit;
                }

                $idx++;
            }
        }

        $totalWithoutTax = $total_revenue;
        $validate_qty = true;

        if( !empty($ppn_total) ){
            $total_revenue += $ppn_total;
        }

        $dataRevenue['Revenue']['total'] = $total_revenue;
        $dataRevenue['Revenue']['total_without_tax'] = $totalWithoutTax;
        $dataRevenue['Revenue']['branch_id'] = Configure::read('__Site.config_branch_id');

        $flag = $this->saveAll($dataRevenue, array(
            'validate' => 'only',
        ));

        $validate_qty = true;
        $qtyReview = $this->checkQtyUsed( $ttuj_id, $id );
        $qtyTtuj = !empty($qtyReview['qtyTtuj'])?$qtyReview['qtyTtuj']:0;
        $qtyUse = !empty($qtyReview['qtyUsed'])?$qtyReview['qtyUsed']:0;
        $qtyUse += $total_qty;

        if( empty($is_import) && !empty($ttuj_id) && $qtyUse > $qtyTtuj ) {
            $validate_qty = false;
        }

        if( empty($dataRevenue['RevenueDetail']) ) {
            $checkQty = false;
            $text = __('Mohon masukan informasi muatan truk');
            $result = array(
                'status' => 'error',
                'msg' => $text,
                'data' => $dataRevenue,
            );
        } else if( $flag && $validate_qty ){
            if( empty($is_import) ) {
                if( $qtyUse >= $qtyTtuj ) {
                    $dataTtuj['Ttuj']['is_revenue'] = 1;
                } else {
                    $dataTtuj['Ttuj']['is_revenue'] = 0;
                }
            }
        }else{
            $checkQty = false;
            $text = __('Gagal menyimpan Revenue');

            if( empty($flag) ) {
                $text .= ', mohon lengkapi field-field yang dibutuhkan';
            }
            
            if( empty($validate_qty) ){
                $text .= ', jumlah muatan melebihi jumlah maksimum TTUJ';
            }

            $result = array(
                'status' => 'error',
                'msg' => $text,
                'data' => $dataRevenue,
            );
        }

        if( $checkQty ) {
            if($id && $data_local){
                $dataRevenue['Revenue']['id'] = $id;
                $msg = 'merubah';
            }else{
                $msg = 'membuat';
            }

            if( !empty($id) ){
                $this->RevenueDetail->updateAll(array(
                    'RevenueDetail.status' => 0
                ), array(
                    'RevenueDetail.revenue_id' => $id,
                ));
            }

            $flag = $this->saveAll($dataRevenue);

            if( !empty($flag) ){
                $id = $this->id;
                $this->Log = ClassRegistry::init('Log');

                if( empty($is_import) ) {
                    $this->_callSetJournal($id, $dataRevenue);

                    if( !empty($dataTtuj) && !empty($ttuj_id) ) {
                        $this->Ttuj->id = $ttuj_id;
                        $this->Ttuj->save($dataTtuj);
                    }

                    if( !empty($ttuj_id) && !empty($data_local) && $data_local['Ttuj']['id'] <> $ttuj_id ) {
                        $this->Ttuj->set('is_revenue', 0);
                        $this->Ttuj->id = $data_local['Ttuj']['id'];
                        $this->Ttuj->save();
                    }
                }
            }else{
                $result = array(
                    'status' => 'error',
                    'msg' => sprintf(__('Gagal %s Revenue'), $msg),
                );
                $this->Log->logActivity( sprintf(__('Gagal %s Revenue #%s'), $msg, $id), $controller->user_data, $controller->RequestHandler, $controller->params, 1, false, $id ); 
            }
            
            if( empty($id) ) {
                $msgAlert = sprintf(__('Sukses %s Revenue! No Ref: %s'), $msg, str_pad($id, 5, '0', STR_PAD_LEFT));
            } else {
                $msgAlert = sprintf(__('Sukses %s Revenue!'), $msg);
            }

            $result = array(
                'status' => 'success',
                'msg' => $msgAlert,
            );

            $this->Log->logActivity( sprintf(__('Sukses %s Revenue #%s'), $msg, $id), $controller->user_data, $controller->RequestHandler, $controller->params, 0, false, $id );
        }

        return $result;
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
        $customer_group_id = !empty($data['named']['customer_group_id'])?$data['named']['customer_group_id']:false;

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
                    'Ttuj.truck_id' => $nopol,
                    'Revenue.truck_id' => $nopol,
                );
            } else {
                $conditionsNopol = array(
                    'Truck.nopol LIKE' => '%'.$nopol.'%',
                    'Ttuj.nopol LIKE' => '%'.$nopol.'%',
                    'Revenue.nopol LIKE' => '%'.$nopol.'%',
                );
            }

            $default_options['contain'][] = 'Truck';
            $default_options['contain'][] = 'Ttuj';
            $default_options['conditions'][]['OR'] = $conditionsNopol;
        }
        if(!empty($customer)){
            $default_options['conditions']['Revenue.customer_id'] = $customer;
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Revenue.no_doc LIKE'] = '%'.$nodoc.'%';
        }
        if(!empty($nottuj)){
            $ttuj = $this->Ttuj->getData('list', array(
                'conditions' => array(
                    'Ttuj.no_ttuj LIKE' => '%'.$nottuj.'%',
                ),
            ), true, array(
                'status' => 'all',
                'branch' => false,
            ));
            $default_options['conditions']['Revenue.ttuj_id'] = $ttuj;
        }
        if(!empty($noref)){
            $noref = intval($noref);
            $default_options['conditions']['Revenue.id LIKE'] = '%'.$noref.'%';
        }
        if(!empty($status)){
            if( $status == 'paid' ) {
                $revenueList = $this->getData('list', array(
                    'conditions' => !empty($default_options['conditions'])?$default_options['conditions']:false,
                    'contain' => array(
                        'Ttuj',
                    ),
                    'fields' => array(
                        'Revenue.id', 'Revenue.id'
                    ),
                ));
                $paidList = $this->InvoiceDetail->getInvoicedRevenueList($revenueList);
                $default_options['conditions']['Revenue.id'] = $paidList;
            } else if( $status == 'void' ) {
                $default_options['conditions']['Revenue.status'] = 0;
            } else {
                $default_options['conditions']['Revenue.transaction_status'] = $status;
                $default_options['conditions']['Revenue.status'] = 1;
            }
        }
        if(!empty($customer_group_id)){
            $customer_id = $this->CustomerNoType->getData('list', array(
                'conditions' => array(
                    'CustomerNoType.customer_group_id' => $customer_group_id,
                ),
                'fields' => array(
                    'CustomerNoType.id',
                ),
            ));

            $default_options['conditions']['Revenue.customer_id'] = $customer_id;
        }

        if(!empty($fromcity) || !empty($tocity)){
            $this->RevenueDetail->bindModel(array(
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
            if(!empty($fromcity)){
                $default_options['conditions'][]['OR'] = array(
                    'Ttuj.from_city_id' => $fromcity,
                    'Revenue.from_city_id' => $fromcity,
                );
            }
            if(!empty($tocity)){
                $default_options['conditions'][]['OR'] = array(
                    'Ttuj.to_city_id' => $tocity,
                    'Revenue.to_city_id' => $tocity,
                );
            }
            
            $default_options['contain'][] = 'Ttuj';
        }
        
        return $default_options;
    }

    function getTotal ( $data, $id, $params = false ) {
        if( empty($data['Revenue']) ) {
            $dateFrom = !empty($params['named']['DateFrom'])?$params['named']['DateFrom']:false;
            $dateTo = !empty($params['named']['DateTo'])?$params['named']['DateTo']:false;
            $default_options = array(
                'conditions' => array(
                    'Ttuj.truck_id' => $id,
                ),
                'contain' => array(
                    'Ttuj',
                ),
            );

            if( !empty($dateFrom) || !empty($dateTo) ) {
                if( !empty($dateFrom) ) {
                    $default_options['conditions']['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m-%d\') >='] = $dateFrom;
                }

                if( !empty($dateTo) ) {
                    $default_options['conditions']['DATE_FORMAT(Revenue.date_revenue, \'%Y-%m-%d\') <='] = $dateTo;
                }
            }

            $this->virtualFields['total'] = 'SUM(total_without_tax)';
            $value = $this->getData('first', $default_options, true, array(
                'branch' => false,
            ));

            if( !empty($value) ) {
                $data = array_merge($data, $value);
            }
        }

        return $data;
    }
}
?>