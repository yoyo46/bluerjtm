<?php
App::uses('AppController', 'Controller');
class SpkController extends AppController {
    public $uses = array(
        'Spk',
    );

    public $components = array(
        'RjSpk'
    );

    function beforeFilter() {
        parent::beforeFilter();
        $this->set('title_for_layout', __('ERP RJTM | Gudang'));
        $this->set('module_title', __('Gudang'));
    }

    function search( $index = 'index', $id = false, $data_action = false ){
        $refine = array();
        if(!empty($this->request->data)) {
            $refine = $this->RjSpk->processRefine($this->request->data);
            $params = $this->RjSpk->generateSearchURL($refine);
            if(!empty($id)){
                array_push($params, $id);
            }
            if(!empty($data_action)){
                array_push($params, $data_action);
            }
            $params['action'] = $index;

            $this->redirect($params);
        }
        $this->redirect('/');
    }

    public function index() {
        $this->set('sub_module_title', __('SPK'));
        
        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
        $dateTo = date('Y-m-d');

        $params = $this->MkCommon->_callRefineParams($this->params, array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
        $options =  $this->Spk->_callRefineParams($params);
        $this->paginate = $this->Spk->getData('paginate', $options, array(
            'status' => 'all',
        ));
        $values = $this->paginate('Spk');
        $values = $this->Spk->getMergeList($values, array(
            'contain' => array(
                'Vendor',
            ),
        ));

        $this->MkCommon->_layout_file('select');
        $this->set('active_menu', 'spk');
        $this->set(compact(
            'values'
        ));
    }

    function add(){
        $this->set('sub_module_title', __('Buat SPK'));

        $data = $this->request->data;

        if( !empty($data) ) {
            $data = $this->RjSpk->_callBeforeSave($data);
            $result = $this->Spk->doSave($data);
            $this->MkCommon->setProcessParams($result, array(
                'controller' => 'products',
                'action' => 'receipts',
                'admin' => false,
            ));
        }

        $this->RjSpk->_callSpkBeforeRender($data);

        $this->set(array(
            'active_menu' => 'receipts',
        ));
    }

    function doSpk($id = false, $data_local = false){
        $this->loadModel('Spk');
        $this->loadModel('Employe');
        $this->loadModel('Truck');

        if(!empty($this->request->data)){
            $data = $this->request->data;
            $data['Revenue']['date_sj'] = !empty($data['Revenue']['date_sj']) ? date('Y-m-d', strtotime($data['Revenue']['date_sj'])) : '';
            $data['Revenue']['date_revenue'] = $this->MkCommon->getDate($data['Revenue']['date_revenue']);
            $data['Revenue']['ppn'] = !empty($data['Revenue']['ppn'])?$data['Revenue']['ppn']:0;
            $data['Revenue']['pph'] = !empty($data['Revenue']['pph'])?$data['Revenue']['pph']:0;
            $tarif_angkutan_types = !empty($data['RevenueDetail']['tarif_angkutan_type'])?$data['RevenueDetail']['tarif_angkutan_type']:array();
            $dataRevenues = array();
            $flagSave = array();
            $dataTtuj = array();
            $checkQty = true;

            if( !empty($tarif_angkutan_types) ) {
                $tarif_angkutan_types = array_unique($tarif_angkutan_types);

                foreach ($tarif_angkutan_types as $key => $tarif_angkutan_type) {
                    $dataRevenue = $data;
                    $dataRevenue['Revenue']['type'] = $tarif_angkutan_type;
                    $dataRevenuDetail = array();

                    // if( !empty($i) ) {
                    //     $dataRevenue['Revenue']['no_doc'] .= '/'.chr(64+$i);
                    // }

                    if( !empty($dataRevenue['RevenueDetail']['tarif_angkutan_type']) ) {
                        $idx = 0;

                        foreach ($dataRevenue['RevenueDetail']['tarif_angkutan_type'] as $keyDetail => $revenueDetail) {
                            $tarifType = isset($data['RevenueDetail']['tarif_angkutan_type'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_type'][$keyDetail]:false;

                            if( $tarifType == $tarif_angkutan_type ) {
                                $dataRevenuDetail['RevenueDetail']['city_id'][$idx] = isset($data['RevenueDetail']['city_id'][$keyDetail])?$data['RevenueDetail']['city_id'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['tarif_angkutan_id'][$idx] = isset($data['RevenueDetail']['tarif_angkutan_id'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_id'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['tarif_angkutan_type'][$idx] = isset($data['RevenueDetail']['tarif_angkutan_type'][$keyDetail])?$data['RevenueDetail']['tarif_angkutan_type'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['no_do'][$idx] = isset($data['RevenueDetail']['no_do'][$keyDetail])?$data['RevenueDetail']['no_do'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['no_sj'][$idx] = isset($data['RevenueDetail']['no_sj'][$keyDetail])?$data['RevenueDetail']['no_sj'][$keyDetail]:false;
                                // $dataRevenuDetail['RevenueDetail']['note'][$idx] = isset($data['RevenueDetail']['note'][$keyDetail])?$data['RevenueDetail']['note'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['group_motor_id'][$idx] = isset($data['RevenueDetail']['group_motor_id'][$keyDetail])?$data['RevenueDetail']['group_motor_id'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['qty_unit'][$idx] = isset($data['RevenueDetail']['qty_unit'][$keyDetail])?$data['RevenueDetail']['qty_unit'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['payment_type'][$idx] = isset($data['RevenueDetail']['payment_type'][$keyDetail])?$data['RevenueDetail']['payment_type'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['is_charge'][$idx] = isset($data['RevenueDetail']['is_charge'][$keyDetail])?$data['RevenueDetail']['is_charge'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['price_unit'][$idx] = isset($data['RevenueDetail']['price_unit'][$keyDetail])?$data['RevenueDetail']['price_unit'][$keyDetail]:false;
                                $dataRevenuDetail['RevenueDetail']['total_price_unit'][$idx] = isset($data['RevenueDetail']['total_price_unit'][$keyDetail])?$data['RevenueDetail']['total_price_unit'][$keyDetail]:false;
                                $idx++;
                            }
                        }
                    }

                    unset($dataRevenue['RevenueDetail']);
                    $dataRevenue['RevenueDetail'] = !empty($dataRevenuDetail['RevenueDetail'])?$dataRevenuDetail['RevenueDetail']:false;
                    $dataRevenues[$key] = $dataRevenue;
                    $i++;
                }
            }
            
            if( !empty($dataRevenues) ) {
                if($id && $data_local){
                    $this->Revenue->id = $id;
                    $msg = 'merubah';
                }else{
                    $this->loadModel('Revenue');
                    $this->Revenue->create();
                    $msg = 'membuat';
                }

                foreach ($dataRevenues as $key => $dataRevenue) {
                    /*validasi revenue detail*/
                    $validate_detail = true;
                    $validate_qty = true;
                    $total_revenue = 0;
                    $total_qty = 0;
                    $array_ttuj_tipe_motor = array();

                    if( !empty($dataRevenue['Ttuj']) ) {
                        $tarif = $this->TarifAngkutan->findTarif($dataRevenue['Ttuj']['from_city_id'], $dataRevenue['Ttuj']['to_city_id'], $dataRevenue['Revenue']['customer_id'], $dataRevenue['Ttuj']['truck_capacity']);

                        if( !empty($tarif['jenis_unit']) && $tarif['jenis_unit'] == 'per_truck' ) {
                            $tarifTruck = $tarif;
                        }
                    }

                    if(!empty($dataRevenue['RevenueDetail'])){
                        foreach ($dataRevenue['RevenueDetail']['no_do'] as $keyDetail => $value) {
                            $tarif_angkutan_type = !empty($dataRevenue['RevenueDetail']['tarif_angkutan_type'][$keyDetail])?$dataRevenue['RevenueDetail']['tarif_angkutan_type'][$keyDetail]:'angkut';

                            if( $tarif_angkutan_type == $dataRevenue['Revenue']['type'] ) {
                                $data_detail['RevenueDetail'] = array(
                                    'no_do' => $value,
                                    'no_sj' => $dataRevenue['RevenueDetail']['no_sj'][$keyDetail],
                                    // 'note' => $dataRevenue['RevenueDetail']['note'][$keyDetail],
                                    'qty_unit' => !empty($dataRevenue['RevenueDetail']['qty_unit'][$keyDetail])?$dataRevenue['RevenueDetail']['qty_unit'][$keyDetail]:0,
                                    'price_unit' => !empty($dataRevenue['RevenueDetail']['price_unit'][$keyDetail])?$dataRevenue['RevenueDetail']['price_unit'][$keyDetail]:0,
                                    'total_price_unit' => !empty($dataRevenue['RevenueDetail']['total_price_unit'][$keyDetail])?$dataRevenue['RevenueDetail']['total_price_unit'][$keyDetail]:0,
                                    'city_id' => $dataRevenue['RevenueDetail']['city_id'][$keyDetail],
                                    'group_motor_id' => $dataRevenue['RevenueDetail']['group_motor_id'][$keyDetail],
                                    'tarif_angkutan_id' => $dataRevenue['RevenueDetail']['tarif_angkutan_id'][$keyDetail],
                                    'tarif_angkutan_type' => $tarif_angkutan_type,
                                    'payment_type' => $dataRevenue['RevenueDetail']['payment_type'][$keyDetail],
                                    'is_charge' => !empty($dataRevenue['RevenueDetail']['is_charge'][$keyDetail])?$dataRevenue['RevenueDetail']['is_charge'][$keyDetail]:0,
                                );

                                $this->Revenue->RevenueDetail->set($data_detail);
                                if( !$this->Revenue->RevenueDetail->validates() ){
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
                    }

                    $totalWithoutTax = $total_revenue;

                    if( !empty($dataRevenue['Revenue']['pph']) && $dataRevenue['Revenue']['pph'] > 0 ){
                        $pph = $total_revenue * ($dataRevenue['Revenue']['pph'] / 100);
                    }
                    if( !empty($dataRevenue['Revenue']['ppn']) && $dataRevenue['Revenue']['ppn'] > 0 ){
                        $ppn = $total_revenue * ($dataRevenue['Revenue']['ppn'] / 100);
                    }

                    if( !empty($dataRevenue['Revenue']['pph']) && $dataRevenue['Revenue']['pph'] > 0 ){
                        $total_revenue -= $pph;
                    }
                    if( !empty($dataRevenue['Revenue']['ppn']) && $dataRevenue['Revenue']['ppn'] > 0 ){
                        $total_revenue += $ppn;
                    }

                    $dataRevenue['Revenue']['total'] = $total_revenue;
                    $dataRevenue['Revenue']['total_without_tax'] = $totalWithoutTax;
                    $dataRevenues[$key] = $dataRevenue;
                    /*end validasi revenue detail*/

                    $this->Revenue->set($dataRevenues);
                    $validate_qty = true;
                    $qtyReview = $this->Revenue->checkQtyUsed( $dataRevenue['Revenue']['ttuj_id'], $id );
                    $qtyTtuj = !empty($qtyReview['qtyTtuj'])?$qtyReview['qtyTtuj']:0;
                    $qtyUse = !empty($qtyReview['qtyUsed'])?$qtyReview['qtyUsed']:0;
                    $qtyUse += $total_qty;

                    if( $qtyUse > $qtyTtuj ) {
                        $validate_qty = false;
                    }

                    if( $this->Revenue->validates($dataRevenue) && $validate_detail && $validate_qty ){
                        if( $dataRevenue['Revenue']['type'] == 'angkut' ) {
                            if( $qtyUse >= $qtyTtuj ) {
                                $dataTtuj['Ttuj']['is_revenue'] = 1;
                            } else {
                                $dataTtuj['Ttuj']['is_revenue'] = 0;
                            }
                        }
                    }else{
                        $checkQty = false;
                        $text = sprintf(__('Gagal %s Revenue'), $msg);
                        if(!$validate_detail){
                            $text .= ', mohon lengkapi field-field yang kosong';
                        }
                        if(!$validate_qty){
                            $text .= ', jumlah muatan melebihi jumlah maksimum TTUJ';
                        }
                        $this->MkCommon->setCustomFlash($text, 'error');
                        break;
                    }
                }

                if( $checkQty ) {
                    foreach ($dataRevenues as $key => $dataRevenue) {
                        if($id && $data_local){
                            $this->Revenue->id = $id;
                            $msg = 'merubah';
                        }else{
                            $this->loadModel('Revenue');
                            $this->Revenue->create();
                            $msg = 'membuat';
                        }

                        if($this->Revenue->save($dataRevenue)){
                            $revenue_id = $this->Revenue->id;

                            if( $dataRevenue['Revenue']['type'] == 'angkut' ) {
                                $no_ref = $revenue_id;
                            }

                            if($id && $data_local){
                                $this->Revenue->RevenueDetail->deleteAll(array(
                                    'revenue_id' => $revenue_id
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

                            $getLastReference = intval($this->Revenue->RevenueDetail->getLastReference())+1;

                            foreach ($dataRevenue['RevenueDetail']['no_do'] as $key => $value) {
                                $this->Revenue->RevenueDetail->create();
                                $data_detail['RevenueDetail'] = array(
                                    'no_do' => $value,
                                    'no_sj' => $dataRevenue['RevenueDetail']['no_sj'][$key],
                                    // 'note' => $dataRevenue['RevenueDetail']['note'][$key],
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
                                );

                                $this->Revenue->RevenueDetail->set($data_detail);
                                $this->Revenue->RevenueDetail->save();
                            }

                            if( $dataRevenue['Revenue']['type'] == 'angkut' ) {
                                if( !empty($dataTtuj) ) {
                                    $this->Ttuj->id = $dataRevenue['Revenue']['ttuj_id'];
                                    $this->Ttuj->save($dataTtuj);
                                }

                                if( !empty($data_local) && $data_local['Ttuj']['id'] <> $dataRevenue['Revenue']['ttuj_id'] ) {
                                    $this->Ttuj->set('is_revenue', 0);
                                    $this->Ttuj->id = $data_local['Ttuj']['id'];
                                    $this->Ttuj->save();
                                }
                            }
                            $flagSave[] = true;
                        }else{
                            $this->MkCommon->setCustomFlash(sprintf(__('Gagal %s Revenue'), $msg), 'error'); 
                            $this->Log->logActivity( sprintf(__('Gagal %s Revenue'), $msg), $this->user_data, $this->RequestHandler, $this->params, 1, false, $id ); 
                        }
                    }

                    if( count($flagSave) == count($dataRevenues) ) {
                        if( empty($id) ) {
                            $msgAlert = sprintf(__('Sukses %s Revenue! No Ref: %s'), $msg, str_pad($no_ref, 5, '0', STR_PAD_LEFT));
                        } else {
                            $msgAlert = sprintf(__('Sukses %s Revenue!'), $msg);
                        }

                        $this->MkCommon->setCustomFlash($msgAlert, 'success');
                        $this->Log->logActivity( sprintf(__('Sukses %s Revenue #%s'), $msg, $no_ref), $this->user_data, $this->RequestHandler, $this->params, 0, false, $id );
                        $this->redirect(array(
                            'controller' => 'revenues',
                            'action' => 'index'
                        ));
                    }
                }
            } else {
                $this->MkCommon->setCustomFlash(__('Gagal menyimpan Revenue'), 'error');
            }
        }else if($id && $data_local){
            $this->request->data = $data_local;
        }

        $employes = $this->Employe->getData('list', array(
            'fields' => array(
                'Employe.id', 'Employe.full_name'
            ),
        ));
        $trucks = $this->Truck->getData('list', array(
            'fields' => array(
                'Truck.id', 'Truck.nopol'
            ),
        ));

        $this->set(compact(
            'employes', 'trucks', 'id', 'data_local'
        ));
        $this->set('active_menu', 'internal');
        $this->render('internal_form');
    }
}