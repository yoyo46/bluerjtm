<?php
	if(!empty($user_id)){
		$user_group = !empty($users['Group']['name']) ? $users['Group']['name'] : '-';
		echo $this->Html->tag('div', $user_group, array('id' => 'group_user'));
	}else{
?>
<?php 
        echo $this->Form->create('User', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'getUserEmploye'
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('group_id',array(
                        'label'=> __('Grup User'),
                        'class'=>'form-control',
                        'required' => false,
                        'empty' => __('Pilih Grup User'),
                        'options' => $groups
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('Employe.full_name',array(
                        'label'=> __('Nama Karyawan'),
                        'class'=>'form-control',
                        'required' => false,
                        'placeholder' => __('Nama Karyawan'),
                    ));
            ?>
        </div>
    </div>
</div>
<div class="form-group action">
    <?php
            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                'div' => false, 
                'class'=> 'btn btn-success btn-sm ajaxModal',
                'data-action' => $data_action,
                'data-parent' => true,
                'title' => $title,
            ));
            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                'controller' => 'ajax',
                'action' => 'getUserEmploye',
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxModal',
                'data-action' => $data_action,
                'title' => $title,
            ));
    ?>
</div>
<?php 
    echo $this->Form->end();
?>
<div class="box-body table-responsive browse-form">
    <table class="table table-hover">
        <tr>
            <?php 
                    echo $this->Html->tag('th', __('Nama'));
                    echo $this->Html->tag('th', __('Grup User'));
            ?>
        </tr>
        <?php
                if(!empty($users)){
                    foreach ($users as $key => $value) {
                        $full_name = !empty($value['Employe']['full_name'])?$value['Employe']['full_name']:false;
        ?>
        <tr data-value="<?php echo $value['User']['id'];?>" data-rel="#cash-auth-<?php echo $rel;?>" data-change=".<?php echo $data_change;?>">
            <td><?php echo $full_name;?></td>
            <td><?php echo $value['Group']['name'];?></td>
        </tr>
        <?php
                    }
                }else{
                    echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                        'colspan' => '2'
                    )));
                }
        ?>
    </table>
</div><!-- /.box-body -->
<?php
        echo $this->element('pagination', array(
            'options' => array(
                'data-action' => $data_action,
                'class' => 'ajaxModal',
            ),
        ));
?>
<?php
	}
?>