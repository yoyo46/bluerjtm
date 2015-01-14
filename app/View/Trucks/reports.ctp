<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_report_truck');

        echo $this->Form->create('Truck', array(
            'url'=> $this->Html->url( array(
                'controller' => 'trucks',
                'action' => 'search',
                'reports'
            )), 
            'class' => 'form-inline text-right hidden-print',
            'inputDefaults' => array('div' => false),
        ));

        $find_sort = array(
            '' => __('Urutkan Berdasarkan'),
            '1' => __('No. Pol A - Z'),
            '2' => __('No. Pol Z - A'),
            '3' => __('Nama Supir Z - A'),
            '4' => __('Nama Supir Z - A'),
        );

        echo $this->Html->tag('div', $this->Form->input('Truck.sortby', array(
            'label'=> false,
            'options'=> $find_sort,
            'div' => false,
            'data-placeholder' => 'Order By',
            'autocomplete'=> false,
            'empty'=> false,
            'error' => false,
            'onChange' => 'submit()',
            'class' => 'form-control order-by-list'
        )), array(
            'class' => 'form-group'
        ));

        echo $this->Form->end();
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo __('Laporan Truk');?>
        <small class="pull-right">
            <?php
                if(!empty($from_date) || !empty($to_date)){
                    $text = 'periode ';
                    if(!empty($from_date)){
                        $text .= date('d/m/Y', $from_date);
                    }

                    if(!empty($to_date)){
                        if(!empty($from_date)){
                            $text .= ' - ';
                        }
                        $text .= date('d/m/Y', $to_date);
                    }

                    echo $text;
                }else{
                    echo __('Semua Truk');
                }
            ?>
        </small>
    </h2>
    <div class="row no-print">
        <div class="col-xs-12">
            <button class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
            <button class="btn btn-success pull-right"><i class="fa fa-table"></i> Download Excel</button>
            <button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Download PDF</button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo __('Nopol');?></th>
                    <th><?php echo __('Merek');?></th>
                    <th><?php echo __('Pemilik');?></th>
                    <th><?php echo __('MerJenisek');?></th>
                    <th><?php echo __('Supir');?></th>
                    <th><?php echo __('Kapasitas');?></th>
                    <th><?php echo __('Tahun');?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(!empty($trucks)){
                        foreach ($trucks as $key => $truck) {
                            $content = $this->Html->tag('td', $truck['Truck']['nopol']);
                            $content .= $this->Html->tag('td', $truck['TruckBrand']['name']);
                            $content .= $this->Html->tag('td', $truck['Truck']['atas_nama']);
                            $content .= $this->Html->tag('td', $truck['TruckCategory']['name']);
                            $content .= $this->Html->tag('td', $truck['Driver']['name']);
                            $content .= $this->Html->tag('td', $truck['Truck']['capacity']);
                            $content .= $this->Html->tag('td', $truck['Truck']['tahun']);

                            echo $this->Html->tag('tr', $content);
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                            'colspan' => '7'
                        )));
                    }
                ?>
            </tbody>
        </table>
    </div>
</section>