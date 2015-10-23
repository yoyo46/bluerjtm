<?php
        $this->Html->addCrumb($module_title);
?>
<div class="row">
    <div class="col-md-12">
        <!-- The time line -->
        <?php
            if(!empty($values)){
        ?>
        <ul class="timeline">
            <!-- timeline time label -->
            <?php
                    $dateCreated = '';

                    foreach ($values as $key => $value) {
                        $action = $this->Common->filterEmptyField($value, 'Notification', 'action');
                        $name = $this->Common->filterEmptyField($value, 'Notification', 'name');
                        $url = $this->Common->filterEmptyField($value, 'Notification', 'url');
                        $created = $this->Common->filterEmptyField($value, 'Notification', 'created');
                        $type_notif = $this->Common->filterEmptyField($value, 'Notification', 'type_notif');
                        $read = $this->Common->filterEmptyField($value, 'Notification', 'read');

                        $addClass = '';
                        $customCreated = $this->Common->formatDate($created, 'd M, Y');
                        $customCreatedNormal = $this->Common->formatDate($created, 'd/m/Y');

                        if( !empty($read) ) {
                            $addClass = 'read';
                        }

                        if($dateCreated != $customCreated){
                            echo $this->Html->tag('li', $this->Html->tag('span', $customCreated, array(
                                'class' => 'bg-blue'
                            )), array(
                                'class' => 'time-label'
                            ));

                            $dateCreated = $customCreated;
                        }

                        $notif = $this->Common->_callGetNotificationIcon($type_notif, true);
                        $content = $this->Html->tag('span', sprintf('%s %s', $this->Common->icon('clock-o'), $customCreatedNormal), array(
                            'class' => 'time',
                        ));
                        $content .= $this->Html->tag('h3', $action, array(
                            'class' => 'timeline-header',
                        ));
                        $content .= $this->Html->tag('div', $name, array(
                            'class' => 'timeline-body'
                        ));

                        if(!empty($url)){
                            $link = $this->Common->_callNotificationUrl($value, __('Selengkapnya..'), array(
                                'escape' => false,
                                'class' => 'btn btn-primary btn-xs',
                            ));
                            $content .= $this->Html->tag('div', $link, array(
                                'class' => 'timeline-footer',
                            ));
                        }

                        $content = $notif.$this->Html->tag('div', $content, array(
                            'class' => sprintf('timeline-item %s', $addClass),
                        ));
                        echo $this->Html->tag('li', $content);
                    }
            ?>
            <li>
                <i class="fa fa-clock-o bg-blue"></i>
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