<?php 
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'settings',
                'action' => 'download_uang_jalan',
                'admin' => false,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
            'id' => 'form-search',
        ));
?>
<div class="form-group text-center">
    <?php 
            echo $this->Common->getCheckboxBranch('Search');
    ?>
</div>
<div class="form-group text-center">
    <?php
            echo $this->Html->link(__('Download'), array(
                'controller' => 'reports',
                'action' => 'generate_excel',
                'uang_jalan',
                'admin' => false,
            ), array(
                'class'=> 'btn btn-success btn-sm ajax-link',
                'data-form' => '#form-search',
                'data-wrapper-write' => '.wrapper-download',
                'data-request' => '#form-search',
                'data-modal-close' => 'true',
            ));
    ?>
</div>
<?php 
        echo $this->Form->end();
?>  