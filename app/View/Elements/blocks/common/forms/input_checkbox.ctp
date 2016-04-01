<?php 
		$label = !empty($label)?$label:false;
		$fieldName = !empty($fieldName)?$fieldName:false;
?>
<div class="form-group">
    <div class="checkbox aset-handling">
        <label>
            <?php 
					echo $this->Form->checkbox($fieldName,array(
						'label'=> false, 
						'required' => false,
						'class' => 'aset-handling-form',
					)).$label;
			?>
        </label>
    </div>
</div>