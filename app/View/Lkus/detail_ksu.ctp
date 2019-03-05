<?php 
		$customer_name = $this->Common->filterEmptyField($Ksu, 'Customer', 'customer_name_code');
        $status = $this->Lku->getCheckStatus($Ksu, 'Ksu');
		$completed_date = $this->Common->filterEmptyField($Ksu, 'Ksu', 'completed_date');
		$completed_desc = $this->Common->filterEmptyField($Ksu, 'Ksu', 'completed_desc');
		$completed_nodoc = $this->Common->filterEmptyField($Ksu, 'Ksu', 'completed_nodoc', '-');
		$completed = $this->Common->filterEmptyField($Ksu, 'Ksu', 'completed');

		$no_ttuj = $this->Common->filterEmptyField($Ksu, 'Ttuj', 'no_ttuj');
		$nopol = $this->Common->filterEmptyField($Ksu, 'Ttuj', 'nopol');

		$driver = $this->Common->_callGetDriver($Ksu);

		$customCompletedDate = $this->Common->customDate($completed_date, 'd M Y');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi KSU'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body table-responsive">
	    		<table class="table table-hover">
	    			<tr>
						<th width="30%"><?php echo __('No. Dokumen');?></th>
						<td><?php echo $Ksu['Ksu']['no_doc'];?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Ttuj');?></th>
						<td><?php echo $no_ttuj;?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Truk');?></th>
						<td><?php echo $nopol;?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Supir');?></th>
						<td><?php echo $driver;?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Customer');?></th>
						<td><?php echo $customer_name;?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Tgl KSU');?></th>
						<td><?php echo date('d M Y', strtotime($Ksu['Ksu']['tgl_ksu']));?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Total Klaim');?></th>
						<td><?php echo !empty($Ksu['Ksu']['total_klaim'])?$Ksu['Ksu']['total_klaim']:'-';?></td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Total Pembayaran');?></th>
						<td><?php echo $this->Number->currency($Ksu['Ksu']['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
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
					<tr>
						<th width="30%"><?php echo __('Kekurangan ATPM');?></th>
						<?php 
	                		if(empty($Ksu['Ksu']['kekurangan_atpm'])){
	                            echo $this->Html->tag('td', '<span class="label label-danger">Tidak</span>');
	                        }else{
	                            echo $this->Html->tag('td', '<span class="label label-success">Dibayar Main Dealer</span>');
	                        }
	                	?>
					</tr>
	                <?php
	                	if(!empty($Ksu['Ksu']['kekurangan_atpm'])){
	                ?>
	                <tr>
						<th width="30%"><?php echo __('Tgl ATPM');?></th>
						<td>
						<?php 
	                		if(!empty($Ksu['Ksu']['date_atpm'])){
	                            echo date('Y/m/d', strtotime($Ksu['Ksu']['date_atpm']));
	                        }else{
	                            echo '-';
	                        }
	                	?>
	                	</td>
					</tr>
					<tr>
						<th width="30%"><?php echo __('Keterangan');?></th>
						<td>
						<?php 
	                		if(!empty($Ksu['Ksu']['description_atpm'])){
	                            echo $Ksu['Ksu']['description_atpm'];
	                        }else{
	                            echo '-';
	                        }
	                	?>
	                	</td>
					</tr>
	                <?php
	                	}
	                ?>
	            </table>
	        </div>
        </div>
    </div>
    <?php 
    		if( !empty($Ksu['KsuDetail']) ) {
    ?>
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi KSU Detail'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body table-responsive">
	        	<table class="table table-hover">
		            <thead>
		                <tr>
		                    <th><?php echo __('Perlengkapan');?></th>
		                    <th><?php echo __('No. Rangka');?></th>
		                    <th><?php echo __('Keterangan');?></th>
		                    <th><?php echo __('Jumlah');?></th>
		                    <?php if(empty($Ksu['Ksu']['kekurangan_atpm'])){?>
		                    <th><?php printf(__('Biaya Klaim (%s)'), Configure::read('__Site.config_currency_code'));?></th>
		                    <th><?php  printf(__('Total (%s)'), Configure::read('__Site.config_currency_code')) ;?></th>
		                    <?php }?>
		                </tr>
		            </thead>
		            <tbody class="tipe-motor-table">
		                <?php
		                    $total = 0;
		                    foreach ($Ksu['KsuDetail'] as $key => $value) {
		                        $price = (isset($value['price']) && !empty($value['price'])) ? str_replace(',', '', $value['price']) : 0;
		                        $qty = (isset($value['qty']) && !empty($value['qty'])) ? $value['qty'] : 0;
		                ?>
		                <tr>
		                    <td>
		                        <?php
		                        	if(!empty($value['Perlengkapan']['name'])){
		                        		echo $value['Perlengkapan']['name'];
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
		                    <td class="qty-tipe-motor" align="center">
		                        <?php
		                            echo $qty;
		                        ?>
		                    </td>
		                    <?php if(empty($Ksu['Ksu']['kekurangan_atpm'])){?>
		                    <td align="right">
		                        <?php 
		                        	echo $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array('places' => 0));
		                        ?>
		                    </td>
		                    <td class="total-price-claim" align="right">
		                        <?php 
		                        	if(!empty($Ksu['Ksu']['kekurangan_atpm'])){
		                        		echo '-';
		                        	}else{
			                            $value_price = 0;
			                            if(!empty($price) && !empty($qty)){
			                                $value_price = $price * $qty;
			                                $total += $value_price;
			                            }

			                            echo $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0));
			                        }
		                        ?>
		                    </td>
		                    <?php }?>
		                </tr>
		                <?php
		                    }

		                    if(empty($Ksu['Ksu']['kekurangan_atpm'])){
		                ?>
		                <tr id="field-grand-total-ksu">
		                    <td align="right" colspan="5"><?php echo __('Total Biaya Klaim')?></td>
		                    <td align="right" id="grand-total-ksu">
		                        <?php 
		                            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
		                        ?>
		                    </td>
		                    <td>&nbsp;</td>
		                </tr>
		                <?php
		                	}
		                ?>
		            </tbody>
		        </table>
	        </div>
        </div>
    </div>
    <?php 
    		}
    ?>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'ksus', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>