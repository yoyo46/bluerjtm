<?php 
        $data = $this->request->data;
        $optionCode = Common::hashEmptyField($data, 'Search.product_code_options');
?>
<div id="search-select-multiple" class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'products',
                        'action' => 'search',
                        'stock_cards',
                    )), 
                    'class' => 'form-search',
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                            'textGroup' => $this->Common->icon('calendar'),
                            'positionGroup' => 'positionGroup',
                            'class' => 'form-control pull-right date-range',
                        ));
                        echo $this->Common->buildInputForm('group', __('Group Barang'), array(
                            'empty' => __('- Pilih Group -'),
                            'class' => 'form-control chosen-select',
                            'options' => !empty($productCategories)?$productCategories:array(),
                        ));
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'products', 
                                'action' => 'stock_cards', 
                            ),
                        ));
                ?>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php
                            echo $this->Form->label('product_code', __('Kode Barang'));
                    ?>
                    <div class="row temp-document-picker">
                        <div class="col-sm-10">
                            <?php
                                    echo $this->Common->buildInputForm('product_code', false, array(
                                        'type' => 'select',
                                        'class' => 'chosen-select form-control full',
                                        'frameClass' => false,
                                        'attributes' => array(
                                            'options' => $optionCode,
                                            'multiple' => true,
                                            'data-tag' => 'true',
                                        ),
                                    ));
                            ?>
                        </div>
                        <div class="col-sm-2 hidden-xs">
                            <?php 
                                    $attrBrowse = array(
                                        'class' => 'ajaxCustomModal btn bg-maroon',
                                        'escape' => false,
                                        'allow' => true,
                                        'title' => __('List Barang'),
                                    );
                                    $urlBrowse = array(
                                        'controller'=> 'ajax', 
                                        'action' => 'products',
                                        'type' => 'select-multiple',
                                        'admin' => false,
                                    );
                                    echo $this->Html->link($this->Common->icon('plus-square'), $urlBrowse, $attrBrowse);
                            ?>
                        </div>
                    </div>
                </div>
                <?php 
                        // Custom Otorisasi
                        echo $this->Common->getCheckboxBranch();
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