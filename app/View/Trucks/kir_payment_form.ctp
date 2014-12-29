<?php
        $this->Html->addCrumb('Pembayaran KIR', array(
            'controller' => 'trucks',
            'action' => 'kir_payments'
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
        echo $this->Form->create('KirPayment', array(
            'url'=> $this->Html->url( null, true ), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
    ?>
    <div class="box-body">
        <div class="form-group">
            <?php 
                echo $this->Form->input('kir_id', array(
                    'label'=> __('No. Pol *'), 
                    'class'=>'form-control change-link',
                    'required' => false,
                    'empty' => __('Pilih No. Pol'),
                    'url' => $this->Html->url(array(
                        'controller' => 'trucks',
                        'action' => 'kir_payment_add',
                    )),
                ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('tgl_kir', array(
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
                    echo $this->Form->input('from_date', array(
                        'label'=> __('Tgl KIR ditetapkan'), 
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
                        'label'=> __('Berlaku Hingga'), 
                        'class'=>'form-control',
                        'type' => 'text',
                        'required' => false,
                        'disabled' => true,
                    ));
            ?>
        </div>
        <div class="form-group">
            <?php 
                echo $this->Form->label('price', __('Biaya Perpanjang KIR')); 
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
                    'alert' => __('Anda yakin ingin menolak pembayaran KIR truk ini?'),
                    'action_type' => 'rejected',
                ));
                echo $this->Form->button(__('Bayar'), array(
                    'div' => false, 
                    'class'=> 'btn btn-success btn-lg',
                    'type' => 'submit',
                ));
                echo $this->Html->link(__('Kembali'), array(
                    'controller' => 'trucks',
                    'action' => 'kir_payments'
                ), array(
                    'class'=> 'btn btn-default',
                ));
        ?>
    </div>
    <?php
        echo $this->Form->end();
    ?>
</div>