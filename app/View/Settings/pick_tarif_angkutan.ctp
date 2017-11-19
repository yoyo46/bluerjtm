<?php 
        echo $this->Form->create('TarifAngkutan', array(
            'url'=> $this->Html->url( array(
                'controller' => 'settings',
                'action' => 'download_tarif_angkutan',
                'admin' => false,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="form-group text-center">
    <?php 
            echo $this->Common->getCheckboxBranch();
    ?>
</div>
<div class="form-group text-center">
    <?php
            echo $this->Form->button(__('Download'), array(
                'div' => false, 
                'class'=> 'btn btn-success btn-sm',
                'type' => 'submit',
            ));
    ?>
</div>
<?php 
        echo $this->Form->end();
?>  