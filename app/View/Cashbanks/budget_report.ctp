<?php 
        $full_name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');
        
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/cashbanks/searchs/budget_report');
?>
<section class="content invoice">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
            ));
            echo $this->Common->_getPrint(array(
                '_attr' => array(
                    'escape' => false,
                    'class' => 'ajaxLink',
                    'data-form' => '#form-search',
                ),
                '_ajax' => true,
                'url_excel' => array(
                    'controller' => 'reports',
                    'action' => 'generate_excel',
                    'budget_report',
                ),
            ));
    ?>
    <div class="table-responsive">
        <?php 
                if(!empty($values)){
                    $dataColumns = array();

                    if( !empty($values[0]) ) {
                        foreach ($values[0] as $label => $value) {
                            $attr = Common::_callUnset($value, array(
                                'field_model',
                                'width',
                            ));

                            $dataColumns[] = array_merge(array(
                                'name' => $label,
                                'field_model' => Common::hashEmptyField($value, 'field_model'),
                                'class' => 'text-center',
                            ), $attr);
                        }
                    }

                    $fieldColumn = $this->Common->_generateShowHideColumn($dataColumns, 'field-table');
        ?>
        <table class="table table-bordered easyui-datagrid" style="<?php echo !empty($_freeze)?'width: 100%;height: 550px;':''; ?>" singleSelect="true">
            <?php
                    if(!empty($fieldColumn)){
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn), array(
                            'frozen' => 'true',
                        ));
                    }
            ?>
            <tbody>
                <?php
                        $unset = array(
                            'text',
                            'field_model',
                            'rowspan',
                        );

                        foreach($values as $key => $value){
                            $content = array();

                            if( !empty($value) ) {
                                foreach ($value as $key => $val) {
                                    $title = Common::hashEmptyField($val, 'text', false, false, false);
                                    $childs = Common::hashEmptyField($val, 'child');
                                    $attr = Common::_callUnset($val, $unset);

                                    if( !empty($childs) ) {
                                        foreach ($childs as $key => $child) {
                                            $title = Common::hashEmptyField($child, 'text');
                                            $attr = Common::_callUnset($child, $unset);

                                            $content[] = array(
                                                $title,
                                                $attr,
                                            );
                                        }
                                    } else {
                                        $content[] = array(
                                            $title,
                                            $attr,
                                        );
                                    }
                                }
                            }
                            echo($this->Html->tableCells(array($content)));
                        }
                ?>
            </tbody>
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
<script type="text/javascript">
    function targetBudget(value,row,index, rel){
        var tmp = 'row.month_budget_'+rel;

        var target = eval(tmp);
        target = parseInt(target.replace(/,/gi, "").replace(/-/gi, ""));

        if( isNaN(target) ) {
            target = 0;
        }
        
        value = value.toString().replace(/,/gi, "");

        if( value.toString().indexOf('(') >= 0 ) {
            value = value.toString().replace(/\(/gi, "").replace(/\)/gi, "");
            value = value * -1;
        }

        value = parseInt(value);
        if ( isNaN(value) ){
            value = 0;
        }

        if (!isNaN(value) && !isNaN(target) && target != 0){
            if( value >= target ) {
                return 'background-color:#f2dede;color:#a94442;';
            } else {
                return 'background-color:#dff0d8;color:#3c763d;';
            }
        } else {
            return false;
        }
    }
    function targetSelisih(value,row,index, rel){
        value = value.toString().replace(/,/gi, "");

        if( value.toString().indexOf('(') >= 0 ) {
            value = value.toString().replace(/\(/gi, "").replace(/\)/gi, "");
            value = value * -1;
        }

        value = parseInt(value);

        if ( !isNaN(value) ){
            if( value < 0 ) {
                return 'background-color:#f2dede;color:#a94442;';
            } else {
                return 'background-color:#dff0d8;color:#3c763d;';
            }
        } else {
            return false;
        }
    }
    function targetPercent(value,row,index, rel){
        var tmp = 'row.month_selisih_'+rel;

        var selisih = eval(tmp);
        selisih = selisih.replace(/,/gi, "").replace(/-/gi, "");
        if( selisih.toString().indexOf('(') >= 0 ) {
            selisih = selisih.toString().replace(/\(/gi, "").replace(/\)/gi, "");
            selisih = selisih * -1;
        }
        selisih = parseInt(selisih);

        value = value.toString().replace(/,/gi, "");
        if( value.toString().indexOf('(') >= 0 ) {
            value = value.toString().replace(/\(/gi, "").replace(/\)/gi, "");
            value = value * -1;
        }

        value = parseInt(value);

        if( !isNaN(selisih) ) {
            if ( !isNaN(value) ){
                if( value < 0 ) {
                    return 'background-color:#f2dede;color:#a94442;';
                } else {
                    return 'background-color:#dff0d8;color:#3c763d;';
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
</script>