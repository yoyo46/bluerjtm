<?php 
        echo $this->Form->create('Search', array(
            'url' => array(
                'controller' => 'ajax',
                'action' => 'search',
                'supplier_quotations',
                'admin' => false,
            ),
            'class' => 'ajax-form',
            'data-wrapper-write' => '#wrapper-modal-write',
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('nodoc', __('No Penawaran'));
        ?>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->label('datettuj', __('Tgl SQ'));
            ?>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <?php 
                        echo $this->Form->input('date',array(
                            'label'=> false,
                            'class'=>'form-control pull-right date-range',
                            'required' => false,
                            'autocomplete'=> 'off', 
                        ));
                ?>
            </div>
        </div>
    </div>
</div>
<?php 
        echo $this->element('blocks/common/searchs/box_action', array(
            '_url' => array(
                'controller' => 'ajax', 
                'action' => 'supplier_quotations', 
                !empty($vendor_id)?$vendor_id:false,
                'admin' => false,
            ),
            'linkOptions' => array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxCustomModal',
                'title' => __('Data Penawaran Supplier'),
            ),
        ));
        echo $this->Form->end();
?>