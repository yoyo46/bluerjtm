modelName<?php
		$this->Html->addCrumb(__('Pengaturan Cost Center'));

		echo $this->Form->create('CogsSetting', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Pengaturan Cost Center'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.Cashbank.cogs_id', __('Kas/Bank'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.Cashbank.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.Cashbank.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.Revenue.cogs_id', __('Revenue'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.Revenue.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.Revenue.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.GeneralLedger.cogs_id', __('Jurnal Umum'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.GeneralLedger.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.GeneralLedger.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.DocumentPayment.cogs_id', __('Pembayaran Surat-Surat Truk'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.DocumentPayment.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.DocumentPayment.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.InsurancePayment.cogs_id', __('Pembayaran Asuransi'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.InsurancePayment.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.InsurancePayment.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.InvoicePayment.cogs_id', __('Pembayaran invoice'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.InvoicePayment.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.InvoicePayment.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.LkuKsuPayment.cogs_id', __('Pembayaran LKU / KSU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.LkuKsuPayment.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.LkuKsuPayment.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.LakaPayment.cogs_id', __('Pembayaran LAKA'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.LakaPayment.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.LakaPayment.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.LeasingPayment.cogs_id', __('Pembayaran Leasing'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.LeasingPayment.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.LeasingPayment.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.PurchaseOrderPayment.cogs_id', __('Pembayaran PO'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.PurchaseOrderPayment.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.PurchaseOrderPayment.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.PurchaseOrderAsset.cogs_id', __('Pembayaran PO Asset'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.PurchaseOrderAsset.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.PurchaseOrderAsset.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.AssetSell.cogs_id', __('Penjualan Asset'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.AssetSell.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.AssetSell.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.TtujPayment.cogs_id', __('Pembayaran Uang Jalan/Komisi'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.TtujPayment.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.TtujPayment.id');
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('CogsSetting.TtujPaymentCost.cogs_id', __('Pembayaran Biaya TTUJ'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('CogsSetting.TtujPaymentCost.cogs_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih Cost Center'),
								'options' => $cogs,
							));
							echo $this->Form->hidden('CogsSetting.TtujPaymentCost.id');
					?>
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
	?>
</div>
<?php
	echo $this->Form->end();
?>