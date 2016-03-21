<?php 
        $title = !empty($title)?$title:false;
        $_add = !empty($_add)?$_add:false;
?>
<div class="box-header">
    <?php 
            echo $this->Html->tag('h3', $title, array(
                'class' => 'box-title'
            ));


            if( !empty($_add) ) {
                echo $this->Html->tag('div', $this->Common->link(__('Tambah'), $_add, array(
                    'data-icon' => 'plus',
                    'class' => 'btn btn-app pull-right'
                )), array(
                    'class' => 'box-tools'
                ));
            }
    ?>
</div>