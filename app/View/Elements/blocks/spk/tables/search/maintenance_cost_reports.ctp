<div class="box box-primary">
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
                        'controller' => 'spk',
                        'action' => 'search',
                        'maintenance_cost_report'
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
                            echo $this->Form->label('fromMonth', __('Tahun'));
                            echo $this->Form->year('from', 1949, date('Y'), array(
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => false,
                            ));
                    ?>
                </div>
                <?php
                        echo $this->Common->buildInputForm('nopol', __('No Pol'));
                ?>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            // Custom Otorisasi
                            echo $this->Common->getCheckboxBranch();
                    ?>
                </div>
                <?php
                        
                        echo $this->element('blocks/common/searchs/box_action', array(
                            '_url' => array(
                                'controller' => 'spk', 
                                'action' => 'maintenance_cost_report', 
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