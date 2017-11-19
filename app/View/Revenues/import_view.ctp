<?php 
        $this->Html->addCrumb($sub_module_title);

        $code = !empty($code)?$code:false;
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.id', __('No. Ref'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.no_doc', __('No. Dok'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.date_revenue', __('Tgl Revenue'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.no_ttuj', __('No. TTUJ'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.nopol', __('Truk'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Customer.code', __('Customer'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.from_city_name', __('Dari-Tujuan'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Unit TTUJ'), array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('th', __('Unit Revenue'), array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('th', __('Selisih'), array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('Revenue.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($revenues)){
                        foreach ($revenues as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
                            $is_manual = $this->Common->filterEmptyField($value, 'Revenue', 'is_manual');
                            $periode = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue', false, false, array(
                                'date' => 'Y-m',
                            ));
                            
                            $from_city = $this->Common->filterEmptyField($value, 'FromCity', 'name');
                            $to_city = $this->Common->filterEmptyField($value, 'ToCity', 'name');

                            $from_city = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name', $from_city);
                            $to_city = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name', $to_city);

                            $ttuj_unit = Common::hashEmptyField($value, 'ttuj_unit', 0);
                            $revenue_unit = Common::hashEmptyField($value, 'qty_unit', 0);
                            $selisih = $ttuj_unit - $revenue_unit;

                            if( $selisih < 0 ) {
                                $style = 'alert alert-danger';
                            } else {
                                $style = '';
                            }
            ?>
            <tr class="<?php echo $style; ?>">
                <td><?php echo str_pad($value['Revenue']['id'], 5, '0', STR_PAD_LEFT);?></td>
                <td><?php echo $value['Revenue']['no_doc'];?></td>
                <td><?php echo $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y');?></td>
                <td><?php echo Common::hashEmptyField($value, 'Ttuj.no_ttuj', '-');?></td>
                <td>
                    <?php
                            if( !empty($value['Revenue']['nopol']) ) {
                                echo $value['Revenue']['nopol'];
                            } elseif( !empty($value['Ttuj']['nopol']) ) {
                                echo $value['Ttuj']['nopol'];
                            } elseif( !empty($value['Truck']['nopol']) ) {
                                echo $value['Truck']['nopol'];
                            } else {
                                echo '-';
                            }
                    ?>
                </td>
                <td>
                    <?php
                            echo !empty($value['Customer']['code'])?$value['Customer']['code']:'-';
                    ?>
                </td>
                <?php 
                        echo $this->Html->tag('td', sprintf('%s - %s', $from_city, $to_city));
                ?>
                <td align="center"><?php echo $ttuj_unit;?></td>
                <td align="center"><?php echo $revenue_unit;?></td>
                <td align="center"><?php echo $selisih;?></td>
                <td><?php echo $this->Common->customDate($value['Revenue']['created']);?></td>
                <td class="action">
                    <?php
                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'revenues',
                                'action' => 'import_toggle',
                                $id,
                                $code,
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus revenue ini?'));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '11'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php
            echo $this->element('pagination');
    ?>
</div>
<div class="box-footer text-center action">
    <?php
            echo $this->Html->link(__('Kembali'), array(
                'action' => 'import_by_ttuj',
            ), array(
                'class'=> 'btn btn-default',
            ));
            echo $this->Html->link(__('Simpan & Lanjutkan'), array(
                'action' => 'process_import_by_ttuj',
                $code,
            ), array(
                'class'=> 'btn btn-success btn-lg',
            ));
    ?>
</div>