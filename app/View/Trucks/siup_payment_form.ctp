<?php
        $this->Html->addCrumb('Pembayaran SIUP', array(
            'controller' => 'trucks',
            'action' => 'siup_payments'
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
        echo $this->Form->create('SiupPayment', array(
            'url'=> $this->Html->url( null, true ), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
    ?>
    <div class="box-body">
        <div class="form-group">
            <?php 
                echo $this->Form->input('siup_id', array(
                    'label'=> __('No. Pol *'), 
                    'class'=>'form-control change-link',
                    'required' => false,
                    'empty' => __('Pilih No. Pol'),
                    'url' => $this->Html->url(array(
                        'controller' => 'trucks',
                        'action' => 'siup_payment_add',
                    )),
                ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('from_date', array(
                        'label'=> __('Tgl Berakhir SIUP'), 
                        'class'=>'form-control',
                        'type' => 'text',
                        'required' => false,
                        'disabled' => true,
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('to_date', array(
                        'label'=> __('Berlaku Sampai'), 
                        'class'=>'form-control',
                        'type' => 'text',
                        'required' => false,
                        'disabled' => true,
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->label('price_estimate', __('Estimasi Biaya SIUP')); 
            ?>
            <div class="input-group">
                <?php 
                        echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
                            'class' => 'input-group-addon'
                        ));
                        echo $this->Form->input('price_estimate', array(
                            'type' => 'text',
                            'label'=> false, 
                            'class'=>'form-control input_price',
                            'required' => false,
                            'placeholder' => __('Estimasi Biaya SIUP'),
                            'disabled' => true,
                        ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('tgl_siup', array(
                        'label'=> __('Tanggal Perpanjang'), 
                        'class'=>'form-control',
                        'type' => 'text',
                        'required' => false,
                        'disabled' => true,
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                echo $this->Form->label('price', __('Biaya Perpanjang SIUP')); 
            ?>
            <div class="input-group">
                <?php 
                    echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
                        'class' => 'input-group-addon'
                    ));
                    echo $this->Form->input('price', array(
                        'type' => 'text',
                        'class'=>'form-control input_price',
                        'disabled' => true,
                        'required' => false,
                        'label'=> false, 
                    ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('siup_payment_date', array(
                        'label'=> __('Tgl Dibayar *'), 
                        'class'=>'form-control custom-date',
                        'type' => 'text',
                        'required' => false,
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('note', array(
                        'label'=> __('Keterangan'), 
                        'class'=>'form-control',
                        'type' => 'textarea',
                        'required' => false,
                    ));
            ?>
        </div>
    </div>

    <div class="box-footer text-center action">
        <?php
                echo $this->Form->hidden('rejected', array(
                    'value'=> 0,
                    'id' => 'rejected'
                ));
                echo $this->Html->link(__('Tolak'), 'javascript:', array(
                    'class'=> 'btn btn-danger submit-link',
                    'alert' => __('Anda yakin ingin menolak pembayaran SIUP truk ini?'),
                    'action_type' => 'rejected',
                ));
                echo $this->Form->button(__('Bayar'), array(
                    'div' => false, 
                    'class'=> 'btn btn-success btn-lg',
                    'type' => 'submit',
                ));
                echo $this->Html->link(__('Kembali'), array(
                    'controller' => 'trucks',
                    'action' => 'siup_payments'
                ), array(
                    'class'=> 'btn btn-default',
                ));
        ?>
    </div>
    <?php
        echo $this->Form->end();
    ?>
</div>