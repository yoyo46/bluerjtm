<?php 
        $payment_id = !empty($payment_id)?$payment_id:false;
        $vendor_id = !empty($vendor_id)?$vendor_id:false;

        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'purchases',
                'action' => 'search',
                'po_documents',
                'admin' => false,
            ),
            'class' => 'ajax-form',
            'data-wrapper-write' => '#wrapper-modal-write',
        ));

        echo $this->Form->hidden('vendor_id', array(
            'value' => $vendor_id,
        ));
        echo $this->Form->hidden('payment_id', array(
            'value' => $payment_id,
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
                'controller' => 'purchases', 
                'action' => 'po_documents', 
                'payment_id' => $payment_id,
                'vendor_id' => $vendor_id,
                'admin' => false,
            ),
            'linkOptions' => array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxCustomModal',
                'title' => __('Daftar PO'),
            ),
        ));
        echo $this->Form->end();
?>