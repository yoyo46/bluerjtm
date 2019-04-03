<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'controller' => 'titipan',
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
                ?>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Common->getCheckboxBranch();
                    ?>
                </div>
                <?php 
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'titipan', 
                                'action' => 'reports', 
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