<?php 
        $productCategories = !empty($productCategories)?$productCategories:false;
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'products',
                        'action' => 'search',
                        'min_stock_report'
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                    'class' => 'form-search',
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('code',array(
                                'label'=> __('Kode Barang'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <?php
                        echo $this->Common->_callInputForm('status_stock', array(
                            'label' => __('Status *'),
                            'empty' => __('Pilih Status'),
                            'class' => 'form-control',
                            'options' => array(
                                'stock_empty' => __('Stok Kosong'),
                                'stock_minimum' => __('Kurang dari Minimum Stok'),
                            ),
                        ));
                ?>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('name',array(
                                'label'=> __('Nama Barang'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('group',array(
                                'label'=> __('Group Barang'),
                                'class'=>'form-control chosen-select',
                                'required' => false,
                                'options' => $productCategories,
                                'empty' => __('Pilih Group'),
                            ));
                    ?>
                </div>
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'controller' => 'products', 
                                'action' => 'min_stock_report', 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
                            ));
                    ?>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->hidden('title',array(
                    'value'=> $sub_module_title,
                ));
                echo $this->Form->end();
        ?>
    </div>
</div>