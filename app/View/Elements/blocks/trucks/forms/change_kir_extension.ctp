<div class="wrapper-kir-extension">
	<div class="form-group">
		<?php 
				echo $this->Form->input('Kir.to_date', array(
					'label'=> __('Diperpanjang Hingga'), 
					'class'=>'form-control',
					'type' => 'text',
					'required' => false,
					'readonly' => true,
				));
		?>
	</div>
</div>