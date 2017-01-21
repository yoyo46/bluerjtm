<?php 
        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'products',
                'action' => 'search',
                'expenditure_documents',
                'admin' => false,
            ),
            'class' => 'ajax-form',
            'data-wrapper-write' => '#wrapper-modal-write',
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('nodoc', __('No Dokumen'));
                echo $this->Common->buildInputForm('document_type', __('Jenis'), array(
                    'empty' => __('Pilih Jenis SPK'),
                    'options' => array(
                        'internal' => __('Internal'),
                        'wht' => __('WHT'),
                    ),
                ));
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
                'action' => 'expenditure_documents', 
                'admin' => false,
            ),
            'linkOptions' => array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxCustomModal',
                'title' => __('Daftar SPK'),
            ),
        ));
        echo $this->Form->end();
?>