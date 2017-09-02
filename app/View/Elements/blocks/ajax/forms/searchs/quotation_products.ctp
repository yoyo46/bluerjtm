<?php 
        $data = $this->request->data;

        $action_type = !empty($action_type)?$action_type:false;
        $vendor_id = !empty($vendor_id)?$vendor_id:false;
        $data_type = !empty($type)?$type:false;
        $document_type = Common::hashEmptyField($data, 'Spk.document_type');
        
        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'ajax',
                'action' => 'search',
                'products',
                'action_type' => $action_type,
                'vendor_id' => $vendor_id,
                'type' => $data_type,
                'document_type' => $document_type,
                'admin' => false,
            ),
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
                'action' => 'products', 
                'action_type' => $action_type,
                'vendor_id' => $vendor_id,
                'type' => $data_type,
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