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
        <table class="table table-bordered report monitoring" style="<?php echo $addStyle; ?>" border="<?php echo $border; ?>">
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
                                $style = '';

                                for ($i=1; $i <= $lastDay; $i++) {
                                    $idx = $i;

                                    if( $idx < 10 ) {
                                        $idx = sprintf('0%s', $idx);
                                    }
                                    $point = array();


                                    if( !empty($dataTtuj[$nopol]['Berangkat'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Berangkat'][$idx];
                                        $checkDate = date('Y-m-d', strtotime($dataTtuj[$nopol]['Berangkat'][$idx]['datetime']));

                                        if ( $checkDate == date('Y-m-d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth)))) ) {
                                            $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Berangkat'][$idx]['datetime']));
                                            $point[] = $this->Html->tag('div', $this->Html->tag('div', $this->Html->image('/img/truck.png'), array(
                                                'title' => __('Truk Berangkat'),
                                                'class' => 'popover-hover-bottom',
                                                'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                            )), array(
                                                'class' => 'text-center berangkat',
                                                'style' => 'width: 40px;',
                                            ));
                                            $bg = 'berangkat';
                                        }
                                    }
                                    if( !empty($dataTtuj[$nopol]['Tiba'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Tiba'][$idx];
                                        $checkDate = date('Y-m-d', strtotime($dataTtuj[$nopol]['Tiba'][$idx]['datetime']));

                                        if ( $checkDate == date('Y-m-d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth)))) ) {
                                            $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Tiba'][$idx]['datetime']));
                                            $point[] = $this->Html->tag('div', $this->Html->tag('div', $this->Html->image('/img/arrive.png'), array(
                                                'title' => __('Sampai Tujuan'),
                                                'class' => 'popover-hover-bottom',
                                                'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                            )), array(
                                                'class' => 'text-center tiba',
                                                'style' => 'width: 40px;',
                                            ));
                                            $bg = 'tiba';
                                        }
                                    }
                                    if( !empty($dataTtuj[$nopol]['Bongkaran'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Bongkaran'][$idx];
                                        $checkDate = date('Y-m-d', strtotime($dataTtuj[$nopol]['Bongkaran'][$idx]['datetime']));

                                        if ( $checkDate == date('Y-m-d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth)))) ) {
                                            $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Bongkaran'][$idx]['datetime']));
                                            $point[] = $this->Html->tag('div', $this->Html->tag('div', $this->Html->image('/img/bongkaran.png'), array(
                                                'title' => __('Truk Bongkaran'),
                                                'class' => 'popover-hover-bottom',
                                                'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                            )), array(
                                                'class' => 'text-center bongkaran',
                                                'style' => 'width: 40px;',
                                            ));
                                            $bg = 'bongkaran';
                                        }
                                    }
                                    if( !empty($dataTtuj[$nopol]['Balik'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Balik'][$idx];
                                        $checkDate = date('Y-m-d', strtotime($dataTtuj[$nopol]['Balik'][$idx]['datetime']));

                                        if ( $checkDate == date('Y-m-d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth)))) ) {
                                            $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Balik'][$idx]['datetime']));
                                            $point[] = $this->Html->tag('div', $this->Html->tag('div', $this->Html->image('/img/on-the-way.gif'), array(
                                                'title' => __('Truk Balik'),
                                                'class' => 'popover-hover-bottom',
                                                'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                            )), array(
                                                'class' => 'text-center balik',
                                                'style' => 'width: 40px;',
                                            ));
                                            $bg = 'balik';
                                        }
                                    }
                                    if( !empty($dataTtuj[$nopol]['Pool'][$idx]) ) {
                                        $data = $dataTtuj[$nopol]['Pool'][$idx];
                                        $checkDate = date('Y-m-d', strtotime($dataTtuj[$nopol]['Pool'][$idx]['datetime']));

                                        if ( $checkDate == date('Y-m-d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth)))) ) {
                                            $dateTime = date('d M Y H:i', strtotime($dataTtuj[$nopol]['Pool'][$idx]['datetime']));
                                            $point[] = $this->Html->tag('div', $this->Html->tag('div', $this->Html->image('/img/pool.png'), array(
                                                'title' => __('Sampai Pool'),
                                                'class' => 'popover-hover-bottom',
                                                'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))))
                                            )), array(
                                                'class' => 'text-center pool',
                                                'style' => 'width: 40px;',
                                            ));
                                            $bg = '';
                                        }
                                    }
                                    if( !empty($dataEvent[$nopol][sprintf("%02s", $i)]) ) {
                                        foreach ($dataEvent[$nopol][sprintf("%02s", $i)] as $key => $event) {
                                            $title = $event['title'];
                                            $dateTime = date('d M Y', strtotime($event['date']));

                                            if( !empty($event['time']) ) {
                                                $time = $this->Html->tag('p', sprintf(__('Pukul: %s', $event['time'])));
                                            }

                                            $point[] = $this->Html->tag('div', $this->Html->tag('div', $this->Common->photo_thumbnail(array(
                                                'save_path' => Configure::read('__Site.truck_photo_folder'), 
                                                'src' => $event['icon'], 
                                                'thumb'=>true,
                                                'size' => 'ps',
                                                'thumb' => true,
                                            ), array(
                                                'class' => 'icon-calendar'
                                            )), array(
                                                'title' => $title,
                                                'class' => 'popover-hover-bottom',
                                                'data-content' => sprintf('%s %s%s', $this->Html->tag('p', $event['note']), $this->Html->tag('p', sprintf(__('Tanggal: %s', $dateTime))), $time)
                                            )), array(
                                                'class' => 'text-center',
                                                'style' => 'width: 40px;',
                                            ));
                                            $style = sprintf('background: %s;', $event['color']);
                                        }
                                    }
                                    if( empty($point) ){
                                        // if( date('d') < $i ) {
                                        //     $bg = '';
                                        // }
                                        echo $this->Html->tag('td', $this->Html->link('&nbsp;', array(
                                            'controller' => 'ajax',
                                            'action' => 'event_add',
                                            $nopol,
                                            date('Y-m-d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth))))
                                        ), array(
                                            'escape' => false,
                                            'class' => 'event-add show ajaxModal',
                                            'title' => __('Tambah Event'),
                                            'data-action' => 'event',
                                        )), array(
                                            'class' => 'text-center '.$bg,
                                            'style' => 'width: 40px;',
                                        ));
                                    } else {
                                        if( count($point) > 1 ) {
                                            echo $this->Html->tag('td', $this->Html->tag('div', $this->Html->link(sprintf('%s <i class="fa fa-list"></i>', count($point)), '#multiple-'.$i, array(
                                                'escape' => false,
                                            )), array(
                                                'class' => 'text-center',
                                                'style' => 'width: 40px;',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#myModal'.$i
                                            )), array(
                                                'class' => 'multiple',  
                                            ));
                ?>
                <div class="modal fade multiple-modal" id="myModal<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">List Kalender <?php echo date('d M Y', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth)))); ?></h4>
                            </div>
                            <div class="modal-body">
                                <ul class="row list-calendar">
                                    <li class="col-sm-3 text-center">
                                        <?php 
                                                echo implode('</li><li class="col-sm-3 text-center">', $point);
                                        ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                                        } else {
                                            echo $this->Html->tag('td', implode('', $point));
                                        }
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