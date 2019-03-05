<?php 
        $title = !empty($title)?$title:false;
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No. Doc'),
            ),
            'group' => array(
                'name' => __('Cabang'),
            ),
            'nopol' => array(
                'name' => __('Nopol'),
                'class' => 'text-center',
            ),
            'driver' => array(
                'name' => __('Supir'),
            ),
            'laka_date' => array(
                'name' => __('Tgl LAKA'),
            ),
            'location' => array(
                'name' => __('Lokasi LAKA'),
            ),
            'capacity' => array(
                'name' => __('Status Muatan'),
                'class' => 'text-center',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        echo $this->element('blocks/ajax/searchs/laka', array(
            'urlForm' => array(
                'controller' => 'ajax',
                'action' => 'search',
                'laka_picker',
                'admin' => false,
            ),
            'urlReset' => array(
                'controller' => 'ajax',
                'action' => 'laka_picker',
            ),
        ));
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <?php
                if( !empty($fieldColumn) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                }
        ?>
        <?php
                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'Laka', 'id');
                        $nodoc = $this->Common->filterEmptyField($value, 'Laka', 'nodoc');
                        $document_date = $this->Common->filterEmptyField($value, 'Laka', 'tgl_laka');
                        $lokasi = $this->Common->filterEmptyField($value, 'Laka', 'lokasi_laka');
                        $status_muatan = $this->Common->filterEmptyField($value, 'Laka', 'status_muatan');
                        $nopol = $this->Common->filterEmptyField($value, 'Laka', 'nopol');
                        
                        $driver = $this->Common->_callGetDriver($value);

                        $status_muatan = strtoupper($status_muatan);
                        $document_date = $this->Common->formatDate($document_date, 'd M Y');

                        $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');
                        $attr = array(
                            'data-value' => $$return_value,
                            'data-change' => $target,
                            'data-trigger' => 'change',
                        );

                        echo $this->Html->tableCells(array(
                            array(
                                $nodoc,
                                $branch,
                                array(
                                    $nopol,
                                    array(
                                        'class' => 'text-center',
                                    ),
                                ),
                                $driver,
                                $document_date,
                                $lokasi,
                                array(
                                    $status_muatan,
                                    array(
                                        'class' => 'text-center',
                                    ),
                                ),
                            )
                        ), $attr, $attr);
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '8'
                    )));
                }
        ?>
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => 'browse-form',
                'class' => 'ajaxModal',
                'title' => $title,
            ),
        ));
?>