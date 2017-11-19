<?php 
        $urlForm = array(
            'controller' => 'products',
            'action' => 'search',
            'stocks',
            $id,
            'admin' => false,
        );
        $urlReset = array(
            'controller' => 'products', 
            'action' => 'stocks', 
            $id,
            'admin' => false,
        );

        echo $this->Form->create('Search', array(
            'url' => $urlForm,
            'class' => 'ajax-form',
            'data-wrapper-write' => '#wrapper-modal-write',
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('serial_number', __('Serial Number'));
        ?>
    </div>
</div>
<?php 
        echo $this->element('blocks/common/searchs/box_action', array(
            '_url' => $urlReset,
            'linkOptions' => array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxCustomModal',
                'title' => __('Stok Barang'),
            ),
        ));
        echo $this->Form->end();
?>