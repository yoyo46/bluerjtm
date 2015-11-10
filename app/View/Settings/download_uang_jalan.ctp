<?php 
        $dataColumns = array(
            array(
                'label' => __('ID'),
            ),
            array(
                'label' => __('Kode Cabang'),
            ),
            array(
                'label' => __('Nama'),
            ),
            array(
                'label' => __('Dari'),
            ),
            array(
                'label' => __('Tujuan'),
            ),
            array(
                'label' => __('Kapasitas'),
            ),
            array(
                'label' => __('Jarak Tempuh'),
            ),
            array(
                'label' => __('Lead Time Sampai Tujuan'),
            ),
            array(
                'label' => __('Lead Time Ke Pool'),
            ),
            array(
                'label' => __('Klasifikasi 1'),
            ),
            array(
                'label' => __('Klasifikasi 2'),
            ),
            array(
                'label' => __('Klasifikasi 3'),
            ),
            array(
                'label' => __('Klasifikasi 4'),
            ),
            array(
                'label' => __('Uang Jalan Pertama'),
            ),
            array(
                'label' => __('Uang Jalan Per Unit ?'),
            ),
        );

        for ($i=1; $i <= $UangJalanTipeMotorCnt; $i++) {
            $dataColumns = array_merge($dataColumns, array(
                array(
                    'label' => __('Group Motor Uang Jalan ').$i,
                ),
                array(
                    'label' => __('Biaya Uang Jalan Per Group ').$i,
                ),
            ));
        }

        $dataColumns = array_merge($dataColumns, array(
            array(
                'label' => __('Uang Jalan Kedua'),
            ),
            array(
                'label' => __('Uang Jalan Extra'),
            ),
            array(
                'label' => __('Uang Jalan Extra Per Unit ?'),
            ),
            array(
                'label' => __('Min Kapasitas Ujalan Extra'),
            ),
            array(
                'label' => __('Komisi'),
            ),
            array(
                'label' => __('Komisi Per Unit ?'),
            ),
        ));

        for ($i=1; $i <= $CommissionGroupMotorCnt; $i++) {
            $dataColumns = array_merge($dataColumns, array(
                array(
                    'label' => __('Group Motor Komisi ').$i,
                ),
                array(
                    'label' => __('Biaya Komisi Per Group ').$i,
                ),
            ));
        }

        $dataColumns = array_merge($dataColumns, array(
            array(
                'label' => __('Komisi Extra'),
            ),
            array(
                'label' => __('Min Kapasitas Komisi Extra'),
            ),
            array(
                'label' => __('Komisi Extra Per Unit ?'),
            ),
            array(
                'label' => __('Uang Penyebrangan'),
            ),
            array(
                'label' => __('Uang Penyebrangan Per Unit ?'),
            ),
        ));

        for ($i=1; $i <= $AsdpGroupMotorCnt; $i++) {
            $dataColumns = array_merge($dataColumns, array(
                array(
                    'label' => __('Group Motor Uang Penyebrangan ').$i,
                ),
                array(
                    'label' => __('Biaya Uang Penyebrangan Per Group ').$i,
                ),
            ));
        }

        $dataColumns = array_merge($dataColumns, array(
            array(
                'label' => __('Uang Kawal'),
            ),
            array(
                'label' => __('Uang Kawal Per Unit ?'),
            ),
        ));

        for ($i=1; $i <= $UangKawalGroupMotorCnt; $i++) {
            $dataColumns = array_merge($dataColumns, array(
                array(
                    'label' => __('Group Motor Uang Kawal ').$i,
                ),
                array(
                    'label' => __('Biaya Uang Kawal Per Group ').$i,
                ),
            ));
        }

        $dataColumns = array_merge($dataColumns, array(
            array(
                'label' => __('Uang Keamanan'),
            ),
            array(
                'label' => __('Uang Keamanan Per Unit ?'),
            ),
        ));

        for ($i=1; $i <= $UangKeamananGroupMotorCnt; $i++) {
            $dataColumns = array_merge($dataColumns, array(
                array(
                    'label' => __('Group Motor Uang Keamanan ').$i,
                ),
                array(
                    'label' => __('Biaya Uang Keamanan Per Group ').$i,
                ),
            ));
        }

        $this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);
        $this->PhpExcel->addTableHeader($dataColumns, array('name' => 'Cambria', 'bold' => true));

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'UangJalan', 'id', '');
                $title = $this->Common->filterEmptyField($value, 'UangJalan', 'title', '');
                $capacity = $this->Common->filterEmptyField($value, 'UangJalan', 'capacity', '');
                $distance = $this->Common->filterEmptyField($value, 'UangJalan', 'distance', '');
                $uang_jalan_1 = $this->Common->filterEmptyField($value, 'UangJalan', 'uang_jalan_1', '');
                $uang_jalan_2 = $this->Common->filterEmptyField($value, 'UangJalan', 'uang_jalan_2', '');
                $uang_jalan_per_unit = $this->Common->filterEmptyField($value, 'UangJalan', 'uang_jalan_per_unit', '');

                $uang_jalan_extra = $this->Common->filterEmptyField($value, 'UangJalan', 'uang_jalan_extra', '');
                $uang_jalan_extra_per_unit = $this->Common->filterEmptyField($value, 'UangJalan', 'uang_jalan_extra_per_unit', '');
                $min_capacity = $this->Common->filterEmptyField($value, 'UangJalan', 'min_capacity', '');

                $commission = $this->Common->filterEmptyField($value, 'UangJalan', 'commission', '');
                $commission_per_unit = $this->Common->filterEmptyField($value, 'UangJalan', 'commission_per_unit', '');

                $commission_extra = $this->Common->filterEmptyField($value, 'UangJalan', 'commission_extra', '');
                $commission_extra_per_unit = $this->Common->filterEmptyField($value, 'UangJalan', 'commission_extra_per_unit', '');
                $commission_min_qty = $this->Common->filterEmptyField($value, 'UangJalan', 'commission_min_qty', '');

                $asdp = $this->Common->filterEmptyField($value, 'UangJalan', 'asdp', '');
                $asdp_per_unit = $this->Common->filterEmptyField($value, 'UangJalan', 'asdp_per_unit', '');

                $uang_kawal = $this->Common->filterEmptyField($value, 'UangJalan', 'uang_kawal', '');
                $uang_kawal_per_unit = $this->Common->filterEmptyField($value, 'UangJalan', 'uang_kawal_per_unit', '');

                $uang_keamanan = $this->Common->filterEmptyField($value, 'UangJalan', 'uang_keamanan', '');
                $uang_keamanan_per_unit = $this->Common->filterEmptyField($value, 'UangJalan', 'uang_keamanan_per_unit', '');

                $arrive_lead_time = $this->Common->filterEmptyField($value, 'UangJalan', 'arrive_lead_time', '');
                $back_lead_time = $this->Common->filterEmptyField($value, 'UangJalan', 'back_lead_time', '');

                $group_classification_1_id = $this->Common->filterEmptyField($value, 'UangJalan', 'group_classification_1_id', '');
                $group_classification_2_id = $this->Common->filterEmptyField($value, 'UangJalan', 'group_classification_2_id', '');
                $group_classification_3_id = $this->Common->filterEmptyField($value, 'UangJalan', 'group_classification_3_id', '');
                $group_classification_4_id = $this->Common->filterEmptyField($value, 'UangJalan', 'group_classification_4_id', '');

                $classifications1 = $this->Common->filterEmptyField($groupClassifications, $group_classification_1_id, false, '');
                $classifications2 = $this->Common->filterEmptyField($groupClassifications, $group_classification_2_id, false, '');
                $classifications3 = $this->Common->filterEmptyField($groupClassifications, $group_classification_3_id, false, '');
                $classifications4 = $this->Common->filterEmptyField($groupClassifications, $group_classification_4_id, false, '');

                $branch = $this->Common->filterEmptyField($value, 'Branch', 'code', '');
                $from = $this->Common->filterEmptyField($value, 'FromCity', 'name', '');
                $to = $this->Common->filterEmptyField($value, 'ToCity', 'name', '');

                $dataContent = array(
                    $id,
                    $branch,
                    $title,
                    $from,
                    $to,
                    $capacity,
                    $distance,
                    $arrive_lead_time,
                    $back_lead_time,
                    $classifications1,
                    $classifications2,
                    $classifications3,
                    $classifications4,
                    $uang_jalan_1,
                    $uang_jalan_per_unit,
                );

                $dataContent = $this->Common->groupMotorTable($dataContent, array(
                    'cnt' => $UangJalanTipeMotorCnt,
                    'data' => $this->Common->filterEmptyField($value, 'UangJalanTipeMotor'),
                    'modelName' => 'UangJalanTipeMotor',
                    'fieldName' => 'uang_jalan_1',
                ));

                $dataContent = array_merge($dataContent, array(
                    $uang_jalan_2,
                    $uang_jalan_extra,
                    $uang_jalan_extra_per_unit,
                    $min_capacity,
                    $commission,
                    $commission_per_unit,
                ));

                $dataContent = $this->Common->groupMotorTable($dataContent, array(
                    'cnt' => $CommissionGroupMotorCnt,
                    'data' => $this->Common->filterEmptyField($value, 'CommissionGroupMotor'),
                    'modelName' => 'CommissionGroupMotor',
                    'fieldName' => 'commission',
                ));

                $dataContent = array_merge($dataContent, array(
                    $commission_extra,
                    $commission_min_qty,
                    $commission_extra_per_unit,
                    $asdp,
                    $asdp_per_unit,
                ));

                $dataContent = $this->Common->groupMotorTable($dataContent, array(
                    'cnt' => $AsdpGroupMotorCnt,
                    'data' => $this->Common->filterEmptyField($value, 'AsdpGroupMotor'),
                    'modelName' => 'AsdpGroupMotor',
                    'fieldName' => 'asdp',
                ));

                $dataContent = array_merge($dataContent, array(
                    $uang_kawal,
                    $uang_kawal_per_unit,
                ));

                $dataContent = $this->Common->groupMotorTable($dataContent, array(
                    'cnt' => $UangKawalGroupMotorCnt,
                    'data' => $this->Common->filterEmptyField($value, 'UangKawalGroupMotor'),
                    'modelName' => 'UangKawalGroupMotor',
                    'fieldName' => 'uang_kawal',
                ));

                $dataContent = array_merge($dataContent, array(
                    $uang_keamanan,
                    $uang_keamanan_per_unit,
                ));

                $dataContent = $this->Common->groupMotorTable($dataContent, array(
                    'cnt' => $UangKeamananGroupMotorCnt,
                    'data' => $this->Common->filterEmptyField($value, 'UangKeamananGroupMotor'),
                    'modelName' => 'UangKeamananGroupMotor',
                    'fieldName' => 'uang_keamanan',
                ));

                $this->PhpExcel->addTableRow($dataContent);
            }

            $this->PhpExcel->addTableFooter()->output($module_title.'.xls', 'Excel5');
        }
?>