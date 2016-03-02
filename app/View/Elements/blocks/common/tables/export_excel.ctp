<style>
    .string{ mso-number-format:\@; }
</style>
<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $filename = !empty($filename)?$filename:$sub_module_title;
        $contentHeader = !empty($contentHeader)?$contentHeader:false;
        $topHeader = !empty($topHeader)?$topHeader:false;

        $contentTr = isset($contentTr)?$contentTr:true;
        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename='.$filename.'.xls');
?>
<section class="content invoice">
    <?php 
            echo $topHeader;
            
            if( !empty($customHeader) ) {
                echo $customHeader;
            } else if( !empty($sub_module_title) ) {
    ?>
    <h2 class="page-header" style="text-align: center;">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <?php 
            }

            echo $contentHeader;

            if( !empty($tableContent) ) {
                echo $tableContent;
            } else {
    ?>
    <table style="width: 100%;" singleSelect="true" border="1">
        <?php
                if( !empty($tableHead) ) {
                    echo $this->Html->tag('thead', $this->Html->tag('tr', $tableHead));
                }
                if( !empty($tableBody) ) {
                    echo $this->Html->tag('tbody', $tableBody);
                }
        ?>
    </table>
    <?php 
            }

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
    ?>
</div>