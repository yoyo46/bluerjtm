<?php
        if(!empty($datas)){
            $grandtotalQty = 0;
            $grandtotalPrice = 0;
            $grandtotal = 0;

            if( $type=='ksu' ) {
                $colspan = 12;
            } else {
                $colspan = 11;
            }

            foreach ($datas as $key => $value) {
                $id = $this->Common->filterEmptyField($value, $modelName, 'id');
                $no_doc = $this->Common->filterEmptyField($value, $modelName, 'no_doc');
                $tgl = $this->Common->filterEmptyField($value, $modelName, 'tgl_'.$type);
                $paid = $this->Common->filterEmptyField($value, $modelName, 'complete_paid');
                $completed = $this->Common->filterEmptyField($value, $modelName, 'completed');
                $kekurangan_atpm = $this->Common->filterEmptyField($value, $modelName, 'kekurangan_atpm');

                $detail = $this->Common->filterEmptyField($value, $modelName.'Detail');

                $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
                $capacity = $this->Common->filterEmptyField($value, 'Truck', 'capacity');
                $category = $this->Common->filterEmptyField($value, 'TruckCategory', 'name');

                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $driver = $this->Common->_callGetDriver($value);
                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $rowspan = count($detail);

                if( !empty($completed) || !empty($paid) ) {
                    $statusCompleted = __('Selesai');
                    $lblClassComplated = 'success';
                } else {
                    $statusCompleted = __('Belum');
                    $lblClassComplated = 'default';
                }

                if( !empty($kekurangan_atpm) ) {
                    $statusAtpm = __('Ya');
                    $lblClassAtpm = 'success';
                } else {
                    $statusAtpm = __('Tidak');
                    $lblClassAtpm = 'primary';
                }

                if( !empty($detail[0]) ) {
                    $firstDetail = $detail[0];

                    unset($detail[0]);
                } else {
                    $firstDetail = array();
                }

                $firstDetailBiaya = !empty($firstDetail[0])?$firstDetail[0]:false;

                if( $type == 'ksu' ) {
                    $detailName = $this->Common->filterEmptyField($firstDetail, 'Perlengkapan', 'name');
                } else {
                    $detailName = $this->Common->filterEmptyField($firstDetail, 'PartsMotor', 'name');
                }

                $qty = $this->Common->filterEmptyField($firstDetailBiaya, 'qty', 0);
                $price = $this->Common->filterEmptyField($firstDetailBiaya, 'price', 0);
                $total = $qty*$price;

                $grandtotalQty += $qty;
                $grandtotalPrice += $price;
                $grandtotal += $total;

                $customTgl = $this->Common->customDate($tgl, 'd M Y');
                $customPrice = $this->Common->getFormatPrice($price, '-');
                $customTotal = $this->Common->getFormatPrice($total, '-');

                $completed = $this->Html->tag('span', $statusCompleted, array(
                    'class' => sprintf('label label-%s', $lblClassComplated),
                ));
                $atpm = $this->Html->tag('span', $statusAtpm, array(
                    'class' => sprintf('label label-%s', $lblClassAtpm),
                ));
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $no_doc, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;',
            ));
            echo $this->Html->tag('td', $completed, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;',
            ));

            if( $type == 'ksu' ) {
                echo $this->Html->tag('td', $atpm, array(
                    'rowspan' => $rowspan,
                    'style' => 'vertical-align:top;text-align:center;',
                ));
            }

            echo $this->Html->tag('td', $customTgl, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;text-align:center;',
            ));
            echo $this->Html->tag('td', $no_ttuj, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;',
            ));
            echo $this->Html->tag('td', $nopol, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;',
            ));
            echo $this->Html->tag('td', $capacity, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;text-align:center;',
            ));
            echo $this->Html->tag('td', $category, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;text-align:center;',
            ));
            echo $this->Html->tag('td', $driver, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;',
            ));
            echo $this->Html->tag('td', $from_city_name, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;',
            ));
            echo $this->Html->tag('td', $to_city_name, array(
                'rowspan' => $rowspan,
                'style' => 'vertical-align:top;',
            ));
            echo $this->Html->tag('td', $detailName);
            echo $this->Html->tag('td', $qty, array(
                'style' => 'text-align:center;',
            ));
            echo $this->Html->tag('td', $customPrice, array(
                'style' => 'text-align:right;',
            ));
            echo $this->Html->tag('td', $customTotal, array(
                'style' => 'text-align:right;',
            ));
    ?>
</tr>
<?php
                if( $rowspan > 1 && !empty($detail) ) {
                    foreach ($detail as $key => $valueDetail) {
                        $valueDetailBiaya = !empty($valueDetail[0])?$valueDetail[0]:false;

                        if( $type == 'ksu' ) {
                            $detailName = $this->Common->filterEmptyField($valueDetail, 'Perlengkapan', 'name');
                        } else {
                            $detailName = $this->Common->filterEmptyField($valueDetail, 'PartsMotor', 'name');
                        }

                        $qty = $this->Common->filterEmptyField($valueDetailBiaya, 'qty', 0);
                        $price = $this->Common->filterEmptyField($valueDetailBiaya, 'price', 0);
                        $total = $qty*$price;

                        $grandtotalQty += $qty;
                        $grandtotalPrice += $price;
                        $grandtotal += $total;

                        $customPrice = $this->Common->getFormatPrice($price, '-');
                        $customTotal = $this->Common->getFormatPrice($total, '-');

                        $contentTr = $this->Html->tag('td', $detailName);
                        $contentTr .= $this->Html->tag('td', $qty, array(
                            'style' => 'text-align:center;',
                        ));
                        $contentTr .= $this->Html->tag('td', $customPrice, array(
                            'style' => 'text-align:right;',
                        ));
                        $contentTr .= $this->Html->tag('td', $customTotal, array(
                            'style' => 'text-align:right;',
                        ));

                        echo $this->Html->tag('tr', $contentTr);
                    }
                }
            }
?>

<tr style="font-weight:bold;">
    <?php 
            $customGrandtotalQty = $this->Common->getFormatPrice($grandtotalQty);
            $customGrandtotalPrice = $this->Common->getFormatPrice($grandtotalPrice);
            $customGrandtotal = $this->Common->getFormatPrice($grandtotal);

            echo $this->Html->tag('td', __('Total'), array(
                'colspan' => $colspan,
                'style' => 'text-align:right;',
            ));
            echo $this->Html->tag('td', $customGrandtotalQty, array(
                'style' => 'text-align:center;',
            ));
            echo $this->Html->tag('td', $customGrandtotalPrice, array(
                'style' => 'text-align:right;',
            ));
            echo $this->Html->tag('td', $customGrandtotal, array(
                'style' => 'text-align:right;',
            ));
    ?>
</tr>
<?php
        }
?>