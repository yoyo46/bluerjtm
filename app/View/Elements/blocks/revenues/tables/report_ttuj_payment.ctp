<?php
        if(!empty($values)){
            $grandtotal = 0;
            $totalPaid = 0;
            $totalSaldo = 0;

            foreach ($values as $key => $value) {
                $date_payment = $this->Common->filterEmptyField($value, 'TtujPayment', 'date_payment');
                $nodoc = $this->Common->filterEmptyField($value, 'TtujPayment', 'nodoc');
                $type = $this->Common->filterEmptyField($value, 'TtujPaymentDetail', 'type');
                $paid = $this->Common->filterEmptyField($value, 'TtujPayment', 'paid');

                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $customer = $this->Common->filterEmptyField($value, 'Ttuj', 'customer_name');
                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $note = $this->Common->filterEmptyField($value, 'Ttuj', 'note');
                $total = $this->Common->getBiayaTtuj($value, $type, false, false, 'Ttuj');

                $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');

                $driver_name = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');
                $driver_name = $this->Common->filterEmptyField($value, 'DriverPenganti', 'driver_name', $driver_name);

                $customDatePayment = $this->Common->formatDate($date_payment, 'd/m/Y');
                $customTtujDate = $this->Common->formatDate($ttuj_date, 'd/m/Y');

                $saldo = $total - $paid;

                $grandtotal += $total;
                $totalPaid += $paid;
                $totalSaldo += $saldo;
                $customType = $this->Common->_callLabelBiayaTtuj($type);
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $customDatePayment);
            echo $this->Html->tag('td', $nodoc);
            echo $this->Html->tag('td', $branch);
            echo $this->Html->tag('td', $no_ttuj);
            echo $this->Html->tag('td', $customTtujDate);
            echo $this->Html->tag('td', $nopol);
            echo $this->Html->tag('td', $customer);
            echo $this->Html->tag('td', $from_city_name);
            echo $this->Html->tag('td', $to_city_name);
            echo $this->Html->tag('td', $driver_name);
            echo $this->Html->tag('td', $note);
            echo $this->Html->tag('td', $customType);
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($paid));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($saldo));
    ?>
</tr>
<?php
            }
?>

<tr>
    <?php 
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', $this->Html->tag('strong', __('Total')), array(
                'style' => 'text-align: right;vertical-align: middle;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($grandtotal)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalPaid)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalSaldo)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
    ?>
</tr>
<?php
        }
?>