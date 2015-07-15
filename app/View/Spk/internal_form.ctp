<?php
		$this->Html->addCrumb(__('SPK'), array(
			'controller' => 'spk',
			'action' => 'internal'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Spk', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));

?>
<div class="spk-form">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Informasi SPK Internal'); ?></h3>
	    </div>
	    <div class="box-body">
	        <div class="form-group">
	        	<?php 
						echo $this->Form->input('no_doc',array(
							'label'=> __('No. Dokumen'), 
							'class'=>'form-control',
							'required' => false,
							'placeholder' => __('No. Dokumen'),
						));
				?>
	        </div>
	        <div class="form-group">
				<?php 
						echo $this->Form->input('Spk.date_spk',array(
							'type' => 'text',
							'label'=> __('Tanggal SPK *'), 
							'class'=>'form-control custom-date',
							'required' => false,
							'value' => (!empty($this->request->data['Spk']['date_spk'])) ? $this->request->data['Spk']['date_spk'] : date('d/m/Y')
						));
				?>
			</div>
	        <div class="form-group">
				<?php 
						echo $this->Form->input('Spk.type',array(
							'label'=> __('Jenis Dokumen *'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Jenis Dokumen'),
							'options' => array(
								'maintenance' => __('Maintenance'),
							),
						));
				?>
			</div>
	        <div class="form-group">
				<?php 
						echo $this->Form->input('Spk.type',array(
							'label'=> __('Jenis Dokumen *'), 
							'class'=>'form-control',
							'required' => false,
							'empty' => __('Pilih Jenis Dokumen'),
							'options' => array(
								'maintenance' => __('Maintenance'),
							),
						));
				?>
			</div>
	        <div class="form-group">
                <?php 
    					$attrBrowse = array(
                            'class' => 'ajaxModal visible-xs',
                            'escape' => false,
                            'title' => __('Data Truk'),
                            'data-action' => 'browse-form',
                            'data-change' => 'truckID',
                            'id' => 'truckBrowse',
                        );
    					$urlBrowse = array(
                            'controller'=> 'ajax', 
                            'action' => 'getTrucks',
                            'spk',
                            !empty($data_local['Spk']['id'])?$data_local['Spk']['id']:false,
                        );
                        echo $this->Form->label('truck_id', __('No. Pol * ').$this->Common->rule_link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
                ?>
                <div class="row">
                    <div class="col-sm-10">
			        	<?php 
								echo $this->Form->input('truck_id',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Pilih No. Pol --'),
									'div' => array(
										'class' => 'truck_id'
									),
									'id' => 'truckID',
								));
						?>
                    </div>
    				<div class="col-sm-2 hidden-xs">
                        <?php 
    							$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                                echo $this->Common->rule_link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
                        ?>
                    </div>
                </div>
	        </div>
	        <div class="form-group">
                <?php 
    					$attrBrowse = array(
                            'class' => 'ajaxModal visible-xs',
                            'escape' => false,
                            'title' => __('Data Kepala Mekanik'),
                            'data-action' => 'browse-form',
                            'data-change' => 'employeID',
                            'id' => 'employeBrowse',
                        );
    					$urlBrowse = array(
                            'controller'=> 'ajax', 
                            'action' => 'getEmploye',
                            'spk',
                            !empty($data_local['Spk']['id'])?$data_local['Spk']['id']:false,
                        );
                        echo $this->Form->label('employe_id', __('Kepala Mekanik * ').$this->Common->rule_link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
                ?>
                <div class="row">
                    <div class="col-sm-10">
			        	<?php 
								echo $this->Form->input('employe_id',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'empty' => __('Pilih Kepala Mekanik --'),
									'div' => array(
										'class' => 'employe_id'
									),
									'id' => 'employeID',
								));
						?>
                    </div>
    				<div class="col-sm-2 hidden-xs">
                        <?php 
    							$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                                echo $this->Common->rule_link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
                        ?>
                    </div>
                </div>
	        </div>
        	<div class="form-group">
	        	<?php 
						echo $this->Form->label('date_target_from', __('Tgl Selesai'));
				?>
        		<div class="row">
        			<div class="col-sm-6">
			        	<?php 
								echo $this->Form->input('date_target_from', array(
									'label'=> false, 
									'class'=>'form-control custom-date',
									'type' => 'text',
								));
						?>
        			</div>
        			<div class="col-sm-6">
			        	<?php 
								echo $this->Form->input('date_target_to', array(
									'label'=> false, 
									'class'=>'form-control custom-date',
									'type' => 'text',
								));
						?>
        			</div>
        		</div>
	        </div>
	        <div class="form-group">
				<?php 
						echo $this->Form->input('Spk.note',array(
							'label'=> __('Keterangan'), 
							'class'=>'form-control',
							'required' => false,
						));
				?>
			</div>
	    </div>
	</div>
</div>
<div id="detail-product">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Informasi Item'); ?></h3>
	    </div>
	    <div class="box-body table-responsive">
	        <div class="form-group">
	            <?php
	                    echo $this->Common->rule_link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
	                        'class' => 'add-custom-field btn btn-success btn-xs',
	                        'action_type' => 'spk_internal',
	                        'escape' => false
	                    ));
	            ?>
	        </div>
	        <table class="table table-hover">
	            <thead>
	                <tr>
	                    <th><?php echo __('Kode Barang');?></th>
	                    <th><?php echo __('Nama');?></th>
	                    <th><?php echo __('Jenis');?></th>
	                    <th><?php echo __('Tipe');?></th>
	                    <th><?php echo __('Merk');?></th>
	                    <th><?php echo __('Qty');?></th>
	                    <th><?php echo __('Satuan');?></th>
	                    <th><?php echo __('Keterangan');?></th>
	                    <th><?php echo __('Action');?></th>
	                </tr>
	            </thead>
	            <tbody class="spk-items">
	                <?php
		                    $count = 1;
		                    if(!empty($this->request->data['LkuDetail'])){
		                        $count = count($this->request->data['LkuDetail']);
		                    }
		                    $total = 0;
		                    for ($i=0; $i < $count; $i++) { 
		                        $price = (isset($this->request->data['LkuDetail'][$i]['price']) && !empty($this->request->data['LkuDetail'][$i]['price'])) ? $this->request->data['LkuDetail'][$i]['price'] : 0;
		                        $qty = (isset($this->request->data['LkuDetail'][$i]['qty']) && !empty($this->request->data['LkuDetail'][$i]['qty'])) ? $this->request->data['LkuDetail'][$i]['qty'] : 0;
	                ?>
	                <tr>
	                    <td>
	                        <?php
	                            echo $this->Form->input('LkuDetail.tipe_motor_id.', array(
	                                'options' => !empty($tipe_motor_list)?$tipe_motor_list:false,
	                                'label' => false,
	                                'empty' => __('Pilih Tipe Motor'),
	                                'class' => 'lku-choose-tipe-motor form-control',
	                                'required' => false,
	                                'value' => (isset($this->request->data['LkuDetail'][$i]['tipe_motor_id']) && !empty($this->request->data['LkuDetail'][$i]['tipe_motor_id'])) ? $this->request->data['LkuDetail'][$i]['tipe_motor_id'] : ''
	                            ));
	                        ?>
	                    </td>
	                    <td class="lku-color-motor" align="center">
	                        <?php
	                                if( isset($this->request->data['LkuDetail'][$i]['ColorMotor']['name']) && !empty($this->request->data['LkuDetail'][$i]['ColorMotor']['name']) ){
	                                    echo $this->request->data['LkuDetail'][$i]['ColorMotor']['name'];
	                                }else{
	                                    echo '-';
	                                }
	                        ?>
	                    </td>
	                    <td>
	                        <?php 
	                            echo $this->Form->input('LkuDetail.no_rangka.', array(
	                                'type' => 'text',
	                                'label' => false,
	                                'class' => 'form-control',
	                                'required' => false,
	                                'value' => (isset($this->request->data['LkuDetail'][$i]['no_rangka']) && !empty($this->request->data['LkuDetail'][$i]['no_rangka'])) ? $this->request->data['LkuDetail'][$i]['no_rangka'] : ''
	                            ));
	                        ?>
	                    </td>
	                    <td>
	                        <?php 
	                            echo $this->Form->input('LkuDetail.note.', array(
	                                'type' => 'text',
	                                'label' => false,
	                                'class' => 'form-control',
	                                'required' => false,
	                                'value' => (isset($this->request->data['LkuDetail'][$i]['note']) && !empty($this->request->data['LkuDetail'][$i]['note'])) ? $this->request->data['LkuDetail'][$i]['note'] : ''
	                            ));
	                        ?>
	                    </td>
	                    <td>
	                        <?php 
	                            echo $this->Form->input('LkuDetail.part_motor_id.', array(
	                                'label' => false,
	                                'class' => 'form-control',
	                                'required' => false,
	                                'empty' => __('Pilih Part Motor'),
	                                'options' => $part_motors,
	                                'value' => (isset($this->request->data['LkuDetail'][$i]['part_motor_id']) && !empty($this->request->data['LkuDetail'][$i]['part_motor_id'])) ? $this->request->data['LkuDetail'][$i]['part_motor_id'] : ''
	                            ));
	                        ?>
	                    </td>
	                    <td class="qty-tipe-motor" align="center">
	                        <?php
	                            if(!empty($qty) && !empty($this->request->data['LkuDetail'][$i]['TipeMotor']['TtujTipeMotor']['qty'])){
	                                $options = array();

	                                for ($a=1; $a <= $this->request->data['LkuDetail'][$i]['TipeMotor']['TtujTipeMotor']['qty'] ; $a++) { 
	                                    $options[$a] = $a;
	                                }

	                                echo $this->Form->input('LkuDetail.qty.', array(
	                                    'options' => $options,
	                                    'empty' => __('Pilih Jumlah Klaim'),
	                                    'class' => 'claim-number form-control',
	                                    'div' => false,
	                                    'label' => false,
	                                    'value' => $qty
	                                ));
	                            }else{
	                                echo '-';
	                            }
	                        ?>
	                    </td>
	                    <td align="right">
	                        <?php 
	                            echo $this->Form->input('LkuDetail.price.', array(
	                                'type' => 'text',
	                                'label' => false,
	                                'class' => 'form-control price-tipe-motor input_number',
	                                'required' => false,
	                                'value' => $price
	                            ));
	                        ?>
	                    </td>
	                    <td class="total-price-claim" align="right">
	                        <?php 
	                            $value_price = 0;
	                            if(!empty($price) && !empty($qty)){
	                                $value_price = $price * $qty;
	                                $total += $value_price;
	                            }

	                            echo $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0));
	                        ?>
	                    </td>
	                    <td>
	                        <?php
	                            echo $this->Common->rule_link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
	                                'class' => 'delete-custom-field btn btn-danger btn-xs',
	                                'escape' => false,
	                                'action_type' => 'lku_first'
	                            ));
	                        ?>
	                    </td>
	                </tr>
	                <?php
	                    }
	                ?>
	                <tr id="field-grand-total-lku">
	                    <td align="right" colspan="6"><?php echo __('Total Biaya Klaim')?></td>
	                    <td align="right" id="grand-total-lku">
	                        <?php 
	                            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
	                        ?>
	                    </td>
	                    <td>&nbsp;</td>
	                </tr>
	            </tbody>
	        </table>
	    </div>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Common->rule_link(__('Kembali'), array(
				'action' => 'index', 
			), array(
				'class'=> 'btn btn-default',
			));

    		echo $this->Form->button(__('Simpan'), array(
    			'type' => 'submit',
				'class'=> 'btn btn-success submit-form btn-lg',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>