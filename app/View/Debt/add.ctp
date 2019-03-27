<?php
		$this->Html->addCrumb($module_title, array(
			'controller' => 'debt',
			'action' => 'index'
		));
		$this->Html->addCrumb($sub_module_title);
		$value = !empty($value)?$value:false;

		echo $this->Form->create('Debt', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <div class="box-body">
		<div class="form-group">
			<?php
					echo $this->Form->input('nodoc',array(
						'label'=> __('No. Dokumen *'), 
						'class'=>'form-control on-focus',
						'required' => false,
						'placeholder' => __('No. Dokumen'),
					));
			?>
		</div>
		<?php
				echo $this->Html->tag('div', $this->Form->input('coa_id',array(
					'label'=> __('Account Kas/Bank *'), 
					'class'=>'form-control chosen-select',
					'required' => false,
					'empty' => __('Pilih Kas/Bank '),
					'options' => $coas
				)), array(
					'class' => 'form-group'
				));
				echo $this->element('blocks/common/forms/cost_center');
				echo $this->Html->tag('div', $this->Form->input('transaction_date',array(
					'label'=> __('Tanggal *'), 
					'class'=>'form-control custom-date',
					'required' => false,
					'placeholder' => __('Tanggal'),
					'type' => 'text',
					'value' => (!empty($this->request->data['CashBank']['transaction_date'])) ? $this->request->data['CashBank']['transaction_date'] : date('d/m/Y')
				)), array(
					'class' => 'form-group'
				));

				echo $this->Html->tag('div', $this->Form->input('note',array(
					'type' => 'textarea',
					'label'=> __('Keterangan'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Keterangan'),
				)), array(
					'class' => 'form-group'
				));
		?>
		<div class="form-group">
        	<?php 
        			$attrBrowse = array(
                        'class' => 'ajaxModal visible-xs browse-docs',
                        'escape' => false,
                        'title' => __('List Karyawan'),
                        'data-action' => 'browse-cash-banks',
                        'data-change' => 'cashbanks-info-table',
                        'url' => $this->Html->url( array(
                            'action' => 'users',
                            'bypass' => true,
                        ))
                    );
					$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                    echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Pilih Karyawan'), 'javascript:', $attrBrowse);
            ?>
        </div>
    </div>
</div>

<div class="cashbank-info-detail <?php echo (!empty($this->request->data['DebtDetail'])) ? '' : 'hide';?>">
	<div class="box">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Detail Info Karyawan'); ?></h3>
	    </div>
	    <div class="box-body table-responsive">
	        <table class="table table-hover cashbanks-info-table">
	        	<thead>
	        		<tr>
                    	<?php 
	                    		echo $this->Html->tag('th', __('Karyawan'));
	                    		echo $this->Html->tag('th', __('Kategori'));
	                    		echo $this->Html->tag('th', __('Ket.'));
	                    		echo $this->Html->tag('th', __('Total'), array(
	                    			'width' => '30%'
	                    		));
	                    		echo $this->Html->tag('th', '');
                    	?>
	        		</tr>
	        	</thead>
                <?php
		    		echo $this->element('blocks/debt/info_detail');
		    	?>
	    	</table>
	    </div>
	</div>
</div>

<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'controller' => 'debt', 
				'action' => 'index', 
			), array(
				'class'=> 'btn btn-default',
			));
			$this->Common->_getButtonPostingUnposting( $value, 'CashBank', array( 'Commit', 'Draft' ) );
	?>
</div>
<?php
		echo $this->Form->end();
?>