<?php 
        $checkbox = isset($checkbox)?$checkbox:true;
		$alias = sprintf('child-%s-%s', $ttuj['Ttuj']['id'], $data_type);

        if( !empty($checkbox) ) {
            printf('<tr data-value="%s" data-type="%s" class="child %s">', $alias, $data_type, $alias);

			$checkboxContent = $this->Form->checkbox('ttuj_checked.', array(
                'class' => 'check-option',
                'value' => $ttuj['Ttuj']['id'],
            ));
            $checkboxContent .= $this->Form->input('TtujPayment.ttuj_id.', array(
                'type' => 'hidden',
                'value' => $ttuj['Ttuj']['id'],
            ));

            echo $this->Html->tag('td', $checkboxContent, array(
                'class' => 'checkbox-action',
            ));
        } else {
            printf('<tr class="child child-%s">', $alias);
        }
?>
    <td><?php echo $ttuj['Ttuj']['no_ttuj'];?></td>
    <td><?php echo date('d/m/Y', strtotime($ttuj['Ttuj']['ttuj_date']));?></td>
    <td><?php echo $ttuj['Ttuj']['nopol'];?></td>
    <td><?php echo !empty($ttuj['Customer']['customer_name_code'])?$ttuj['Customer']['customer_name_code']:false;?></td>
    <td><?php echo $ttuj['Ttuj']['to_city_name'];?></td>
    <td>
    	<?php
    			if( !empty($ttuj['DriverPenganti']['driver_name']) ) {
    				echo $ttuj['DriverPenganti']['driver_name'];
    			} else {
    				echo $ttuj['Ttuj']['driver_name'];
    			}
		?>
	</td>
    <td>
    	<?php
                switch ($data_type) {
                    case 'asdp':
                        echo __('Uang Penyebrangan');
                        break;
                    
                    default:
                        echo ucwords(str_replace('_', ' ', $data_type));
                        break;
                }
		?>
	</td>
    <td class="total-ttuj">
    	<?php
    			echo $this->Common->getBiayaTtuj( $ttuj, $data_type );
		?>
	</td>
    <td>
    	<?php
    			echo $this->Form->input('TtujPayment.amount_payment.',array(
                    'label'=> false,
                    'class'=>'form-control input_price sisa-ttuj text-right',
                    'required' => false,
                    'value' => !empty($this->request->data['TtujPayment']['amount_payment'][$idx])?$this->request->data['TtujPayment']['amount_payment'][$idx]:0,
                ));
                echo $this->Form->hidden('TtujPayment.ttuj_id.',array(
                    'value'=> $ttuj['Ttuj']['id'],
                ));
                echo $this->Form->hidden('TtujPayment.data_type.',array(
                    'value'=> $data_type,
                ));
		?>
	</td>
</tr>