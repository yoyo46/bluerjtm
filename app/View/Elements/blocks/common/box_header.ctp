<?php 
        $title = !empty($title)?$title:false;
        $_add = !empty($_add)?$_add:false;
        $_add_multiple = !empty($_add_multiple)?$_add_multiple:false;
        $_label_multiple = !empty($_label_multiple)?$_label_multiple:false;
        $_print = !empty($_print)?$_print:false;
?>
<div class="box-header">
    <?php 
            echo $this->Html->tag('h3', $title, array(
                'class' => 'box-title'
            ));

            if( empty($data_action) && !empty($_print) ) {
                if( is_array($_print) ) {
                    echo $this->Common->_getPrint(array_merge(array(
                        '_attr' => array(
                            'escape' => false,
                        ),
                    ), $_print));
                } else {
                    echo $this->Common->_getPrint(array(
                        '_attr' => array(
                            'escape' => false,
                        ),
                    ));
                }
            }

            if( !empty($_add) ) {
                echo $this->Html->tag('div', $this->Common->link(__('Tambah'), $_add, array(
                    'data-icon' => 'plus',
                    'class' => 'btn btn-app pull-right'
                )), array(
                    'class' => 'box-tools'
                ));
            } else if( !empty($_add_multiple) ) {
                $menus = array();

                foreach ($_add_multiple as $key => $val) {
                    $label = $this->Common->filterEmptyField($val, 'label');
                    $url = $this->Common->filterEmptyField($val, 'url');
                    $link = $this->Html->link($label, $url, array(
                        'escape' => false,
                    ));

                    if( !empty($link) ) {
                        $menus[] = $this->Html->tag('li', $link);
                    }
                }

                if( !empty($menus) ) {
    ?>
    <div class="box-tools">
        <div class="btn-group pull-right">
            <?php 
                    echo $this->Html->tag('button', sprintf('%s %s', $this->Common->icon('plus'), $_label_multiple), array(
                        'data-toggle' => 'dropdown',
                        'class' => 'btn btn-app btn-success dropdown-toggle'
                    ));
            ?>
            <ul class="dropdown-menu" role="menu">
                <?php 
                        echo implode($this->Html->tag('li', '', array(
                            'class' => 'divider',
                        )), $menus);
                ?>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <?php
                }
            }
    ?>
    <div class="clear"></div>
</div>