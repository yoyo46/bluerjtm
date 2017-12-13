<?php
        $urlTopSpk = $this->Html->url(array(
            'controller' => 'pages',
            'action' => 'top_spk',
            'bypass' => true,
        ));
        $year = !empty($year)?$year:date('Y');
?>
<div class="box-header">
    <i class="fa fa-wrench"></i>
    <h3 id="wrapper-top-spk-title" class="box-title">
        Perbaikan truk Tertinggi Tahun <span><?php echo $year; ?></span>
    </h3>
    <div class="pull-right box-tools">
        <?php
                echo $this->Common->_callInputForm('select_year', array(
                    'class' => 'form-control ajax-change',
                    'options' => $this->Common->_callPeriodeYear(10, date('Y')+1),
                    'frameClass' => 'form-group no-margin',
                    'data-wrapper-write-page' => '#wrapper-top-spk,#wrapper-top-spk-title',
                    'href' => $urlTopSpk,
                    'value' => $year,
                ));
        ?>
        <!-- <button class="btn btn-primary btn-sm daterange-top-spk pull-right ajax-change" data-toggle="tooltip" title="" data-original-title="Date range" data-trigger="change" ori-href="<?php echo $urlTopSpk; ?>" href="<?php echo $urlTopSpk; ?>" data-wrapper-write="#wrapper-top-spk"><i class="fa fa-calendar"></i></button> -->
    </div>
</div><!-- /.box-header -->
<div class="box-body">
    <ul id="wrapper-top-spk" class="todo-list">
        <?php
                if( !empty($top_spk) ) {
                    foreach ($top_spk as $key => $spk) {
                        $truck_id = Common::hashEmptyField($spk, 'Truck.id');
                        $nopol = Common::hashEmptyField($spk, 'Truck.nopol');
                        $year = Common::hashEmptyField($spk, 'ProductExpenditure.transaction_date', null, array(
                            'date' => 'Y',
                        ));
                        $date = __('01/01/%s - 31/12/%s', $year, $year);
                        $grandtotal = Common::hashEmptyField($spk, 'ProductHistory.grandtotal');

                        $urlSpk = array(
                            'controller' => 'products',
                            'action' => 'expenditure_reports',
                            'nopol' => $nopol,
                            'date' => Common::_callUrlEncode($date),
                            'bypass' => false,
                        );
        ?>
        <li>
            <!-- todo text -->
            <?php
                    echo $this->Html->tag('span', __('%s, total perbaikan %s', $this->Html->link($nopol, $urlSpk, array(
                        'target' => 'blank',
                        'escape' => false,
                    )), Common::getFormatPrice($grandtotal)), array(
                        'class' => 'text',
                    ));
            ?>
            <div class="tools">
                <?php
                        echo $this->Html->link($this->Common->icon('eye'), $urlSpk, array(
                            'target' => 'blank',
                            'escape' => false,
                        ));
                ?>
            </div>
        </li>
        <?php
                    }
                } else {
                    echo $this->Html->tag('li', $this->Html->tag('div', __('Data tidak tersedia'), array(
                        'class' => 'alert alert-danger no-margin',
                    )), array(
                        'class' => 'no-padding',
                    ));
                }
        ?>
    </ul>
</div><!-- /.box-body -->