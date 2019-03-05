<?php
        $idx = 1;

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
                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $customer_name = $this->Common->filterEmptyField($value, 'Ttuj', 'customer_name');
                $note_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'note', false, false, 'EOL');
                $qtyTtuj = $this->Common->filterEmptyField($value, 'Ttuj', 'qty');

                $driver = $this->Common->_callGetDriver($value);
                $tujuan = sprintf('%s - %s', $from_city_name, $to_city_name);

                if( !empty($id) ) {
                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                } else {
                    $noref = '';
                }
                
                $customDate = $this->Common->formatDate($tgl_surat_jalan, 'd M Y');
                $customDateTtuj = $this->Common->formatDate($ttuj_date, 'd M Y');

                $totalUnit += $qtyTtuj;
                $totalSj += $qty;

                if( $idx%2 == 0 ) {
                    $style = 'background-color: #d9edf7;';
                } else {
                    $style = 'background-color: #f5f5f5;';
                }
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $noref, array(
                'class' => 'string',
                'style' => $style,
            ));
            echo $this->Html->tag('td', $nodoc, array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', $customDate, array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', $note, array(
                'style' => $style,
            ));

            echo $this->Html->tag('td', $no_ttuj, array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', $customDateTtuj, array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', $nopol, array(
                'style' => 'text-align: center;'.$style,
            ));
            echo $this->Html->tag('td', $driver, array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', $tujuan, array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', $customer_name, array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', $note_ttuj, array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', $qtyTtuj, array(
                'style' => 'text-align: center;'.$style,
            ));

            echo $this->Html->tag('td', $qty, array(
                'style' => 'text-align: center;'.$style,
            ));
    ?>
</tr>
<?php
                $old_ttuj_id = $ttuj_id;
                $idx++;
            }

            if( $idx%2 == 0 ) {
                $style = 'background-color: #d9edf7;';
            } else {
                $style = 'background-color: #f5f5f5;';
            }
?>
<tr style="font-weight: bold;">
    <?php 
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => $style,
            ));
            echo $this->Html->tag('td', __('Total'), array(
                'style' => 'text-align: right;'.$style,
            ));
            // echo $this->Html->tag('td', $totalUnit, array(
            //     'style' => 'text-align: center;'
            // ));
            echo $this->Html->tag('td', $totalSj, array(
                'style' => 'text-align: center;'.$style,
            ));
    ?>
</tr>
<?php
        }
?>