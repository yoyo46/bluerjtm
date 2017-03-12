<?php 

        $full_name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');
        $element = 'blocks/ttuj/tables/report_recap_sj';
        $dataColumns = array(
            'no_ttuj' => array(
                'name' => __('NO TTUJ'),
                'field_model' => 'Ttuj.no_ttuj',
                'style' => 'text-align: center;width: 150px;',
                'data-options' => 'field:\'no_ttuj\',width:120',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'date' => array(
                'name' => __('TANGGAL'),
                'field_model' => 'Ttuj.ttuj_date',
                'align' => 'center',
                'style' => 'text-align: center;width: 100px;',
                'data-options' => 'field:\'ttuj_date\',width:100',
            ),
            'city_name' => array(
                'name' => __('Dari - TUJUAN'),
                'field_model' => 'Ttuj.from_city_name',
                'style' => 'text-align: center;width: 180px;',
                'data-options' => 'field:\'city_name\',width:180',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'nopol' => array(
                'name' => __('NOPOL'),
                'field_model' => 'Ttuj.nopol',
                'style' => 'text-align: center;width: 120px;',
                'data-options' => 'field:\'nopol\',width:100',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'driver_name' => array(
                'name' => __('Supir'),
                'field_model' => 'Ttuj.driver_name',
                'style' => 'text-align: center;width: 150px;',
                'data-options' => 'field:\'driver_name\',width:150',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'unit' => array(
                'name' => __('UNIT'),
                'align' => 'center',
                'style' => 'text-align: center;width: 80px;',
                'data-options' => 'field:\'unit\',width:80',
            ),
            'sj_receipt' => array(
                'name' => __('SJ KEMBALI'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'sj_receipt\',width:100',
                'align' => 'center',
            ),
            'date_receipt' => array(
                'name' => __('TGL SJ KEMBALI'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'date_receipt\',width:100',
                'align' => 'center',
            ),
            'sj_not_receipt' => array(
                'name' => __('SJ BELUM KEMBALI'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'sj_not_receipt\',width:100',
                'align' => 'center',
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', true );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => sprintf('%s - %s', $module_title, $period_text),
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/ttuj/searchs/report_recap_sj');
?>
<section class="content invoice">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => sprintf('%s - %s', $module_title, $period_text),
            ));
            echo $this->Common->_getPrint(array(
                '_attr' => array(
                    'escape' => false,
                ),
            ));
    ?>
    <div class="table-responsive">
        <?php 
                if(!empty($values)){
        ?>
        <table id="tt" class="table table-bordered table-colored sorting">
            <thead>
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <?php 
                    echo $this->Html->tag('tbody', $this->element($element));
            ?>
        </table>
        <?php 
                } else {
                    echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                    ));
                }
        ?>
    </div><!-- /.box-body -->
    <?php 
            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            echo $this->Html->tag('div', $this->element('pagination'), array(
                'class' => 'pagination-report'
            ));
    ?>
</div>
<?php 
        }
?>