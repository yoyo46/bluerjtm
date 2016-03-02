<?php
        $full_name = $this->Common->filterEmptyField($value, 'Employe', 'full_name');

        if(!empty($invDetails)){
            $idx = 1;
            $totalPrice = 0;

            foreach ($invDetails as $key => $value) {
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $category = $this->Common->filterEmptyField($value, 'TruckCategory', 'name');

                $date = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');

                $unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'qty_unit');
                $rate = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit');
                $no_do = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_do', '&nbsp;');
                $no_sj = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_sj', '&nbsp;');

                $price = $rate * $unit;
                $customDate = $this->Common->formatDate($date, 'd-M-y');
                $customPrice = $this->Common->getFormatPrice($price, false);
                $customRate = $this->Common->getFormatPrice($rate, false);

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
            echo $this->Html->tag('td', $customPrice, array(
                'style' => 'text-align:right;border-left: 1px solid #000;'
            ));
            echo $this->Html->tag('td', $to_city_name, array(
                'style' => 'text-align:center;border-left: 1px solid #000;border-right: 1px solid #000;'
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