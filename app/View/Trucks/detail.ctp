<?php 
		$this->Html->addCrumb(__('Truk'), array(
			'controller' => 'trucks',
			'action' => 'index'
		));
	    $this->Html->addCrumb($sub_module_title);
	    echo $this->element('blocks/trucks/info_truck');
?>
<div class="form-group text-center action">
    <?php
            if( in_array('update_trucks', $allowModule) ) {
                echo $this->Common->rule_link(__('Rubah'), array(
                    'action' => 'edit', 
                    $truck['Truck']['id'],
                ), array(
                    'escape' => false, 
                    'class'=> 'btn btn-primary btn-sm',
                ));
            }
            
            echo $this->Common->rule_link(__('Kembali'), array(
                'action' => 'index', 
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm',
            ));
    ?>
</div>