<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'products',
                        'action' => 'search',
                        'current_stock_reports',
                    )), 
                    'class' => 'form-search',
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('code', __('Kode Barang'));
                        echo $this->Common->buildInputForm('group', __('Group Barang'), array(
                            'empty' => __('- Pilih Group -'),
                            'class' => 'form-control chosen-select',
                            'options' => !empty($productCategories)?$productCategories:array(),
                        ));
                        // Custom Otorisasi
                        echo $this->Common->getCheckboxBranch();
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('name', __('Nama Barang'));
                        echo $this->Common->buildInputForm('unit', __('Unit'), array(
                            'empty' => __('- Pilih Unit -'),
                            'class' => 'form-control chosen-select',
                            'options' => !empty($productUnits)?$productUnits:array(),
                        ));
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'products', 
                                'action' => 'current_stock_reports', 
                            ),
                        ));
                ?>
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