<?php 
        $coa_type = !empty($coa_type)?$coa_type:false;
        $element = 'blocks/cashbanks/tables/balance_sheets';

        $this->Html->addCrumb($module_title);

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
        $addStyle = 'width: 100%;height: 550px;';
        $addClass = 'easyui-datagrid';
?>
<div class="table-responsive">
    <?php 
            if(!empty($values)){
    ?>
    <table id="tt" class="table sorting <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" singleSelect="true">
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
                echo $this->Html->tag('tbody', $this->element($element, array(
                    'values' => $values,
                    'coa_type' => $coa_type,
                    'main_total' => true,
                )));
        ?>
    </table>
    <?php 
            } else {
                echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                    'class' => 'alert alert-warning text-center',
                ));
            }
    ?>
</div>