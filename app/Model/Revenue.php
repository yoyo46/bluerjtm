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

    function getMerge ($data, $invoice_id, $id, $data_type = 'first') {
        if(empty($data['Revenue'])){
            switch ($data_type) {
                case 'all':
                    $data_merge = $this->getData('all', array(
                        'conditions' => array(
                            'Revenue.id' => $id,
                            'Revenue.status' => 1,
                        ),
                        'contain' => array(
                            'RevenueDetail' => array(
                                'conditions' => array(
                                    'RevenueDetail.invoice_id' => $invoice_id,
                                ),
                            ),
                        ),
                    ));

                    if(!empty($data_merge)){
                        $data['Revenue'] = $data_merge;
                    }
                    break;
                
                default:
                    $data_merge = $this->find('first', array(
                        'conditions' => array(
                            'Revenue.id' => $id
                        )
                    ));

                    if(!empty($data_merge)){
                        $data = array_merge($data, $data_merge);
                    }
                    break;
            }
        }

        return $data;
    }

    function checkQtyUsed ( $ttuj_id = false, $id = false ) {
        // $this->TtujTipeMotorUse = ClassRegistry::init('TtujTipeMotorUse');
        $this->Ttuj = ClassRegistry::init('Ttuj');

        $revenue_id = $this->find('list', array(
            'conditions' => array(
                'Revenue.ttuj_id' => $ttuj_id,
                'Revenue.status' => 1,
            ),
        ));
        // $qtyUsed = $this->TtujTipeMotorUse->find('first', array(
        //     'conditions' => array(
        //         'TtujTipeMotorUse.revenue_id' => $revenue_id,
        //         'TtujTipeMotorUse.revenue_id <>' => $id,
        //     ),
        //     'fields' => array(
        //         'SUM(TtujTipeMotorUse.qty) as count_qty'
        //     )
        // ));
        $qtyUsed = $this->RevenueDetail->getData('first', array(
            'conditions' => array(
                'RevenueDetail.revenue_id' => $revenue_id,
                'RevenueDetail.revenue_id <>' => $id,
                'Revenue.status' => 1,
            ),
            'fields' => array(
                'SUM(RevenueDetail.qty_unit) as count_qty'
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

    function getPaid ( $data, $ttuj_id, $data_type = false ) {
        $conditions = array(
            'Revenue.ttuj_id' => $ttuj_id,
            'Revenue.status' => 1,
            'Revenue.transaction_status' => 'invoiced',
        );

        if( in_array($data_type, array( 'unit', 'invoiced' )) ) {
            $revenues = $this->getData('list', array(
                'conditions' => $conditions,
                'fields' => array(
                    'Revenue.id', 'Revenue.id',
                ),
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
                ));

                if( !empty($revenue) ) {
                    $data['Ttuj']['is_invoice'] = true;
                }

                return $data;
                break;
        }
    }

    function getDocumentCashBank () {
        $result = array(
            'docs' => array(),
            'docs_type' => false,
        );

        $docTmps = $this->getData('all', array(
            'conditions' => array(
                'Revenue.paid_ppn' => 0,
                'Revenue.transaction_status <>' => 'unposting',
                'Revenue.status' => 1,
            ),
            'order' => array(
                'Revenue.id' => 'ASC'
            ),
            'contain' => array(
                'CustomerNoType',
            ),
        ), false);
        $docs = array();
        
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

    function getProsesInvoice ( $customer_id, $invoice_id, $tarif_type ) {
        $revenueDetails = $this->RevenueDetail->getData('list', array(
            'conditions' => array(
                'Revenue.customer_id' => $customer_id,
                'Revenue.transaction_status' => array( 'posting', 'half_invoiced' ),
                'RevenueDetail.tarif_angkutan_type' => $tarif_type,
                'RevenueDetail.invoice_id' => NULL,
                'Revenue.status' => 1,
            ),
            'contain' => array(
                'Revenue'
            ),
            'fields' => array(
                'RevenueDetail.id', 'Revenue.id'
            ),
        ));
        $revenueId = array();

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

        $revenueId = array_unique($revenueId);

        if( !empty($revenueId) ) {
            foreach ($revenueId as $key => $revenue_id) {
                $revenueDetails = $this->RevenueDetail->getData('first', array(
                    'conditions' => array(
                        'RevenueDetail.revenue_id' => $revenue_id,
                        'RevenueDetail.invoice_id' => NULL,
                    ),
                ), false);

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
}
?>