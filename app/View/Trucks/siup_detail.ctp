<?php
        $this->Html->addCrumb('Pembayaran Ijin Usaha', array(
            'controller' => 'trucks',
            'action' => 'siup_payments'
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Informasi Ijin Usaha')?></h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('No. Pol')?></dt>
                    <dd><?php echo $siup['Siup']['no_pol'];?></dd>
                    <dt><?php echo __('Tanggal Perpanjang')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['Siup']['tgl_siup']);?></dd>
                    <dt><?php echo __('Tgl Berakhir Ijin Usaha')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['Siup']['from_date']);?></dd>
                    <dt><?php echo __('Berlaku Sampai')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['Siup']['to_date']);?></dd>
                    <dt><?php echo __('Estimasi Biaya')?></dt>
                    <dd><?php echo $this->Number->currency($siup['Siup']['price_estimate'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Biaya Perpanjang')?></dt>
                    <dd><?php echo $this->Number->currency($siup['Siup']['price'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Denda')?></dt>
                    <dd>
                        <?php 
                            $denda = 0;
                            if(!empty($siup['Siup']['denda'])){
                                $denda = $siup['Siup']['denda'];
                            }
                            echo $this->Number->currency($denda, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                    <dt><?php echo __('Total Pembayaran')?></dt>
                    <dd>
                        <?php 
                            $total = $siup['Siup']['price'] + $denda;
                            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Informasi Pembayaran')?></h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('Tgl Dibayar')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['SiupPayment']['siup_payment_date']);?></dd>
                    <dt><?php echo __('Tgl Dokumen Dibuat')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['SiupPayment']['created']);?></dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($siup['SiupPayment']['note'])?str_replace(PHP_EOL, '<br>', $siup['SiupPayment']['note']):'-';?></dd>
                    <dt><?php echo __('Status')?></dt>
                    <dd>
                        <?php 
                                if( !empty($siup['SiupPayment']['rejected']) ) {
                                    echo '<span class="label label-danger">Ditolak</span>'; 
                                } else {
                                    echo '<span class="label label-success">Sudah Bayar</span>'; 
                                }
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="box-footer text-center action">
    <?php
            echo $this->Common->rule_link(__('Kembali'), array(
                'controller' => 'trucks',
                'action' => 'siup_payments'
            ), array(
                'class'=> 'btn btn-default',
            ));
    ?>
</div>