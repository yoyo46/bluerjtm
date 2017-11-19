<?php 
        $id = $this->Common->filterEmptyField($truck, 'Truck', 'id');
        $branch_id = $this->Common->filterEmptyField($truck, 'Truck', 'branch_id');
        
		$this->Html->addCrumb(__('Truk'), array(
			'controller' => 'trucks',
			'action' => 'index'
		));
	    $this->Html->addCrumb($sub_module_title);

	    echo $this->element('blocks/trucks/info_truck');
?>
<div class="form-group text-center action">
    <?php
            echo $this->Html->link(__('Edit'), array(
                'action' => 'edit', 
                $id,
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-primary btn-sm',
                'branch_id' => $branch_id,
            ));
            
            echo $this->Html->link(__('Kembali'), array(
                'action' => 'index', 
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm',
            ));
    ?>
</div>