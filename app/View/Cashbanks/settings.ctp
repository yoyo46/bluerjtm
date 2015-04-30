<?php
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('CashBankSetting', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Setting COA'); ?></h3>
    </div>
    <div class="box-body">
    	<?php
    		if(!empty($cash_bank_settings)){
    	?>
    	<table class="table table-hover">
    		<thead>
    			<tr>
    				<th>Nama</th>
    				<th>Kredit</th>
    				<th>Debit</th>
    			</tr>
    		</thead>
    		<tbody>
    			<?php
    			foreach ($cash_bank_settings as $key => $value) {
    				$value = $value['CashBankSetting'];
    			?>
    			<tr>
    				<td>
    					<?php 
    						echo $value['name'];
    						echo $this->Form->hidden('CashBankSetting.id.'.$value['id'], array(
    							'value' => $value['id'],
    						));
    					?>
    				</td>
    				<td>
    					<?php 
    						if(in_array($value['type_setting'], array('credit', 'both'))){
    							echo $this->Form->input('CashBankSetting.coa_credit_id.'.$value['id'], array(
	    							'options' => $coas,
	    							'empty' => __('Pilih COA Kredit'),
	    							'class' => 'form-control',
	    							'label' => false,
	    							'value' => $value['coa_credit_id']
	    						));
    						}else{
    							echo '-';
    						}
    					?>
    				</td>
    				<td>
    					<?php 
    						if(in_array($value['type_setting'], array('debit', 'both'))){
    							echo $this->Form->input('CashBankSetting.coa_debit_id.'.$value['id'], array(
	    							'options' => $coas,
	    							'empty' => __('Pilih COA debit'),
	    							'class' => 'form-control',
	    							'label' => false,
	    							'value' => $value['coa_debit_id']
	    						));
    						}else{
    							echo '-';
    						}
    					?>
    				</td>
    			</tr>
        <?php
        		}
        ?>
        		<tr>
        			<td colspan="3" align="center">
        				<?php
					    		echo $this->Form->button(__('simpan'), array(
									'class'=> 'btn btn-success btn-lg',
									'type' => 'submit',
								));
						?>
        			</td>
        		</tr>
        	</tbody>
        </table>
        <?php
        	}else{
        		echo $this->Html->tag('div', __('List Kas Bank belum tersedia.'), array(
        			'class' => 'alert alert-danger'
        		));
        	}
        ?>
    </div>
</div>
<?php
		echo $this->Form->end();
?>