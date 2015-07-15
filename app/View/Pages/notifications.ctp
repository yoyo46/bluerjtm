<?php
        $this->Html->addCrumb($module_title);
?>
<div class="row">
    <div class="col-md-12">
        <!-- The time line -->
        <?php
            if(!empty($Notifications)){
        ?>
        <ul class="timeline">
            <!-- timeline time label -->
            <?php
                    $date_created = '';
                    foreach ($Notifications as $key => $notification) {
                        $created = date('d M, Y', strtotime($notification['Notification']['created']));
                        if($date_created != $created){
                            echo $this->Html->tag('li', $this->Html->tag('span', $created, array('class' => 'bg-blue')), array('class' => 'time-label') );

                            $date_created = $created;
                        }

                        $notif = '';
                        $content = '';

                        if(!empty($notification['Notification']['type_notif'])){
                            switch ($notification['Notification']['type_notif']) {
                                case 'warning':
                                    $notif = sprintf('<i class="fa fa-%s bg-yellow"></i> ', $notification['Notification']['icon_modul']);
                                break;
                                case 'success':
                                    $notif .= sprintf('<i class="fa fa-%s bg-green"></i> ', $notification['Notification']['icon_modul']);
                                break;
                                case 'danger':
                                    $notif = sprintf('<i class="fa fa-%s bg-red"></i> ', $notification['Notification']['icon_modul']);
                                break;
                            }
                        }

                        $content .= sprintf('<span class="time"><i class="fa fa-clock-o"></i> %s</span>', $this->Common->formatDate($created));

                        $content .= $this->Html->tag('div', $notification['Notification']['name'], array(
                            'class' => 'timeline-body'
                        ));

                        $link = '';
                        if(!empty($notification['Notification']['url'])){
                            $url = unserialize($notification['Notification']['url']);
                            $link = $this->Common->rule_link($notification['Notification']['link'], $url, array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                        }

                        if(!empty($link)){
                            $content .= $this->Html->tag('div', $link);
                        }

                        $content = $notif.$this->Html->tag('div', $content, array(
                            'class' => 'timeline-item'
                        ));
                        echo $this->Html->tag('li', $content);
                    }
            ?>
            <li>
                <i class="fa fa-clock-o"></i>
            </li>
        </ul>
        <?php
            }else{
                echo $this->Html->tag('div', __('Data Tidak Tersedia'), array(
                    'class' => 'alert alert-danger'
                ));
            }
        ?>
        <?php echo $this->element('pagination');?>
    </div><!-- /.col -->
</div><!-- /.row -->