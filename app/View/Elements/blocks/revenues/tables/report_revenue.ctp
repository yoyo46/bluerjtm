<?php
        if(!empty($values)){
            $totalUnit = 0;
            $totalInvoice = 0;

            foreach ($values as $key => $value) {
                $date = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');
                $unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'qty_unit');
                $price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit');
                $is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
                $total_price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'total_price_unit');

                $customer = $this->Common->filterEmptyField($value, 'Customer', 'code');
                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
                $from_city_name = $this->Common->filterEmptyField($value, 'FromCity', 'name');
                $to_city_name = $this->Common->filterEmptyField($value, 'City', 'name');
                $no_invoice = $this->Common->filterEmptyField($value, 'Invoice', 'no_invoice');
                $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');

                $status = $this->Revenue->_callStatus($value, 'Ttuj', 'nodoc');
                $customDate = $this->Common->formatDate($date, 'd/m/Y');
                $totalPriceFormat = '';

                if( !empty($is_charge) ) {
                    $totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
                    $customPrice = $this->Common->getFormatPrice($price_unit, false);
                } else {
                    $total_price_unit = 0;
                    $customPrice = '';
                }

                $totalUnit += $unit;
                $totalInvoice += $total_price_unit;
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $customDate, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $branch);
            echo $this->Html->tag('td', $customer);
            echo $this->Html->tag('td', $no_ttuj);
            echo $this->Html->tag('td', $nopol);
            echo $this->Html->tag('td', $from_city_name);
            echo $this->Html->tag('td', $to_city_name);
            echo $this->Html->tag('td', $unit, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $customPrice, array(
                'style' => 'text-align: right;'
            ));
            echo $this->Html->tag('td', $totalPriceFormat, array(
                'style' => 'text-align: right;'
            ));
            echo $this->Html->tag('td', $no_invoice);
            echo $this->Html->tag('td', $status);
    ?>
</tr>
<?php
            }

            $totalUnit = $this->Common->getFormatPrice($totalUnit);
            $totalInvoice = $this->Common->getFormatPrice($totalInvoice);
?>
<tr style="font-weight: bold;">
    <?php 
            echo $this->Html->tag('td', __('Total'), array(
                'colspan' => 7,
                'style' => 'text-align: right;'
            ));
            echo $this->Html->tag('td', $totalUnit, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $totalInvoice, array(
                'style' => 'text-align: right;'
            ));
            echo $this->Html->tag('td', '', array(
                'colspan' => 2,
            ));
    ?>
</tr>
<?php
        }
?>