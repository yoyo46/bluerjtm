<?php
		$this->Html->addCrumb(__('COA'), array(
			'controller' => 'settings',
			'action' => 'coas'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Coa', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
    	<?php 
    			if( !empty($coa) ) {
    				echo '<dl>';
    				echo $this->Html->tag('dt', __('Parent COA'));
					echo $this->Html->tag('dd', sprintf('%s - %s', $coa['Coa']['code'], $coa['Coa']['name']));
					echo '</dl>';
				}
		?>
		<div class="form-group">
			<?php 
					echo $this->Form->label('code', __('Kode COA *'));
			?>
			<div class="input-group">
				<?php
    					if( !empty($coa) ) {
							echo $this->Html->tag('span', sprintf('%s -', $coa['Coa']['code']), array(
								'class' => 'input-group-addon'
							));
						}
						echo $this->Form->input('code',array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('Kode COA')
						));
				?>
			</div>
		</div>
		<?php
				echo $this->Html->tag('div', $this->Form->input('name',array(
					'label'=> __('Nama COA *'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama COA')
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
					'action' => 'coas', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>