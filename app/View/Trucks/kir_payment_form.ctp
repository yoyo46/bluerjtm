<?php
        $this->Html->addCrumb('Pembayaran KIR', array(
            'controller' => 'trucks',
            'action' => 'kir_payments'
        ));
        $this->Html->addCrumb($sub_module_title);

        echo $this->Form->create('KirPayment', array(
            'url'=> $this->Html->url( null, true ), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Biaya KIR'); ?></h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <?php 
                            $attrBrowse = array(
                                'class' => 'ajaxModal visible-xs',
                                'escape' => false,
                                'title' => __('Data KIR'),
                                'data-action' => 'browse-form',
                                'data-change' => 'truckID',
                            );
                            $urlBrowse = array(
                                'controller'=> 'ajax', 
                                'action' => 'getKirs',
                            );
                            echo $this->Form->label('kir_id', __('No. Pol * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
                    ?>
                    <div class="row">
                        <div class="col-sm-10">
                            <?php 
                                    echo $this->Form->input('kir_id', array(
                                        'label'=> false, 
                                        'class'=>'form-control change-link',
                                        'required' => false,
                                        'empty' => __('Pilih No. Pol'),
                                        'id' => 'truckID',
                                        'url' => $this->Html->url(array(
                                            'controller' => 'trucks',
                                            'action' => 'kir_payment_add',
                                        )),
                                    ));
                            ?>
                        </div>
                        <div class="col-sm-2 hidden-xs">
                            <?php 
                                    $attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                                    echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                        echo $this->Html->tag('div', $this->Form->input('coa_id',array(
                            'label'=> __('Account Kas/Bank *'), 
                            'class'=>'form-control',
                            'required' => false,
                            'empty' => __('Pilih Kas/Bank '),
                            'options' => !empty($coas)?$coas:false,
                        )), array(
                            'class' => 'form-group'
                        ));
                ?>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('from_date', array(
                                'label'=> __('Tgl Berakhir KIR'), 
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
                            echo $this->Form->label('price_estimate', __('Estimasi Biaya KIR')); 
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
                                    'placeholder' => __('Estimasi Biaya KIR'),
                                    'disabled' => true,
                                ));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('tgl_kir', array(
                                'label'=> __('Tgl Perpanjang'), 
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
                        echo $this->Form->label('denda', __('Denda')); 
                    ?>
                    <div class="input-group">
                        <?php 
                            echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
                                'class' => 'input-group-addon'
                            ));
                            echo $this->Form->input('denda', array(
                                'type' => 'text',
                                'class'=>'form-control input_price',
                                'disabled' => true,
                                'required' => false,
                                'label'=> false, 
                            ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Informasi Pembayaran'); ?></h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('kir_payment_date', array(
                                'label'=> __('Tgl Dibayar *'), 
                                'class'=>'form-control custom-date',
                                'type' => 'text',
                                'required' => false,
                                'value' => (!empty($this->request->data['KirPayment']['kir_payment_date'])) ? $this->request->data['KirPayment']['kir_payment_date'] : date('d/m/Y')
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
        </div>
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