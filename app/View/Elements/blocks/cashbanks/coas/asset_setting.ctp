<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Asuransi'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CoaSettingDetail.Asuransi.coa_id', __('Pembayaran Asuransi'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CoaSettingDetail.Asuransi.coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
							echo $this->Form->hidden('CoaSettingDetail.Asuransi.id');
					?>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Pembelian'); ?></h3>
    </div>
    <div class="box-body">
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
    </div>
</div>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Hutang Karyawan'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CoaSettingDetail.Debt.coa_id', __('Hutang Karyawan'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CoaSettingDetail.Debt.coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
							echo $this->Form->hidden('CoaSettingDetail.Debt.id');
					?>
				</div>
			</div>
        </div>
    </div>
</div>