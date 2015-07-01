<header class="header">
    <?php 
            echo $this->Html->link('RJTM ERP', '/', array(
                'class' => 'logo'
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
                <!-- User Account: style can be found in dropdown.less -->
                <?php
                    $name = $User['first_name'];
                    if(!empty($User['last_name'])){
                        $name .= ' '.$User['last_name'];
                    }
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
                                    echo $this->Html->link(__('Rubah Password'), array(
                                        'controller' => 'users',
                                        'action' => 'authorization'
                                    ));
                                ?>
                            </div>
                            <div class="col-xs-4 text-center">
                                <?php
                                    echo $this->Html->link(__('Rubah Profile'), array(
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