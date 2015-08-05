<?php 
        $data_action = !empty($data_action)?$data_action:false;
        $dataColumns = array(
            'title' => array(
                'name' => __('Nama'),
                'field_model' => 'UangKuli.title',
                'display' => true,
            ),
            'customer' => array(
                'name' => __('Customer'),
                'field_model' => 'Customer.name',
                'display' => ($data_action == 'bongkar')?true:false,
            ),
            'city' => array(
                'name' => __('Kota Asal'),
                'field_model' => 'City.name',
                'display' => true,
            ),
            'modified' => array(
                'name' => __('Diubah'),
                'field_model' => 'UangKuli.modified',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_uang_kulis');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'settings',
                        'action' => 'uang_kuli_add',
                        $data_action,
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
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
                        if(!empty($uangKulis)){
                            foreach ($uangKulis as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'UangKuli', 'id');
                                $title = $this->Common->filterEmptyField($value, 'UangKuli', 'title');
                                $customer = $this->Common->filterEmptyField($value, 'Customer', 'customer_name');
                                $city = $this->Common->filterEmptyField($value, 'City', 'name');
                                $modified = $this->Common->filterEmptyField($value, 'UangKuli', 'modified');
                ?>
                <tr>
                    <td><?php echo $title;?></td>
                    <?php 
                            if( $data_action == 'bongkar' ) {
                                echo $this->Html->tag('td', $customer);
                            }
                    ?>
                    <td><?php echo $city;?></td>
                    <td><?php echo $this->Time->niceShort($modified);?></td>
                    <td class="action">
                        <?php 
                                echo $this->Html->link('Edit', array(
                                    'controller' => 'settings',
                                    'action' => 'uang_kuli_edit',
                                    $data_action,
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'settings',
                                    'action' => 'uang_kuli_toggle',
                                    $data_action,
                                    $id,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Anda yakin ingin menghapus data Uang Kuli ini?'));
                        ?>
                    </td>
                </tr>
                <?php
                            }
                        } else {
                            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                                'class' => 'alert alert-warning text-center',
                                'colspan' => '8'
                            )));
                        }
                ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>