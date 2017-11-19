<?php
        $full_name = $this->Common->filterEmptyField($value, 'Employe', 'full_name');

        if(!empty($invDetails)){
            $idx = 1;
            $totalPrice = 0;
            $temp = false;
            $old_detail_id = false;
            $recenueCnt = array();
            $recenueCharge = array();
            $recenueCol = array();

            foreach ($invDetails as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
                $is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
                $detail_id = $this->Common->filterEmptyField($value, 'RevenueDetail', 'id');
                $city_name = $this->Common->filterEmptyField($value, 'City', 'name');

                if( empty($is_charge) ) {
                    if( empty($recenueCharge[$id]) ) {
                        $recenueCol[$detail_id] = true;
                    } else if( !empty($recenueCharge[$id]) ) {
                        // $invDetails[$key]['City']['name'] = $recenueCharge[$id];
                    }

                    if( !empty($recenueCnt[$id][$old_detail_id]) ) {
                        $recenueCnt[$id][$old_detail_id]++;
                    } else if( !empty($recenueCharge[$id]) ) {
                        $recenueCnt[$id][$old_detail_id] = 1;
                    }
                } else {
                    $old_detail_id = $detail_id;
                    $recenueCharge[$id] = $city_name;
                }                       
            }

            foreach ($invDetails as $key => $value) {
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $to_city_name = $this->Common->filterEmptyField($value, 'City', 'name');
                $category = $this->Common->filterEmptyField($value, 'TruckCategory', 'name');

                $revenue_id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
                $date = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');
                $revenue_tarif_type = $this->Common->filterEmptyField($value, 'Revenue', 'revenue_tarif_type');

                $detail_id = $this->Common->filterEmptyField($value, 'RevenueDetail', 'id');
                $unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'qty_unit');
                $rate = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit');
                $no_do = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_do', '&nbsp;');
                $no_sj = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_sj', '&nbsp;');
                $is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
                $total_price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'total_price_unit');

                $customDate = $this->Common->formatDate($date, 'd-M-y');
                $totalPriceFormat = '';

                if( !empty($is_charge) ) {
                    $totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
                    $customRate = $this->Common->getFormatPrice($rate, false);
                } else {
                    $total_price_unit = 0;
                    $customRate = '';
                }

                if( !empty($value['Revenue']['nopol']) ) {
                    $nopol = $value['Revenue']['nopol'];
                } else if( !empty($value['Revenue']['Ttuj']['nopol']) ) {
                    $nopol = $value['Revenue']['Ttuj']['nopol'];
                } else if( !empty($value['Truck']['nopol']) ) {
                    $nopol = $value['Truck']['nopol'];
                }

                $totalPrice += $total_price_unit;
?>
<tr>
    <?php
            echo $this->Html->tag('td', $idx, array(
                'style' => 'text-align:center;border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $nopol, array(
                'style' => 'border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $category, array(
                'style' => 'text-align:center;border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $customDate, array(
                'style' => 'text-align:center;border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $unit, array(
                'style' => 'text-align:center;border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $customRate, array(
                'style' => 'text-align:center;border-left: 1px solid #000;'
            ));

            if( $revenue_tarif_type == 'per_truck' ){
                if( !empty($recenueCnt[$revenue_id][$detail_id]) ) {
                    echo $this->Html->tag('td', $totalPriceFormat, array(
                        'style' => 'text-align:right;border-left: 1px solid #000;vertical-align: middle;',
                        'rowspan' => $recenueCnt[$revenue_id][$detail_id] + 1,
                    ));
                } else if( !empty($is_charge) ) {
                    echo $this->Html->tag('td', $totalPriceFormat, array(
                        'style' => 'text-align:right;border-left: 1px solid #000;'
                    ));
                } else if( !empty($recenueCol[$detail_id]) ) {
                    echo $this->Html->tag('td', '', array(
                        'style' => 'text-align:right;border-left: 1px solid #000;'
                    ));
                }
            } else {
                echo $this->Html->tag('td', $totalPriceFormat, array(
                    'style' => 'text-align:right;border-left: 1px solid #000;'
                ));
            }

            echo $this->Html->tag('td', $to_city_name, array(
                'style' => 'text-align:center;border-left: 1px solid #000;border-right: 1px solid #000;'
            ));
    ?>
</tr>
<?php
            $idx++;
            $temp = $revenue_id;
        }
        
        $customTotalPrice = $this->Common->getFormatPrice($totalPrice, false);
        $terbilang = $this->Common->terbilang($totalPrice);
        $terbilang = strtolower($terbilang);
        $terbilang = ucfirst($terbilang);
?>
<tr>
    <?php
            echo $this->Html->tag('td', '&nbsp;', array(
                'colspan' => 5,
                'style' => 'text-align:center;border-top: 1px solid #000;'
            ));
            echo $this->Html->tag('td', __('Total'), array(
                'style' => 'text-align:center;border-left: none;border-top: 1px solid #000;border-bottom: none;'
            ));
            echo $this->Html->tag('td', $customTotalPrice, array(
                'style' => 'text-align:right;border-top: 1px solid #000;border-left: 1px solid #000;border-bottom: 1px solid #000;'
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'colspan' => 1,
                'style' => 'border-top: 1px solid #000;border-left: 1px solid #000;'
            ));
    ?>
</tr>
<?php

        } else {
            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                'class' => 'alert alert-warning text-center',
                'colspan' => 9,
            )));
        }
?>