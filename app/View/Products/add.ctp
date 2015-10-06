<?php
		$this->Html->addCrumb(__('Barang'), array(
			'action' => 'index',
			'admin' => false,
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Product');
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Info Barang'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildForm('code', __('Kode *'));
						echo $this->Common->buildForm('name', __('Nama *'));
						echo $this->Common->buildForm('product_unit_id', __('Satuan *'), array(
							'empty' => __('Pilih Satuan'),
						));
						echo $this->Common->buildForm('product_category_id', __('Group *'), array(
							'empty' => __('Pilih Grup'),
						));
			    ?>
		    </div>
		</div>
    </div>
    <div class="col-sm-6">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Spesifikasi Barang'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildForm('type_struck', __('Tipe Truk'));
				?>
				<div class="form-group">
					<?php 
							echo $this->Form->label('life_time', __('Life Time'));
					?>
					<div class="row">
						<div class="col-sm-6">
							<div class="input-group">
								<?php 
										echo $this->Form->input('life_time',array(
											'label'=> false, 
											'class'=>'form-control',
											'required' => false,
											'div' => false,
										));
										echo $this->Html->tag('span', __('Bulan'), array(
											'class'=>'input-group-addon',
										));
								?>
		                    </div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<?php 
							echo $this->Form->label('size', __('Ukuran'));
					?>
					<div class="row">
						<div class="col-sm-6">
							<?php 
									echo $this->Form->input('size',array(
										'label'=> false, 
										'class'=>'form-control',
										'required' => false,
										'div' => false,
									));
							?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<?php 
							echo $this->Form->label('volume', __('Volume'));
					?>
					<div class="row">
						<div class="col-sm-6">
							<?php 
									echo $this->Form->input('volume',array(
										'label'=> false, 
										'class'=>'form-control',
										'required' => false,
										'div' => false,
									));
							?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<?php 
							echo $this->Form->label('weight', __('Berat'));
					?>
					<div class="row">
						<div class="col-sm-6">
							<?php 
									echo $this->Form->input('weight',array(
										'label'=> false, 
										'class'=>'form-control',
										'required' => false,
										'div' => false,
									));
							?>
						</div>
					</div>
				</div>
		    </div>
		</div>
    </div>
    <div class="col-sm-6">
		<div class="box box-info">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Tipe Barang'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="row">
		    		<div class="col-sm-4">
		    			<div class="radio">
		    				<label>
					    		<?php 
					    				echo $this->Form->radio('type', array(
									    	'barang_jadi' => __('Barang Jadi'),
									    	'bahan_mentah' => __('Bahan Mentah'),
									    	'barang_bekas' => __('Barang Bekas'),
								    	), array(
					    					'legend' => false,
										    'separator' => '</label></div></div><div class="col-sm-4"><div class="radio"><label>',
										));
					    		?>
		    				</label>
		    			</div>
		    		</div>
		    	</div>
		    </div>
		</div>
    </div>
    <div class="col-sm-6">
		<div class="box box-warning">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Tambahan'); ?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="row">
		    		<div class="col-sm-6">
		    			<div class="checkbox">
					  		<label>
					  			<?php 
										echo $this->Form->input('is_supplier_quotation',array(
											'type' => 'checkbox',
											'label'=> false, 
											'div' => false,
										));
										echo __('Harus ada Supplier quotation ?');
								?>
						  	</label>
						</div>
		    		</div>
		    		<div class="col-sm-6">
		    			<div class="checkbox">
					  		<label>
					  			<?php 
										echo $this->Form->input('is_auto_code',array(
											'type' => 'checkbox',
											'label'=> false, 
											'div' => false,
										));
										echo __('Ada nomor seri ?');
								?>
						  	</label>
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
					'action' => 'index', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
</div>
<?php
		echo $this->Form->end();
?>