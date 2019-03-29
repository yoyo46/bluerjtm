<?php
        if(!empty($values)){
            $grandtotal = 0;
            $totalPaid = 0;
            $totalNoClaim = 0;
            $totalStood = 0;
            $totalLainnya = 0;
            $totalTitipan = 0;
            $totalClaim = 0;
            $totalUnitClaim = 0;
            $totalLaka = 0;
            $grandTotalTrans = 0;

            foreach ($values as $key => $value) {
                $id = Common::hashEmptyField($value, 'TtujPayment.id');
                $detail_id = Common::hashEmptyField($value, 'TtujPaymentDetail.id');
                $date_payment = $this->Common->filterEmptyField($value, 'TtujPayment', 'date_payment');
                $nodoc = $this->Common->filterEmptyField($value, 'TtujPayment', 'nodoc');
                $type = $this->Common->filterEmptyField($value, 'TtujPaymentDetail', 'type');
                // $paid = $this->Common->filterEmptyField($value, 'TtujPayment', 'paid');
                $paid = $this->Common->filterEmptyField($value, 'TtujPaymentDetail', 'amount');

                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $customer = $this->Common->filterEmptyField($value, 'Customer', 'code');
                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $note = $this->Common->filterEmptyField($value, 'Ttuj', 'note');
                $total = $this->Common->getBiayaTtuj($value, $type, false, false, 'Ttuj');

                $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');

                $driver = $this->Common->_callGetDataDriver($value);
                $driver_name = Common::hashEmptyField($driver, 'driver_name', '-');

                $customDatePayment = $this->Common->formatDate($date_payment, 'd M Y');
                $customTtujDate = $this->Common->formatDate($ttuj_date, 'd M Y');

                $no_claim = Common::hashEmptyField($value, 'TtujPaymentDetail.no_claim', 0);
                $stood = Common::hashEmptyField($value, 'TtujPaymentDetail.stood', 0);
                $lainnya = Common::hashEmptyField($value, 'TtujPaymentDetail.lainnya', 0);
                $titipan = Common::hashEmptyField($value, 'TtujPaymentDetail.titipan', 0);
                $claim = Common::hashEmptyField($value, 'TtujPaymentDetail.claim', 0);
                $unit_claim = Common::hashEmptyField($value, 'TtujPaymentDetail.unit_claim', 0);
                $laka = Common::hashEmptyField($value, 'TtujPaymentDetail.laka', 0);
                $laka_note = Common::hashEmptyField($value, 'TtujPaymentDetail.laka_note', '-');

                $grandtotal += $total;
                $totalPaid += $paid;
                $totalNoClaim += $no_claim;
                $totalStood += $stood;
                $totalLainnya += $lainnya;
                $totalTitipan += $titipan;
                $totalClaim += $claim;
                $totalUnitClaim += $unit_claim;
                $totalLaka += $laka;

                $total_trans = $paid + $no_claim + $stood + $lainnya - $titipan - $claim - $laka;
                $grandTotalTrans += $total_trans;

                $customType = $this->Common->_callLabelBiayaTtuj($type);
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $branch);
            echo $this->Html->tag('td', $no_ttuj);
            echo $this->Html->tag('td', $customTtujDate);
            echo $this->Html->tag('td', $nopol);
            echo $this->Html->tag('td', $customer);
            echo $this->Html->tag('td', $from_city_name);
            echo $this->Html->tag('td', $to_city_name);
            echo $this->Html->tag('td', $driver_name);
            echo $this->Html->tag('td', $nodoc);
            echo $this->Html->tag('td', $note);
            echo $this->Html->tag('td', $customType);
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total));
            echo $this->Html->tag('td', __('OK'));
            echo $this->Html->tag('td', date('d M Y'));
            echo $this->Html->tag('td', Common::hashEmptyField($driver, 'id', '-'));
            echo $this->Html->tag('td', Common::hashEmptyField($driver, 'account_name', '-'));
            echo $this->Html->tag('td', Common::hashEmptyField($driver, 'account_number', '-'));
            echo $this->Html->tag('td', Common::hashEmptyField($driver, 'bank_name', '-'));
            echo $this->Html->tag('td', $customDatePayment);
            echo $this->Html->tag('td', $this->Common->getFormatPrice($paid));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($no_claim));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($stood));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($lainnya));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($titipan));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($claim));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($unit_claim));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($laka));
            echo $this->Html->tag('td', $laka_note);
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_trans));
            echo $this->Html->tag('td', $this->Common->_callNoRefCMS($detail_id, $driver_name));
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
            echo $this->Html->tag('td', $this->Html->tag('strong', __('Total')), array(
                'style' => 'text-align: right;vertical-align: middle;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($grandtotal)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalPaid)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalNoClaim)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalStood)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalLainnya)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalTitipan)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalClaim)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $totalUnitClaim), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($totalLaka)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
            echo $this->Html->tag('td', '&nbsp;');
            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($grandTotalTrans)), array(
                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
            ));
    ?>
</tr>
<?php
        }
?>