<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
            ),
            'name' => array(
                'name' => __('Nama Group'),
            ),
            'year' => array(
                'name' => __('Umur Ekonomis'),
            ),
            'nilai' => array(
                'name' => __('Nilai Sisa'),
            ),
            'action' => array(
                'name' => __('Action'),
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/assets/searchs/groups');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_add' => array(
                    'action' => 'group_add',
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'AssetGroup', 'id');
                            $code = $this->Common->filterEmptyField($value, 'AssetGroup', 'code');
                            $name = $this->Common->filterEmptyField($value, 'AssetGroup', 'name');
                            $umur_ekonomis = $this->Common->filterEmptyField($value, 'AssetGroup', 'umur_ekonomis');
                            $nilai_sisa = $this->Common->filterEmptyField($value, 'AssetGroup', 'nilai_sisa');
                            $created = $this->Common->filterEmptyField($value, 'AssetGroup', 'created');

                            $created = $this->Common->formatDate($created);

                            $action = $this->Html->link(__('Edit'), array(
                                'action' => 'group_edit',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            $action .= $this->Html->link(__('Hapus'), array(
                                'action' => 'group_toggle',
                                $id,
                                'admin' => false,
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus group ini?'));

                            echo $this->Html->tableCells(array(
                                array(
                                    $code,
                                    $name,
                                    array(
                                        $umur_ekonomis,
                                        array(
                                            'class' => 'actions tacenter',
                                        ),
                                    ),
                                    $nilai_sisa,
                                    $created,
                                    array(
                                        $action,
                                        array(
                                            'class' => 'actions tacenter',
                                        ),
                                    ),
                                )
                            ));
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '10'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>