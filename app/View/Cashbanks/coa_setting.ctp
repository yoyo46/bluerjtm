<?php
		$this->Html->addCrumb(__('COA Setting'));

		echo $this->Form->create('CoaSetting', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Pengaturan COA'); ?></h3>
    </div>
    <div class="box-body">
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
								'class'=>'form-control',
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
								'class'=>'form-control',
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
							echo $this->Form->label('uang_jalan_void_coa_debit_id', __('Void Uang Jalan'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_jalan_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_jalan_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
								'class'=>'form-control',
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
								'class'=>'form-control',
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
							echo $this->Form->label('commission_void_coa_debit_id', __('Void Komisi'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('commission_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('commission_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
								'class'=>'form-control',
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
								'class'=>'form-control',
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
							echo $this->Form->label('uang_kuli_muat_void_coa_debit_id', __('Void Uang Kuli Muat'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kuli_muat_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kuli_muat_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
								'class'=>'form-control',
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
								'class'=>'form-control',
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
							echo $this->Form->label('uang_kuli_bongkar_void_coa_debit_id', __('Void Uang Kuli Bongkar'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kuli_bongkar_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kuli_bongkar_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
								'class'=>'form-control',
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
								'class'=>'form-control',
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
							echo $this->Form->label('asdp_void_coa_debit_id', __('Void Uang Penyebrangan'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('asdp_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('asdp_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
								'class'=>'form-control',
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
								'class'=>'form-control',
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
							echo $this->Form->label('uang_kawal_void_coa_debit_id', __('Void Uang Kawal'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kawal_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_kawal_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
								'class'=>'form-control',
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
								'class'=>'form-control',
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
							echo $this->Form->label('uang_keamanan_void_coa_debit_id', __('Void Uang Keamanan'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_keamanan_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('uang_keamanan_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('invoice_coa_debit_id', __('Invoice'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('invoice_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
								'class'=>'form-control',
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
							echo $this->Form->label('invoice_void_coa_debit_id', __('Void Invoice'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('invoice_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('invoice_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('pembayaran_invoice_coa_debit_id', __('Pembayaran Invoice'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('pembayaran_invoice_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('pembayaran_invoice_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('pembayaran_invoice_void_coa_debit_id', __('Void Pembayaran Invoice'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('pembayaran_invoice_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('pembayaran_invoice_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('lku_coa_debit_id', __('LKU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('lku_void_coa_debit_id', __('Void LKU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('lku_payment_coa_debit_id', __('Pembayaran LKU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_payment_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_payment_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('lku_payment_void_coa_debit_id', __('Void Pembayaran LKU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_payment_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_payment_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('ksu_coa_debit_id', __('KSU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ksu_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ksu_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('ksu_void_coa_debit_id', __('Void KSU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ksu_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ksu_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('ksu_payment_coa_debit_id', __('Pembayaran KSU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ksu_payment_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ksu_payment_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('ksu_payment_void_coa_debit_id', __('Void Pembayaran KSU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ksu_payment_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ksu_payment_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('kir_payment_coa_debit_id', __('Pembayaran KIR'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('kir_payment_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('kir_payment_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('kir_payment_void_coa_debit_id', __('Void Pembayaran KIR'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('kir_payment_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('kir_payment_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('siup_payment_coa_debit_id', __('Pembayaran Ijin Usaha'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('siup_payment_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('siup_payment_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('siup_payment_void_coa_debit_id', __('Void Pembayaran Ijin Usaha'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('siup_payment_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('siup_payment_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('stnk_payment_coa_debit_id', __('Pembayaran STNK'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('stnk_payment_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('stnk_payment_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
							echo $this->Form->label('stnk_payment_void_coa_debit_id', __('Void Pembayaran STNK'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('stnk_payment_void_coa_debit_id',array(
								'label'=> false, 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih COA'),
								'options' => $coas,
							));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('stnk_payment_void_coa_credit_id',array(
								'label'=> false, 
								'class'=>'form-control',
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
<div id="form-authorize" class="hide">
	<?php 
		echo $this->Form->input('CashBankAuthMaster.employe_id.', array(
			'label' => false,
			'empty' => __('Pilih Karyawan'),
			'options' => $employes,
			'class' => 'form-control cash-bank-auth-user',
			'div' => false
		));
	?>
</div>