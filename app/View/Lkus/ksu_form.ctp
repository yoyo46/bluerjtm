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
	<div class="box">
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
                            'class' => 'ajaxModal visible-xs browse-docs',
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
                    	echo $this->Form->label('no_ttuj', __('No. TTUJ * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
                ?>
                <div class="row">
                    <div class="col-sm-10">
			        	<?php 
								echo $this->Form->input('no_ttuj',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									// 'options' => $ttujs,
									// 'empty' => __('Pilih TTUJ'),
									'id' => 'getTtujInfoKsu',
									'readonly' => true,
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
							'label'=> __('Tgl Klaim'), 
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
								'label'=> __('Tgl ATPM *'), 
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
	<?php
			if(!empty($id)){
				$completed_date = $this->Common->filterEmptyField($this->request->data, 'Ksu', 'completed_date');

				$customCompletedDate = $this->Common->customDate($completed_date, 'd/m/Y', date('d/m/Y'));
	?>
	<div class="box box-success">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('KSU Selesai?'); ?></h3>
	    </div>
	    <div class="box-body">
	    	<?php 
	    		echo $this->Html->tag('p', __('Digunakan apabila KSU selesai.'));
	    	?>
	    	<div class="form-group">
	    		<div class="checkbox">
                    <label class="completed-handle">
                    	<?php 
                    		echo $this->Form->checkbox('completed').' Proses sudah selesai?';
                    	?>
                    </label>
                </div>
	    	</div>
            <div id="desc-complete" class="<?php echo !empty($this->request->data['Ksu']['completed']) ? '' : 'hide';?>">
	    		<div class="form-group">
                	<?php 
							echo $this->Form->input('completed_date',array(
								'label'=> __('Tgl Selesai *'), 
								'class'=>'form-control custom-date',
								'required' => false,
								'type' => 'text',
								'value' => $customCompletedDate,
							));

							if ($this->Form->isFieldError('completed')) {
							    echo $this->Form->error('completed');
							}
					?>
            	</div>
	    		<div class="form-group">
                	<?php 
							echo $this->Form->input('completed_nodoc',array(
								'label'=> __('No. Dokumen'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
            	</div>
	    		<div class="form-group">
                	<?php 
							echo $this->Form->input('completed_desc',array(
								'label'=> __('Keterangan *'), 
								'class'=>'form-control',
								'required' => false,
								'type' => 'textarea'
							));

							if ($this->Form->isFieldError('completed')) {
							    echo $this->Form->error('completed');
							}
					?>
            	</div>
            </div>
	    </div>
	</div>
	<?php
			}
	?>
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