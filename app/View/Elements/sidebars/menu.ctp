<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="img/avatar3.png" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>Hello, Jane</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
                <span class="input-group-btn">
                    <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <?php 
                    echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-dashboard"></i> <span>Dashboard</span>', '/', array(
                        'escape' => false
                    )), array(
                        'class' => ( !empty($active_menu) && $active_menu == 'dashboard' )?'active':'',
                    ));

                    $activeTruck = false;
                    $truckMenu = array(
                        'drivers', 'trucks', 'directions'
                    );

                    if( !empty($active_menu) && in_array($active_menu, $truckMenu) ) {
                        $activeTruck = 'active';
                    }
            ?>
            <li class="treeview <?php echo $activeTruck; ?>">
                <a href="#">
                    <i class="fa fa-truck"></i>
                    <span>Truk</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Supir', array(
                                'controller' => 'trucks',
                                'action' => 'drivers',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'drivers' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Truk', array(
                                'controller' => 'trucks',
                                'action' => 'index',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Rute', array(
                                'controller' => 'trucks',
                                'action' => 'directions',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'directions' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    $activeSetting = false;
                    $settingMenu = array(
                        'cities', 'company_types', 'companies',
                        'vendors'
                    );

                    if( !empty($active_menu) && in_array($active_menu, $settingMenu) ) {
                        $activeSetting = 'active';
                    }
            ?>
            <li class="treeview <?php echo $activeSetting; ?>">
                <a href="#">
                    <i class="fa fa-wrench"></i>
                    <span>Data Master</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> COA', array(
                                'controller' => 'settings',
                                'action' => 'coas',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'coas' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Kota', array(
                                'controller' => 'settings',
                                'action' => 'cities',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'cities' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Tipe Customer', array(
                                'controller' => 'settings',
                                'action' => 'company_types',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'company_types' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Customer', array(
                                'controller' => 'settings',
                                'action' => 'companies',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'companies' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Vendor', array(
                                'controller' => 'settings',
                                'action' => 'vendors',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'vendors' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-th"></i>
                    <span>User</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> list user', array(
                                'controller' => 'users',
                                'action' => 'list_user',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'user' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> group', array(
                                'controller' => 'users',
                                'action' => 'groups',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'user' )?'active':'',
                            ));

                            if(in_array($GroupId, array('3'))){
                                $link_text = '<i class="fa fa-th"></i> '.$this->Html->tag('span', 'Access List Manager');
                                echo $this->Html->tag('li', $this->Html->link($link_text, array(
                                    'controller' => 'acl_manager',
                                    'action' => 'acl',
                                ), array(
                                    'escape' => false
                                )), array(
                                    'class' => ( !empty($active_menu) && $active_menu == 'acl' )?'active':'',
                                ));
                            }
                    ?>
                </ul>
                <?php
                    
                ?>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>