<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'debt',
                        'action' => 'search',
                        'reports',
                    )), 
                    'class' => 'form-search',
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('name', __('Nama'));
                        echo $this->Common->buildInputForm('phone', __('No. Telp'));
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'debt', 
                                'action' => 'reports', 
                            ),
                        ));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('type', __('Kategori'), array(
                            'empty' => __('Pilih Kategori'),
                            'options' => array(
                                'Karyawan' => __('Karyawan'),
                                'Supir' => __('Supir'),
                            ),
                        ));
                ?>
                <div class="form-group">
                    <?php 
                            echo $this->Common->getCheckboxBranch();
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