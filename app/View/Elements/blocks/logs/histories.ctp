<?php 
        if( !empty($logs) ) {
?>
<div class="box history-logs collapsed-box">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', __('Histori Log'), array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body table-responsive" style="display: none;">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', __('Activitas'));
                        echo $this->Html->tag('th', __('Status'));
                        echo $this->Html->tag('th', __('Date'));
                ?>
            </tr>
            <?php
                    foreach ($logs as $key => $value) {
                        $action = $this->Common->filterEmptyField($value, 'Log', 'action');
                        $created = $this->Common->filterEmptyField($value, 'Log', 'created');
                        $name = $this->Common->filterEmptyField($value, 'Employe', 'full_name');
                        $error = $this->Common->filterEmptyField($value, 'Log', 'error');
            ?>
            <tr>
                <td>
                    <?php
                            if( strstr($action, 'add') ) {
                                $tmpMsg = __('Created by');
                            } else if( strstr($action, 'edit') ) {
                                $tmpMsg = __('Modified by');
                            } else if( strstr($action, 'rejected') ) {
                                $tmpMsg = __('Rejected by');
                            } else if( strstr($action, 'void') ) {
                                $tmpMsg = __('Void by');
                            }

                            if( !empty($tmpMsg) ) {
                                printf('%s %s', $tmpMsg, $name);
                            }
                    ?>
                </td>
                <td>
                    <?php
                            if( !empty($error) ) {
                                echo $this->Html->tag('div', __('Gagal'), array(
                                    'class' => 'label label-danger',
                                ));
                            } else {
                                echo $this->Html->tag('div', __('Berhasil'), array(
                                    'class' => 'label label-success',
                                ));
                            }
                    ?>
                </td>
                <td><?php echo $this->Time->niceShort($created);?></td>
            </tr>
            <?php 
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>
<?php 
        }
?>