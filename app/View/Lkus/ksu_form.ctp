<?php
		$this->Html->addCrumb(__('KSU'), array(
			'controller' => 'lkus',
			'action' => 'ksus'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Ksu', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="ttuj-form">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Informasi KSU'); ?></h3>
	    </div>
	    <div class="box-body">
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('no_doc',array(
							'label'=> __('No. Dokumen *'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('No. Dokumen'),
							'readonly' => (!empty($id)) ? true : false
						));
				?>
	        </div>
	        <div class="form-group">
	        	<?php 
    					$attrBrowse = array(
                            'class' => 'ajaxModal visible-xs',
                            'escape' => false,
                            'title' => __('Data TTUJ'),
                            'data-action' => 'browse-form',
                            'data-change' => 'getTtujInfoKsu',
                        );
    					$urlBrowse = array(
                            'controller'=> 'ajax', 
                            'action' => 'getTtujs',
                            'ksu',
                        );
                    	echo $this->Form->label('ttuj_id', __('No. TTUJ * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
                ?>
                <div class="row">
                    <div class="col-sm-10">
			        	<?php 
								echo $this->Form->input('ttuj_id',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'options' => $ttujs,
									'empty' => __('Pilih TTUJ'),
									'id' => 'getTtujInfoKsu'
								));
						?>
                    </div>
    				<div class="col-sm-2 hidden-xs">
                        <?php 
    							$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                                echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
                        ?>
                    </div>
                </div>
	        </div>
	        <div id="ttuj-info">
	        	<?php
	        		echo $this->element('blocks/lkus/ksus_info');
	        	?>
	        </div>
	        <div class="form-group">
				<?php 
						echo $this->Form->input('tgl_ksu',array(
							'label'=> __('Tanggal Klaim'), 
							'class'=>'form-control custom-date',
							'type' => 'text',
							'value' => (!empty($this->request->data['Ksu']['tgl_ksu'])) ? $this->request->data['Ksu']['tgl_ksu'] : date('d/m/Y')
						));
				?>
			</div>
			<div class="form-group list-report-monitoring">
            <?php 
                    echo $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('kekurangan_atpm',array(
                        'type' => 'checkbox',
                        'label'=> false,
                        'required' => false,
                        'div' => false,
                        'class' => 'handle-atpm',
                    )).__('Kekurangan ATPM atau Main Dealer?')), array(
                        'class' => 'checkbox col-sm-6',
                    ));
            ?>
            <div class="clear"></div>
            <div id="atpm-box" class="<?php echo !empty($this->request->data['Ksu']['kekurangan_atpm']) ? '' : 'hide';?>">
            	<div class="form-group">
					<?php 
							echo $this->Form->input('date_atpm',array(
								'label'=> __('Tanggal ATPM *'), 
								'class'=>'form-control custom-date',
								'type' => 'text',
								'value' => (!empty($this->request->data['Ksu']['date_atpm'])) ? $this->request->data['Ksu']['date_atpm'] : date('d/m/Y'),
								'id' => 'atpm_handle'
							));
					?>
				</div>
				<div class="form-group">
					<?php 
							echo $this->Form->input('description_atpm',array(
								'label'=> __('Keterangan'), 
								'type' => 'textarea',
								'class'=>'form-control',
							));
					?>
				</div>
            </div>
            
        </div>
	    </div>
	</div>
	<div id="detail-perlengkapan">
		<?php 
			if(!empty($this->request->data['KsuDetail'])){
				echo $this->element('blocks/lkus/ksus_info_tipe_motor'); 
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
	    		echo $this->Form->button(__('simpan'), array(
					'class'=> 'btn btn-success btn-lg',
					'type' => 'submit',
				));
		?>
	</div>
</div>
<?php
		echo $this->Form->end();
?>