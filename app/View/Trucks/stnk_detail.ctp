<?php
        $this->Html->addCrumb('Pembayaran STNK', array(
            'controller' => 'trucks',
            'action' => 'stnk_payments'
        ));
        $this->Html->addCrumb($sub_module_title);
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
                    <dt><?php echo __('Tanggal Perpanjang')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['tgl_bayar']);?></dd>
                    <dt><?php echo __('Tgl Berakhir STNK')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['from_date']);?></dd>
                    <dt><?php echo __('Berlaku Sampai')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['to_date']);?></dd>
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
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['plat_from_date']);?></dd>
                    <dt><?php echo __('Plat Berlaku Sampai')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['Stnk']['plat_to_date']);?></dd>
                    <?php
                            }
                    ?>
                    <hr>
                    <dt><?php echo __('Estimasi Biaya')?></dt>
                    <dd><?php echo $this->Number->currency($stnk['Stnk']['price_estimate'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
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
                    <dt><?php echo __('Total Pembayaran')?></dt>
                    <dd>
                        <?php 
                            $total = $stnk['Stnk']['price'] + $denda;
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
                    <dd><?php echo $this->Common->customDate($stnk['StnkPayment']['stnk_payment_date']);?></dd>
                    <dt><?php echo __('Tgl Dokumen Dibuat')?></dt>
                    <dd><?php echo $this->Common->customDate($stnk['StnkPayment']['created']);?></dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($stnk['StnkPayment']['note'])?str_replace(PHP_EOL, '<br>', $stnk['StnkPayment']['note']):'-';?></dd>
                    <dt><?php echo __('Status')?></dt>
                    <dd>
                        <?php 
                                if( !empty($stnk['StnkPayment']['rejected']) ) {
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
            echo $this->Html->link(__('Kembali'), array(
                'controller' => 'trucks',
                'action' => 'stnk_payments'
            ), array(
                'class'=> 'btn btn-default',
            ));
    ?>
</div>