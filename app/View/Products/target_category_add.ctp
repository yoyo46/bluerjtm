<?php
		$this->Html->addCrumb(__('Target Grup Barang'), array(
			'action' => 'index',
			'admin' => false,
		));
		$this->Html->addCrumb($sub_module_title);

		$truck_categories = !empty($truck_categories)?$truck_categories:false;
		$branch = Configure::read('__Site.Branch.code');

		echo $this->Form->create('ProductCategoryTarget');
?>
<div class="row">
	<div class="col-sm-12">
		<div class="box">
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildForm('product_category_id', __('Group *'), array(
							'empty' => __('Pilih Grup'),
						));
			    ?>
				<div class="form-group">
					<?php 
							echo $this->Form->label('target', __('Target *'));
					?>
					<div class="row">
						<div class="col-sm-6">
							<div class="input-group">
								<?php 
										echo $this->Form->input('target',array(
											'type' => 'text',
											'label'=> false, 
											'class'=>'form-control',
											'required' => false,
											'div' => false,
										));
										echo $this->Html->tag('span', __('Per KM'), array(
											'class'=>'input-group-addon',
										));
								?>
		                    </div>
						</div>
					</div>
				</div>
		    </div>
		</div>
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
					'action' => 'target_categories', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
</div>
<?php
		echo $this->Form->end();
?>