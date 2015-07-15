<?php
		$this->Html->addCrumb(__('TTUJ'), array(
			'controller' => 'revenues',
			'action' => 'ttuj'
		));
		$this->Html->addCrumb(__('Surat Jalan'), array(
			'controller' => 'revenues',
			'action' => 'surat_jalan',
			$ttuj_id,
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('SuratJalan', array(
				'url'=> $this->Html->url( null, true ), 
				'role' => 'form',
				'inputDefaults' => array('div' => false),
			));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('tgl_surat_jalan',array(
						'label'=> __('Tgl Terima *'), 
						'class'=>'form-control custom-date',
						'required' => false,
						'type' => 'text',
						'value' => (!empty($this->request->data['SuratJalan']['tgl_surat_jalan'])) ? $this->request->data['SuratJalan']['tgl_surat_jalan'] : date('d/m/Y')
					));
			?>
        </div>
        <div class="row">
        	<div class="col-sm-3">
        		<div class="form-group">
		        	<?php 
							echo $this->Form->input('qty',array(
								'label'=> __('Qty Diterima *'), 
								'class'=>'form-control input_number',
								'required' => false,
							));
					?>
		        </div>
        	</div>
        </div>
		<div class="form-group">
        	<?php 
					echo $this->Form->input('note',array(
						'label'=> __('Keterangan'), 
						'class'=>'form-control',
						'required' => false,
						'type' => 'textarea',
					));
			?>
        </div>
    </div>

    <div class="box-footer text-center action">
    	<?php
	    		echo $this->Form->button(__('Simpan'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    		echo $this->Html->link(__('Kembali'), array(
					'controller' => 'revenues',
					'action' => 'surat_jalan',
					$ttuj_id,
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>