<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'nomor_id' => array(
                'name' => __('No. ID'),
                'field_model' => 'Driver.id',
                'display' => true,
            ),
            'nopol' => array(
                'name' => __('Truk'),
                'field_model' => 'Truk.nopol',
                'sorting' => false,
                'display' => true,
            ),
            'name' => array(
                'name' => __('Nama Lengkap'),
                'field_model' => 'Driver.driver_name',
                'display' => true,
            ),
            'identity_number' => array(
                'name' => __('No. KTP'),
                'field_model' => 'Driver.identity_number',
                'display' => false,
            ),
            'address' => array(
                'name' => __('Alamat'),
                'field_model' => 'Driver.address',
                'display' => true,
            ),
            'city' => array(
                'name' => __('Kota'),
                'field_model' => 'Driver.city',
                'display' => true,
            ),
            'provinsi' => array(
                'name' => __('Provinsi'),
                'field_model' => 'Driver.provinsi',
                'display' => false,
            ),
            'no_hp' => array(
                'name' => __('No. HP'),
                'field_model' => 'Driver.no_hp',
                'display' => true,
            ),
            'phone' => array(
                'name' => __('No. Telp'),
                'field_model' => 'Driver.phone',
                'display' => true,
            ),
            'tempat_lahir' => array(
                'name' => __('Tempat Lahir'),
                'field_model' => 'Driver.tempat_lahir',
                'display' => false,
            ),
            'birth_date' => array(
                'name' => __('Tgl Lahir'),
                'field_model' => 'Driver.birth_date',
                'display' => false,
            ),
            'sim' => array(
                'name' => __('Jenis SIM'),
                'field_model' => 'Driver.jenis_sim_id',
                'display' => true,
            ),
            'no_sim' => array(
                'name' => __('No. SIM'),
                'field_model' => 'Driver.no_sim',
                'display' => false,
            ),
            'expired_date_sim' => array(
                'name' => __('Tgl Berakhir SIM'),
                'field_model' => 'Driver.expired_date_sim',
                'display' => false,
            ),
            'kontak_darurat_name' => array(
                'name' => __('Nama Kontak Darurat'),
                'field_model' => 'Driver.kontak_darurat_name',
                'display' => true,
            ),
            'kontak_darurat_no_hp' => array(
                'name' => __('No. Hp Kontak Darurat'),
                'field_model' => 'Driver.kontak_darurat_no_hp',
                'display' => true,
            ),
            'kontak_darurat_phone' => array(
                'name' => __('Telp Kontak Darurat'),
                'field_model' => 'Driver.kontak_darurat_phone',
                'display' => false,
            ),
            'relation' => array(
                'name' => __('Hubungan'),
                'field_model' => 'Driver.driver_relation_id',
                'display' => false,
            ),
            'join_date' => array(
                'name' => __('Tgl Diterima'),
                'field_model' => 'Driver.join_date',
                'display' => false,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Driver.status',
                'display' => true,
            ),
        );
        $showHideColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'show-hide' );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', $data_action );

        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $tdStyle = '';
            $border = 0;

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
            } else {
                $this->Html->addCrumb($sub_module_title);
                echo $this->element('blocks/trucks/search_report_driver');
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo __('Laporan Supir');?>
    </h2>
    <?php 
            echo $this->Common->_getPrint(array(
                '_attr' => array(
                    'class' => 'ajaxLink',
                    'data-request' => '#form-report',
                ),
            ), $showHideColumn);
    ?>
    <div class="table-responsive center-table">
        <?php 
                }
        ?>
        <table class="table table-bordered sorting" border="<?php echo $border; ?>">
            <thead>
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                        echo $this->element('blocks/trucks/tables/driver_reports');
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($drivers)){
                        echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                        ));
                    }
        ?>
    </div>
    <?php 
            }

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            
            if( $data_action != 'excel' ) {
                echo $this->Html->tag('div', $this->element('pagination'), array(
                    'class' => 'pagination-report'
                ));
    ?>
</section>
<?php
        }
    } else{
        echo $this->element('blocks/common/tables/export_pdf', array(
            'tableHead' => $fieldColumn,
            'tableBody' => $this->element('blocks/trucks/tables/driver_reports'),
        ));
    }
?>