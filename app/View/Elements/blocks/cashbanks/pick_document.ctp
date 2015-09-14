<?php 
		$document_type = !empty($this->request->data['CashBank']['document_type'])?$this->request->data['CashBank']['document_type']:false;
?>
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
                        'class' => 'ajaxModal visible-xs browse-docs',
                        'escape' => false,
                        'data-action' => 'browse-form',
                        'data-change' => 'document-id',
                    );
					$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                    echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowseDocument, $attrBrowse);
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