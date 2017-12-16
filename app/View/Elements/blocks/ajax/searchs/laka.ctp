<?php 
        $data_action = !empty($data_action)?$data_action:false;
        $payment_id = !empty($payment_id)?$payment_id:false;
        $title = !empty($title)?$title:__('LAKA');
        
        $urlForm = !empty($urlForm)?$urlForm:false;
        $urlReset = !empty($urlReset)?$urlReset:false;

        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url($urlForm), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
            'class' => 'ajax-form',
            'data-wrapper-write' => '#wrapper-modal-write',
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date',array(
                        'label'=> __('Tanggal Berakhir'),
                        'class'=>'form-control date-range',
                        'required' => false,
                        'placeholder' => __('Tanggal')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->label('type', __('Truk'));
            ?>
            <div class="row">
                <div class="col-sm-4">
                    <?php 
                            echo $this->Form->input('type',array(
                                'label'=> false,
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => false,
                                'options' => array(
                                    '1' => __('Nopol'),
                                    '2' => __('ID Truk'),
                                ),
                            ));
                    ?>
                </div>
                <div class="col-sm-8">
                    <?php 
                            echo $this->Form->input('nopol',array(
                                'label'=> false,
                                'class'=>'form-control on-focus',
                                'required' => false,
                            ));
                    ?>
                </div>
            </div>
        </div>
        <?php
                echo $this->element('blocks/common/searchs/box_action', array(
                    '_url' => $urlReset,
                    'linkOptions' => array(
                        'escape' => false, 
                        'class'=> 'btn btn-default btn-sm ajaxCustomModal',
                        'title' => $title,
                    ),
                ));
        ?>
    </div>
</div>
<?php 
    echo $this->Form->end();
?>