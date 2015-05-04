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
							echo $this->Form->label('cashbank_out_coa_debit_id', __('Kas Bank Keluar'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('cashbank_out_coa_debit_id',array(
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
							echo $this->Form->input('cashbank_out_coa_credit_id',array(
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
							echo $this->Form->label('cashbank_in_coa_debit_id', __('Kas Bank Masuk'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('cashbank_in_coa_debit_id',array(
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
							echo $this->Form->input('cashbank_in_coa_credit_id',array(
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
							echo $this->Form->label('ttuj_coa_debit_id', __('TTUJ'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ttuj_coa_debit_id',array(
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
							echo $this->Form->input('ttuj_coa_credit_id',array(
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
							echo $this->Form->label('lku_ksu_coa_debit_id', __('LKU/KSU'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('lku_ksu_coa_debit_id',array(
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
							echo $this->Form->input('lku_ksu_coa_credit_id',array(
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
							echo $this->Form->label('kir_coa_debit_id', __('KIR'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('kir_coa_debit_id',array(
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
							echo $this->Form->input('kir_coa_credit_id',array(
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
							echo $this->Form->label('siup_coa_debit_id', __('Ijin Usaha'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('siup_coa_debit_id',array(
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
							echo $this->Form->input('siup_coa_credit_id',array(
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
							echo $this->Form->label('laka_coa_debit_id', __('LAKA'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('laka_coa_debit_id',array(
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
							echo $this->Form->input('laka_coa_credit_id',array(
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
							echo $this->Form->label('ppn_coa_debit_id', __('PPN Masuk'));
					?>
				</div>
				<div class="col-sm-4">
		        	<?php 
							echo $this->Form->input('ppn_coa_debit_id',array(
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
							echo $this->Form->input('ppn_coa_credit_id',array(
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