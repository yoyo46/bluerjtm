<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail LKU'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <div class="form-group">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
                        'class' => 'add-custom-field btn btn-success btn-xs',
                        'action_type' => 'lku_tipe_motor',
                        'escape' => false
                    ));
            ?>
        </div>
        <table class="table table-hover">
        	<thead>
        		<tr>
        			<th><?php echo __('Tipe Motor');?></th>
                    <th><?php echo __('Warna');?></th>
                    <th><?php echo __('No. Rangka');?></th>
                    <th><?php echo __('Keterangan');?></th>
                    <th><?php echo __('Jumlah Unit');?></th>
                    <th><?php printf(__('Biaya Klaim (%s)'), Configure::read('__Site.config_currency_code'));?></th>
                    <th><?php  printf(__('Total (%s)'), Configure::read('__Site.config_currency_code')) ;?></th>
        		</tr>
        	</thead>
        	<tbody class="tipe-motor-table">
        		<tr>
                    <td>
                        <?php
                            echo $this->Form->input('LkuDetail.tipe_motor_id.1', array(
                                'options' => $tipe_motor_list,
                                'label' => false,
                                'empty' => __('Pilih Tipe Motor'),
                                'class' => 'lku-choose-tipe-motor form-control'
                            ));
                        ?>
                    </td>
        			<td class="lku-color-motor">-</td>
                    <td>
                        <?php 
                            echo $this->Form->input('LkuDetail.no_rangka.1', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control'
                            ));
                        ?>
                    </td>
                    <td>
                        <?php 
                            echo $this->Form->input('LkuDetail.note.1', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control'
                            ));
                        ?>
                    </td>
                    <td class="qty-tipe-motor">-</td>
                    <td>
                        <?php 
                            echo $this->Form->input('LkuDetail.note.1', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control'
                            ));
                        ?>
                    </td>
        		</tr>
        	</tbody>
    	</table>
    </div>
</div>
<div class="hide">
    <div id="select-tipe-motor">
        <?php
            echo $this->Form->input('tipe_motor_id', array(
                'label' => false,
                'options' => $tipe_motor_list,
                'empty' => __('Pilih Tipe Motor'),
                'class' => 'lku-choose-tipe-motor'
            ));
        ?>
    </div>
</div>