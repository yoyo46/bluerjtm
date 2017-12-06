<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Pembelian'); ?></h3>
    </div>
    <div class="box-body">
        <!-- <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							// echo $this->Form->label('CoaSettingDetail.Asset.coa_id', __('Asset'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							// echo $this->Form->input('CoaSettingDetail.Asset.coa_id', array(
							// 	'label'=> false, 
							// 	'class'=>'form-control chosen-select',
							// 	'required' => false,
							// 	'empty' => __('Pilih COA'),
							// 	'options' => $coas,
							// ));
							// echo $this->Form->hidden('CoaSettingDetail.Asset.id');
					?>
				</div>
			</div>
        </div> -->
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CoaSettingDetail.HutangUsaha.coa_id', __('Hutang Pembelian'));
					?>
				</div>
				<div class="col-sm-4 col-sm-offset-4">
		        	<?php 
							echo $this->Form->input('CoaSettingDetail.HutangUsaha.coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
							echo $this->Form->hidden('CoaSettingDetail.HutangUsaha.id');
					?>
				</div>
			</div>
        </div>
        <!-- <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							// echo $this->Form->label('CoaSettingDetail.HutangProduct.coa_id', __('Hutang Pembelian Barang'));
					?>
				</div>
				<div class="col-sm-4 col-sm-offset-4">
		        	<?php 
							// echo $this->Form->input('CoaSettingDetail.HutangProduct.coa_id',array(
							// 	'label'=> false, 
							// 	'class'=>'form-control chosen-select',
							// 	'required' => false,
							// 	'empty' => __('Pilih COA'),
							// 	'options' => $coas,
							// ));
							// echo $this->Form->hidden('CoaSettingDetail.HutangProduct.id');
					?>
				</div>
			</div>
        </div> -->
    </div>
</div>