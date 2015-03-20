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
					echo $this->Html->tag('dd', sprintf('%s %s', $coa['Coa']['code'], $coa['Coa']['name']));
					echo '</dl>';
				}
		?>
		<div class="form-group">
			<?php 
					echo $this->Form->label('code', __('Kode COA'));
			?>
			<div class="input-group">
				<?php
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

				echo $this->Html->tag('div', $this->Form->input('name_en',array(
					'label'=> __('Nama COA English'), 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Nama COA')
				)), array(
					'class' => 'form-group'
				));

				if(!empty($coa['Coa']['level']) && $coa['Coa']['level'] == 3){
					echo $this->Html->tag('div', $this->Form->input('balance', array(
						'label'=> __('Balance *'), 
						'class'=>'form-control input_price',
						'required' => false,
						'placeholder' => __('Balance'),
						'type' => 'text'
					)), array(
						'class' => 'form-group'
					));

					echo $this->Html->tag('div', $this->Form->input('type', array(
						'label'=> __('Tipe COA'), 
						'class'=>'form-control',
						'required' => false,
						'empty' => __('Pilih Tipe COA'),
						'options' => array(
							'debit' => __('Debit'),
							'credit' => __('Credit'),
						)
					)), array(
						'class' => 'form-group'
					));
		?>
			    	<div class="form-group">
			    		<div class="checkbox">
			                <label>
			                	<?php 
			                		echo $this->Form->checkbox('is_cash_bank').' Termasuk Kas Bank?';
			                	?>
			                </label>
			            </div>
			    	</div>
		<?php
				}
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