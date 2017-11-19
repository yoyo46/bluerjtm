<?php 
        $_url = !empty($_url)?$_url:'#';
        $linkOptions = !empty($linkOptions)?$linkOptions:array(
            'escape' => false, 
            'class'=> 'btn btn-default btn-sm',
        );
?>
<div class="form-group action">
    <?php
            echo $this->Form->button($this->Common->icon('search').__(' Cari'), array(
                'div' => false, 
                'class'=> 'btn btn-success btn-sm',
                'type' => 'submit',
            ));
            echo $this->Html->link($this->Common->icon('refresh').__(' Reset'), $_url, $linkOptions);
    ?>
</div>