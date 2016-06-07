<?php
        $this->Html->addCrumb('Pembayaran STNK', array(
            'controller' => 'trucks',
            'action' => 'stnk_payments'
        ));
        $this->Html->addCrumb($sub_module_title);
        
        $coa_name = $this->Common->filterEmptyField($stnk, 'Coa', 'coa_name', '-');
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Informasi STNK')?></h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('No. Pol')?></dt>
                    <dd><?php echo $stnk['Stnk']['no_pol'];?></dd>
                    <dt><?php echo __('Account Kas/Bank')?></dt>
                    <dd><?php echo $coa_name;?></dd>
                    <dt><?php echo __('Tgl Perpanjang')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['tgl_bayar'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Tgl Berakhir STNK')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['from_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Diperpanjang Hingga')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['to_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Estimasi Biaya')?></dt>
                    <dd><?php echo $this->Number->currency($stnk['Stnk']['price_estimate'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($stnk['Stnk']['note'])?str_replace(PHP_EOL, '<br>', $stnk['Stnk']['note']):'-';?></dd>
                    <hr>
                    <dt><?php echo __('Ganti Plat?')?></dt>
                    <dd>
                        <?php 
                                if( !empty($stnk['Stnk']['is_change_plat']) ) {
                                    echo '<span class="label label-success">Ya</span>'; 
                                } else {
                                    echo '<span class="label label-danger">Tidak</span>'; 
                                }
                        ?>
                    </dd>
                    <?php 
                            if( !empty($stnk['Stnk']['is_change_plat']) ) {
                    ?>
                    <dt><?php echo __('Tgl Ganti Plat')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['plat_from_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Plat Diperpanjang Hingga')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['plat_to_date'], 'd/m/Y');?></dd>
                    <?php
                            }
                    ?>
                    <hr>
                    <dt><?php echo __('Biaya Perpanjang')?></dt>
                    <dd><?php echo $this->Number->currency($stnk['Stnk']['price'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Denda')?></dt>
                    <dd>
                        <?php 
                            $denda = 0;
                            if(!empty($stnk['Stnk']['denda'])){
                                $denda = $stnk['Stnk']['denda'];
                            }
                            echo $this->Number->currency($denda, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                    <dt><?php echo __('Biaya Lain')?></dt>
                    <dd>
                        <?php 
                                $biaya_lain = 0;
                                if(!empty($stnk['Stnk']['biaya_lain'])){
                                    $biaya_lain = $stnk['Stnk']['biaya_lain'];
                                }
                                echo $this->Number->currency($biaya_lain, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                    <dt><?php echo __('Total Pembayaran')?></dt>
                    <dd>
                        <?php 
                            $total = $stnk['Stnk']['price'] + $denda + $biaya_lain;
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
                            $id = $this->Common->filterEmptyField($stnk, 'StnkPayment', 'id');
                            $paid = $this->Common->filterEmptyField($stnk, 'Stnk', 'paid');
                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                    ?>
                    <dt><?php echo __('No. Referensi')?></dt>
                    <dd><?php echo $noref;?></dd>
                    <dt><?php echo __('Tgl Dibayar')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['StnkPayment']['stnk_payment_date'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Tgl Dokumen Dibuat')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['StnkPayment']['created'], 'd/m/Y');?></dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($stnk['StnkPayment']['note'])?str_replace(PHP_EOL, '<br>', $stnk['StnkPayment']['note']):'-';?></dd>
                    <dt><?php echo __('Status')?></dt>
                    <dd>
                        <?php 
                                if(empty($stnk['StnkPayment']['is_void'])){
                                    if( $paid == 'full' ) {
                                        echo '<span class="label label-success">Sudah Bayar</span>'; 
                                    } else if( $paid == 'half' ) {
                                        echo '<span class="label label-success">Dibayar Sebagian</span>'; 
                                    } else if( !empty($stnk['Stnk']['rejected']) ) {
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
                'action' => 'stnk_payments'
            ), array(
                'class'=> 'btn btn-default',
            ));
    ?>
</div>