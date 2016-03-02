<?php
        $full_name = $this->Common->filterEmptyField($value, 'Employe', 'full_name');

        if(!empty($invDetails)){
            $idx = 1;
            $totalUnit = 0;
            $totalPrice = 0;

            foreach ($invDetails as $key => $value) {
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $capacity = $this->Common->filterEmptyField($value, 'Ttuj', 'truck_capacity');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $group_motor = $this->Common->filterEmptyField($value, 'GroupMotor', 'name');

                $date = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');

                $unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'qty_unit');
                $rate = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit');
                $no_do = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_do', '&nbsp;');
                $no_sj = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_sj', '&nbsp;');

                $price = $rate * $unit;
                $customDate = $this->Common->formatDate($date, 'd-M-y');
                $customPrice = $this->Common->getFormatPrice($price, false);
                $customRate = $this->Common->getFormatPrice($rate, false);

                $totalUnit += $unit;
                $totalPrice += $price;
?>
<tr>
    <?php
            echo $this->Html->tag('td', $idx, array(
                'style' => 'text-align:center;border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $nopol, array(
                'style' => 'border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $capacity, array(
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
            echo $this->Html->tag('td', $customPrice, array(
                'style' => 'text-align:right;border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $to_city_name, array(
                'style' => 'text-align:center;border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', sprintf('(%s)', $group_motor), array(
                'style' => 'text-align:center;border-right: 1px solid #000;'
            ));
    ?>
</tr>
<?php
            $idx++;
        }
        
        $customTotalPrice = $this->Common->getFormatPrice($totalPrice, false);
        $terbilang = $this->Common->terbilang($totalPrice);
        $terbilang = strtolower($terbilang);
        $terbilang = ucfirst($terbilang);
?>
<tr>
    <?php
            echo $this->Html->tag('td', '&nbsp;', array(
                'colspan' => 4,
                'style' => 'text-align:center;border-top: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $totalUnit, array(
                'style' => 'text-align:center;border-left: 1px solid #000;border-top: 1px solid #000;border-bottom: 1px solid #000;'
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => 'border-top: 1px solid #000;border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $customTotalPrice, array(
                'style' => 'text-align:right;border-top: 1px solid #000;border-left: 1px solid #000;border-bottom: 1px solid #000;'
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'colspan' => 2,
                'style' => 'border-top: 1px solid #000;border-left: 1px solid #000;'
            ));
    ?>
</tr>
<tr>
    <?php
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', $this->Html->tag('div', sprintf(__('TERBILANG : %s'), $terbilang), array(
                'style' => 'margin-top: 20px;'
            )), array(
                'colspan' => 8,
                'style' => 'font-weight: 600;'
            ));
    ?>
</tr>
<tr>
    <td valign="top" align="right" style="padding-top: 50px;" colspan="9">
        <div style="width: 200px;text-align: center;margin: 20px 70px 0 0;">
            <?php 
                    echo $this->Html->tag('p', sprintf('%s, %s', $this->Common->getDataSetting( $setting, 'pusat' ), date('d F Y')), array(
                        'style' => 'text-align: center;font-weight: 600;',
                    ));
                    echo $this->Html->tag('p', $full_name, array(
                        'style' => 'margin: 70px 0 0;border-bottom: 1px solid #000;display: inline-block;text-align: center;font-weight: bold;',
                    ));
                    echo $this->Html->tag('p', __('Billing Officer'), array(
                        'style' => 'text-align: center;font-weight: 600;'
                    ));
            ?>
        </div>
    </td>
</tr>
<?php

        } else {
            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                'class' => 'alert alert-warning text-center',
                'colspan' => 9,
            )));
        }
?>