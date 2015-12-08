<?php 
        $id = $this->Common->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'id');
        $no_ttuj = $this->Common->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'no_ttuj');
        $ttuj_date = $this->Common->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'ttuj_date');
        $nopol = $this->Common->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'nopol');
        $from_city_name = $this->Common->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'from_city_name');
        $to_city_name = $this->Common->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'to_city_name');
        $note = $this->Common->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'note');

        $data_type = !empty($data_type)?$data_type:false;
        $data_type = $this->Common->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'data_type', $data_type);

        $customer_name_code = $this->Common->filterEmptyField($ttuj, 'Customer', 'code');
        $driver_name = $this->Common->filterEmptyField($ttuj, 'UangJalanKomisiPayment', 'driver_name');
        $driver_name = $this->Common->filterEmptyField($ttuj, 'DriverPenganti', 'driver_name', $driver_name);

        $checkbox = isset($checkbox)?$checkbox:true;
		$alias = sprintf('child-%s-%s', $id, $data_type);
        $addClass = !empty($isAjax)?'hide':'';

        if( !empty($checkbox) ) {
            printf('<tr data-value="%s" data-type="%s" class="child %s">', $alias, $data_type, $alias);

			$checkboxContent = $this->Form->checkbox('ttuj_checked.', array(
                'class' => 'check-option',
                'value' => $id,
            ));
            $checkboxContent .= $this->Form->input('TtujPayment.ttuj_id.', array(
                'type' => 'hidden',
                'value' => $id,
            ));

            echo $this->Html->tag('td', $checkboxContent, array(
                'class' => 'checkbox-action',
            ));
        } else {
            printf('<tr class="child child-%s">', $alias);
        }
?>
    <td><?php echo $no_ttuj;?></td>
    <td><?php echo date('d M Y', strtotime($ttuj_date));?></td>
    <td><?php echo $nopol;?></td>
    <td><?php echo $customer_name_code;?></td>
    <td><?php echo $from_city_name;?></td>
    <td><?php echo $to_city_name;?></td>
    <td>
    	<?php
    			echo $driver_name;
		?>
	</td>
    <td class="text-center">
    	<?php
                switch ($data_type) {
                    case 'asdp':
                        echo __('Uang Penyebrangan');
                        break;
                    case 'uang_jalan':
                        echo __('Uang Jalan 1');
                        break;
                    case 'commission':
                        echo __('Komisi');
                        break;
                    case 'commission_extra':
                        echo __('Komisi Extra');
                        break;
                    default:
                        echo ucwords(str_replace('_', ' ', $data_type));
                        break;
                }
		?>
	</td>
    <?php 
            if( !empty($checkbox) ) {
    ?>
    <td class="on-remove">
        <?php
                echo $note;
        ?>
    </td>
    <?php 
            }
    ?>
    <td class="total-ttuj">
    	<?php
    			echo $this->Common->getBiayaTtuj( $ttuj, $data_type, true, false );
		?>
	</td>
    <td>
    	<?php
                $sisaAmount = $this->Common->getBiayaTtuj( $ttuj, $data_type );
    			echo $this->Form->input('TtujPayment.amount_payment.',array(
                    'label'=> false,
                    'class'=>'form-control input_price_min sisa-ttuj text-right',
                    'required' => false,
                    'value' => !empty($this->request->data['TtujPayment']['amount_payment'][$idx])?$this->request->data['TtujPayment']['amount_payment'][$idx]:$sisaAmount,
                ));
                echo $this->Form->hidden('TtujPayment.ttuj_id.',array(
                    'value'=> $id,
                ));
                echo $this->Form->hidden('TtujPayment.data_type.',array(
                    'value'=> $data_type,
                ));
		?>
	</td>
    <td class="ttuj-payment-action <?php echo $addClass; ?>">
        <?php
                echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
                    'class' => 'delete-biaya btn btn-danger btn-xs',
                    'escape' => false,
                    'data-id' => sprintf('child-%s', $alias),
                ));
        ?>
</tr>