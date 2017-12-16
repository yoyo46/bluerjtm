<?php
		$wrapperClass = isset($wrapper_class)?$wrapper_class:false;
		$inputID = isset($input_id)?$input_id:'document-id';
		$readonly = isset($readonly)?$readonly:true;
		$onchange = !empty($onchange)?$onchange:false;
		$attributes = !empty($attributes)?$attributes:array();
		$pickupTitle = isset($pickup_title)?$pickup_title:strip_tags($label);
		$attrBrowse = array(
            'class' => 'ajaxModal visible-xs browse-docs',
            'escape' => false,
            'data-action' => 'browse-form',
            'data-change' => $inputID,
            'title' => $pickupTitle,
        );
?>
<div class="form-group <?php echo $wrapperClass; ?>">
	<?php
			echo $this->Form->label($fieldName, $label.$this->Html->link($this->Common->icon('plus-square'), $dataUrl, $attrBrowse));
	?>
	<div class="row">
		<div class="col-sm-10">
			<?php
					echo $this->Common->buildInputForm($fieldName, false, array(
						'type' => 'text',
						'id' => $inputID,
						'placeholder' => $label,
						'readonly' => $readonly,
						'frameClass' => false,
						'attributes' => array_merge(array(
							'data-change' => $onchange,
						), $attributes),
					));
			?>
		</div>
		<div class="col-sm-2">
			<?php 
					$attrBrowse['class'] = 'btn bg-maroon ajaxCustomModal hidden-xs';
                    echo $this->Html->link($this->Common->icon('plus-square'), $dataUrl, $attrBrowse);
            ?>
		</div>
	</div>
</div>