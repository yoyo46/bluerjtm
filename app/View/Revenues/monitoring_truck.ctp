<?php 
        $this->Html->addCrumb($sub_module_title);
        $addStyle = 'min-width: 1500px;';
        $tdStyle = '';
        $border = 0;

?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <div class="month-name-container">
        <?php
                $prevMonthName = date('F Y', strtotime($prevMonth));
                echo $this->Html->tag('div', $this->Html->link(sprintf('%s %s', $this->Html->tag('b', '', array(
                    'class' => 'fa fa-angle-left'
                )), $this->Html->tag('span', $prevMonthName, array(
                    'class' => 'month-name'
                ))), array(
                    'month' => $this->Common->toSlug($prevMonthName),
                ), array(
                    'escape' => false
                )), array(
                    'class' => 'last-month pull-left'
                ));

                $currentMonthName = date('F Y', strtotime($currentMonth));
                echo $this->Html->tag('div', $currentMonthName, array(
                    'class' => 'current-month text-center'
                ));
                
                $nextMonthName = date('F Y', strtotime($nextMonth));
                echo $this->Html->tag('div', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', $nextMonthName, array(
                    'class' => 'month-name'
                )), $this->Html->tag('b', '', array(
                    'class' => 'fa fa-angle-right'
                ))), array(
                    'month' => $this->Common->toSlug($nextMonthName),
                ), array(
                    'escape' => false
                )), array(
                    'class' => 'next-month pull-right'
                ));
        ?>
    </div>
    <div class="table-responsive margin">
        <table class="table table-bordered report" style="<?php echo $addStyle; ?>" border="<?php echo $border; ?>">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', __('Truk'), array(
                                'class' => 'text-center',
                                'style' => 'width: 300px;',
                            ));

                            for ($i=1; $i <= $lastDay; $i++) {
                                echo $this->Html->tag('th', $i, array(
                                    'class' => 'text-center',
                                    'style' => 'width: 100px;',
                                    'title' => date('l', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth)))),
                                ));
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                        if( !empty($trucks) ) {
                            foreach ($trucks as $key => $truck) {
                                $nopol = $truck['Truck']['nopol'];
                                echo '<tr>';
                                echo $this->Html->tag('td', $nopol, array(
                                    'class' => 'text-center',
                                    'style' => 'width: 100px;',
                                ));
                                $bg = '';

                                for ($i=1; $i <= $lastDay; $i++) {
                                    $idx = $i;

                                    if( $idx < 10 ) {
                                        $idx = sprintf('0%s', $idx);
                                    }

                                    if( !empty($dataTtuj[$nopol]['Berangkat'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Berangkat'][$idx];
                                        $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Berangkat'][$idx]['datetime']));
                                        echo $this->Html->tag('td', $this->Html->tag('div', '<i class="fa fa-circle"></i>', array(
                                            'title' => __('Truk Berangkat'),
                                            'class' => 'popover-hover-bottom',
                                            'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                        )), array(
                                            'class' => 'text-center berangkat',
                                            'style' => 'width: 100px;',
                                        ));
                                        $bg = 'color';
                                    } else if( !empty($dataTtuj[$nopol]['Tiba'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Tiba'][$idx];
                                        $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Tiba'][$idx]['datetime']));
                                        echo $this->Html->tag('td', $this->Html->tag('div', '<i class="fa fa-circle"></i>', array(
                                            'title' => __('Sampai Tujuan'),
                                            'class' => 'popover-hover-bottom',
                                            'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                        )), array(
                                            'class' => 'text-center tiba',
                                            'style' => 'width: 100px;',
                                        ));
                                    } else if( !empty($dataTtuj[$nopol]['Bongkaran'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Bongkaran'][$idx];
                                        $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Bongkaran'][$idx]['datetime']));
                                        echo $this->Html->tag('td', $this->Html->tag('div', '<i class="fa fa-circle"></i>', array(
                                            'title' => __('Truk Bongkaran'),
                                            'class' => 'popover-hover-bottom',
                                            'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                        )), array(
                                            'class' => 'text-center bongkaran',
                                            'style' => 'width: 100px;',
                                        ));
                                    } else if( !empty($dataTtuj[$nopol]['Balik'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Balik'][$idx];
                                        $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Balik'][$idx]['datetime']));
                                        echo $this->Html->tag('td', $this->Html->tag('div', '<i class="fa fa-circle"></i>', array(
                                            'title' => __('Truk Balik'),
                                            'class' => 'popover-hover-bottom',
                                            'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                        )), array(
                                            'class' => 'text-center balik',
                                            'style' => 'width: 100px;',
                                        ));
                                    } else if( !empty($dataTtuj[$nopol]['Pool'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Pool'][$idx];
                                        $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Pool'][$idx]['datetime']));
                                        echo $this->Html->tag('td', $this->Html->tag('div', '<i class="fa fa-circle"></i>', array(
                                            'title' => __('Sampai Pool'),
                                            'class' => 'popover-hover-bottom',
                                            'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                        )), array(
                                            'class' => 'text-center pool',
                                            'style' => 'width: 100px;',
                                        ));
                                        $bg = '';
                                    } else {
                                        echo $this->Html->tag('td', $this->Html->link('&nbsp;', array(
                                            'controller' => 'ajax',
                                            'action' => 'event_add',
                                            date('Y-m-d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth))))
                                        ), array(
                                            'escape' => false,
                                            'class' => 'event-add show',
                                        )), array(
                                            'class' => 'text-center '.$bg,
                                            'style' => 'width: 100px;',
                                        ));
                                    }
                                }
                                echo '</tr>';
                            }
                        }
                ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>