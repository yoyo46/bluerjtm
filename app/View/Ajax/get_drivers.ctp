<?php 
        $title = !empty($title)?$title:false;
        $dataColumns = array(
            'branch' => array(
                'name' => __('Cabang'),
                'field_model' => false,
                'display' => true,
            ),
            'no_id' => array(
                'name' => __('No. ID'),
                'field_model' => false,
                'display' => true,
            ),
            'name' => array(
                'name' => __('Nama'),
                'field_model' => false,
                'display' => true,
            ),
            'alias' => array(
                'name' => __('Panggilan'),
                'field_model' => false,
                'display' => true,
            ),
            'identity_number' => array(
                'name' => __('No. Identitas'),
                'field_model' => false,
                'display' => true,
            ),
            'Address' => array(
                'name' => __('Alamat'),
                'field_model' => false,
                'display' => true,
            ),
            'phone' => array(
                'name' => __('Telepon'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        echo $this->element('blocks/ajax/search_supir');
?>
<div class="box-body table-responsive browse-form">
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
                if( !empty($drivers) ){
                    foreach ($drivers as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'Driver', 'id');
                        $branch = $this->Common->filterEmptyField($value, 'City', 'name');
                        $no_id = $this->Common->filterEmptyField($value, 'Driver', 'no_id');
                        $name = $this->Common->filterEmptyField($value, 'Driver', 'name');
                        $alias = $this->Common->filterEmptyField($value, 'Driver', 'alias');
                        $identity_number = $this->Common->filterEmptyField($value, 'Driver', 'identity_number');
                        $address = $this->Common->filterEmptyField($value, 'Driver', 'address');
                        $phone = $this->Common->filterEmptyField($value, 'Driver', 'phone');
        ?>
        <tr data-value="<?php echo $id;?>" data-change="#<?php echo $data_change;?>">
            <td><?php echo $branch;?></td>
            <td><?php echo $no_id;?></td>
            <td><?php echo $name;?></td>
            <td><?php echo $alias;?></td>
            <td><?php echo $identity_number;?></td>
            <td><?php echo $address;?></td>
            <td><?php echo $phone;?></td>
        </tr>
        <?php
                    }
                } else {
                     echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '9'
                    )));
                }
        ?>
        </tbody>
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => $data_action,
                'class' => 'ajaxModal',
                'title' => $title,
            ),
        ));
?>