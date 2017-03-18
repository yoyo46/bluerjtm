<?php
		$this->Html->addCrumb(__('Vendor'), array(
			'action' => 'vendors'
		));
		$this->Html->addCrumb($sub_module_title);

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
        			printf(__('Upload By Excel ( %s )'), $this->Html->link($this->Html->tag('small', __('Download Template')), array(
						'action' => 'vendor_import',
						'download'
					), array(
						'class' => 'download-template',
						'escape' => false,
					)))
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