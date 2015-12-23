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
                'label' => __('Tipe Tarif'),
            ),
            array(
                'label' => __('Dari'),
            ),
            array(
                'label' => __('Tujuan'),
            ),
            array(
                'label' => __('Kode Customer'),
            ),
            array(
                'label' => __('Jenis Tarif'),
            ),
            array(
                'label' => __('Group Motor'),
            ),
            array(
                'label' => __('Kapasitas'),
            ),
            array(
                'label' => __('Tarif angkutan'),
            ),
        );

        $this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);
        $this->PhpExcel->addTableHeader($dataColumns, array('name' => 'Cambria', 'bold' => true));

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'TarifAngkutan', 'id', '');
                $title = $this->Common->filterEmptyField($value, 'TarifAngkutan', 'name_tarif', '');
                $type = $this->Common->filterEmptyField($value, 'TarifAngkutan', 'type', '');
                $jenis_unit = $this->Common->filterEmptyField($value, 'TarifAngkutan', 'jenis_unit', '');
                $capacity = $this->Common->filterEmptyField($value, 'TarifAngkutan', 'capacity', '');
                $tarif = $this->Common->filterEmptyField($value, 'TarifAngkutan', 'tarif', '');

                $customer = $this->Common->filterEmptyField($value, 'Customer', 'code', '');
                $groupMotor = $this->Common->filterEmptyField($value, 'GroupMotor', 'name', '');

                $branch = $this->Common->filterEmptyField($value, 'Branch', 'code', '');
                $from = $this->Common->filterEmptyField($value, 'FromCity', 'name', '');
                $to = $this->Common->filterEmptyField($value, 'ToCity', 'name', '');
                $customType = ucwords($type);

                $dataContent = array(
                    $id,
                    $branch,
                    $title,
                    $customType,
                    $from,
                    $to,
                    $customer,
                    $jenis_unit,
                    $groupMotor,
                    $capacity,
                    $tarif,
                );

                $this->PhpExcel->addTableRow($dataContent);
            }

            $this->PhpExcel->addTableFooter()->output($module_title.'.xls', 'Excel5');
        }
?>