<?php
		$this->Html->addCrumb(__('COA Setting'));

		echo $this->Form->create('CoaSetting', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Uang Jalan'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Html->tag('label', __('Module'));
					?>
				</div>
				<div class="col-sm-4 text-center">
		        	<?php 
							echo $this->Html->tag('label', __('Debit'));
					?>
				</div>
				<div class="col-sm-4 text-center">
		        	<?php 
							echo $this->Html->tag('label', __('Credit'));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('uang_jalan_coa_debit_id', __('Uang Jalan'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_jalan_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_jalan_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('commission_coa_debit_id', __('Komisi'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('commission_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('commission_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('uang_kuli_muat_coa_debit_id', __('Uang Kuli Muat'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kuli_muat_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kuli_muat_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('uang_kuli_bongkar_coa_debit_id', __('Uang Kuli Bongkar'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kuli_bongkar_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kuli_bongkar_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('asdp_coa_debit_id', __('Uang Penyebrangan'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('asdp_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('asdp_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('uang_kawal_coa_debit_id', __('Uang Kawal'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kawal_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kawal_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('uang_keamanan_coa_debit_id', __('Uang Keamanan'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_keamanan_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_keamanan_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('TTUJ'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('revenue_coa_debit_id', __('Revenue'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('revenue_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('revenue_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('ppn_coa_debit_id', __('PPN'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ppn_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ppn_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('pph_coa_debit_id', __('PPh'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('pph_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('pph_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('invoice_coa_debit_id', __('Invoice 1'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('invoice_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('invoice_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('invoice_coa_debit_id', __('Invoice 2'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('invoice_coa_2_debit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('invoice_coa_2_credit_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('pembayaran_invoice_coa_id', __('Pembayaran Invoice'));
					?>
				</div>
				<div class="col-sm-4 col-sm-offset-4">
		        	<?php 
							echo $this->Form->input('pembayaran_invoice_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('lku_payment_coa_id', __('Pembayaran LKU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_payment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('ksu_payment_coa_id', __('Pembayaran KSU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ksu_payment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('uang_Jalan_commission_payment_coa_id', __('Pembayaran Uang Jalan/Komisi'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_Jalan_commission_payment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('biaya_ttuj_payment_coa_id', __('Pembayaran Biaya TTUJ'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('biaya_ttuj_payment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Dokumen'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('kir_payment_coa_id', __('Pembayaran KIR'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('kir_payment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('siup_payment_coa_id', __('Pembayaran Ijin Usaha'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('siup_payment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('stnk_payment_coa_id', __('Pembayaran STNK'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('stnk_payment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('stnk_payment_coa_id', __('Pembayaran Denda'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('document_denda_payment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('stnk_payment_coa_id', __('Pembayaran Biaya Lain-lain'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('document_other_payment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Leasing'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('leasing_installment_coa_id', __('Angsuran Pokok'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('leasing_installment_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('leasing_installment_rate_coa_id', __('Angsuran Bunga'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('leasing_installment_rate_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
			</div>
        </div>
        <div class="form-group">
			<div class="row">
				<div class="col-sm-4">
			    	<?php 
							echo $this->Form->label('leasing_denda_coa_id', __('Denda'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('leasing_denda_coa_id',array(
								'label'=> false, 
								'class'=>'form-control chosen-select',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
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