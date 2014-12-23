<?php
    $name = $User['first_name'];
    if(!empty($User['last_name'])){
        $name .= ' '.$User['last_name'];
    }
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="left-side sidebar-offcanvas">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <?php 
                    echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-dashboard"></i> <span>Dashboard</span>', '/', array(
                        'escape' => false
                    )), array(
                        'class' => ( !empty($active_menu) && $active_menu == 'dashboard' )?'active':'',
                    ));
            ?>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-user"></i>
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

                            // if(in_array($GroupId, array('3'))){
                            //     $link_text = '<i class="fa fa-angle-double-right"></i> '.$this->Html->tag('span', 'Access List Manager');
                            //     echo $this->Html->tag('li', $this->Html->link($link_text, array(
                            //         'controller' => 'acl_manager',
                            //         'action' => 'acl',
                            //     ), array(
                            //         'escape' => false
                            //     )), array(
                            //         'class' => ( !empty($active_menu) && $active_menu == 'acl' )?'active':'',
                            //     ));
                            // }
                    ?>
                </ul>
                <?php
                    
                ?>
            </li>
            <?php

                    $activeTtuj = false;
                    $ttujMenu = array(
                        'ttuj'
                    );

                    if( !empty($active_menu) && in_array($active_menu, $ttujMenu) ) {
                        $activeTtuj = 'active';
                    }
            ?>
            <li class="treeview <?php echo $activeTtuj; ?>">
                <a href="#">
                    <i class="fa fa-tag"></i>
                    <span>TTUJ</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> TTUJ', array(
                                'controller' => 'revenues',
                                'action' => 'ttuj',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'ttuj' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php

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
                    ?>
                </ul>
            </li>
            <?php

                    $activePayment = false;
            ?>
            <li class="treeview <?php echo $activePayment; ?>">
                <a href="#">
                    <i class="fa fa-dollar"></i>
                    <span>Pembayaran</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Histori KIR', array(
                                'controller' => 'trucks',
                                'action' => 'kir',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'drivers' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran KIR', array(
                                'controller' => 'trucks',
                                'action' => 'kir_add',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'drivers' )?'active':'',
                            ));

                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Histori STNK', array(
                                'controller' => 'trucks',
                                'action' => 'stnk',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));

                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran STNK', array(
                                'controller' => 'trucks',
                                'action' => 'stnk_add',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));

                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Histori SIUP', array(
                                'controller' => 'trucks',
                                'action' => 'siup',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));

                             echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Pembayaran SIUP', array(
                                'controller' => 'trucks',
                                'action' => 'siup_add',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'trucks' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
            <?php 
                    $activeSetting = false;
                    $settingMenu = array(
                        'cities', 'customer_types', 'customers',
                        'vendors', 'companies', 'uang_jalan',
                        'perlengkapan', 'coas', 'type_motor'
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Perlengkapan', array(
                                'controller' => 'settings',
                                'action' => 'perlengkapan',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'perlengkapan' )?'active':'',
                            ));
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Customer', array(
                                'controller' => 'settings',
                                'action' => 'customers',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'customers' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Company', array(
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
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Uang Jalan', array(
                                'controller' => 'settings',
                                'action' => 'uang_jalan',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'uang_jalan' )?'active':'',
                            ));
                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-angle-double-right"></i> Tipe Motor', array(
                                'controller' => 'settings',
                                'action' => 'type_motors',
                            ), array(
                                'escape' => false
                            )), array(
                                'class' => ( !empty($active_menu) && $active_menu == 'type_motor' )?'active':'',
                            ));
                    ?>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>