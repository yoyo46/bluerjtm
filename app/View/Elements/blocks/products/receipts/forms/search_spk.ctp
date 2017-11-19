<?php 
        $payment_id = !empty($payment_id)?$payment_id:false;
        $vendor_id = !empty($vendor_id)?$vendor_id:false;
        $type = !empty($type)?$type:'spk';

        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'products',
                'action' => 'search',
                'receipt_documents',
                $type,
                'admin' => false,
            ),
            'class' => 'ajax-form',
            'data-wrapper-write' => '#wrapper-modal-write',
        ));

        echo $this->Form->hidden('vendor_id', array(
            'value' => $vendor_id,
        ));
        echo $this->Form->hidden('receipt_id', array(
            'value' => $receipt_id,
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('nodoc', __('No Dokumen'));
        ?>
    </div>
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                    'textGroup' => $this->Common->icon('calendar'),
                    'positionGroup' => 'positionGroup',
                    'class' => 'form-control pull-right date-range',
                ));
        ?>
    </div>
</div>
<?php 
        echo $this->element('blocks/common/searchs/box_action', array(
            '_url' => array(
                'controller' => 'products', 
                'action' => 'receipt_documents', 
                $type,
                'receipt_id' => $receipt_id,
                'vendor_id' => $vendor_id,
                'admin' => false,
            ),
            'linkOptions' => array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxCustomModal',
                'title' => __('Daftar SPK Internal'),
            ),
        ));
        echo $this->Form->end();
?>