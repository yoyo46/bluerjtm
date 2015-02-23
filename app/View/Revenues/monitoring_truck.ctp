<?php 
        $this->Html->addCrumb($sub_module_title);
        $addStyle = 'min-width: 1500px;';
        $tdStyle = '';
        $border = 0;
        echo $this->element('blocks/trucks/search_report_monitoring_truck');
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <div class="month-name-container">
        <?php
                $prevMonthName = date('F Y', strtotime($prevMonth));
                $urlPrev = $this->passedArgs;
                $urlPrev['month'] = $this->Common->toSlug($prevMonthName);
                echo $this->Html->tag('div', $this->Html->link(sprintf('%s %s', $this->Html->tag('b', '', array(
                    'class' => 'fa fa-angle-left'
                )), $this->Html->tag('span', $prevMonthName, array(
                    'class' => 'month-name'
                ))), $urlPrev, array(
                    'escape' => false
                )), array(
                    'class' => 'last-month pull-left'
                ));

                $currentMonthName = date('F Y', strtotime($currentMonth));
                echo $this->Html->tag('div', $currentMonthName, array(
                    'class' => 'current-month text-center'
                ));
                
                $nextMonthName = date('F Y', strtotime($nextMonth));
                $urlNext = $this->passedArgs;
                $urlNext['month'] = $this->Common->toSlug($nextMonthName);
                echo $this->Html->tag('div', $this->Html->link(sprintf('%s %s', $this->Html->tag('span', $nextMonthName, array(
                    'class' => 'month-name'
                )), $this->Html->tag('b', '', array(
                    'class' => 'fa fa-angle-right'
                ))), $urlNext, array(
                    'escape' => false
                )), array(
                    'class' => 'next-month pull-right'
                ));
        ?>
    </div>
    <div class="table-responsive margin frame-frezee">
        <table class="table table-bordered report monitoring" style="<?php echo $addStyle; ?>" border="<?php echo $border; ?>">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', __('Truk'), array(
                                'class' => 'text-center headcol',
                                'style' => 'width: 100px;',
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
                                    'class' => 'text-center headcol',
                                    'style' => 'width: 100px;',
                                ));
                                $bg = '';

                                for ($i=1; $i <= $lastDay; $i++) {
                                    $idx = $i;

                                    if( $idx < 10 ) {
                                        $idx = sprintf('0%s', $idx);
                                    }
                                    $point = array();
                                    $style = '';
                                    $rit = '';

                                    if( !empty($dataRit[$nopol]['rit'][$idx]) && count($dataRit[$nopol]['rit'][$idx]) > 1 ) {
                                        $rit = count($dataRit[$nopol]['rit'][$idx]);
                                    }

                                    if( !empty($dataTtuj[$nopol][$idx]) ) {
                                        foreach ($dataTtuj[$nopol][$idx] as $key => $data) {
                                            $style = sprintf('background: %s;', $data['color']);

                                            $formTtuj = $this->Html->tag('p', sprintf(__('Berangkat: %s', $data['from_date'])));

                                            if( !empty($data['tglTiba']) ) {
                                                $formTtuj .= $this->Html->tag('p', sprintf(__('Tiba: %s', $data['tglTiba'])));
                                            }
                                            if( !empty($data['tglBongkaran']) ) {
                                                $formTtuj .= $this->Html->tag('p', sprintf(__('Bongkaran: %s', $data['tglBongkaran'])));
                                            }
                                            if( !empty($data['tglBalik']) ) {
                                                $formTtuj .= $this->Html->tag('p', sprintf(__('Balik: %s', $data['tglBalik'])));
                                            }

                                            if( !empty($data['is_laka']) ) {
                                                $formLaka = $this->Html->tag('p', sprintf(__('Supir: %s', $data['driver_name'])));
                                                $formLaka .= $this->Html->tag('p', sprintf(__('Lokasi: %s', $data['lokasi_laka'])));
                                                $formLaka .= $this->Html->tag('p', sprintf(__('Kondisi Truk: %s', $data['truck_condition'])));
                                                $formTtuj .= $this->Html->tag('p', sprintf(__('Tanggal LAKA: %s', $data['laka_date'])));

                                                if( !empty($data['laka_completed_date']) ) {
                                                    $formTtuj .= $this->Html->tag('p', sprintf(__('Selesai LAKA: %s', $data['laka_completed_date'])));
                                                }
                                                
                                                if( !empty($data['icon']) ) {
                                                    $icon = $this->Html->image('/img/accident.png', array(
                                                        'class' => 'ico-calendar'
                                                    ));
                                                } else {
                                                    $icon = '&nbsp;';
                                                }
                                                if( !empty($data['iconPopup']) ) {
                                                    $icon .= $this->Html->image($data['iconPopup'], array(
                                                        'class' => 'icon-popup'
                                                    ));
                                                }
                                            } else {
                                                $formTtuj .= $this->Html->tag('p', sprintf(__('Sampai Pool: %s', $data['to_date'])));
                                            }

                                            if( !empty($data['icon']) ) {
                                                $icon = $this->Html->image($data['icon'], array(
                                                    'class' => 'ico-calendar'
                                                ));
                                            } else {
                                                $icon = $this->Html->tag('span', '&nbsp;', array(
                                                    'class' => 'ico-calendar'
                                                ));
                                            }
                                            if( !empty($data['iconPopup']) ) {
                                                $icon .= $this->Html->image($data['iconPopup'], array(
                                                    'class' => 'icon-popup'
                                                ));
                                            }
                                            if( !empty($data['url']) ) {
                                                $icon = $this->Html->link($icon, $data['url'], array(
                                                    'escape' => false,
                                                    'target' => 'blank',
                                                ));
                                            }

                                            $point[] = $this->Html->tag('div', $this->Html->tag('div', $icon, array(
                                                'title' => $data['title'],
                                                'class' => 'popover-hover-top',
                                                'data-content' => sprintf('%s %s %s %s %s', $this->Html->tag('label', $data['Tujuan']), $this->Html->tag('p', sprintf(__('Supir: %s', $data['Driver']))), $this->Html->tag('p', sprintf(__('Truk: %s', $nopol))), $this->Html->tag('p', sprintf(__('Muatan: %s', $data['Muatan']))), $formTtuj)
                                            )), array(
                                                'class' => 'text-center',
                                            ));
                                        }
                                    }

                                    if( !empty($dataEvent[$nopol][sprintf("%02s", $i)]) ) {
                                        foreach ($dataEvent[$nopol][sprintf("%02s", $i)] as $key => $event) {
                                            $title = $event['title'];
                                            $fromDateTime = $this->Html->tag('p', sprintf(__('Tanggal: %s', $event['from_date'])));
                                            $toDateTime = $this->Html->tag('p', sprintf(__('Sampai: %s', $event['to_date'])));

                                            if( !empty($event['icon']) ) {
                                                $icon = $this->Common->photo_thumbnail(array(
                                                    'save_path' => Configure::read('__Site.truck_photo_folder'), 
                                                    'src' => $event['icon'], 
                                                    'thumb'=>true,
                                                    'size' => 'ps',
                                                    'thumb' => true,
                                                ), array(
                                                    'class' => 'ico-calendar'
                                                ));
                                            } else {
                                                $icon = $this->Html->tag('span', '&nbsp;', array(
                                                    'class' => 'ico-calendar'
                                                ));
                                            }
                                            if( !empty($event['iconPopup']) ) {
                                                $icon .= $this->Html->image($event['iconPopup'], array(
                                                    'class' => 'icon-popup'
                                                ));
                                            }

                                            $point[] = $this->Html->tag('div', $this->Html->tag('div', $icon, array(
                                                'title' => $title.' <span class="pull-right"><a href="javascript:"><i class="popover-close">Tutup</i></a></span>',
                                                'class' => 'popover-hover-top-click',
                                                'data-content' => sprintf('%s%s%s%s', $this->Html->tag('p', $event['note']), $fromDateTime, $toDateTime, $this->Html->link('<i class="fa fa-times"></i> '.__('Hapus'), array(
                                                    'controller' => 'ajax',
                                                    'action' => 'event_delete',
                                                    $event['id'],
                                                ), array(
                                                    'escape' => false,
                                                    'class' => 'text-red',
                                                ), __('Anda yakin ingin menghapus event ini ?'))),
                                            )), array(
                                                'class' => 'text-center parent-popover',
                                            ));
                                            $style = sprintf('background: %s;', $event['color']);
                                        }
                                    }

                                    if( empty($point) ){
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
                                            'style' => $style,
                                        ));
                                    } else {
                                        if( count($point) > 1 ) {
                                            echo $this->Html->tag('td', $this->Html->tag('div', $this->Html->link(sprintf('%s <i class="fa fa-list"></i>', $rit), '#multiple-'.$i, array(
                                                'escape' => false,
                                            )), array(
                                                'class' => 'text-center',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#myModal'.$i
                                            )), array(
                                                'class' => 'multiple', 
                                                'style' => $style,
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
                                    <li class="col-sm-2 text-center">
                                        <?php 
                                                rsort($point);
                                                echo str_replace('popover-hover-top-click', 'popover-hover-bottom-click', str_replace('popover-hover-top', 'popover-hover-bottom', implode('</li><li class="col-sm-2 text-center">', $point)));
                                        ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                                        } else {
                                            echo $this->Html->tag('td', implode('', $point), array(
                                                'style' => $style,
                                            ));
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