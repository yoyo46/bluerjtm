<?php 
		$title = !empty($title)?$title:'text';
		$fieldName = !empty($fieldName)?$fieldName:false;
		$inputClass = !empty($inputClass)?$inputClass:false;
		$icon = !empty($icon)?$icon:false;
		$groupText = !empty($groupText)?$groupText:false;
		$options = !empty($options)?$options:array();
		$position = !empty($position)?$position:'right';
?>
<div class="form-group">
    <?php 
            echo $this->Form->label($fieldName, $title);
    ?>
    <div class="input-group">
    	<?php 
    			if( !empty($icon) ) {
    				$groupText = $this->Common->icon($icon);
    			}

           		echo $this->Html->tag('div', $groupText, array(
           			'class' => 'input-group-addon',
       			));
                echo $this->Form->input($fieldName, array_merge($options, array(
                    'label'=> false,
                    'required' => false,
                    'class' => sprintf('form-control pull-%s %s', $position, $inputClass),
                )));
        ?>
    </div>
</div>