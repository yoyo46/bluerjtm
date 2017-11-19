<?php 
        $notifs = $this->Common->filterEmptyField($notifications, 'notifications');
        $notifCnt = $this->Common->filterEmptyField($notifications, 'cnt');

        $app_notifs = $this->Common->filterEmptyField($approval_notifs, 'notifications');
        $app_notifCnt = $this->Common->filterEmptyField($approval_notifs, 'cnt');

        $paid_notifs = $this->Common->filterEmptyField($payment_notifs, 'notifications');
        $paid_notifCnt = $this->Common->filterEmptyField($payment_notifs, 'cnt');
?>
<header class="header hiddn-print">
    <?php 
            echo $this->Html->link($this->Html->image('/img/logo-rjtm.png').__('RJTM'), '/', array(
                'class' => 'logo',
                'escape' => false,
            ));
    ?>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <?php 
                        if(!empty($paid_notifs)){
                ?>
                <li class="dropdown notifications-menu">
                    <a href="javascript:" class="dropdown-toggle" data-toggle="dropdown" title="Notifikasi Kas/Bank">
                        <?php
                                echo $this->Common->icon('money');
                                echo $this->Html->tag('span', $paid_notifCnt, array(
                                    'class' => 'label label-info',
                                ));
                        ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                                echo $this->Html->tag('li', sprintf('Anda memiliki %s notifikasi Pembayaran', $paid_notifCnt), array(
                                    'class' => 'header'
                                ));

                        ?>
                        <li>
                            <ul class="menu">
                                <?php
                                        foreach ($paid_notifs as $key => $notification) {
                                            echo $this->Common->getPaymentNotif($notification);
                                        }
                                ?>
                            </ul>
                        </li>
                        <?php
                                echo $this->Html->tag('li', $this->Html->link(__('Selengkapnya'), array(
                                    'controller' => 'leasings',
                                    'action' => 'leasing_report',
                                    'status' => 'unpaid',
                                )), array(
                                    'class' => 'footer'
                                ) );
                        ?>
                    </ul>
                </li>
                <?php
                        }

                        if(!empty($app_notifs)){
                ?>
                <li class="dropdown notifications-menu">
                    <a href="javascript:" class="dropdown-toggle" data-toggle="dropdown" title="Notifikasi Kas/Bank">
                        <?php
                                echo $this->Common->icon('refresh');
                                echo $this->Html->tag('span', $app_notifCnt, array(
                                    'class' => 'label label-info',
                                ));
                        ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                                echo $this->Html->tag('li', sprintf('Anda memiliki %s notifikasi kas/bank', $app_notifCnt), array(
                                    'class' => 'header'
                                ));

                        ?>
                        <li>
                            <ul class="menu">
                                <?php
                                        foreach ($app_notifs as $key => $notification) {
                                            echo $this->Common->getNotif($notification);
                                        }
                                ?>
                            </ul>
                        </li>
                        <?php
                                echo $this->Html->tag('li', $this->Html->link(__('Selengkapnya'), array(
                                    'controller' => 'pages',
                                    'action' => 'approval_notifications',
                                )), array(
                                    'class' => 'footer'
                                ) );
                        ?>
                    </ul>
                </li>
                <?php
                        }

                        if(!empty($notifs)){
                ?>
                <li class="dropdown notifications-menu">
                    <a href="javascript:" class="dropdown-toggle" data-toggle="dropdown" title="Notifikasi">
                        <?php
                                echo $this->Common->icon('warning');
                                echo $this->Html->tag('span', $notifCnt, array(
                                    'class' => 'label label-info',
                                ));
                        ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                                echo $this->Html->tag('li', sprintf('Anda memiliki %s notifikasi', $notifCnt), array(
                                    'class' => 'header'
                                ));

                        ?>
                        <li>
                            <ul class="menu">
                                <?php
                                        foreach ($notifs as $key => $notification) {
                                            echo $this->Common->getNotif($notification);
                                        }
                                ?>
                            </ul>
                        </li>
                        <?php
                                echo $this->Html->tag('li', $this->Html->link(__('Selengkapnya'), array(
                                    'controller' => 'pages',
                                    'action' => 'notifications',
                                )), array(
                                    'class' => 'footer'
                                ) );
                        ?>
                    </ul>
                </li>
                <?php
                        }

                        $name = !empty($User['Employe']['full_name']) ? $User['Employe']['full_name'] : '';
                ?>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        <span><?php echo $name;?> <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <p>
                                <?php
                                    echo $name;

                                    printf(__('<small>Pegawai sejak %s</small>'), $this->Common->customDate($User['created'], 'F, Y'));
                                ?>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-6 text-center">
                                <?php
                                    echo $this->Html->link(__('Ganti<br>Password'), array(
                                        'controller' => 'users',
                                        'action' => 'authorization'
                                    ), array(
                                        'escape' => false,
                                    ));
                                ?>
                            </div>
                            <div class="col-xs-6 text-center">
                                <?php
                                    echo $this->Html->link(__('Edit<br>Profile'), array(
                                        'controller' => 'users',
                                        'action' => 'profile'
                                    ), array(
                                        'escape' => false,
                                    ));
                                ?>
                            </div>
                            <?php 
                                    // if(in_array($GroupId, array(1))){
                                    //     echo $this->Html->tag('div', $this->Html->link(__('User Permission'), array(
                                    //         'controller'=>'user_permissions', 
                                    //         'action'=>'index',
                                    //     ), array(
                                    //         'escape' => false
                                    //     )), array(
                                    //         'class' => 'col-xs-4 text-center'
                                    //     ));
                                    // }
                            ?>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right">
                                <?php
                                    echo $this->Html->link(__('Sign out'), array(
                                        'controller' => 'users',
                                        'action' => 'logout'
                                    ), array(
                                        'class' => 'btn btn-default btn-fla'
                                    ));
                                ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>