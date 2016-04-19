<?php
		$readonly = isset($readonly)?$readonly:true;
		$onchange = !empty($onchange)?$onchange:false;
?>
<div class="form-group">
	<?php
			echo $this->Form->label($fieldName, $label);
	?>
	<div class="row">
		<div class="col-sm-10">
			<?php
					echo $this->Common->buildInputForm($fieldName, false, array(
						'type' => 'text',
						'id' => 'document-id',
						'placeholder' => $label,
						'readonly' => $readonly,
						'frameClass' => false,
						'attributes' => array(
							'data-change' => $onchange,
						),
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
                        'title' => $label,
                    );
					$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                    echo $this->Html->link($this->Common->icon('plus-square'), $dataUrl, $attrBrowse);
            ?>
		</div>
	</div>
</div>