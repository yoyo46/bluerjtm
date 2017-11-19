<div class="box">
    <?php
            echo $this->element('blocks/common/searchs/box_header');
    ?>
    <div class="box-body">
        <?php 
                echo $this->Form->create('Search', array(
                    'url'=> $this->Html->url( array(
                        'action' => 'search',
                        'groups',
                    )), 
                    'role' => 'form',
                    'inputDefaults' => array('div' => false),
                ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('code', __('Kode Group'));
                        echo $this->element('blocks/common/forms/submit_action', array(
                            'frameClass' => 'form-group action',
                            'btnClass' => 'btn-sm',
                            'submitText' => sprintf(__('%s Search'), $this->Common->icon('search')),
                            'backText' => sprintf(__('%s Reset'), $this->Common->icon('refresh')),
                            'urlBack' => array(
                                'action' => 'groups', 
                            ),
                        ));
                ?>
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Common->buildInputForm('name', __('Nama Group'));
                ?>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>