<?php 
        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'ajax',
                'action' => 'search',
                'quotation_products',
                'admin' => false,
            ),
            'class' => 'ajax-form',
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('code', __('Kode Barang'));
                echo $this->Common->buildInputForm('group', __('Grup Barang'), array(
                    'empty' => __('- Pilih Grup -'),
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
            '_url' => array(
                'controller' => 'ajax', 
                'action' => 'quotation_products', 
                'admin' => false,
            ),
            'linkOptions' => array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxCustomModal',
                'title' => __('Daftar Barang'),
            ),
        ));
        echo $this->Form->end();
?>