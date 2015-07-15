<?php
		$this->Html->addCrumb(__('TTUJ'), array(
			'controller' => 'revenues',
			'action' => 'ttuj'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->element('blocks/revenues/info_ttuj');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));

                // if( in_array('insert_revenues', $allowModule) ) {
                if( $flagAdd ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Common->rule_link('<i class="fa fa-plus"></i> Terima Surat Jalan', array(
                    'controller' => 'revenues',
                    'action' => 'surat_jalan_add',
                    $ttuj_id,
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app btn-success pull-right'
                ));
            ?>
            <div class="clear"></div>
        </div>
        <?php 
                }
        ?>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', __('Tgl Terima'));
                        echo $this->Html->tag('th', __('Qty'));
                        echo $this->Html->tag('th', __('Keterangan'));
                        echo $this->Html->tag('th', __('Dibuat'));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    if(!empty($suratJalans)){
                    	$totalQtySJ = 0;

                        foreach ($suratJalans as $key => $value) {
                            $id = $value['SuratJalan']['id'];
                    		$totalQtySJ += $value['SuratJalan']['qty'];
            ?>
            <tr>
                <td><?php echo $this->Common->customDate($value['SuratJalan']['tgl_surat_jalan'], 'd/m/Y');?></td>
                <td><?php echo $value['SuratJalan']['qty'];?></td>
                <td><?php echo $value['SuratJalan']['note'];?></td>
                <td><?php echo $this->Common->customDate($value['SuratJalan']['created']);?></td>
                <td class="action">
                    <?php
                            // if( in_array('delete_revenues', $allowModule) ) {
                                echo $this->Common->rule_link(__('Ubah'), array(
                                    'controller' => 'revenues',
                                    'action' => 'surat_jalan_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                    'title' => 'disable status brand'
                                ));
                            // }
                            // if( in_array('delete_revenues', $allowModule) ) {
                                echo $this->Common->rule_link(__('Hapus'), array(
                                    'controller' => 'revenues',
                                    'action' => 'surat_jalan_delete',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan membatalkan data ini?'));
                            // }
                    ?>
                </td>
            </tr>
            <?php
                        }
            ?>
            <tr>
                <td><?php echo $this->Html->tag('strong', __('Total'));?></td>
                <td><?php echo $this->Html->tag('strong', $totalQtySJ);?></td>
                <td colspan="3">&nbsp;</td>
            </tr>
            <?php
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '10'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Common->rule_link(__('Kembali'), array(
				'controller' => 'revenues',
				'action' => 'ttuj' 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>