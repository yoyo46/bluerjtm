<?php
        $this->Html->addCrumb('Pembayaran Ijin Usaha', array(
            'controller' => 'trucks',
            'action' => 'siup_payments'
        ));
        $this->Html->addCrumb($sub_module_title);
        
        $coa_name = $this->Common->filterEmptyField($siup, 'Coa', 'coa_name', '-');
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
                    <dt><?php echo __('Account Kas/Bank')?></dt>
                    <dd><?php echo $coa_name;?></dd>
                    <dt><?php echo __('Tgl Perpanjang')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['Siup']['tgl_siup'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Tgl Berakhir Ijin Usaha')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['Siup']['from_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Diperpanjang Hingga')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['Siup']['to_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Estimasi Biaya')?></dt>
                    <dd><?php echo $this->Number->currency($siup['Siup']['price_estimate'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($siup['Siup']['note'])?str_replace(PHP_EOL, '<br>', $siup['Siup']['note']):'-';?></dd>
                    <hr>
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
                    <dt><?php echo __('Biaya Lain')?></dt>
                    <dd>
                        <?php 
                                $biaya_lain = 0;
                                if(!empty($siup['Siup']['biaya_lain'])){
                                    $biaya_lain = $siup['Siup']['biaya_lain'];
                                }
                                echo $this->Number->currency($biaya_lain, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                    <dt><?php echo __('Total Pembayaran')?></dt>
                    <dd>
                        <?php 
                            $total = $siup['Siup']['price'] + $denda + $biaya_lain;
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
                    <?php 
                            $id = $this->Common->filterEmptyField($siup, 'SiupPayment', 'id');
                            $paid = $this->Common->filterEmptyField($siup, 'Kir', 'paid');
                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                    ?>
                    <dt><?php echo __('No. Referensi')?></dt>
                    <dd><?php echo $noref;?></dd>
                    <dt><?php echo __('Tgl Dibayar')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['SiupPayment']['siup_payment_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Tgl Dokumen Dibuat')?></dt>
                    <dd><?php echo $this->Common->customDate($siup['SiupPayment']['created'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($siup['SiupPayment']['note'])?str_replace(PHP_EOL, '<br>', $siup['SiupPayment']['note']):'-';?></dd>
                    <dt><?php echo __('Status')?></dt>
                    <dd>
                        <?php 
                                if(empty($siup['SiupPayment']['is_void'])){
                                    if( $paid == 'full' ) {
                                        echo '<span class="label label-success">Sudah Bayar</span>'; 
                                    } else if( $paid == 'half' ) {
                                        echo '<span class="label label-success">Dibayar Sebagian</span>'; 
                                    } else if( !empty($siup['Siup']['rejected']) ) {
                                        echo '<span class="label label-danger">Ditolak</span>'; 
                                    } else {
                                        echo '<span class="label label-default">Belum Bayar</span>';  
                                    }
                                }else{
                                    echo '<span class="label label-danger">Non-Aktif</span>'; 
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
            echo $this->Html->link(__('Kembali'), array(
                'controller' => 'trucks',
                'action' => 'siup_payments'
            ), array(
                'class'=> 'btn btn-default',
            ));
    ?>
</div>