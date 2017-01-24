<?php 
        $data = $this->request->data;
        $element = 'blocks/assets/tables/reports/items';
        $child = array();
        $nowYear = date('Y');

        $full_name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');
        $status = $this->Common->filterEmptyField($data, 'Search', 'status');
        $periode_closing = Configure::read('__Site.Closing.periode');
        $lastMonthYear = $this->Common->formatDate($periode_closing, 'M Y');

        // if( $year < $nowYear ) {
        //     $lastYear = $this->Common->formatDate(sprintf('%s-12', $year), 't M Y');
        // }
        if( !empty($periode_closing) ) {
            $closingDate = $this->Common->formatDate($periode_closing, 't M Y');
        } else {
            $closingDate = date('d M Y');
        }

        if( !empty($data_action) ){
            $headerRowspan = 2;
        } else {
            $headerRowspan = false;
        }

        for ($i=1; $i <= 12; $i++) { 
            $monthName = date("F", mktime(0, 0, 0, $i, 10));
            $child[$monthName] = array(
                'name' => $monthName,
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\''.$monthName.'\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
            );
        }

        $dataColumns = array(
            'name' => array(
                'name' => __('Nama Asset'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'date\',width:100',
                'rowspan' => $headerRowspan,
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'style' => 'text-align: center;left: 120px;vertical-align: middle;',
                'data-options' => 'field:\'nodoc\',width:120',
                'rowspan' => $headerRowspan,
            ),
            'contract' => array(
                'name' => __('No Kontrak'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'branch\',width:100',
                'rowspan' => $headerRowspan,
            ),
            'year' => array(
                'name' => __('Tahun Asset'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'nottuj\',width:120',
                'align' => 'center',
                'rowspan' => $headerRowspan,
            ),
            'perolehan' => array(
                'name' => __('Nilai Perolehan'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'ttujdate\',width:120',
                'fix_column' => true,
                'align' => 'right',
                'mainalign' => 'center',
                'rowspan' => $headerRowspan,
            ),
            'dep' => array(
                'name' => __('Dep/Bln'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'nopol\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
                'rowspan' => 2,
            ),
            'date' => array(
                'name' => __('Tgl Neraca'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'customer\',width:120',
                'align' => 'center',
                'rowspan' => 2,
            ),
            'percent' => array(
                'name' => __('%'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'from\',width:80',
                'align' => 'center',
                'rowspan' => 2,
            ),
            'month' => array(
                'name' => sprintf(__('Penyusutan Selama %s'), $year),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'month\'',
                'align' => 'center',
                'child' => $child,
            ),
            'depr' => array(
                'name' => sprintf(__('Penystn. Selama %s'), $year),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'depr\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
                'rowspan' => 2,
            ),
            'ak_peyusutan' => array(
                'name' => sprintf(__('Ak.Penyusutan s/d %s'), $closingDate),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'ak_peyusutan\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
                'rowspan' => 2,
            ),
            'nilai_buku' => array(
                'name' => sprintf(__('Nilai Buku PER. %s'), $lastMonthYear),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'nilai_buku\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
                'rowspan' => 2,
            ),
            'sisa' => array(
                'name' => __('Sisa Bulan'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'sisa\',width:80',
                'align' => 'center',
                'rowspan' => 2,
            ),
        );

        if( $status == 'sold' ) {
            $dataColumns = array_merge($dataColumns, array(
                'sell' => array(
                    'name' => __('Harga Jual'),
                    'style' => 'text-align: left;vertical-align: middle;',
                    'data-options' => 'field:\'sell\',width:120',
                    'align' => 'right',
                    'mainalign' => 'center',
                    'rowspan' => 2,
                ),
                'profit' => array(
                    'name' => __('Laba'),
                    'style' => 'text-align: left;vertical-align: middle;',
                    'data-options' => 'field:\'profit\',width:120',
                    'align' => 'right',
                    'mainalign' => 'center',
                    'rowspan' => 2,
                ),
            ));
        }

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', true );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($sub_module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
            $addStyle = 'width: 100%;height: 550px;';
            $addClass = 'easyui-datagrid';
            // $addClass = '';

            echo $this->element('blocks/assets/searchs/reports');
?>
<section class="content invoice">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
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
        <table id="tt" class="table table-bordered <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" singleSelect="true">
            <thead frozen="true">
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