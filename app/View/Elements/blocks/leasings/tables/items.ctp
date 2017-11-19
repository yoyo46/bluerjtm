<?php                        
        $value = !empty($value)?$value:false;
        $id = $this->Common->filterEmptyField($value, 'LeasingDetail', 'id');
        $truck_id = $this->Common->filterEmptyField($value, 'LeasingDetail', 'truck_id');
        $nopol = $this->Common->filterEmptyField($value, 'LeasingDetail', 'nopol');
        $price = $this->Common->filterEmptyField($value, 'LeasingDetail', 'price');
        $note = $this->Common->filterEmptyField($value, 'LeasingDetail', 'note');
        $asset_group_id = $this->Common->filterEmptyField($value, 'LeasingDetail', 'asset_group_id');

        $customTotal = !empty($total)?$total:0;

        $hiddenContent = $this->Form->hidden('LeasingDetail.id.', array(
            'value' => $id,
        ));
        $hiddenContent .= $this->Form->hidden('LeasingDetail.truck_id.', array(
            'value' => $truck_id,
        ));
?>
<tr classs="child child-<?php echo $idx; ?>" rel="<?php echo $idx; ?>">
    <td>
        <?php
                echo $this->Form->input('LeasingDetail.nopol.', array(
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control',
                    'required' => false,
                    'value' => $nopol,
                ));
                echo $this->Form->error('LeasingDetail.'.$idx.'.nopol');
                echo $hiddenContent;
        ?>
    </td>
    <td>
        <?php 
                echo $this->Form->input('LeasingDetail.note.', array(
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control',
                    'required' => false,
                    'value' => $note,
                ));
                echo $this->Form->error('LeasingDetail.'.$idx.'.note');
        ?>
    </td>
    <td>
        <?php 
                echo $this->Form->input('LeasingDetail.asset_group_id.', array(
                    'label' => false,
                    'class' => 'form-control',
                    'required' => false,
                    'empty' => __('Pilih Group Asset'),
                    'options' => $assetGroups,
                    'value' => $asset_group_id,
                ));
                echo $this->Form->error('LeasingDetail.'.$idx.'.asset_group_id');
        ?>
    </td>
    <td align="right box-price">
        <?php 
                echo $this->Form->input('LeasingDetail.price.', array(
                    'type' => 'text',
                    'label' => false,
                    'class' => 'form-control price-leasing-truck input_price input_number text-right',
                    'required' => false,
                    'value' => $customTotal
                ));
                echo $this->Form->error('LeasingDetail.'.$idx.'.price');
        ?>
    </td>
    <td class="action-table">
        <?php
            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                'class' => 'delete-custom-field btn btn-danger btn-xs',
                'escape' => false,
                'action_type' => 'leasing_first'
            ));
        ?>
    </td>
</tr>