<div class="form-group">
	<?php
			echo $this->Form->label('CashBank.document_id', __('No Doc *'));
	?>
	<div class="row">
		<div class="col-sm-10">
			<?php
					echo $this->Form->input('CashBank.document_id',array(
						'label'=> false, 
						'class'=>'form-control',
						'required' => false,
						'options' => $docs,
						'empty' => __('Pilih Cash Bank'),
						'id' => 'document-id',
					));
			?>
		</div>
		<div class="col-sm-2">
			<?php 
					$attrBrowse = array(
                        'class' => 'ajaxModal visible-xs',
                        'escape' => false,
                        'data-action' => 'browse-form',
                        'data-change' => 'document-id',
                    );
					$urlBrowse = array(
                        'controller'=> 'ajax', 
                        'action' => 'getCashBankPpnRevenue'
                    );
					$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                    echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
            ?>
		</div>
	</div>
</div>
<?php 
		echo $this->Form->input('CashBank.document_type',array(
			'label'=> false, 
			'class'=>'form-control',
			'required' => false,
			'type' => 'hidden',
			'id' => 'document-type'
		));
?>