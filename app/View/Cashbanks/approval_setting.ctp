<?php
		$this->Html->addCrumb(__('Approval Setting'));

		echo $this->Form->create('CashBank', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Otorisasi Kas Bank'); ?></h3>
    </div>
    <div class="box-body table-responsive">
    	<div class="form-group action">
    		<?php 
	                echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Tambah Otorisasi'), 'javascript:', array(
	                	'escape' => false,
	                	'class' => 'btn btn-success add-custom-field',
	                	'action_type' => 'auth-cash-bank'
	                ));

	                echo $this->Html->link('<i class="fa fa-times-circle"></i> '.__('Hapus'), 'javascript:', array(
	                	'escape' => false,
	                	'class' => 'btn btn-danger delete-custom-field',
	                	'action_type' => 'auth-cash-bank'
	                ));
	        ?>
    	</div>
        <table class="table table-hover">
        	<thead>
        		<tr>
        			<th width="30%"><?php echo __('Nama');?></th>
                    <th width="30%"><?php echo __('Grup');?></th>
                    <th width="30%"><?php echo __('level Otorisasi');?></th>
        		</tr>
        	</thead>
        	<tbody class="cashbanks-auth-table">
        		<?php
        			$count = 1;
        			if(!empty($auth_data['CashBankAuthMaster'])){
        				$count = count($auth_data['CashBankAuthMaster']);
        			}

        			for ($i=1; $i <= $count; $i++) { 
        				$id = $i-1;
        		?>
	            <tr class="cash-auth-row" id="cash-auth-<?php echo $i?>" rel="<?php echo $i?>">
	                <td>
	                	<div class="row">
	                		<div class="col-sm-10">
	                			<?php 
			                		echo $this->Form->input('CashBankAuthMaster.employe_id.', array(
			                			'label' => false,
			                			'empty' => __('Pilih Karyawan'),
			                			'options' => $employes,
			                			'class' => 'form-control cash-bank-auth-user',
			                			'div' => false,
			                			'value' => (isset($auth_data) && !empty($auth_data['CashBankAuthMaster'][$id]['employe_id'])) ? $auth_data['CashBankAuthMaster'][$id]['employe_id'] : ''
			                		));

			                		echo $this->Form->input('CashBankAuthMaster.id.', array(
			                			'type' => 'hidden',
			                			'value' => (isset($auth_data) && !empty($auth_data['CashBankAuthMaster'][$id]['id'])) ? $auth_data['CashBankAuthMaster'][$id]['id'] : $i
			                		));
			                	?>
	                		</div>
	                		<div class="col-sm-2">
	                			<?php 
										$attrBrowse = array(
			                                'class' => 'ajaxModal visible-xs',
			                                'escape' => false,
			                                'title' => __('Data Karyawan'),
			                                'data-action' => 'browse-form',
			                                'data-change' => 'cash-bank-auth-user',
			                            );
			        					$urlBrowse = array(
			                                'controller'=> 'ajax', 
			                                'action' => 'getUserEmploye',
			                                $i
			                            );
										$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
			                            echo $this->Html->link('<i class="fa fa-search"></i>', $urlBrowse, $attrBrowse);
			                    ?>
	                		</div>
	                	</div>
	                </td>
	                <td align="center" class="group_auth">
	                	<?php
	                		if(isset($auth_data) && !empty($auth_data['CashBankAuthMaster'][$id]['group'])){
	                			echo $auth_data['CashBankAuthMaster'][$id]['group'];
	                		}else{
	                			echo '-';
	                		}
	                	?>
	                </td>
	                <td align="center">
	                	<?php
	                		if(isset($auth_data) && !empty($auth_data['CashBankAuthMaster'][$id]['level'])){
	                			echo $auth_data['CashBankAuthMaster'][$id]['level'];
	                		}else{
	                			echo '1';
	                		}
	                	?>
	                </td>
	            </tr>
	            <?php
        			}
        		?>
        	</tbody>
    	</table>
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