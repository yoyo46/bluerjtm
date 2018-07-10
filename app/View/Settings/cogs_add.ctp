<?php
		$this->Html->addCrumb(__('Cost Center'), array(
			'controller' => 'settings',
			'action' => 'cogs'
		));
		$this->Html->addCrumb($sub_module_title);
		$cogsCode = '';
		if(!empty($value['Cogs']['code'])){
			$cogsCode = $value['Cogs']['code'];
		}
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Cogs', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
		<div class="form-group">
			<?php 
					echo $this->Form->label('code', __('Kode *'));
			?>
			<div class="row">
				<div class="col-sm-4">
					<div class="input-group">
						<?php 
								echo $this->Form->input('code',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
								));
						?>
		            </div>
				</div>
			</div>
		</div>
		<?php
				echo $this->Html->tag('div', $this->Form->input('name',array(
					'label'=> __('Nama Cost Center *'), 
					'class'=>'form-control',
					'required' => false,
				)), array(
					'class' => 'form-group'
				));
				echo $this->Html->tag('div', $this->Form->input('note',array(
					'label'=> __('Keterangan'), 
					'class'=>'form-control',
					'required' => false,
				)), array(
					'class' => 'form-group'
				));
		?>
    </div>

    <div class="box-footer text-center action">
    	<?php
	    		echo $this->Form->button(__('Simpan'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    		echo $this->Html->link(__('Kembali'), array(
					'action' => 'cogs', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>