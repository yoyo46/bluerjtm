<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <table style="width: 100%;" singleSelect="true" border="1">
        <?php
                if( !empty($tableHead) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $tableHead));
                }
                if( !empty($tableBody) ) {
                    echo $this->Html->tag('tbody', $this->Html->tag('tr', $tableBody));
                }
        ?>
    </table>
    <?php 
            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
    ?>
</div>