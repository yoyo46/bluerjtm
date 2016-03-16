<?php
		echo $this->Form->create('Import', array(
			'url'=> $this->Html->url( null, true ), 
			'type' => 'file',
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">
        	<?php
        			echo __('Upload Journal By Excel');
			?>
    	</h3>
    </div>
    <div class="box-body">
    	<?php 
				echo $this->Html->tag('div', $this->Form->input('importdata',array(
					'label'=> __('Upload *'), 
					'class'=>'form-control',
					'required' => false,
					'type' => 'file',
				)), array(
					'class' => 'form-group'
				));
		?>
		<div class="box-footer text-center action">
			<?php
		    		echo $this->Form->button(__('Import'), array(
						'div' => false, 
						'class'=> 'btn btn-success',
						'type' => 'submit',
					));
			?>
		</div>
    </div>
</div>
<?php
		echo $this->Form->end();
?>