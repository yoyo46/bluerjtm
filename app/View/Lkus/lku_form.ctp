<?php 
		if( !empty($step) ) {
?>
<script type="text/javascript">
	window.location.hash = '<?php echo $step; ?>';
</script>
<?php
		}

		$this->Html->addCrumb(__('TTUJ'), array(
			'controller' => 'revenues',
			'action' => 'ttuj'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Ttuj', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="ttuj-form">
	<div id="step1">
		<div class="row">
			<div class="col-sm-6">
				<div class="box box-primary">
				    <div class="box-header">
				        <h3 class="box-title"><?php echo __('Informasi LKU'); ?></h3>
				    </div>
				    <div class="box-body">
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('no_doc',array(
										'label'=> __('No. Dokumen *'), 
										'class'=>'form-control',
										'required' => false,
										'placeholder' => __('No. Dokumen')
									));
							?>
				        </div>
				        <div class="form-group">
				        	<?php 
									echo $this->Form->input('ttuj_id',array(
										'label'=> __('No. TTUJ *'), 
										'class'=>'form-control',
										'required' => false,
										'options' => $ttujs,
										'empty' => __('Pilih TTUJ'),
										'id' => 'getTtujInfo'
									));
							?>
				        </div>
				        <div id="ttuj-info">
				        	<?php
				        		echo $this->element('blocks/lkus/lkus_info');
				        	?>
				        </div>
				        <div class="form-group">
							<?php 
									echo $this->Form->input('tgl_klaim',array(
										'label'=> __('Tanggal Klaim'), 
										'class'=>'form-control custom-date',
									));
							?>
						</div>
				    </div>
				</div>
			</div>
			<div class="col-sm-12" id="detail-tipe-motor">
				<?php 
					if(!empty($this->request->data['LkuDetail'])){
						echo $this->element('blocks/lkus/lkus_info_tipe_motor'); 
					}
				?>
			</div>
		</div>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Html->link(__('Kembali'), array(
						'action' => 'index', 
					), array(
						'class'=> 'btn btn-default',
					));
		    		echo $this->Form->submit(__('simpan'), array(
						'class'=> 'btn btn-success'
					));
			?>
		</div>
	</div>
</div>
<?php
		echo $this->Form->end();
?>
<div class="hide">
	<div class="">

	</div>
</div>