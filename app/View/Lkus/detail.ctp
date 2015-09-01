<?php 
		$customer_name = $this->Common->filterEmptyField($Lku, 'Customer', 'customer_name_code');
        $status = $this->Lku->getCheckStatus($Lku, 'Lku');
		$completed_date = $this->Common->filterEmptyField($Lku, 'Lku', 'completed_date');
		$completed_desc = $this->Common->filterEmptyField($Lku, 'Lku', 'completed_desc');
		$completed_nodoc = $this->Common->filterEmptyField($Lku, 'Lku', 'completed_nodoc', '-');
		$completed = $this->Common->filterEmptyField($Lku, 'Lku', 'completed');

		$customCompletedDate = $this->Common->customDate($completed_date, 'd/m/Y');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi LKU'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body table-responsive">
	        	<table class="table table-hover">
	        		<tr>
						<th width="30%"><?php echo __('No. Dokumen');?></th>
						<td><?php echo $Lku['Lku']['no_doc'];?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Ttuj');?></th>
						<td><?php echo $Lku['Ttuj']['no_ttuj'];?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Customer');?></th>
						<td><?php echo $customer_name;?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Tgl LKU');?></th>
						<td><?php echo date('d/m/Y', strtotime($Lku['Lku']['tgl_lku']));?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Total Klaim');?></th>
						<td><?php echo !empty($Lku['Lku']['total_klaim'])?$Lku['Lku']['total_klaim']:'-';?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Total Pembayaran');?></th>
						<td><?php echo $this->Number->currency($Lku['Lku']['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Status');?></th>
						<?php 
	                            echo $this->Html->tag('td', $status);
	                	?>
					</tr>
					<?php 
							if( !empty($completed) ) {
					?>
					<tr>
						<th width="30%"><?php echo __('Tgl Selesai');?></th>
						<?php 
	                            echo $this->Html->tag('td', $customCompletedDate);
	                	?>
					</tr>
					<tr>
						<th width="30%"><?php echo __('No. Dokumen');?></th>
						<?php 
	                            echo $this->Html->tag('td', $completed_nodoc);
	                	?>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Keterangan Selesai');?></th>
						<?php 
	                            echo $this->Html->tag('td', $completed_desc);
	                	?>
					</tr>
					<?php 
							}
					?>
	            </table>
	        </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi LKU Detail'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body table-responsive">
	        	<table class="table table-hover">
		            <thead>
		                <tr>
		                    <th><?php echo __('Tipe Motor');?></th>
		                    <th><?php echo __('No. Rangka');?></th>
		                    <th><?php echo __('Keterangan');?></th>
		                    <th><?php echo __('Part Motor');?></th>
		                    <th><?php echo __('Jumlah');?></th>
		                    <th><?php printf(__('Biaya Klaim (%s)'), Configure::read('__Site.config_currency_code'));?></th>
		                    <th><?php  printf(__('Total (%s)'), Configure::read('__Site.config_currency_code')) ;?></th>
		                </tr>
		            </thead>
		            <tbody class="tipe-motor-table">
		                <?php
		                    $total = 0;
		                    foreach ($Lku['LkuDetail'] as $key => $value) {
		                        $price = (isset($value['price']) && !empty($value['price'])) ? str_replace(',', '', $value['price']) : 0;
		                        $qty = (isset($value['qty']) && !empty($value['qty'])) ? $value['qty'] : 0;
		                ?>
		                <tr>
		                    <td>
		                        <?php
		                        	if(!empty($value['TipeMotor']['name'])){
		                        		$text = $value['TipeMotor']['name'];

		                        		if(!empty($value['GroupMotor']['name'])){
		                        			$text .= ' ('.$value['GroupMotor']['name'].')';
		                        		}

		                        		echo $text;
		                        	}else{
		                        		echo '-';
		                        	}
		                        ?>
		                    </td>
		                    <td>
		                        <?php 
		                            if(!empty($value['no_rangka'])){
		                        		echo $value['no_rangka'];
		                        	}else{
		                        		echo '-';
		                        	}
		                        ?>
		                    </td>
		                    <td>
		                        <?php 
		                            if(!empty($value['note'])){
		                        		echo $value['note'];
		                        	}else{
		                        		echo '-';
		                        	}
		                        ?>
		                    </td>
		                    <td>
		                        <?php 
		                            if(!empty($value['PartsMotor']['name'])){
		                        		echo $value['PartsMotor']['name'];
		                        	}else{
		                        		echo '-';
		                        	}
		                        ?>
		                    </td>
		                    <td class="qty-tipe-motor" align="center">
		                        <?php
		                            echo $qty;
		                        ?>
		                    </td>
		                    <td align="right">
		                        <?php 
		                            echo $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array('places' => 0));
		                        ?>
		                    </td>
		                    <td class="total-price-claim" align="right">
		                        <?php 
		                            $value_price = 0;
		                            if(!empty($price) && !empty($qty)){
		                                $value_price = $price * $qty;
		                                $total += $value_price;
		                            }

		                            echo $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0));
		                        ?>
		                    </td>
		                </tr>
		                <?php
		                    }
		                ?>
		                <tr id="field-grand-total-lku">
		                    <td align="right" colspan="6"><?php echo $this->Html->tag('strong', __('Total Biaya Klaim')); ?></td>
		                    <td align="right" id="grand-total-lku">
		                        <?php 
		                            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
		                        ?>
		                    </td>
		                    <td>&nbsp;</td>
		                </tr>
		            </tbody>
		        </table>
	        </div>
        </div>
    </div>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
    			'controller' => 'lkus',
				'action' => 'index', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>