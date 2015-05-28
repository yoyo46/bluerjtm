<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi KSU'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
	        	<dl class="dl-horizontal">
	                <dt><?php echo __('No Dokumen')?></dt>
	                <dd><?php echo $Ksu['Ksu']['no_doc'];?></dd>
	                <dt><?php echo __('Ttuj')?></dt>
	                <dd><?php echo $Ksu['Ttuj']['no_ttuj'];?></dd>
	                <dt><?php echo __('Tanggal KSU')?></dt>
	                <dd><?php echo date('Y/m/d', strtotime($Ksu['Ksu']['tgl_ksu']));?></dd>
	                <dt><?php echo __('Total Klaim')?></dt>
	                <dd><?php echo !empty($Ksu['Ksu']['total_klaim'])?$Ksu['Ksu']['total_klaim']:'-';?></dd>
	                <dt><?php echo __('Total Pembayaran')?></dt>
	                <dd><?php echo $this->Number->currency($Ksu['Ksu']['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></dd>
	                <dt><?php echo __('Status')?></dt>
	                <dd>
	                	<?php 
	                		if(!empty($Ksu['Ksu']['status'])){
	                            echo $this->Html->tag('td', '<span class="label label-success">Aktif</span>');
	                        } else{
	                            echo $this->Html->tag('td', '<span class="label label-danger">Non-aktif</span>');
	                        }
	                	?>
	                </dd>
	                <dt><?php echo __('Status Pembayaran')?></dt>
	                <dd>
	                	<?php 
	                		if(empty($Ksu['Ksu']['kekurangan_atpm'])){
	                            if(!empty($Ksu['Ksu']['complete_paid'])){
	                                echo $this->Html->tag('td', '<span class="label label-success">Pembayaran Lunas</span>');
	                            }else{
	                                echo $this->Html->tag('td', '<span class="label label-success">Dibayar Sebagian</span>');
	                            }
	                        }else{
	                            echo $this->Html->tag('td', '<span class="label label-success">Dibayar Main Dealer</span>');
	                        }
	                	?>
	                </dd>
	                <dt><?php echo __('Kekurangan ATPM')?></dt>
	                <dd>
	                	<?php 
	                		if(empty($Ksu['Ksu']['kekurangan_atpm'])){
	                            echo $this->Html->tag('td', '<span class="label label-danger">Tidak</span>');
	                        }else{
	                            echo $this->Html->tag('td', '<span class="label label-success">Dibayar Main Dealer</span>');
	                        }
	                	?>
	                </dd>
	                <?php
	                	if(!empty($Ksu['Ksu']['kekurangan_atpm'])){
	                ?>
	                <dt><?php echo __('Tanggal ATPM')?></dt>
	                <dd>
	                	<?php 
	                		if(!empty($Ksu['Ksu']['date_atpm'])){
	                            echo date('Y/m/d', strtotime($Ksu['Ksu']['date_atpm']));
	                        }else{
	                            echo '-';
	                        }
	                	?>
	                </dd>
	                <dt><?php echo __('Keterangan')?></dt>
	                <dd>
	                	<?php 
	                		if(!empty($Ksu['Ksu']['description_atpm'])){
	                            echo $Ksu['Ksu']['description_atpm'];
	                        }else{
	                            echo '-';
	                        }
	                	?>
	                </dd>
	                <?php
	                	}
	                ?>
	            </dl>
	        </div>
        </div>
    </div>
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
		                    <th><?php echo __('Jumlah Unit');?></th>
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
</div>