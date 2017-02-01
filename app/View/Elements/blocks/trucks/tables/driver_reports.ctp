<?php
        if(!empty($drivers)){
            foreach ($drivers as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Driver', 'id');
                $no_id = $this->Common->filterEmptyField($value, 'Driver', 'no_id');
                $name = $this->Common->filterEmptyField($value, 'Driver', 'driver_name');
                $identity_number = $this->Common->filterEmptyField($value, 'Driver', 'identity_number');
                $no_sim = $this->Common->filterEmptyField($value, 'Driver', 'no_sim');
                $expired_date_sim = $this->Common->filterEmptyField($value, 'Driver', 'expired_date_sim');
                $alias = $this->Common->filterEmptyField($value, 'Driver', 'alias');
                $address = $this->Common->filterEmptyField($value, 'Driver', 'address');
                $city = $this->Common->filterEmptyField($value, 'Driver', 'city');
                $provinsi = $this->Common->filterEmptyField($value, 'Driver', 'provinsi');
                $phone = $this->Common->filterEmptyField($value, 'Driver', 'phone');
                $no_hp = $this->Common->filterEmptyField($value, 'Driver', 'no_hp');
                $birth_date = $this->Common->filterEmptyField($value, 'Driver', 'birth_date');
                $tempat_lahir = $this->Common->filterEmptyField($value, 'Driver', 'tempat_lahir');
                $kontak_darurat_name = $this->Common->filterEmptyField($value, 'Driver', 'kontak_darurat_name');
                $kontak_darurat_phone = $this->Common->filterEmptyField($value, 'Driver', 'kontak_darurat_phone');
                $kontak_darurat_no_hp = $this->Common->filterEmptyField($value, 'Driver', 'kontak_darurat_no_hp');
                $join_date = $this->Common->filterEmptyField($value, 'Driver', 'join_date');
                $is_resign = $this->Common->filterEmptyField($value, 'Driver', 'is_resign');
                $date_resign = $this->Common->filterEmptyField($value, 'Driver', 'date_resign');
                $status = $this->Common->filterEmptyField($value, 'Driver', 'status');

                $relation = $this->Common->filterEmptyField($value, 'DriverRelation', 'name');
                $sim = $this->Common->filterEmptyField($value, 'JenisSim', 'name');
                $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
                $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');

                $customExpireDate = $this->Common->customDate($expired_date_sim, 'd/m/Y');
                $customBirthDate = $this->Common->customDate($birth_date, 'd/m/Y');
                $customJoinDate = $this->Common->customDate($join_date, 'd/m/Y');
                $customResignDate = $this->Common->customDate($date_resign, 'd/m/Y');

                if( !empty($is_resign) ) {
                    $lblStatus = $this->Html->tag('span', __('Resign'), array(
                        'class' => 'label label-warning',
                    )).'<br>'.$customResignDate;
                } else if( empty($status) ) {
                    $lblStatus = $this->Html->tag('span', __('Non-Aktif'), array(
                        'class' => 'label label-danger',
                    ));
                } else if( !empty($status) ) {
                    $lblStatus = $this->Html->tag('span', __('Aktif'), array(
                        'class' => 'label label-success',
                    ));
                } else {
                    $lblStatus = '-';
                }
                
                $content = $this->Common->_getDataColumn($branch, 'Branch', 'name', array(
                    'style' => 'text-align: left;',
                    'class' => 'branch',
                ));
                $content .= $this->Common->_getDataColumn($no_id, 'Driver', 'id', array(
                    'class' => 'nomor_id',
                    'style' => 'text-align: left;',
                ));
                $content .= $this->Common->_getDataColumn($nopol, 'Truck', 'nopol', array(
                    'style' => 'text-align: left;',
                    'class' => 'nopol',
                ));
                $content .= $this->Common->_getDataColumn($name, 'Driver', 'driver_name', array(
                    'style' => 'text-align: left;',
                    'class' => 'name',
                ));
                $content .= $this->Common->_getDataColumn($identity_number, 'Driver', 'identity_number', array(
                    'class' => 'identity_number',
                    'style' => 'display: none',
                ));
                $content .= $this->Common->_getDataColumn($address, 'Driver', 'address', array(
                    'class' => 'address',
                ));
                $content .= $this->Common->_getDataColumn($city, 'Driver', 'city', array(
                    'class' => 'city',
                ));
                $content .= $this->Common->_getDataColumn($provinsi, 'Driver', 'provinsi', array(
                    'class' => 'provinsi',
                    'style' => 'display: none',
                ));
                $content .= $this->Common->_getDataColumn($no_hp, 'Driver', 'no_hp', array(
                    'class' => 'no_hp',
                ));
                $content .= $this->Common->_getDataColumn($phone, 'Driver', 'phone', array(
                    'class' => 'phone',
                ));
                $content .= $this->Common->_getDataColumn($tempat_lahir, 'Driver', 'tempat_lahir', array(
                    'class' => 'tempat_lahir',
                    'style' => 'display: none',
                ));
                $content .= $this->Common->_getDataColumn($customBirthDate, 'Driver', 'birth_date', array(
                    'class' => 'birth_date',
                    'style' => 'display: none',
                ));
                $content .= $this->Common->_getDataColumn($sim, 'Driver', 'jenis_sim_id', array(
                    'class' => 'sim',
                    'style' => 'text-align:center;',
                ));
                $content .= $this->Common->_getDataColumn($no_sim, 'Driver', 'no_sim', array(
                    'class' => 'no_sim',
                    'style' => 'display: none',
                ));
                $content .= $this->Common->_getDataColumn($customExpireDate, 'Driver', 'expired_date_sim', array(
                    'class' => 'no_sim',
                    'style' => 'display: none',
                ));
                $content .= $this->Common->_getDataColumn($kontak_darurat_name, 'Driver', 'kontak_darurat_name', array(
                    'class' => 'kontak_darurat_name',
                ));
                $content .= $this->Common->_getDataColumn($kontak_darurat_no_hp, 'Driver', 'kontak_darurat_no_hp', array(
                    'class' => 'kontak_darurat_no_hp',
                ));
                $content .= $this->Common->_getDataColumn($kontak_darurat_phone, 'Driver', 'kontak_darurat_phone', array(
                    'class' => 'kontak_darurat_phone',
                    'style' => 'display: none',
                ));
                $content .= $this->Common->_getDataColumn($relation, 'Driver', 'driver_relation_id', array(
                    'class' => 'relation',
                    'style' => 'display: none',
                ));
                $content .= $this->Common->_getDataColumn($customJoinDate, 'Driver', 'join_date', array(
                    'class' => 'join_date',
                    'style' => 'display: none',
                ));
                $content .= $this->Common->_getDataColumn($lblStatus, 'Driver', 'status', array(
                    'class' => 'status',
                ));

                echo $this->Html->tag('tr', $content);
            }
        }
?>