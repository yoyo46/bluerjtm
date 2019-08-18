<?php
        $idx = 1;

        if(!empty($values)){
            foreach ($values as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'BonBiru', 'id');
                $nodoc = $this->Common->filterEmptyField($value, 'BonBiru', 'nodoc');
                $note = $this->Common->filterEmptyField($value, 'BonBiru', 'note');
                $ttuj_id = $this->Common->filterEmptyField($value, 'BonBiruDetail', 'ttuj_id');
                $tgl_bon_biru = $this->Common->filterEmptyField($value, 'BonBiru', 'tgl_bon_biru');

                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $customer_name = $this->Common->filterEmptyField($value, 'Ttuj', 'customer_name');
                $note_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'note', false, false, 'EOL');

                $driver = $this->Common->_callGetDriver($value);
                $tujuan = sprintf('%s - %s', $from_city_name, $to_city_name);

                if( !empty($id) ) {
                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                } else {
                    $noref = '';
                }
                
                $customDate = $this->Common->formatDate($tgl_bon_biru, 'd M Y');
                $customDateTtuj = $this->Common->formatDate($ttuj_date, 'd M Y');

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
    ?>
</tr>
<?php
                $idx++;
            }
        }
?>