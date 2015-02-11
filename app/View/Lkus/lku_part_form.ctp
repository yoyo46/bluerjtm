<?php
		$this->Html->addCrumb(__('LKU'), array(
			'controller' => 'lkus',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Lku', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="ttuj-form">
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
                            'data-change' => 'getTtujInfo',
                        );
    					$urlBrowse = array(
                            'controller'=> 'ajax', 
                            'action' => 'getTtujs',
                            'lku',
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
									'id' => 'getTtujInfo'
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
	        		echo $this->element('blocks/lkus/lkus_info');
	        	?>
	        </div>
	        <div class="form-group">
				<?php 
						echo $this->Form->input('tgl_lku',array(
							'label'=> __('Tanggal Klaim'), 
							'class'=>'form-control custom-date',
							'type' => 'text'
						));
				?>
			</div>
	    </div>
	</div>
	<div id="detail-tipe-motor">
		<?php 
			if(!empty($this->request->data['LkuDetail'])){
				echo $this->element('blocks/lkus/lkus_info_tipe_motor'); 
			}
		?>
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
<?php
		echo $this->Form->end();
?>