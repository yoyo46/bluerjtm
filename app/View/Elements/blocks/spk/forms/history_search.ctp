<?php 
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'spk',
                'action' => 'search',
                'history',
                $id,
            )), 
            'class' => 'ajax-form',
            'data-wrapper-write' => '#wrapper-modal-write',
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('nodoc', __('No SPK'));
                echo $this->Common->buildInputForm('code', __('Kode Barang'));
                echo $this->Common->buildInputForm('name', __('Nama Barang'));
        ?>
    </div>
    <div class="col-sm-6">
        <?php 
                echo $this->Common->buildInputForm('date', __('Tanggal'), array(
                    'textGroup' => $this->Common->icon('calendar'),
                    'positionGroup' => 'positionGroup',
                    'class' => 'form-control pull-right date-range',
                ));
                echo $this->Common->buildInputForm('status', __('Status'), array(
                    'empty' => __('Pilih Status'),
                    'options' => array(
                        'open' => __('Open'),
                        'closed' => __('Closed'),
                        'finish' => __('Finish'),
                    ),
                ));
                echo $this->element('blocks/common/searchs/box_action', array(
                    '_url' => array(
                        'controller' => 'spk', 
                        'action' => 'history', 
                        $id,
                    ),
                    'linkOptions' => array(
                        'escape' => false, 
                        'class'=> 'btn btn-default btn-sm ajaxCustomModal',
                        'title' => $sub_module_title,
                    ),
                ));
        ?>
    </div>
</div>
<?php 
        echo $this->Form->end();
?>