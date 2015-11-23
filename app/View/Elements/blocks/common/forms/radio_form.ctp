<?php 
        $label = !empty($label)?$label:false;
        $labelClass = !empty($labelClass)?$labelClass:false;
        $options = !empty($options)?$options:false;

        $frameClass = !empty($frameClass)?$frameClass:'radio-line';
        $divClass = !empty($divClass)?$divClass:'';
        $class = isset($class)?$class:false;
        $fieldName = !empty($fieldName)?$fieldName:false;
        $default = !empty($default)?$default:false;
?>
<div class="form-group">
	<?php 
            if( !empty($label) ) {
                echo $this->Form->label($fieldName, $label, array(
                    'class' => $labelClass,
                ));
            }
	?>
	<ul class="<?php echo $frameClass; ?>">
		<li class="<?php echo $divClass; ?>">
			<label>
	    		<?php 
	    				echo $this->Form->radio($fieldName, $options, array(
	    					'legend' => false,
						    'separator' => '</label></li><li class="'.$divClass.'"><label>',
						    'class' => $class,
						    'default' => $default,
						));
	    		?>
			</label>
		</li>
	</ul>
</div>