<?php
        if(!empty($values)){
            $grandtotalLeasing = 0;
            $grandtotalDp = 0;
            $grandtotalAnnualInterest = 0;
            $grandtotalLeasingMonth = 0;
            $grandtotalPokok = 0;
            $grandtotalBunga = 0;
            $grandtotalFacility = 0;
            $grandtotalInstallment = 0;
            $grandtotalInstallmentRate = 0;
            $grandtotalAngsuran = 0;
            $grandtotalCountInstallment = 0;
            $grandtotalTotalInstallment = 0;
            $grandtotalTotalInstallmentRate = 0;
            $grandtotalPembayaran = 0;
            $grandtotalCountSisa = 0;
            $grandtotalSisa = 0;
            $grandtotalSisaRate = 0;
            $grandtotalSisaAngsuran = 0;

            foreach ($values as $key => $value) {
                $no_contract = $this->Common->filterEmptyField($value, 'Leasing', 'no_contract');
                $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');
                $start_date = $this->Common->filterEmptyField($value, 'Leasing', 'date_first_installment');
                $end_date = $this->Common->filterEmptyField($value, 'Leasing', 'date_last_installment');
                $total_leasing = $this->Common->filterEmptyField($value, 'Leasing', 'total_leasing');
                $down_payment = $this->Common->filterEmptyField($value, 'Leasing', 'down_payment');
                $leasing_month = $this->Common->filterEmptyField($value, 'Leasing', 'leasing_month');
                $installment = $this->Common->filterEmptyField($value, 'Leasing', 'installment');
                $installment_rate = $this->Common->filterEmptyField($value, 'Leasing', 'installment_rate');
                $annual_interest = $this->Common->filterEmptyField($value, 'Leasing', 'annual_interest');

                $grandtotal_installment = $this->Common->filterEmptyField($value, 'LeasingPayment', 'grandtotal_installment');
                $grandtotal_installment_rate = $this->Common->filterEmptyField($value, 'LeasingPayment', 'grandtotal_installment_rate');
                $count_installment = $this->Common->filterEmptyField($value, 'LeasingInstallment', 'count_installment');

                $pokok = $leasing_month * $installment;
                $bunga = $leasing_month * $installment_rate;
                $totalFacility = $pokok + $bunga;
                $totalAngsuran = $installment + $installment_rate;
                $totalPembayaran = $grandtotal_installment + $grandtotal_installment_rate;

                $count_sisa = $leasing_month - $count_installment;
                $grandtotal_sisa = $pokok - $grandtotal_installment;
                $grandtotal_sisa_rate = $bunga - $grandtotal_installment_rate;
                $grandtotal_sisa_angsuran = $grandtotal_sisa + $grandtotal_sisa_rate;

                $customTotalLeasing = $this->Common->getFormatPrice($total_leasing);
                $customDp = $this->Common->getFormatPrice($down_payment);
                $customPokok = $this->Common->getFormatPrice($pokok);
                $customBunga = $this->Common->getFormatPrice($bunga);
                $customTotalFacility = $this->Common->getFormatPrice($totalFacility);
                $customInstallment = $this->Common->getFormatPrice($installment);
                $customInstallmentRate = $this->Common->getFormatPrice($installment_rate);
                $customTotalAngsuran = $this->Common->getFormatPrice($totalAngsuran);
                $customTotalPembayaran = $this->Common->getFormatPrice($totalPembayaran);

                $customTotalInstallment = $this->Common->getFormatPrice($grandtotal_installment);
                $customTotalInstallmentRate = $this->Common->getFormatPrice($grandtotal_installment_rate);

                $customTotalSisa = $this->Common->getFormatPrice($grandtotal_sisa);
                $customTotalSisaRate = $this->Common->getFormatPrice($grandtotal_sisa_rate);
                $customTotalSisaAngsuran = $this->Common->getFormatPrice($grandtotal_sisa_angsuran);

                $customStartDate = $this->Common->formatDate($start_date, 'd/m/Y');
                $customEndDate = $this->Common->formatDate($end_date, 'd/m/Y');

                $grandtotalLeasing += $total_leasing;
                $grandtotalDp += $down_payment;
                $grandtotalAnnualInterest += $annual_interest;
                $grandtotalLeasingMonth += $leasing_month;
                $grandtotalPokok += $pokok;
                $grandtotalBunga += $bunga;
                $grandtotalFacility += $totalFacility;
                $grandtotalInstallment += $installment;
                $grandtotalInstallmentRate += $installment_rate;
                $grandtotalAngsuran += $totalAngsuran;
                $grandtotalCountInstallment += $count_installment;
                $grandtotalTotalInstallment += $grandtotal_installment;
                $grandtotalTotalInstallmentRate += $grandtotal_installment_rate;
                $grandtotalPembayaran += $totalPembayaran;
                $grandtotalCountSisa += $count_sisa;
                $grandtotalSisa += $grandtotal_sisa;
                $grandtotalSisaRate += $grandtotal_sisa_rate;
                $grandtotalSisaAngsuran += $grandtotal_sisa_angsuran;
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $no_contract);
            echo $this->Html->tag('td', $vendor);
            echo $this->Html->tag('td', $customStartDate, array(
                'style' => 'text-align: center',
            ));
            echo $this->Html->tag('td', $customEndDate, array(
                'style' => 'text-align: center',
            ));
            echo $this->Html->tag('td', $customTotalLeasing, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $customDp, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $annual_interest);
            echo $this->Html->tag('td', $leasing_month);
            echo $this->Html->tag('td', $customPokok);
            echo $this->Html->tag('td', $customBunga);
            echo $this->Html->tag('td', $customTotalFacility);
            echo $this->Html->tag('td', $customInstallment);
            echo $this->Html->tag('td', $customInstallmentRate);
            echo $this->Html->tag('td', $customTotalAngsuran);
            echo $this->Html->tag('td', $count_installment);
            echo $this->Html->tag('td', $customTotalInstallment);
            echo $this->Html->tag('td', $customTotalInstallmentRate);
            echo $this->Html->tag('td', $customTotalPembayaran);
            echo $this->Html->tag('td', $count_sisa);
            echo $this->Html->tag('td', $customTotalSisa);
            echo $this->Html->tag('td', $customTotalSisaRate);
            echo $this->Html->tag('td', $customTotalSisaAngsuran);
    ?>
</tr>
<?php
            }

            $grandtotalLeasing = $this->Common->getFormatPrice($grandtotalLeasing);
            $grandtotalDp = $this->Common->getFormatPrice($grandtotalDp);
            $grandtotalPokok = $this->Common->getFormatPrice($grandtotalPokok);
            $grandtotalBunga = $this->Common->getFormatPrice($grandtotalBunga);
            $grandtotalFacility = $this->Common->getFormatPrice($grandtotalFacility);
            $grandtotalInstallment = $this->Common->getFormatPrice($grandtotalInstallment);
            $grandtotalInstallmentRate = $this->Common->getFormatPrice($grandtotalInstallmentRate);
            $grandtotalAngsuran = $this->Common->getFormatPrice($grandtotalAngsuran);
            $grandtotalTotalInstallment = $this->Common->getFormatPrice($grandtotalTotalInstallment);
            $grandtotalTotalInstallmentRate = $this->Common->getFormatPrice($grandtotalTotalInstallmentRate);
            $grandtotalPembayaran = $this->Common->getFormatPrice($grandtotalPembayaran);
            $grandtotalSisa = $this->Common->getFormatPrice($grandtotalSisa);
            $grandtotalSisaRate = $this->Common->getFormatPrice($grandtotalSisaRate);
            $grandtotalSisaAngsuran = $this->Common->getFormatPrice($grandtotalSisaAngsuran);
?>
<tr>
    <?php 
            echo $this->Html->tag('td', '');
            echo $this->Html->tag('td', '');
            echo $this->Html->tag('td', '');
            echo $this->Html->tag('td', __('Total'), array(
                'style' => 'text-align: center',
            ));
            echo $this->Html->tag('td', $grandtotalLeasing, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $grandtotalDp, array(
                'style' => 'text-align: right',
            ));
            echo $this->Html->tag('td', $grandtotalAnnualInterest);
            echo $this->Html->tag('td', $grandtotalLeasingMonth);
            echo $this->Html->tag('td', $grandtotalPokok);
            echo $this->Html->tag('td', $grandtotalBunga);
            echo $this->Html->tag('td', $grandtotalFacility);
            echo $this->Html->tag('td', $grandtotalInstallment);
            echo $this->Html->tag('td', $grandtotalInstallmentRate);
            echo $this->Html->tag('td', $grandtotalAngsuran);
            echo $this->Html->tag('td', $grandtotalCountInstallment);
            echo $this->Html->tag('td', $grandtotalTotalInstallment);
            echo $this->Html->tag('td', $grandtotalTotalInstallmentRate);
            echo $this->Html->tag('td', $grandtotalPembayaran);
            echo $this->Html->tag('td', $grandtotalCountSisa);
            echo $this->Html->tag('td', $grandtotalSisa);
            echo $this->Html->tag('td', $grandtotalSisaRate);
            echo $this->Html->tag('td', $grandtotalSisaAngsuran);
    ?>
</tr>
<?php
        }
?>
<script type="text/javascript">
    function rowColored (index,row) {
        var value = row.end_date.toLowerCase();
        
        if( value == 'total' ) {
            return 'background: rgba(221, 221, 221,0.5);font-weight:bold;';
        }
    }
</script>