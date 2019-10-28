<?php
class Invoice extends AppModel {
	var $name = 'Invoice';
	var $validate = array(
        'no_invoice' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode Invoice harap diisi, atau masukan kode pattern pada group customer'
            ),
            'checkUnique' => array(
                'rule' => array('checkUnique'),
                'message' => 'Kode Invoice telah terdaftar',
            ),
        ),
        'invoice_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Invoice harap dipilih'
            ),
        ),
        'company_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Company harap dipilih'
            ),
        ),
        'customer_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Customer harap dipilih'
            ),
        ),
        'bank_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Bank harap dipilih'
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
        'tarif_type' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Jenis tarif harap dipilih'
            ),
        ),
        'note' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Keterangan harap diisi'
            ),
        ),
	);

	var $belongsTo = array(
        'Customer' => array(
            'className' => 'Customer',
            'foreignKey' => 'customer_id',
        ),
        'CustomerNoType' => array(
            'className' => 'CustomerNoType',
            'foreignKey' => 'customer_id',
        ),
        'Company' => array(
            'className' => 'Company',
            'foreignKey' => 'company_id',
        ),
        'Bank' => array(
            'className' => 'Bank',
            'foreignKey' => 'bank_id',
        ),
	);

    var $hasMany = array(
        'InvoiceDetail' => array(
            'className' => 'InvoiceDetail',
            'foreignKey' => 'invoice_id',
        ),
        'InvoicePaymentDetail' => array(
            'className' => 'InvoicePaymentDetail',
            'foreignKey' => 'invoice_id',
        ),
        'RevenueDetail' => array(
            'foreignKey' => 'invoice_id',
        ),
    );

	function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $branch = isset($elements['branch'])?$elements['branch']:true;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'Invoice.id' => 'DESC'
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'all':
                $default_options['conditions']['Invoice.status'] = array( 0, 1 );
                break;

            case 'non-active':
                $default_options['conditions']['Invoice.status'] = 0;
                break;
            
            default:
                $default_options['conditions']['Invoice.status'] = 1;
                break;
        }

        if( !empty($branch) ) {
            $default_options['conditions']['Invoice.branch_id'] = Configure::read('__Site.config_branch_id');
        }

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(isset($options['order'])){
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

    function getNoInvoice( $customer_id, $action = 'tarif' ){
        $last_invoice = $this->getData('first', array(
            'conditions' => array(
                'Invoice.customer_id' => $customer_id,
                'Invoice.type_invoice' => $action,
            ),
            'order' => array(
                'id' => 'DESC'
            )
        ), true, array(
            'status' => 'all',
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

    function getMergePayment($data, $id){
        if(empty($data['InvoicePaymentDetail'])){
            $this->InvoicePaymentDetail->virtualFields['total_payment'] = 'SUM(InvoicePaymentDetail.price_pay)';
            $this->InvoicePaymentDetail->virtualFields['total_ppn'] = 'SUM(ppn_nominal)';
            $this->InvoicePaymentDetail->virtualFields['total_pph'] = 'SUM(pph_nominal)';

            $data_merge = $this->InvoicePaymentDetail->getData('first', array(
                'conditions' => array(
                    'InvoicePaymentDetail.invoice_id' => $id,
                    'InvoicePayment.transaction_status' => 'posting',
                    'InvoicePaymentDetail.status' => 1,
                    'InvoicePayment.status' => 1,
                    'InvoicePayment.is_canceled' => 0,
                ),
                'contain' => array(
                    'InvoicePayment'
                ),
                'group' => array(
                    'InvoicePaymentDetail.invoice_id'
                ),
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }

            $data_merge = $this->InvoicePaymentDetail->getData('list', array(
                'conditions' => array(
                    'InvoicePayment.transaction_status' => 'posting',
                    'InvoicePaymentDetail.invoice_id' => $id,
                    'InvoicePayment.status' => 1,
                    'InvoicePayment.is_canceled' => 0,
                ),
                'contain' => array(
                    'InvoicePayment'
                ),
                'fields' => array(
                    'InvoicePayment.id', 'InvoicePayment.date_payment'
                ),
                'group' => array(
                    'InvoicePayment.date_payment'
                ),
            ));

            if(!empty($data_merge)){
                $data['InvoicePaymentDate'] = $data_merge;
            }
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $nodoc = !empty($data['named']['nodoc'])?$data['named']['nodoc']:false;
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $dateFromRange = $this->filterEmptyField($data, 'named', 'DateFromRange');
        $dateToRange = $this->filterEmptyField($data, 'named', 'DateToRange');

        $customer = $this->filterEmptyField($data, 'named', 'customer');
        $company_id = $this->filterEmptyField($data, 'named', 'company_id');
        $customer_group_id = $this->filterEmptyField($data, 'named', 'customer_group_id');
        $status = $this->filterEmptyField($data, 'named', 'status');

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($dateFromRange) || !empty($dateToRange) ) {
            if( !empty($dateFromRange) ) {
                $default_options['conditions']['DATE_FORMAT(Invoice.period_from, \'%Y-%m-%d\') >='] = $dateFromRange;
            }

            if( !empty($dateToRange) ) {
                $default_options['conditions']['DATE_FORMAT(Invoice.period_to, \'%Y-%m-%d\') <='] = $dateToRange;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Invoice.no_invoice LIKE'] = '%'.$nodoc.'%';
        }

        if(!empty($customer)){
            $default_options['conditions']['CustomerNoType.id'] = $customer;
            $default_options['contain'][] = 'CustomerNoType';
        }

        if(!empty($customer_group_id)){
            $default_options['conditions']['CustomerNoType.customer_group_id'] = $customer_group_id;
            $default_options['contain'][] = 'CustomerNoType';
        }

        if(!empty($company_id)){
            $default_options['conditions']['Invoice.company_id'] = $company_id;
        }

        if(!empty($status)){
            switch ($status) {
                case 'paid':
                    $default_options['conditions']['Invoice.complete_paid '] = 1;
                    break;

                case 'halfpaid':
                    $default_options['conditions']['Invoice.complete_paid '] = 0;
                    $default_options['conditions']['Invoice.paid '] = 1;
                    break;

                case 'void':
                    $default_options['conditions']['Invoice.is_canceled '] = 1;
                    break;
                
                default:
                    $default_options['conditions']['Invoice.complete_paid '] = 0;
                    $default_options['conditions']['Invoice.paid '] = 0;
                    $default_options['conditions']['Invoice.is_canceled '] = 0;
                    break;
            }
        }
        
        return $default_options;
    }

    function getMerge($data, $id, $find = 'first'){
        if(empty($data['Invoice'])){
            $data_merge = $this->find($find, array(
                'conditions' => array(
                    'Invoice.id' => $id,
                ),
            ));

            if(!empty($data_merge)){
                if( $find == 'all' ) {
                    $data['Invoice'] = $data_merge;
                } else {
                    $data = array_merge($data, $data_merge);
                }
            }
        }

        return $data;
    }

    function checkUnique () {
        $id = $this->id;
        $id = Common::hashEmptyField($this->data, 'Invoice.id', $id);

        $no_invoice = !empty($this->data['Invoice']['no_invoice'])?$this->data['Invoice']['no_invoice']:false;
        $value = $this->getData('count', array(
            'conditions' => array(
                'Invoice.no_invoice' => $no_invoice,
                'Invoice.id NOT' => $id,
            ),
        ), true, array(
            'status' => 'active',
            'branch' => false,
        ));
        
        if( !empty($value) ) {
            return false;
        } else {
            return true;
        }
    }

    function _callInvNumber ( $customer, $type = 'invoice_number' ) {
        $result = array();

        if( !empty($customer['CustomerGroup']['CustomerGroupPattern']) ) {
            $result['last_number'] = $customer['CustomerGroup']['CustomerGroupPattern']['last_number'];
            $result['min_digit'] = $customer['CustomerGroup']['CustomerGroupPattern']['min_digit'];
            $result['pattern'] = $customer['CustomerGroup']['CustomerGroupPattern']['pattern'];
            $result['invoice_number'] = sprintf('%s%s', str_pad($customer['CustomerGroup']['CustomerGroupPattern']['last_number'], $customer['CustomerGroup']['CustomerGroupPattern']['min_digit'], '0', STR_PAD_LEFT), $customer['CustomerGroup']['CustomerGroupPattern']['pattern']);
        } else if( !empty($customer['CustomerGroupPattern']) ) {
            $result['last_number'] = $customer['CustomerGroupPattern']['last_number'];
            $result['min_digit'] = $customer['CustomerGroupPattern']['min_digit'];
            $result['pattern'] = $customer['CustomerGroupPattern']['pattern'];
            $result['invoice_number'] = sprintf('%s%s', str_pad($customer['CustomerGroupPattern']['last_number'], $customer['CustomerGroupPattern']['min_digit'], '0', STR_PAD_LEFT), $customer['CustomerGroupPattern']['pattern']);
        } else {
            $result['invoice_number'] = '';
        }

        switch ($type) {
            case 'all':
                return $result;
                break;
            
            default:
                return $result['invoice_number'];
                break;
        }
    }

    function _callInvNumberAndPeriode ( $data, $customer, $tarif_type = 'angkut' ) {
        $head_office = Configure::read('__Site.config_branch_head_office');
        $customer_id = Common::hashEmptyField($data, 'Invoice.customer_id');
        $no_invoice = Common::hashEmptyField($data, 'Invoice.no_invoice');

        if( !empty($customer) ) {
            $import_code = Common::hashEmptyField($data, 'Invoice.import_code');

            $conditions = array(
                'Revenue.import_code' => $import_code,
                'Revenue.customer_id' => $customer_id,
                'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
                'Revenue.status' => 1,
            );
            $conditionsDetail = array_merge($conditions, array(
                'RevenueDetail.invoice_id' => NULL,
                'RevenueDetail.is_charge' => 1,
            ));
            $conditionsDetail = Common::_callRevDetailConditions($tarif_type, $conditionsDetail);

            if( !empty($head_office) ) {
                $elementRevenue = array(
                    'branch' => false,
                );
            } else {
                $elementRevenue = false;
            }

            $this->InvoiceDetail->RevenueDetail->virtualFields['total_qty_unit'] = 'SUM(RevenueDetail.qty_unit)';
            $this->InvoiceDetail->RevenueDetail->virtualFields['total'] = 'SUM(RevenueDetail.total_price_unit)';
            $this->InvoiceDetail->RevenueDetail->virtualFields['period_to'] = 'MAX(Revenue.date_revenue)';
            $this->InvoiceDetail->RevenueDetail->virtualFields['period_from'] = 'MIN(Revenue.date_revenue)';
            
            $revenueDetail = $this->InvoiceDetail->RevenueDetail->getData('first', array(
                'conditions' => $conditionsDetail,
                'order' => array(
                    'Revenue.date_revenue' => 'ASC'
                ),
                'group' => array(
                    'Revenue.customer_id'
                ),
            ), $elementRevenue);

            if( !empty($revenueDetail) ) {
                $revenueId = $this->InvoiceDetail->RevenueDetail->getData('list', array(
                    'conditions' => $conditionsDetail,
                    'fields' => array(
                        'RevenueDetail.revenue_id',
                        'RevenueDetail.revenue_id',
                    ),
                ), $elementRevenue);

                $conditions['Revenue.id'] = $revenueId;
                $conditionRevenue = $conditions;

                $this->InvoiceDetail->Revenue->virtualFields['total_pph'] = 'SUM(Revenue.total_without_tax * (Revenue.pph / 100))';
                $revenue = $this->InvoiceDetail->Revenue->getData('first', array(
                    'conditions' => $conditionRevenue,
                ), true, $elementRevenue);

                $total_pph = Common::hashEmptyField($revenue, 'Revenue.total_pph');
                $total = Common::hashEmptyField($revenueDetail, 'RevenueDetail.total');
                $period_from = Common::hashEmptyField($revenueDetail, 'RevenueDetail.period_from');
                $period_to = Common::hashEmptyField($revenueDetail, 'RevenueDetail.period_to');
                $is_diff_periode = Common::hashEmptyField($customer, 'CustomerNoType.is_diff_periode');
                $total_qty_unit = Common::hashEmptyField($revenueDetail, 'RevenueDetail.total_qty_unit');

                $monthFrom = Common::formatDate($period_from, 'Y-m');
                $monthTo = Common::formatDate($period_to, 'Y-m');

                $period_from_tmp = Common::formatDate($period_from, 'Y-m-d');
                $period_to_tmp = Common::formatDate($period_to, 'Y-m-d');
                
                $data['Invoice']['period_from'] = $period_from_tmp;
                $data['Invoice']['period_to'] = $period_to_tmp;
                $data['Invoice']['total'] = $total;
                $data['Invoice']['total_revenue'] = $total;
                $data['Invoice']['total_pph'] = $total_pph;

                switch ($tarif_type) {
                    case 'kuli':
                        $ket = __('BIAYA KULI MUAT SEPEDA MOTOR');
                        break;

                    case 'asuransi':
                        $ket = __('BIAYA ASURANSI SEPEDA MOTOR');
                        break;

                    case 'subsidi':
                        $ket = __('BIAYA SUBSIDI SEPEDA MOTOR');
                        break;
                    
                    default:
                        $ket = __('JASA ANGKUT SEPEDA MOTOR');
                        break;
                }

                $ket = strtolower($ket);
                $data['Invoice']['note'] = __('%s%sSebanyak %s unit%sPeriode : %s', ucwords($ket), PHP_EOL, $total_qty_unit, PHP_EOL, Common::getCombineDate($period_from, $period_to, 'long', 's/d'));

                unset($this->InvoiceDetail->RevenueDetail->virtualFields['total_qty_unit']);
                unset($this->InvoiceDetail->RevenueDetail->virtualFields['total']);
                unset($this->InvoiceDetail->RevenueDetail->virtualFields['period_to']);
                unset($this->InvoiceDetail->RevenueDetail->virtualFields['period_from']);
                unset($this->InvoiceDetail->Revenue->virtualFields['total_pph']);
            }

            if( empty($no_invoice) ) {
                $customer_group_id = Common::hashEmptyField($customer, 'CustomerNoType.customer_group_id');
                $customer = $this->Customer->CustomerGroup->getMerge($customer, $customer_group_id);

                if( !empty($customer['CustomerGroupPattern']) ) {
                    $data['Invoice']['no_invoice'] = $this->_callInvNumber( $customer );
                }
            }
        }

        return $data;
    }

    function getProsesInvoice ( $data = NULL ) {
        if( !empty($data) ) {
            $revenueId = array();

            foreach ($data as $key => &$inv) {
                $customer_id = Common::hashEmptyField($inv, 'Invoice.customer_id');
                $import_code = Common::hashEmptyField($inv, 'Invoice.import_code');
                $action = Common::hashEmptyField($inv, 'Invoice.jenis_kwitansi');
                $tarif_type = Common::hashEmptyField($inv, 'Invoice.tarif_type');
                $invoice_date = Common::hashEmptyField($inv, 'Invoice.invoice_date');
                $invoice_number = Common::hashEmptyField($inv, 'Invoice.no_invoice');

                $head_office = Configure::read('__Site.config_branch_head_office');
                $elementRevenue = false;

                if( !empty($head_office) || !empty($import_code) ) {
                    $elementRevenue = array(
                        'branch' => false,
                    );
                }
                
                $customer = $this->CustomerNoType->find('first', array(
                    'conditions' => array(
                        'CustomerNoType.id' => $customer_id,
                        'CustomerNoType.status' => 1,
                    ),
                ));
                $customer_group_id = Common::hashEmptyField($customer, 'CustomerNoType.customer_group_id');
                $customer = $this->Customer->CustomerGroup->CustomerGroupPattern->getMerge($customer, $customer_group_id);

                $inv = $this->_callInvNumberAndPeriode($inv, $customer);

                $customer_name_code = Common::hashEmptyField($customer, 'CustomerNoType.customer_name_code');

                if( !empty($inv) ) {
                    if( in_array($action, array( 'tarif', 'tarif_name' )) ){
                        $options = array(
                            'conditions' => array(
                                'Revenue.customer_id' => $customer_id,
                                'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
                                'RevenueDetail.invoice_id' => NULL,
                                'Revenue.status' => 1,
                                'RevenueDetail.status' => 1,
                                'Revenue.import_code' => $import_code,
                            ),
                            'order' => array(
                                'Revenue.date_revenue' => 'ASC',
                                'Revenue.id' => 'ASC',
                                'RevenueDetail.id' => 'ASC',
                            )
                        );
                        $options['conditions'] = Common::_callRevDetailConditions($tarif_type, $options['conditions']);

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

                        $revenue_detail = $this->RevenueDetail->getData('all', $options, $elementRevenue);

                        $result = array();
                        $flag = true;
                        $errorMsg = array();

                        if(!empty($revenue_detail)){
                            foreach ($revenue_detail as $key => $value) {
                                if( $action == 'tarif_name' ) {
                                    $grouping = Common::hashEmptyField($value, 'TarifAngkutan.name_tarif');
                                } else {
                                    $grouping = Common::hashEmptyField($value, 'RevenueDetail.price_unit');
                                }

                                $result[$grouping][] = $value;
                            }
                        }

                        if(!empty($result)){
                            $counter_inv = false;

                            if( empty($invoice_number) ) {
                                $invoice_number = $this->Customer->CustomerGroup->CustomerGroupPattern->getNoInvoice($customer);
                                $counter_inv = true;
                            }

                            foreach ($result as $key => $value) {
                                $this->create();

                                $inv['Invoice']['no_invoice'] = $invoice_number;
                                $this->set($inv);

                                if($this->save()){
                                    $invoice_id = $this->id;

                                    $titleJournalInv = sprintf(__('Invoice customer: %s, No: %s'), $customer_name_code, $invoice_number);
                                    $journalData = array(
                                        'date' => $invoice_date,
                                        'document_id' => $invoice_id,
                                        'title' => $titleJournalInv,
                                        'document_no' => $invoice_number,
                                        'type' => 'invoice',
                                    );

                                    if( !empty($counter_inv) ) {
                                        $invoice_number = $this->Customer->CustomerGroup->CustomerGroupPattern->addPattern($customer, $inv);
                                    }

                                    $this->Customer->CustomerGroup->CustomerGroupPattern->addPattern($customer, $inv);

                                    $total_price = 0;

                                    foreach ($value as $key => $value_detail) {
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

                                    $this->updateAll(array(
                                        'Invoice.total' => $total_price,
                                    ), array(
                                        'Invoice.id' => $invoice_id,
                                    ));

                                    if( !empty($journalData) ) {
                                        $this->Bank->Coa->Journal->setJournal($total_price, array(
                                            'credit' => 'invoice_coa_credit_id',
                                            'debit' => 'invoice_coa_debit_id',
                                        ), $journalData);
                                        $this->Bank->Coa->Journal->setJournal($total_price, array(
                                            'credit' => 'invoice_coa_2_credit_id',
                                            'debit' => 'invoice_coa_2_debit_id',
                                        ), $journalData);
                                    }
                                }
                            }
                        }
                    } else {
                        $no_invoice = Common::hashEmptyField($inv, 'Invoice.no_invoice');

                        $this->create();
                        $flag = $this->save($inv);

                        if( !empty($flag) ) {
                            $invoice_id = $this->id;
                            $total = Common::hashEmptyField($inv, 'Invoice.total');

                            if( !empty($total) ) {
                                $titleJournalInv = sprintf(__('Invoice customer: %s, No: %s'), $customer_name_code, $no_invoice);

                                $this->Bank->Coa->Journal->setJournal($total, array(
                                    'credit' => 'invoice_coa_credit_id',
                                    'debit' => 'invoice_coa_debit_id',
                                ), array(
                                    'date' => $invoice_date,
                                    'document_id' => $invoice_id,
                                    'title' => $titleJournalInv,
                                    'document_no' => $no_invoice,
                                    'type' => 'invoice',
                                ));
                                $this->Bank->Coa->Journal->setJournal($total, array(
                                    'credit' => 'invoice_coa_2_credit_id',
                                    'debit' => 'invoice_coa_2_debit_id',
                                ), array(
                                    'date' => $invoice_date,
                                    'document_id' => $invoice_id,
                                    'title' => $titleJournalInv,
                                    'document_no' => $no_invoice,
                                    'type' => 'invoice',
                                ));
                            }

                            $options = array(
                                'conditions' => array(
                                    'Revenue.status' => 1,
                                    'RevenueDetail.status' => 1,
                                    'Revenue.customer_id' => $customer_id,
                                    'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
                                    'RevenueDetail.invoice_id' => NULL,
                                    'Revenue.import_code' => $import_code,
                                ),
                                'contain' => array(
                                    'Revenue'
                                ),
                                'fields' => array(
                                    'RevenueDetail.id', 'Revenue.id'
                                ),
                                'order' => false,
                            );
                            $options['conditions'] = Common::_callRevDetailConditions($tarif_type, $options['conditions']);
                            $revenueDetails = $this->InvoiceDetail->RevenueDetail->getData('list', $options, $elementRevenue);

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

                                    $this->RevenueDetail->Revenue->id = $revenue_id;
                                    $this->RevenueDetail->Revenue->set('transaction_status', 'invoiced');
                                    $this->RevenueDetail->Revenue->save();
                                }
                            }
                        }
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

                    $this->RevenueDetail->Revenue->id = $revenue_id;

                    if(empty($revenueDetails)){
                        $this->RevenueDetail->Revenue->set('transaction_status', 'invoiced');
                    } else {
                        $this->RevenueDetail->Revenue->set('transaction_status', 'half_invoiced');
                    }

                    $this->RevenueDetail->Revenue->save();
                }
            }
        }
    }

    function _callInvNumberAndPeriodeImport ( $data, $customer, $tarif_type = 'angkut' ) {
        $head_office = Configure::read('__Site.config_branch_head_office');
        $customer_id = Common::hashEmptyField($data, 'Invoice.customer_id');
        $no_invoice = Common::hashEmptyField($data, 'Invoice.no_invoice');

        if( !empty($customer) ) {
            $invoice_id = Common::hashEmptyField($data, 'Invoice.id');
            $import_code = Common::hashEmptyField($data, 'Invoice.import_code');

            $conditions = array(
                'Revenue.customer_id' => $customer_id,
                // 'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
            );
            $conditionsDetail = array_merge($conditions, array(
                'OR' => array(
                    array(
                        'RevenueDetail.invoice_id' => $invoice_id,
                    ),
                    array(
                        'Revenue.import_code' => $import_code,
                        'RevenueDetail.invoice_id' => NULL,
                    ),
                ),
                'RevenueDetail.is_charge' => 1,
            ));
            $conditionsDetail = Common::_callRevDetailConditions($tarif_type, $conditionsDetail);
            $elementRevenue = array(
                'branch' => false,
                'active' => false,
                'status' => 'all',
            );

            $this->InvoiceDetail->RevenueDetail->virtualFields['total_qty_unit'] = 'SUM(RevenueDetail.qty_unit)';
            $this->InvoiceDetail->RevenueDetail->virtualFields['total'] = 'SUM(RevenueDetail.total_price_unit)';
            $this->InvoiceDetail->RevenueDetail->virtualFields['period_to'] = 'MAX(Revenue.date_revenue)';
            $this->InvoiceDetail->RevenueDetail->virtualFields['period_from'] = 'MIN(Revenue.date_revenue)';
            
            $revenueDetail = $this->InvoiceDetail->RevenueDetail->getData('first', array(
                'conditions' => $conditionsDetail,
                'order' => array(
                    'Revenue.date_revenue' => 'ASC'
                ),
                'group' => array(
                    'Revenue.customer_id'
                ),
            ), $elementRevenue);

            if( !empty($revenueDetail) ) {
                $revenueId = $this->InvoiceDetail->RevenueDetail->getData('list', array(
                    'conditions' => $conditionsDetail,
                    'fields' => array(
                        'RevenueDetail.revenue_id',
                        'RevenueDetail.revenue_id',
                    ),
                ), $elementRevenue);

                $conditions['Revenue.id'] = $revenueId;
                $conditionRevenue = $conditions;

                $this->InvoiceDetail->Revenue->virtualFields['total_pph'] = 'SUM(Revenue.total_without_tax * (Revenue.pph / 100))';
                $revenue = $this->InvoiceDetail->Revenue->getData('first', array(
                    'conditions' => $conditionRevenue,
                ), true, $elementRevenue);

                $total_pph = Common::hashEmptyField($revenue, 'Revenue.total_pph');
                $total = Common::hashEmptyField($revenueDetail, 'RevenueDetail.total');
                $period_from = Common::hashEmptyField($revenueDetail, 'RevenueDetail.period_from');
                $period_to = Common::hashEmptyField($revenueDetail, 'RevenueDetail.period_to');
                $is_diff_periode = Common::hashEmptyField($customer, 'CustomerNoType.is_diff_periode');
                $total_qty_unit = Common::hashEmptyField($revenueDetail, 'RevenueDetail.total_qty_unit');

                $monthFrom = Common::formatDate($period_from, 'Y-m');
                $monthTo = Common::formatDate($period_to, 'Y-m');

                $period_from_tmp = Common::formatDate($period_from, 'Y-m-d');
                $period_to_tmp = Common::formatDate($period_to, 'Y-m-d');
                
                $data['Invoice']['period_from'] = $period_from_tmp;
                $data['Invoice']['period_to'] = $period_to_tmp;
                $data['Invoice']['total'] = $total;
                $data['Invoice']['total_revenue'] = $total;
                $data['Invoice']['total_pph'] = $total_pph;

                switch ($tarif_type) {
                    case 'kuli':
                        $ket = __('BIAYA KULI MUAT SEPEDA MOTOR');
                        break;

                    case 'asuransi':
                        $ket = __('BIAYA ASURANSI SEPEDA MOTOR');
                        break;

                    case 'subsidi':
                        $ket = __('BIAYA SUBSIDI SEPEDA MOTOR');
                        break;
                    
                    default:
                        $ket = __('JASA ANGKUT SEPEDA MOTOR');
                        break;
                }

                $ket = strtolower($ket);
                $data['Invoice']['note'] = __('%s%sSebanyak %s unit%sPeriode : %s', ucwords($ket), PHP_EOL, $total_qty_unit, PHP_EOL, Common::getCombineDate($period_from, $period_to, 'long', 's/d'));

                unset($this->InvoiceDetail->RevenueDetail->virtualFields['total_qty_unit']);
                unset($this->InvoiceDetail->RevenueDetail->virtualFields['total']);
                unset($this->InvoiceDetail->RevenueDetail->virtualFields['period_to']);
                unset($this->InvoiceDetail->RevenueDetail->virtualFields['period_from']);
                unset($this->InvoiceDetail->Revenue->virtualFields['total_pph']);
            }

            if( empty($no_invoice) ) {
                $customer_group_id = Common::hashEmptyField($customer, 'CustomerNoType.customer_group_id');
                $customer = $this->Customer->CustomerGroup->getMerge($customer, $customer_group_id);

                if( !empty($customer['CustomerGroupPattern']) ) {
                    $data['Invoice']['no_invoice'] = $this->_callInvNumber( $customer );
                }
            }
        }

        return $data;
    }

    function getProsesInvoiceImport ( $data = NULL ) {
        if( !empty($data) ) {
            $revenueId = array();

            foreach ($data as $key => &$inv) {
                $customer_id = Common::hashEmptyField($inv, 'Invoice.customer_id');
                $session_id = Common::hashEmptyField($inv, 'Invoice.session_id');
                $import_code = Common::hashEmptyField($inv, 'Invoice.import_code');
                $action = Common::hashEmptyField($inv, 'Invoice.type_invoice');
                $tarif_type = Common::hashEmptyField($inv, 'Invoice.tarif_type');
                $invoice_date = Common::hashEmptyField($inv, 'Invoice.invoice_date');
                $invoice_number = Common::hashEmptyField($inv, 'Invoice.no_invoice');

                $head_office = Configure::read('__Site.config_branch_head_office');
                $elementRevenue = array(
                    'active' => false,
                );

                if( !empty($head_office) || !empty($session_id) ) {
                    $elementRevenue['branch'] = false;
                }
                
                $customer = $this->CustomerNoType->find('first', array(
                    'conditions' => array(
                        'CustomerNoType.id' => $customer_id,
                        'CustomerNoType.status' => 1,
                    ),
                ));
                $customer_group_id = Common::hashEmptyField($customer, 'CustomerNoType.customer_group_id');
                $customer = $this->Customer->CustomerGroup->CustomerGroupPattern->getMerge($customer, $customer_group_id);

                $inv = $this->_callInvNumberAndPeriodeImport($inv, $customer);

                $customer_name_code = Common::hashEmptyField($customer, 'CustomerNoType.customer_name_code');

                if( !empty($inv) ) {
                    if( in_array($action, array( 'tarif', 'tarif_name' )) ){
                        $options = array(
                            'conditions' => array(
                                'Revenue.customer_id' => $customer_id,
                                'RevenueDetail.invoice_id' => NULL,
                                'Revenue.session_id' => $session_id,
                            ),
                            'order' => array(
                                'Revenue.date_revenue' => 'ASC',
                                'Revenue.id' => 'ASC',
                                'RevenueDetail.id' => 'ASC',
                            )
                        );
                        $options['conditions'] = Common::_callRevDetailConditions($tarif_type, $options['conditions']);

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

                        $revenue_detail = $this->RevenueDetail->getData('all', $options, $elementRevenue);

                        $result = array();
                        $flag = true;
                        $errorMsg = array();

                        if(!empty($revenue_detail)){
                            foreach ($revenue_detail as $key => $value) {
                                if( $action == 'tarif_name' ) {
                                    $grouping = Common::hashEmptyField($value, 'TarifAngkutan.name_tarif');
                                } else {
                                    $grouping = Common::hashEmptyField($value, 'RevenueDetail.price_unit');
                                }

                                $result[$grouping][] = $value;
                            }
                        }

                        if(!empty($result)){
                            $counter_inv = false;

                            if( empty($invoice_number) ) {
                                $invoice_number = $this->Customer->CustomerGroup->CustomerGroupPattern->getNoInvoice($customer);
                                $counter_inv = true;
                            }

                            foreach ($result as $type_invoice_value => $value) {
                                $inv['Invoice']['no_invoice'] = $invoice_number;
                                $inv['Invoice']['type_invoice_value'] = $type_invoice_value;

                                $checkExistingInv = $this->getData('first', array(
                                    'conditions' => array(
                                        'Invoice.customer_id' => $customer_id,
                                        'Invoice.no_invoice' => $invoice_number,
                                        'Invoice.type_invoice' => $action,
                                        'Invoice.session_id' => $session_id,
                                        'Invoice.type_invoice_value' => $type_invoice_value,
                                    ),
                                ), true, array(
                                    'status' => 'all',
                                    'branch' => false,
                                ));
                                $invoice_last_total = Hash::get($checkExistingInv, 'Invoice.total', 0);

                                if( !empty($inv['Invoice']['id']) ) {
                                    unset($inv['Invoice']['id']);
                                }

                                if( !empty($checkExistingInv['Invoice']['id']) ) {
                                    $inv['Invoice']['id'] = $checkExistingInv['Invoice']['id'];
                                }
                                
                                if($this->saveAll($inv)){
                                    $invoice_id = $this->id;

                                    if( !empty($counter_inv) ) {
                                        $invoice_number = $this->Customer->CustomerGroup->CustomerGroupPattern->addPattern($customer, $inv);
                                    }

                                    $this->Customer->CustomerGroup->CustomerGroupPattern->addPattern($customer, $inv);

                                    $total_price = 0;

                                    foreach ($value as $key => $value_detail) {
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

                                    $this->updateAll(array(
                                        'Invoice.total' => $total_price + $invoice_last_total,
                                    ), array(
                                        'Invoice.id' => $invoice_id,
                                    ));
                                }
                            }
                        }
                    } else {
                        $no_invoice = Common::hashEmptyField($inv, 'Invoice.no_invoice');

                        $this->create();
                        $flag = $this->save($inv);

                        if( !empty($flag) ) {
                            $invoice_id = $this->id;
                            $total = Common::hashEmptyField($inv, 'Invoice.total');
                            $options = array(
                                'conditions' => array(
                                    'Revenue.customer_id' => $customer_id,
                                    'RevenueDetail.invoice_id' => NULL,
                                    'Revenue.import_code' => $import_code,
                                ),
                                'contain' => array(
                                    'Revenue'
                                ),
                                'fields' => array(
                                    'RevenueDetail.id', 'Revenue.id'
                                ),
                                'order' => false,
                            );
                            $options['conditions'] = Common::_callRevDetailConditions($tarif_type, $options['conditions']);
                            $revenueDetails = $this->InvoiceDetail->RevenueDetail->getData('list', $options, $elementRevenue);

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

                                    $this->RevenueDetail->Revenue->id = $revenue_id;
                                    $this->RevenueDetail->Revenue->set('transaction_status', 'invoiced');
                                    $this->RevenueDetail->Revenue->save();
                                }
                            }
                        }
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

                    $this->RevenueDetail->Revenue->id = $revenue_id;

                    if(empty($revenueDetails)){
                        $this->RevenueDetail->Revenue->set('transaction_status', 'invoiced');
                    } else {
                        $this->RevenueDetail->Revenue->set('transaction_status', 'half_invoiced');
                    }

                    $this->RevenueDetail->Revenue->save();
                }
            }
        }
    }
}
?>