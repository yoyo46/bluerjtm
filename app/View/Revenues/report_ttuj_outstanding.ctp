<?php 
        $full_name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');
        
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/search_report_ttuj_outstanding');
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
                    'ttuj_outstanding',
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
        <table class="table table-bordered">
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
                                    $title = Common::hashEmptyField($val, 'text', false, array(
                                        'isset' => true,
                                    ));
                                    $childs = Common::hashEmptyField($val, 'child');
                                    $attr = Common::_callUnset($val, $unset);

                                    if( !empty($childs) ) {
                                        foreach ($childs as $key => $child) {
                                            $title = Common::hashEmptyField($child, 'text', false, array(
                                                'isset' => true,
                                            ));
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