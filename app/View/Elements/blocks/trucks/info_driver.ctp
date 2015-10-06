<div class="box">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', __('Data Supir'), array(
                    'class' => 'box-title',
                ));
        ?>
    </div>
    <div class="box-body">
            <?php
                    if( !empty($driver['Driver']['photo']) ){
                        $photo = $this->Common->photo_thumbnail(array(
                            'save_path' => Configure::read('__Site.profile_photo_folder'), 
                            'src' => $driver['Driver']['photo'], 
                            'thumb'=>true,
                            'size' => 'pm',
                            'thumb' => true,
                        ));

                        echo $this->Html->tag('div', $photo, array(
                            'class' => 'form-group',
                        ));
                    }
            ?>
        <div class="form-group">
            <label><?php echo __('No. ID')?></label>
            <div><?php echo $driver['Driver']['no_id'];?></div>
        </div>
        <div class="form-group">
            <label><?php echo __('Nama Lengkap')?></label>
            <div><?php echo $driver['Driver']['name'];?></div>
        </div>
        <div class="form-group">
            <label><?php echo __('Nama Panggilan')?></label>
            <div><?php echo $driver['Driver']['alias'];?></div>
        </div>
        <div class="form-group">
            <label><?php echo __('No. HP')?></label>
            <div><?php echo $driver['Driver']['no_hp'];?></div>
        </div>
        <div class="form-group">
            <label><?php echo __('No. Telp')?></label>
            <div><?php echo $driver['Driver']['phone'];?></div>
        </div>
    </div>
</div>
