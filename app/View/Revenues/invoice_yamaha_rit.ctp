<link href='https://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Carter+One' rel='stylesheet' type='text/css'>
<style type="text/css">
    body {
        margin: 0;
    }
    
    @media print {
        @page :first {
            margin-top: 0;
        }
    }
</style>
<?php
        $element = 'blocks/revenues/tables/invoice_yamaha_rit';
        $no_invoice = $this->Common->filterEmptyField($value, 'Invoice', 'no_invoice');
        $customer = $this->Common->filterEmptyField($value, 'Customer', 'name');
        $customer_address = $this->Common->filterEmptyField($value, 'Customer', 'address');

        $company = $this->Common->getDataSetting( $setting, 'company_name' );

        $dataColumns = array(
            'no' => array(
                'name' => __('No.'),
                'rowspan' => 2,
                'style' => 'text-align: center;vertical-align: middle;width: 5%;border-left: 1px solid #000;border-bottom: 1px solid #000;background-color: #ccc;',
            ),
            'nopol' => array(
                'name' => __('No Truck'),
                'rowspan' => 2,
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;width: 15%;background-color: #ccc;',
            ),
            'capacity' => array(
                'name' => __('Kap'),
                'rowspan' => 2,
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;width: 8%;background-color: #ccc;',
            ),
            'date' => array(
                'name' => __('Tanggal'),
                'rowspan' => 2,
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;width: 10%;background-color: #ccc;',
            ),
            'jml' => array(
                'name' => __('Jumlah'),
                'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;width: 8%;background-color: #ccc;',
                'child' => array(
                    'unit' => array(
                        'name' => __('Unit'),
                        'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;background-color: #ccc;',
                    ),
                ),
            ),
            'jumlah' => array(
                'name' => __('Jumlah'),
                'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;width: 8%;background-color: #ccc;',
                'child' => array(
                    'unit' => array(
                        'name' => __('RIT'),
                        'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;background-color: #ccc;',
                    ),
                ),
            ),
            'tarif' => array(
                'name' => __('Tarif/'),
                'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;width: 10%;background-color: #ccc;',
                'child' => array(
                    'unit' => array(
                        'name' => __('Per RIT'),
                        'style' => 'text-align: center;border-bottom: 1px solid #000;border-left: 1px solid #000;background-color: #ccc;',
                    ),
                ),
            ),
            'note' => array(
                'name' => __('Ket'),
                'style' => 'text-align: center;vertical-align: middle;border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;background-color: #ccc;',
                'colspan' => 2,
                'rowspan' => 2,
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $sub_module_title,
            ));
        } else {
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        	$this->Html->addCrumb($sub_module_title);
?>
<section class="content invoice">
    <h2 class="page-header hidden-print">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>

    <div class="action-print pull-right">
        <?php
                echo $this->Html->link('<i class="fa fa-print"></i> print', 'javascript:', array(
                    'class' => 'btn btn-default hidden-print print-window',
                    'escape' => false
                ));
        ?>
    </div>
    <?php 
            echo $this->Common->_getPrint();
    ?>
    <div class="clear"></div>

    <div class="table-responsive">
        <div class="page-header" style="margin-bottom: 20px;border-bottom: 2px solid #000;">
            <h3 style="font-family: 'Carter One', cursive;text-transform: uppercase;margin: 0 0 5px;">PT. Roda Jaya Tunas Mas</h3>
            <p style="font-size: 12px;margin-bottom: 5px;line-height: 15px;">Jl. Pegangsaan dua raya no. 88 B Kelapa Gading Jakarta 14250</p>
            <p style="font-size: 12px;margin-bottom: 0;line-height: 15px;">Tlp. 021-468.21201 / email: naila@rjtm.co.id</p>
        </div>
        <?php 
                echo $this->Html->tag('h2', __('Faktur Jasa Angkutan'), array(
                    'style' => 'font-family: \'Cookie\', cursive;text-align: center;font-style: italic;font-size: 32px;margin: 0 0 25px;',
                ));
        ?>
        <div class="sub-title" style="margin-bottom: 20px;">
            <?php 
                    echo $this->element('blocks/common/tables/sub_header', array(
                        'labelName' => __('No Faktur'),
                        'value' => $no_invoice,
                    ));
                    echo $this->element('blocks/common/tables/sub_header', array(
                        'labelName' => __('Nama Pelanggan'),
                        'value' => $customer,
                    ));
                    echo $this->element('blocks/common/tables/sub_header', array(
                        'labelName' => __('Alamat Pelanggan'),
                        'value' => $customer_address,
                    ));
                    echo $this->element('blocks/common/tables/sub_header', array(
                        'labelName' => __('Keterangan'),
                        'value' => __('Jasa Angkut Sepeda Motor Yamaha'),
                    ));
            ?>
        </div>
        <table class="table uppercase" id="yamaha-rit" style="border-top: 1px solid #000;">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
                    
                    echo $this->Html->tag('tbody', $this->element($element));
            ?>
        </table>
    </div><!-- /.box-body -->
</section>
<?php 
        }
?>