<?php
class Asset extends AppModel {
	var $name = 'Asset';
	var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nama asset harap diisi'
            ),
        ),
        'asset_group_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Group Asset harap dipilih'
            ),
        ),
        'purchase_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal pembelian harap dipilih'
            ),
        ),
        'neraca_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Tanggal neraca harap dipilih'
            ),
        ),
        'nilai_perolehan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nilai perolehan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Nilai perolehan harus berupa angka',
            ),
        ),
        'depr_bulan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Depr/bulan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Depr/bulan harus berupa angka',
            ),
        ),
        'ak_penyusutan' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Ak. Penyusutan harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Ak. Penyusutan harus berupa angka',
            ),
        ),
        'nilai_buku' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Nilai Buku harap diisi'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Nilai Buku harus berupa angka',
            ),
        ),
        'status_document' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Status harap dipilih'
            ),
        ),
	);

    var $belongsTo = array(
        'AssetGroup' => array(
            'className' => 'AssetGroup',
            'foreignKey' => 'asset_group_id',
        ),
        'Truck' => array(
            'className' => 'Truck',
            'foreignKey' => 'truck_id',
        ),
    );

    public function getData( $find = 'all', $options = array(), $elements = array()  ) {
        $status = isset($elements['status']) ? $elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
                'Asset.created' => 'DESC',
                'Asset.id' => 'DESC',
            ),
        );

        switch ($status) {
            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                    'Asset.status' => 0,
                ));
                break;
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                    'Asset.status' => 1,
                ));
                break;
        }

        if( !empty($options) ) {
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
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getMerge($data, $id){
        if(empty($data['Asset'])){
            $data_merge = $this->getData('first', array(
                'conditions' => array(
                    'Asset.id' => $id
                )
            ), array(
                'status' => 'all',
            ));

            if(!empty($data_merge)){
                $data = array_merge($data, $data_merge);
            }
        }

        return $data;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $dateFrom = !empty($data['named']['DateFrom'])?$data['named']['DateFrom']:false;
        $dateTo = !empty($data['named']['DateTo'])?$data['named']['DateTo']:false;
        $dateFromRange = !empty($data['named']['DateFromRange'])?$data['named']['DateFromRange']:false;
        $dateToRange = !empty($data['named']['DateToRange'])?$data['named']['DateToRange']:false;
        $asset_group_id = !empty($data['named']['asset_group_id'])?$data['named']['asset_group_id']:false;
        $name = !empty($data['named']['name'])?$data['named']['name']:false;
        $noref = !empty($data['named']['noref'])?$data['named']['noref']:false;

        if( !empty($dateFrom) || !empty($dateTo) ) {
            if( !empty($dateFrom) ) {
                $default_options['conditions']['DATE_FORMAT(Asset.neraca_date, \'%Y-%m-%d\') >='] = $dateFrom;
            }

            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT(Asset.neraca_date, \'%Y-%m-%d\') <='] = $dateTo;
            }
        }
        if( !empty($dateFromRange) || !empty($dateToRange) ) {
            if( !empty($dateFromRange) ) {
                $default_options['conditions']['DATE_FORMAT(Asset.purchase_date, \'%Y-%m-%d\') >='] = $dateFromRange;
            }

            if( !empty($dateToRange) ) {
                $default_options['conditions']['DATE_FORMAT(Asset.purchase_date, \'%Y-%m-%d\') <='] = $dateToRange;
            }
        }
        if( !empty($asset_group_id) ) {
            $default_options['conditions']['Asset.asset_group_id'] = $asset_group_id;
        }
        if( !empty($name) ) {
            $default_options['conditions']['Asset.name LIKE'] = '%'.$name.'%';
        }
        if(!empty($noref)){
            $default_options['conditions']['LPAD(Asset.id, 6, 0) LIKE'] = '%'.$noref.'%';
        }
        
        return $default_options;
    }

    function doSave ( $data, $value = false, $id = false ) {
        $msg = __('Gagal menyimpan asset');

        if( !empty($data) ) {
            $flag = $this->saveAll($data);
            
            if( !empty($flag) ) {
                $msg = __('Berhasil menyimpan asset');
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                    ),
                );
            } else {
                $result = array(
                    'msg' => $msg,
                    'data' => $data,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                        'model' => array(
                        	'Asset',
                    	),
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result['data'] = $value;
        }

        return $result;
    }

    function doDelete( $id ) {
        $result = false;
        $value = $this->getData('first', array(
            'conditions' => array(
                'Asset.id' => $id,
            ),
        ));

        if ( !empty($value) ) {
            $name = !empty($value['Asset']['name'])?$value['Asset']['name']:false;
            $default_msg = sprintf(__('menghapus asset #%s'), $name);

            $this->id = $id;
            $this->set('status', 0);

            if( $this->save() ) {
                $msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                    ),
                );
            } else {
                $msg = sprintf(__('Gagal %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $value,
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result = array(
                'msg' => __('Gagal menghapus asset. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    public function getDataList($data) {
        if( !empty($data) ) {
            if( !empty($data['Advice']) ) {
            	$asset_group_id = !empty($data['Asset']['asset_group_id'])?$data['Asset']['asset_group_id']:false;
                $data = $this->AssetGroup->getMerge( $data, $asset_group_id );
            } else {
                foreach ($data as $key => $value) {
            		$asset_group_id = !empty($value['Asset']['asset_group_id'])?$value['Asset']['asset_group_id']:false;

                	$value = $this->AssetGroup->getMerge( $value, $asset_group_id );
                	$data[$key] = $value;
                }
            }
        }

        return $data;
    }
}
?>