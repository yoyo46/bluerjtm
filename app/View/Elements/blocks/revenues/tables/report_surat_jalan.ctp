<?php
        if(!empty($values)){
            $totalUnit = 0;
            $totalSj = 0;
            $old_ttuj_id = false;

            foreach ($values as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'SuratJalan', 'id');
                $nodoc = $this->Common->filterEmptyField($value, 'SuratJalan', 'nodoc');
                $note = $this->Common->filterEmptyField($value, 'SuratJalan', 'note');
                $ttuj_id = $this->Common->filterEmptyField($value, 'SuratJalanDetail', 'ttuj_id');
                $tgl_surat_jalan = $this->Common->filterEmptyField($value, 'SuratJalan', 'tgl_surat_jalan');
                $qty = $this->Common->filterEmptyField($value, 'SuratJalanDetail', 'qty');

                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $driver_name = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');
                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $customer_name = $this->Common->filterEmptyField($value, 'Ttuj', 'customer_name');
                $note_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'note', false, false, 'EOL');
                $qtyTtuj = $this->Common->filterEmptyField($value, 'Ttuj', 'qty');

                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                $tujuan = sprintf('%s - %s', $from_city_name, $to_city_name);
                
                $customDate = $this->Common->formatDate($tgl_surat_jalan, 'd/m/Y');
                $customDateTtuj = $this->Common->formatDate($ttuj_date, 'd/m/Y');

                $totalUnit += $qtyTtuj;
                $totalSj += $qty;
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $noref, array(
                'class' => 'string',
            ));
            echo $this->Html->tag('td', $nodoc);
            echo $this->Html->tag('td', $customDate);
            echo $this->Html->tag('td', $note);

            echo $this->Html->tag('td', $no_ttuj);
            echo $this->Html->tag('td', $customDateTtuj);
            echo $this->Html->tag('td', $nopol, array(
                'style' => 'text-align: center;'
            ));
            echo $this->Html->tag('td', $driver_name);
            echo $this->Html->tag('td', $tujuan);
            echo $this->Html->tag('td', $customer_name);
            echo $this->Html->tag('td', $note_ttuj);
            echo $this->Html->tag('td', $qtyTtuj, array(
                'style' => 'text-align: center;'
            ));

            echo $this->Html->tag('td', $qty, array(
                'style' => 'text-align: center;'
            ));
    ?>
</tr>
<?php
                $old_ttuj_id = $ttuj_id;
            }
?>
<tr style="font-weight: bold;">
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
            echo $this->Html->tag('td', __('Total'), array(
                'style' => 'text-align: right;'
            ));
            // echo $this->Html->tag('td', $totalUnit, array(
            //     'style' => 'text-align: center;'
            // ));
            echo $this->Html->tag('td', $totalSj, array(
                'style' => 'text-align: center;'
            ));
    ?>
</tr>
<?php
        }
?>