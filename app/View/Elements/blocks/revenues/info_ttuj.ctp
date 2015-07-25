<div class="row">
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi TTUJ'), array(
                            'class' => 'box-title',
                        ));
                ?>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('No. TTUJ')?></dt>
                    <dd><?php echo $ttuj['Ttuj']['no_ttuj'];?></dd>
                    <dt><?php echo __('Nopol')?></dt>
                    <dd><?php echo $ttuj['Ttuj']['nopol'];?></dd>
                    <dt><?php echo __('Customer')?></dt>
                    <dd><?php echo $ttuj['Ttuj']['customer_name'];?></dd>
                    <dt><?php echo __('Dari')?></dt>
                    <dd><?php echo $ttuj['Ttuj']['from_city_name'];?></dd>
                    <dt><?php echo __('Tujuan')?></dt>
                    <dd><?php echo $ttuj['Ttuj']['to_city_name'];?></dd>
                    <dt><?php echo __('Status SJ')?></dt>
                    <dd><?php echo !empty($ttuj['Ttuj']['is_sj_completed'])?$this->Html->tag('span', __('Sudah Terima Semua'), array( 'class' => 'label label-success' )):$this->Html->tag('span', __('Belum Terima Semua'), array( 'class' => 'label label-danger' ));?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box box-success">
            <div class="box-header">
                <?php 
                        echo $this->Html->tag('h3', __('Informasi Muatan'), array(
                            'class' => 'box-title',
                        ));
                        $colSpan = 2;
                        $totalUnitMuatan = 0;
                ?>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped" id="ttujDetail">
                    <thead>
                        <tr>
                            <?php 
                                    if( !empty($ttuj['Ttuj']['is_retail']) ) {
                                        echo $this->Html->tag('th', __('Tujuan'));
                                        $colSpan ++;
                                    }
                                    echo $this->Html->tag('th', __('Tipe Motor'));
                                    echo $this->Html->tag('th', __('Warna Motor'));
                                    echo $this->Html->tag('th', __('Jumlah Unit'));
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                                if( !empty($ttuj['TtujTipeMotor']) ) {
                                    foreach ($ttuj['TtujTipeMotor'] as $key => $ttujTipeMotor) {
                                        $qty = !empty($ttujTipeMotor['TtujTipeMotor']['qty'])?$ttujTipeMotor['TtujTipeMotor']['qty']:0;
                                        $motor = !empty($ttujTipeMotor['TipeMotor']['name'])?$ttujTipeMotor['TipeMotor']['name']:false;
                                        $color_motor = !empty($ttujTipeMotor['ColorMotor']['name'])?$ttujTipeMotor['ColorMotor']['name']:false;
                                        $totalUnitMuatan += $qty;
                        ?>
                        <tr>
                            <?php
                                    if( !empty($ttuj['Ttuj']['is_retail']) ) {
                                        $city = !empty($ttujTipeMotor['City']['name'])?$ttujTipeMotor['City']['name']:false;

                                        echo $this->Html->tag('td', $city);
                                    }

                                    echo $this->Html->tag('td', $motor);
                                    echo $this->Html->tag('td', $color_motor);
                                    echo $this->Html->tag('td', $qty);
                            ?>
                        </tr>
                        <?php
                                    }
                                }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <?php 
                                    echo $this->Html->tag('th', __('Total'), array(
                                        'colspan' => $colSpan,
                                        'class' => 'text-right',
                                    ));
                                    echo $this->Html->tag('th', $totalUnitMuatan, array(
                                        'class' => 'total-unit-muatan',
                                    ));
                            ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>