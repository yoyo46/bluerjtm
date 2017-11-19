<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
                'field_model' => 'Branch.code',
                'display' => true,
            ),
            'name' => array(
                'name' => __('Nama'),
                'field_model' => 'Branch.name',
                'display' => true,
            ),
            'city' => array(
                'name' => __('Kota'),
                'field_model' => 'City.name',
                'display' => true,
            ),
            'address' => array(
                'name' => __('Alamat'),
                'field_model' => 'Branch.address',
                'display' => true,
            ),
            'phone' => array(
                'name' => __('No Telepon'),
                'field_model' => 'Branch.phone',
                'display' => true,
            ),
            'plant' => array(
                'name' => __('Plant ?'),
                'field_model' => 'Branch.is_plant',
                'display' => true,
            ),
            'head_office' => array(
                'name' => __('Head Office ?'),
                'field_model' => 'Branch.head_office',
                'display' => true,
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Customer.created',
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
        echo $this->element('blocks/settings/search_branches');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'settings',
                        'action' => 'branch_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
    </div>
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
            <?php
                    if(!empty($branchs)){
                        foreach ($branchs as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Branch', 'id');
                            $code = $this->Common->filterEmptyField($value, 'Branch', 'code');
                            $name = $this->Common->filterEmptyField($value, 'Branch', 'name');
                            $city = $this->Common->filterEmptyField($value, 'City', 'name');
                            $address = $this->Common->filterEmptyField($value, 'Branch', 'address');
                            $phone = $this->Common->filterEmptyField($value, 'Branch', 'phone');
                            $created = $this->Common->filterEmptyField($value, 'Branch', 'created');
                            $plant = $this->Common->filterEmptyField($value, 'Branch', 'is_plant');
                            $head_office = $this->Common->filterEmptyField($value, 'Branch', 'is_head_office');

                            $plant = $this->Common->getCheckStatus( $plant );
                            $head_office = $this->Common->getCheckStatus( $head_office );
            ?>
            <tr>
                <td><?php echo $code;?></td>
                <td><?php echo $name;?></td>
                <td><?php echo $city;?></td>
                <td><?php echo $address;?></td>
                <td><?php echo $phone;?></td>
                <td class="text-center"><?php echo $plant;?></td>
                <td class="text-center"><?php echo $head_office;?></td>
                <td><?php echo $this->Common->customDate($created);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link(__('Edit'), array(
                                'controller' => 'settings',
                                'action' => 'branch_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'branch_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'data-alert' => __('Anda yakin ingin menghapus data cabang ini ?'),
                            ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '7'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>