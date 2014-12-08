<div class="box box-solid box-info">
    <div class="box-header">
        <h3 class="box-title">Info Truk</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-info btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-info btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <dl class="dl-horizontal">
            <dt><?php echo __('Merek')?></dt>
            <dd><?php echo $truck['TruckBrand']['name'];?></dd>
            <dt><?php echo __('Nopol')?></dt>
            <dd><?php echo $truck['Truck']['nopol'];?></dd>
            <dt><?php echo __('Perusahaan')?></dt>
            <dd><?php echo $truck['Company']['name'];?></dd>
            <dt><?php echo __('Kategori')?></dt>
            <dd><?php echo $truck['TruckCategory']['name'];?></dd>
            <dt><?php echo __('BPKB')?></dt>
            <dd><?php echo $truck['Truck']['bpkb'];?></dd>
            <dt><?php echo __('No. STNK')?></dt>
            <dd><?php echo $truck['Truck']['no_stnk'];?></dd>
            <dt><?php echo __('No. Rangka')?></dt>
            <dd><?php echo $truck['Truck']['no_rangka'];?></dd>
            <dt><?php echo __('No. Mesin')?></dt>
            <dd><?php echo $truck['Truck']['no_machine'];?></dd>
            <dt><?php echo __('Atas Nama')?></dt>
            <dd><?php echo $truck['Truck']['atas_nama'];?></dd>
            <dt><?php echo __('KIR')?></dt>
            <dd>
                <?php 
                        if( !empty($truck['Truck']['kir']) ) {
                            $truckKir = $this->Common->customDate($truck['Truck']['kir']);
                        } else {
                            $truckKir = '-';
                        }

                        echo $truckKir;
                ?>
            </dd>
            <dt><?php echo __('SIUP')?></dt>
            <dd>
                <?php 
                        if( !empty($truck['Truck']['siup']) ) {
                            $truckSiup = $this->Common->customDate($truck['Truck']['siup']);
                        } else {
                            $truckSiup = '-';
                        }

                        echo $truckSiup;
                ?>
            </dd>
        </dl>
    </div>
</div>