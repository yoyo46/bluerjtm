<div class="row">
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi Truk'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('Nopol')?></dt>
                    <dd><?php echo $truck['Truck']['nopol'];?></dd>
                    <dt><?php echo __('Merek')?></dt>
                    <dd><?php echo $truck['TruckBrand']['name'];?></dd>
                    <dt><?php echo __('Jenis Truk')?></dt>
                    <dd><?php echo $truck['TruckCategory']['name'];?></dd>
                    <dt><?php echo __('Fasilitas Truk')?></dt>
                    <dd><?php echo !empty($truck['TruckFacility']['name'])?$truck['TruckFacility']['name']:'-';?></dd>
                    <dt><?php echo __('No Rangka')?></dt>
                    <dd><?php echo $truck['Truck']['no_rangka'];?></dd>
                    <dt><?php echo __('No Mesin')?></dt>
                    <dd><?php echo $truck['Truck']['no_machine'];?></dd>
                    <dt><?php echo __('Pemilik Truk')?></dt>
                    <dd><?php echo $truck['Company']['name'];?></dd>
                    <dt><?php echo __('Supir Truk')?></dt>
                    <dd><?php echo !empty($truck['Driver']['name'])?$truck['Driver']['name']:'-';?></dd>
                    <dt><?php echo __('Kapasitas')?></dt>
                    <dd><?php echo $truck['Truck']['capacity'];?></dd>
                    <dt><?php echo __('Tahun')?></dt>
                    <dd><?php echo $truck['Truck']['tahun'];?></dd>
                    <dt><?php echo __('ini adalah aset?')?></dt>
                    <dd><?php echo !empty($truck['Truck']['is_asset'])?'Ya':'Tidak';;?></dd>
                    <dt><?php echo __('Tahun Neraca')?></dt>
                    <dd><?php echo !empty($truck['Truck']['tahun_neraca'])?$truck['Truck']['tahun_neraca']:'-';?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box box-success">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi Dokumen'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('Atas Nama')?></dt>
                    <dd><?php echo $truck['Truck']['atas_nama'];?></dd>
                    <dt><?php echo __('BPKB')?></dt>
                    <dd><?php echo $truck['Truck']['bpkb'];?></dd>
                    <dt><?php echo __('Tanggal BPKB')?></dt>
                    <dd><?php echo $this->Common->customDate($truck['Truck']['tgl_bpkb'], 'd M Y', '-');?></dd>
                    <dt><?php echo __('No. STNK')?></dt>
                    <dd><?php echo $truck['Truck']['no_stnk'];?></dd>
                    <dt><?php echo __('Tgl STNK 1Thn')?></dt>
                    <dd>
                        <?php
                                echo $this->Common->customDate($truck['Truck']['tgl_stnk'], 'd M Y', '-');
                                echo $this->Html->link(__('&nbsp;&nbsp;Perpanjang STNK ?'), array(
                                    'controller' => 'trucks',
                                    'action' => 'stnk',
                                ), array(
                                    'escape' => false
                                ));
                        ?>
                    </dd>
                    <dt><?php echo __('Tgl STNK 5Thn')?></dt>
                    <dd>
                        <?php
                                echo $this->Common->customDate($truck['Truck']['tgl_stnk_plat'], 'd M Y', '-');
                                echo $this->Html->link(__('&nbsp;&nbsp;Perpanjang Plat ?'), array(
                                    'controller' => 'trucks',
                                    'action' => 'stnk',
                                ), array(
                                    'escape' => false
                                ));
                        ?>
                    </dd>
                    <dt><?php echo __('Biaya BBNKB')?></dt>
                    <dd><?php echo $this->Number->currency($truck['Truck']['bbnkb'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Biaya PKB')?></dt>
                    <dd><?php echo $this->Number->currency($truck['Truck']['pkb'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Biaya SWDKLLJ')?></dt>
                    <dd><?php echo $this->Number->currency($truck['Truck']['swdkllj'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('KIR')?></dt>
                    <dd>
                        <?php 
                                echo $this->Common->customDate($truck['Truck']['tgl_kir'], 'd M Y', '-');
                                echo $this->Html->link(__('&nbsp;&nbsp;Perpanjang KIR ?'), array(
                                    'controller' => 'trucks',
                                    'action' => 'kir',
                                ), array(
                                    'escape' => false
                                ));
                        ?>
                    </dd>
                    <dt><?php echo __('Biaya KIR')?></dt>
                    <dd><?php echo $this->Number->currency($truck['Truck']['kir'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('SIUP')?></dt>
                    <dd>
                        <?php 
                                echo $this->Common->customDate($truck['Truck']['tgl_siup'], 'd M Y', '-');
                                echo $this->Html->link(__('&nbsp;&nbsp;Perpanjang SIUP ?'), array(
                                    'controller' => 'trucks',
                                    'action' => 'siup',
                                ), array(
                                    'escape' => false
                                ));
                        ?>
                    </dd>
                    <dt><?php echo __('Biaya SIUP')?></dt>
                    <dd><?php echo $this->Number->currency($truck['Truck']['siup'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                </dl>
            </div>
        </div>
    </div>
    <?php 
            if( !empty($truck['TruckCustomer']) ) {
    ?>
    <div class="col-sm-6">
        <div class="box box-info">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Alokasi Truk'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <?php 
                            $i = 0;
                            foreach ($truck['TruckCustomer'] as $key => $value) {
                                $i++;
                                $customerType = !empty($value['Customer']['CustomerType']['name'])?$value['Customer']['CustomerType']['name']:'-';
                                echo $this->Html->tag('dt', sprintf(__('Alokasi %s'), $i));
                                echo $this->Html->tag('dd', sprintf(__('%s (%s)'), $value['Customer']['name'], $customerType));
                            }
                    ?>
                </dl>
            </div>
        </div>
    </div>
    <?php 
            }
    ?>
    <?php 
            if( !empty($_show_perlengkapan) && !empty($truckPerlengkapans) ) {
    ?>
    <div class="col-sm-6">
        <div class="box box-warning">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Perlengkapan Truk'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <?php 
                            $i = 0;
                            foreach ($truckPerlengkapans as $key => $value) {
                                $i++;
                                echo $this->Html->tag('dt', $value['Perlengkapan']['name']);
                                echo $this->Html->tag('dd', $value['TruckPerlengkapan']['qty']);
                            }
                    ?>
                </dl>
            </div>
        </div>
    </div>
    <?php 
            }
    ?>
</div>