<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_truck');

        echo $this->Form->create('Truck', array(
            'url'=> $this->Html->url( array(
                'controller' => 'trucks',
                'action' => 'search',
                'index'
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

        echo $this->Html->tag('div', $this->Form->input('Property.sortby', array(
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
        <i class="fa fa-globe"></i> Laporan Truk
        <small class="pull-right">Periode: 01/10/2014 - 31/10/2014</small>
    </h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nopol</th>
                    <th>Merek</th>
                    <th>Pemilik</th>
                    <th>Jenis</th>
                    <th>Supir</th>
                    <th>Kapasitas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>A 6494 P</td>
                    <td>HINO</td>
                    <td>RJTM</td>
                    <td>Trail</td>
                    <td>Randy</td>
                    <td>120</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row no-print">
        <div class="col-xs-12">
            <button class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
            <button class="btn btn-success pull-right"><i class="fa fa-table"></i> Download Excel</button>
            <button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Download PDF</button>
        </div>
    </div>
</section>