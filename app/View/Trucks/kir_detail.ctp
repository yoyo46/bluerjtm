<?php
        $this->Html->addCrumb('Pembayaran KIR', array(
            'controller' => 'trucks',
            'action' => 'kir_payments'
        ));
        $this->Html->addCrumb($sub_module_title);
        
        $coa_name = $this->Common->filterEmptyField($kir, 'Coa', 'coa_name', '-');
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Informasi KIR')?></h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('No. Pol')?></dt>
                    <dd><?php echo $kir['Kir']['no_pol'];?></dd>
                    <dt><?php echo __('Account Kas/Bank')?></dt>
                    <dd><?php echo $coa_name;?></dd>
                    <dt><?php echo __('Tgl Perpanjang')?></dt>
                    <dd><?php echo $this->Common->customDate($kir['Kir']['tgl_kir'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Tgl Berakhir KIR')?></dt>
                    <dd><?php echo $this->Common->customDate($kir['Kir']['from_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Berlaku Sampai')?></dt>
                    <dd><?php echo $this->Common->customDate($kir['Kir']['to_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Estimasi Biaya')?></dt>
                    <dd><?php echo $this->Number->currency($kir['Kir']['price_estimate'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Biaya Perpanjang')?></dt>
                    <dd><?php echo $this->Number->currency($kir['Kir']['price'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Denda')?></dt>
                    <dd>
                        <?php 
                                $denda = 0;
                                if(!empty($kir['Kir']['denda'])){
                                    $denda = $kir['Kir']['denda'];
                                }
                                echo $this->Number->currency($denda, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                    <dt><?php echo __('Biaya Lain')?></dt>
                    <dd>
                        <?php 
                                $biaya_lain = 0;
                                if(!empty($kir['Kir']['biaya_lain'])){
                                    $biaya_lain = $kir['Kir']['biaya_lain'];
                                }
                                echo $this->Number->currency($biaya_lain, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                    <dt><?php echo __('Total Pembayaran')?></dt>
                    <dd>
                        <?php 
                            $total = $kir['Kir']['price'] + $denda + $biaya_lain;
                            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($kir['Kir']['note'])?str_replace(PHP_EOL, '<br>', $kir['Kir']['note']):'-';?></dd>
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
                    <dd><?php echo $this->Common->customDate($kir['KirPayment']['kir_payment_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Tgl Dokumen Dibuat')?></dt>
                    <dd><?php echo $this->Common->customDate($kir['KirPayment']['created'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($kir['KirPayment']['note'])?str_replace(PHP_EOL, '<br>', $kir['KirPayment']['note']):'-';?></dd>
                    <dt><?php echo __('Status')?></dt>
                    <dd>
                        <?php 
                                if(empty($kir['KirPayment']['is_void'])){
                                    if( !empty($kir['Kir']['paid']) ) {
                                        echo '<span class="label label-success">Sudah Bayar</span>'; 
                                    } else if( !empty($kir['Kir']['rejected']) ) {
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
                'action' => 'kir_payments'
            ), array(
                'class'=> 'btn btn-default',
            ));
    ?>
</div>