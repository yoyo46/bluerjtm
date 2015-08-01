<header class="header">
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
                <li class="dropdown notifications-menu">
                    <a href="javascript:" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-warning"></i>
                        <?php
                            if(!empty($notifications)){
                                echo $this->Html->tag('span', count($notifications), array(
                                    'class' => 'label label-info'
                                ));
                            }
                        ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                                echo $this->Html->tag('li', sprintf('Anda memiliki %s notifikasi', count($notifications)), array(
                                    'class' => 'header'
                                ));

                                if(!empty($notifications)){
                        ?>
                        <li>
                            <ul class="menu">
                                <?php
                                    foreach ($notifications as $key => $notification) {
                                        $type_notif = $notification['Notification']['type_notif'];
                                        echo $this->Common->getNotif($type_notif, $notification);
                                    }
                                ?>
                            </ul>
                        </li>
                        <?php
                                }

                                echo $this->Html->tag('li', $this->Html->link(__('Selengkapnya'), array(
                                    'controller' => 'pages',
                                    'action' => 'notifications',
                                )), array(
                                    'class' => 'footer'
                                ) );
                        ?>
                    </ul>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <?php
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
                            <div class="col-xs-4 text-center">
                                <?php
                                    echo $this->Html->link(__('Ganti Password'), array(
                                        'controller' => 'users',
                                        'action' => 'authorization'
                                    ));
                                ?>
                            </div>
                            <div class="col-xs-4 text-center">
                                <?php
                                    echo $this->Html->link(__('Edit Profile'), array(
                                        'controller' => 'users',
                                        'action' => 'profile'
                                    ));
                                ?>
                            </div>
                            <?php 
                                    if(in_array($GroupId, array(1))){
                                        echo $this->Html->tag('div', $this->Html->link(__('User Permission'), array(
                                            'controller'=>'user_permissions', 
                                            'action'=>'index',
                                        ), array(
                                            'escape' => false
                                        )), array(
                                            'class' => 'col-xs-4 text-center'
                                        ));
                                    }
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