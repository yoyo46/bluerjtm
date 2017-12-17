<?php
        $year = !empty($year)?$year:date('Y');

        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url(array(
                'controller' => 'pages',
                'action' => 'chart_maintenance_laka',
                'bypass' => true,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
            'id' => 'form-maintenance-laka',
        ));
?>
<div class="box-header">
    <?php
            echo $this->Common->icon('truck');
            echo $this->Html->tag('h3', __('Biaya Perbaikan LAKA'), array(
                'class' => 'box-title',
            ));
    ?>
    <div class="pull-right box-tools">
        <?php
                echo $this->Common->_callInputForm('select_year', array(
                    'class' => 'form-control chart-change',
                    'options' => $this->Common->_callPeriodeYear(10, date('Y')+1),
                    'frameClass' => 'form-group no-margin',
                    'value' => $year,
                    'data-target' => '#maintenance-laka-pie-chart',
                ));
        ?>
    </div>
</div>
<div class="box-body">
    <?php
            $urlMaintenance = $this->Html->url(array(
                'controller' => 'pages',
                'action' => 'chart_maintenance_laka',
                'bypass' => true,
            ));

            echo $this->Html->tag('div', '', array(
                'id' => 'maintenance-laka-pie-chart',
                'class' => 'load-chart',
                'style' => 'height: 280px;',
                'data-target' => '#maintenance-laka-pie-chart',
                'data-url' => $urlMaintenance,
                'data-type' => 'pie',
                'data-form' => '#form-maintenance-laka',
            ));
    ?>
</div><!-- /.box-body-->
<div class="box-footer clearfix wrapper-top-spk-more">
    <?php
            if( !empty($top_spk) ) {
                echo $this->Html->link(__('Lihat Semua'), array(
                    'controller' => 'spk',
                    'action' => 'maintenance_cost_report',
                    'bypass' => false,
                ), array(
                    'target' => 'blank',
                    'class' => 'pull-right btn btn-default',
                    'escape' => false,
                ));
            }
    ?>
</div>
<?php 
        echo $this->Form->end();
?>