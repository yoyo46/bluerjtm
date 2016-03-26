<?php 
        $is_asset = $this->Common->filterEmptyField($truck, 'Truck', 'is_asset');
        $asset_id = $this->Common->filterEmptyField($truck, 'Asset', 'id');
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi Truk'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <?php
                        if(!empty($truck['Truck']['photo']) && !is_array($truck['Truck']['photo'])){
                            $photo = $this->Common->photo_thumbnail(array(
                                'save_path' => Configure::read('__Site.truck_photo_folder'), 
                                'src' => $truck['Truck']['photo'], 
                                'thumb'=>true,
                                'size' => 'pm',
                                'thumb' => true,
                            ));

                            echo $this->Html->tag('div', $photo, array(
                                'class' => 'form-group',
                            ));
                        }
                ?>
                <dl class="dl-horizontal">
                    <dt><?php echo __('ID Truk')?></dt>
                    <dd><?php echo $truck['Truck']['id'];?></dd>
                    <dt><?php echo __('Cabang')?></dt>
                    <dd><?php echo !empty($truck['Branch']['name'])?$truck['Branch']['name']:'-';?></dd>
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
                    <dd><?php echo !empty($truck['Driver']['driver_name'])?ucwords($truck['Driver']['driver_name']):'-';?></dd>
                    <dt><?php echo __('Kapasitas')?></dt>
                    <dd><?php echo $truck['Truck']['capacity'];?></dd>
                    <dt><?php echo __('Tahun')?></dt>
                    <dd><?php echo $truck['Truck']['tahun'];?></dd>
                    <dt><?php echo __('ini adalah aset?')?></dt>
                    <dd><?php echo !empty($truck['Truck']['is_asset'])?'Ya':'Tidak';;?></dd>
                    <dt><?php echo __('Tahun Neraca')?></dt>
                    <dd><?php echo !empty($truck['Truck']['tahun_neraca'])?$truck['Truck']['tahun_neraca']:'-';?></dd>
                    <?php 
                            if( !empty($is_asset) && !empty($asset_id) ) {
                                echo $this->Html->tag('dt', '&nbsp;');
                                echo $this->Html->tag('dd', $this->Html->link(__('Lihat Detail Asset'), array(
                                    'controller' => 'assets',
                                    'action' => 'edit',
                                    $asset_id,
                                ), array(
                                    'escape' => false,
                                    'target' => '_blank',
                                )));
                            }
                    ?>
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
                    <dt><?php echo __('Tgl BPKB')?></dt>
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
                    <dt><?php echo __('Ijin Usaha')?></dt>
                    <dd>
                        <?php 
                                echo $this->Common->customDate($truck['Truck']['tgl_siup'], 'd M Y', '-');
                                echo $this->Html->link(__('&nbsp;&nbsp;Perpanjang Ijin Usaha ?'), array(
                                    'controller' => 'trucks',
                                    'action' => 'siup',
                                ), array(
                                    'escape' => false
                                ));
                        ?>
                    </dd>
                    <dt><?php echo __('Biaya Ijin Usaha')?></dt>
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
                                echo $this->Html->tag('dt', sprintf(__('Alokasi %s'), $i));
                                echo $this->Html->tag('dd', $value['Customer']['customer_name']);
                            }
                    ?>
                </dl>
            </div>
        </div>
    </div>
    <?php 
            }
    ?>
    <div class="col-sm-6">
        <div class="box box-warning">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Emergency Call'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('Dilengkapi GPS')?></dt>
                    <dd><?php echo !empty($truck['Truck']['is_gps'])?'Ya':'Tidak';?></dd>
                    <dt><?php echo __('Nama')?></dt>
                    <dd><?php echo !empty($truck['Truck']['emergency_name'])?$truck['Truck']['emergency_name']:'-';?></dd>
                    <dt><?php echo __('Telepon')?></dt>
                    <dd><?php echo !empty($truck['Truck']['emergency_call'])?$truck['Truck']['emergency_call']:'-';?></dd>
                </dl>
            </div>
        </div>
    </div>
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
    
            if( !empty($leasing) ) {
    ?>
    <div class="col-sm-6">
        <div class="box box-warning">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Leasing Truk'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <?php 
                            $dataLeasing = $this->Common->filterEmptyField($leasing, 'Leasing');
                            $no_contract = $this->Common->filterEmptyField($leasing, 'Leasing', 'no_contract');
                            $date_last_installment = $this->Common->filterEmptyField($leasing, 'Leasing', 'date_last_installment');

                            $vendor = $this->Common->filterEmptyField($dataLeasing, 'Vendor', 'name');
                            $vendor_phone = $this->Common->filterEmptyField($dataLeasing, 'Vendor', 'phone_number');
                            $vendor_address = $this->Common->filterEmptyField($dataLeasing, 'Vendor', 'address');

                            $customDateLeasing = $this->Common->customDate($date_last_installment, 'd M Y');

                            echo $this->Html->tag('dt', 'No. Kontrak');
                            echo $this->Html->tag('dd', $no_contract);

                            echo $this->Html->tag('dt', 'Perusahaan Leasing');
                            echo $this->Html->tag('dd', $vendor);

                            echo $this->Html->tag('dt', 'Telepon');
                            echo $this->Html->tag('dd', $vendor_phone);

                            echo $this->Html->tag('dt', 'Alamat');
                            echo $this->Html->tag('dd', $vendor_address);

                            echo $this->Html->tag('dt', 'Tgl Angsuran Terakhir');
                            echo $this->Html->tag('dd', $customDateLeasing);
                    ?>
                </dl>
            </div>
        </div>
    </div>
    <?php 
            }
    ?>
</div>