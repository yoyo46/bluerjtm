<?php 
        $nodoc = !empty($nodoc)?$nodoc:false;
        $transaction_id = !empty($transaction_id)?$transaction_id:0;
        $urlForm = !empty($urlForm)?$urlForm:array(
            'controller' => 'products',
            'action' => 'search',
            'spk_products',
            $transaction_id,
            $nodoc,
            'admin' => false,
        );
        $urlReset = !empty($urlReset)?$urlReset:array(
            'controller' => 'products', 
            'action' => 'spk_products', 
            $transaction_id,
            $nodoc,
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
                echo $this->Common->buildInputForm('code', __('Kode Barang'), array(
                    'class'=>'form-control on-focus',
                ));
                echo $this->Common->buildInputForm('group', __('Grup Barang'), array(
                    'class'=>'form-control chosen-select',
                    'options' => $productCategories,
                    'empty' => __('Pilih Group'),
                    'frameClass' => 'select-block form-group',
                ));
        ?>
    </div>
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('name', __('Nama Barang'));
        ?>
    </div>
</div>
<?php 
        echo $this->element('blocks/common/searchs/box_action', array(
            '_url' => $urlReset,
            'linkOptions' => array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxCustomModal',
                'title' => __('Daftar Barang'),
            ),
        ));
        echo $this->Form->end();
?>