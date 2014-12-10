<?php 
	$this->Html->addCrumb(__('Truk'), array(
		'controller' => 'trucks',
		'action' => 'index'
	));
    $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
	<div class="box box-solid">
	    <div class="box-header">
	        <?php 
	                echo $this->Html->tag('h3', $sub_module_title, array(
	                    'class' => 'box-title'
	                ));
	        ?>
	    </div>
	    <div class="box-body">
	        <dl class="dl-horizontal">
	            <dt><?php echo __('Merek')?></dt>
	            <dd><?php echo $truck['TruckBrand']['name'];?></dd>
	            <dt><?php echo __('Nopol')?></dt>
	            <dd><?php echo $truck['Truck']['nopol'];?></dd>
	            <dt><?php echo __('Perusahaan')?></dt>
	            <dd><?php echo $truck['Company']['name'];?></dd>
	            <dt><?php echo __('Kategori')?></dt>
	            <dd><?php echo $truck['TruckCategory']['name'];?></dd>
	            <dt><?php echo __('Nama Supir')?></dt>
	            <dd><?php echo $truck['Driver']['name'];?></dd>
	            <dt><?php echo __('BPKB')?></dt>
	            <dd><?php echo $truck['Truck']['bpkb'];?></dd>
	            <dt><?php echo __('No. STNK')?></dt>
	            <dd><?php echo $truck['Truck']['no_stnk'];?></dd>
	            <dt><?php echo __('No. Rangka')?></dt>
	            <dd><?php echo $truck['Truck']['no_rangka'];?></dd>
	            <dt><?php echo __('No. Mesin')?></dt>
	            <dd><?php echo $truck['Truck']['no_machine'];?></dd>
	            <dt><?php echo __('Kapasitas')?></dt>
	            <dd><?php echo $truck['Truck']['capacity'];?></dd>
	            <dt><?php echo __('Atas Nama')?></dt>
	            <dd><?php echo $truck['Truck']['atas_nama'];?></dd>
	            <dt><?php echo __('KIR')?></dt>
	            <dd>
	            	<?php 
		            		$link = $this->Html->link(__('Perpanjang KIR ?'), array(
		            			'controller' => 'trucks',
		            			'action' => 'kir',
		            			$truck['Truck']['id']
		            		));

		            		if( !empty($truck['Truck']['kir']) ) {
		            			$truckKir = $this->Common->customDate($truck['Truck']['kir']);
		            		} else {
		            			$truckKir = '';
		            		}

		            		echo trim(sprintf('%s %s', $truckKir, $link));
	            	?>
	            </dd>
	            <dt><?php echo __('SIUP')?></dt>
	            <dd>
	            	<?php 
		            		$link = $this->Html->link(__('Perpanjang SIUP ?'), array(
		            			'controller' => 'trucks',
		            			'action' => 'siup',
		            			$truck['Truck']['id']
		            		));

		            		if( !empty($truck['Truck']['siup']) ) {
		            			$truckSiup = $this->Common->customDate($truck['Truck']['siup']);
		            		} else {
		            			$truckSiup = '';
		            		}

		            		echo trim(sprintf('%s %s', $truckSiup, $link));
	            	?>
	            </dd>
	            <dt><?php echo __('No. Kontrak')?></dt>
                <dd><?php echo $truck['Truck']['no_contract'];?></dd>
                <dt><?php echo __('Tanggal BPKB')?></dt>
                <dd><?php echo $this->Common->customDate($truck['Truck']['tgl_bpkb'], 'd F Y');?></dd>
                <dt><?php echo __('Tahun')?></dt>
                <dd><?php echo $this->Common->customDate($truck['Truck']['tahun'], 'Y');?></dd>
                <dt><?php echo __('Tahun Neraca')?></dt>
                <dd><?php echo $this->Common->customDate($truck['Truck']['tahun_neraca'], 'Y');?></dd>
                <dt><?php echo __('Aset')?></dt>
                <dd>
                    <?php 
                        if(!empty($truck['Truck']['is_asset'])){
                            echo '<span class="label label-success">Ya</span>'; 
                        }else{
                            echo '<span class="label label-danger">Tidak</span>';  
                        }
                    ?>
                </dd>
                <dt><?php echo __('Status')?></dt>
                <dd>
                    <?php 
                        if(!empty($truck['Truck']['status'])){
                            echo '<span class="label label-success">Active</span>'; 
                        }else{
                            echo '<span class="label label-danger">Non Active</span>';  
                        }
                        
                    ?>
                </dd>
	        </dl>
	        <div class="form-group text-center action">
	            <?php
	                    echo $this->Form->button(__('Rubah'), array(
	                        'div' => false, 
	                        'class'=> 'btn btn-primary btn-sm',
	                        'type' => 'submit',
	                    ));
	                    echo $this->Html->link(__('Kembali'), array(
	                        'action' => 'index', 
	                    ), array(
	                        'escape' => false, 
	                        'class'=> 'btn btn-default btn-sm',
	                    ));
	            ?>
	        </div>
	    </div>
	</div>
</div>