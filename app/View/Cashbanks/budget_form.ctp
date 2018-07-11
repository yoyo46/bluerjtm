<?php
		$this->Html->addCrumb(__('Budget'), array(
			'action' => 'budgets'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('Budget', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
    <div class="box-body">
        <div class="form-group">
        	<?php 
					echo $this->Form->input('coa_id',array(
						'label'=> __('COA'), 
						'class'=>'form-control chosen-select',
						'required' => false,
						'empty' => __('Pilih COA')
					));
			?>
        </div>
        <div class="form-group">
        	<?php 
        			echo $this->Form->label('year', __('Tahun'));
                    echo $this->Form->year('year', 1949, date('Y') + 5, array(
                        'label'=> false, 
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => false,
                        'name' => 'data[Budget][year]',
                        'empty' => __('Pilih Tahun'),
                    ));
            ?>
        </div>
        <div id="box-field-input">
            <div class="row">
            	<?php 
                        $rel = 0;
                        for ($i=1; $i <= 12; $i++) { 
                            $currentMonth = sprintf("%02s", $i);
                            echo $this->element('blocks/cashbanks/budget_month', array(
                                'rel' => $rel,
                                'month' => $currentMonth,
                            ));
                            $rel++;
                        }
    	        ?>
            </div>
        </div>
    </div>

    <div class="box-footer text-center action">
    	<?php
	    		echo $this->Form->button(__('Simpan'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    		echo $this->Html->link(__('Kembali'), array(
					'action' => 'budgets', 
				), array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>
<div class="hide">
	<div id="target_unit">
		<?php
                echo $this->Form->month('month', array(
                    'label'=> false, 
                    'class'=>'form-control',
                    'required' => false,
                    'empty' => false,
                    'name' => 'data[Budget][month]',
                    'empty' => __('Pilih Bulan'),
                ));
        ?>
	</div>
</div>