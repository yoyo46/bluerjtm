<?php
class Invoice extends AppModel {
	var $name = 'Invoice';
	var $validate = array(
        'no_invoice' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Kode Invoice harap diisi'
            ),
            // 'isUnique' => array(
            //     'rule' => array('isUnique'),
            //     'message' => 'Kode Invoice telah terdaftar',
            // ),
        ),
        'invoice_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tgl Invoice harap dipilih'
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
            if(!empty($options['order'])){
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
        $last_invoice = $this->getData('paginate', array(
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
        debug($last_invoice);die();

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
            $data_merge = $this->InvoicePaymentDetail->getData('first', array(
                'conditions' => array(
                    'InvoicePaymentDetail.invoice_id' => $id,
                    'InvoicePayment.status' => 1,
                ),
                'contain' => array(
                    'InvoicePayment'
                ),
                'fields' => array(
                    'SUM(InvoicePayment.total_payment) total_payment',
                    'SUM(InvoicePayment.total_payment * (InvoicePayment.pph / 100)) total_pph',
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
                    'InvoicePaymentDetail.invoice_id' => $id,
                    'InvoicePayment.status' => 1,
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

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Invoice.invoice_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if(!empty($nodoc)){
            $default_options['conditions']['Invoice.no_invoice LIKE'] = '%'.$nodoc.'%';
        }
        
        return $default_options;
    }

    function getMerge($data, $id, $find = 'first'){
        if(empty($data['Invoice'])){
            $data_merge = $this->find($find, array(
                'conditions' => array(
                    'Invoice.id' => $id
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
}
?>