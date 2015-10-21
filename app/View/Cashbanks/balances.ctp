<?php
        $dataColumns = array(
            'date' => array(
                'name' => __('Tgl'),
                'class' => 'text-center',
            ),
            'nodoc' => array(
                'name' => __('COA'),
                'class' => 'text-center',
            ),
            'desc' => array(
                'name' => __('Tipe'),
                'class' => 'text-left',
            ),
            'balance' => array(
                'name' => __('Balance'),
                'class' => 'text-right',
            ),
            'action' => array(
                'name' => __('Action'),
                'class' => 'text-center',
            ),
        );
        
        echo $this->element('blocks/cashbanks/searchs/balances');
        
        $dataColumns['desc']['style'] = 'text-align: left;width: 40%;';
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title">
            <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
        </h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'cashbanks',
                        'action' => 'balance_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
    </div>
    <?php 
            if(!empty($values)){
    ?>
    <div class="table-responsive">
        <table class="table journal table-no-border red" id="journal-report">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
                    
            ?>
            <tbody>
                <?php
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Balance', 'id');
                            $type = $this->Common->filterEmptyField($value, 'Balance', 'type');
                            $saldo = $this->Common->filterEmptyField($value, 'Balance', 'saldo');
                            $date = $this->Common->filterEmptyField($value, 'Balance', 'date');

                            $coa_name = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');

                            $customDate = $this->Common->formatDate($date, 'd/m/Y');
                            $customSaldo = $this->Common->getFormatPrice($saldo);
                ?>
                <tr>
                    <?php
                            echo $this->Html->tag('td', $customDate);
                            echo $this->Html->tag('td', $coa_name);
                            echo $this->Html->tag('td', $type);
                            echo $this->Html->tag('td', $customSaldo, array(
                                'style' => 'text-align:right;'
                            ));
                    ?>
                    <td class="action">
                        <?php 
                                echo $this->Html->link(__('Detail'), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'balance_detail',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                ));
                                echo $this->Html->link(__('Void'), array(
                                    'controller' => 'cashbanks',
                                    'action' => 'balance_void',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                ), __('Apakah Anda yakin akan membatalkan data ini?'));
                        ?>
                    </td>
                </tr>
                <?php
                        }
                ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php
            } else {
                echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                    'class' => 'alert alert-warning text-center',
                ));
            }
    ?>
</div>