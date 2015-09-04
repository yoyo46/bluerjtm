<?php
	$this->Html->addCrumb(__('Laporan Ritase Truk'), array(
		'controller' => 'revenues',
		'action' => 'ritase_report'
	));
	$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Periode</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('Revenue', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'revenues',
                    'action' => 'search',
                    'detail_ritase',
                    $id
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('date', __('Tanggal'));
                    ?>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php 
                                echo $this->Form->input('Ttuj.date',array(
                                    'label'=> false,
                                    'class'=>'form-control pull-right date-range',
                                    'required' => false,
                                ));
                        ?>
                    </div>
                </div>
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => 'detail_ritase', 
                                $id
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
                            ));
                    ?>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Data Ritase Armada'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="row">
		        	<?php
		        		if(!empty($truk['Truck']['photo']) && !is_array($truk['Truck']['photo'])){
		        	?>
		        	<div class="col-sm-3">
		        		<?php
		    				echo $this->Common->photo_thumbnail(array(
								'save_path' => Configure::read('__Site.truck_photo_folder'), 
								'src' => $truk['Truck']['photo'], 
								'thumb'=>true,
								'size' => 'm',
								'thumb' => true,
							));
		        		?>
		        	</div>
		        	<?php
		        		}
		        	?>
		        	<div class="col-sm-6">
		        		<ul class="conntent-ritase-truk">
		        			<?php
		        					$brand_name = !empty($truk['TruckBrand']['name'])?$truk['TruckBrand']['name']:false;
		        					$category_name = !empty($truk['TruckCategory']['name'])?$truk['TruckCategory']['name']:false;
		        					$facility_name = !empty($truk['TruckFacility']['name'])?$truk['TruckFacility']['name']:false;
		        					$driver_name = !empty($truk['Driver']['name'])?$truk['Driver']['name']:false;

			        				$label = $this->Html->tag('label', 'VIN');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['id']));
			        				$label = $this->Html->tag('label', 'NOPOL');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['nopol']));

			        				$label = $this->Html->tag('label', 'STATUS');
			        				$status = 'AVAILABLE';
			        				if(!empty($truk['Truck']['sold'])){
			        					$status = 'TERJUAL';
			        				}

				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $status));

				        			$label = $this->Html->tag('label', 'TAHUN PERAKITAN');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['tahun']));

				        			$label = $this->Html->tag('label', 'MEREK');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $brand_name));

				        			$label = $this->Html->tag('label', 'JENIS TRUK');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $category_name));

				        			$label = $this->Html->tag('label', 'KAPASITAS');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['capacity']));

				        			if(!empty($facility_name)){
				        				$label = $this->Html->tag('label', 'FASILITAS');
				        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $facility_name));
				        			}

				        			if(!empty($driver_name)){
				        				$label = $this->Html->tag('label', 'Nama Supir');
				        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $driver_name));
				        			}
				        			if(!empty($truk['Customer']['name'])){
				        				$label = $this->Html->tag('label', 'GROUP');
				        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Customer']['name']));
				        			}
			        		?>
		        		</ul>
		        		<div class="clear"></div>
		        		<ul class="second-revenue">
		        			<?php
		        				if(!empty($truk['Truck']['atas_nama'])){
			        				$label = $this->Html->tag('label', 'KEPEMILIKAN');
			        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['atas_nama']));
			        			}
		        				if(!empty($truk['Truck']['no_machine'])){
			        				$label = $this->Html->tag('label', 'No. Mesin');
			        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['no_machine']));
			        			}
		        				if(!empty($truk['Truck']['no_rangka'])){
			        				$label = $this->Html->tag('label', 'No. Rangka');
			        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['no_rangka']));
			        			}
			        		?>
		        		</ul>
		        		<div class="clear"></div>
		        		<div class="row">
		        			<div class="col-sm-12 second-box">
		        				<div class="row">
		        					<div class="col-sm-6">
				        				<ul>
				        					<?php
						        				if(!empty($truk['Truck']['bpkb'])){
							        				$label = $this->Html->tag('label', 'No. BPKB');
							        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['bpkb']));
							        			}
						        				if(!empty($truk['Truck']['no_stnk'])){
							        				$label = $this->Html->tag('label', 'No. STNK');
							        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['no_stnk']));
							        			}
							        		?>
						        		</ul>
				        			</div>
				        			<div class="col-sm-6">
				        				<ul>
				        					<?php
						        				if(!empty($truk['Truck']['tgl_stnk'])){
							        				$label = $this->Html->tag('label', 'Tgl STNK');
							        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $truk['Truck']['tgl_stnk']));
							        			}
							        		?>
						        		</ul>
				        			</div>
		        				</div>
		        			</div>
		        		</div>
		        	</div>
		        	<div class="col-sm-3">
		        		<ul class="second-revenue">
		        			<?php
		        					$total_unit = !empty($total_unit)?$total_unit:0;
			        				$label = $this->Html->tag('label', 'Total Ritase');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $total_ritase));

			        				$label = $this->Html->tag('label', 'Total Unit');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $total_unit));
			        				
			        				$label = $this->Html->tag('label', 'NG Unit');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $total_lku));
			        		?>
		        		</ul>
		        	</div>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-12">
		<div class="box box-primary">
		    <div class="box-body">
		    	<?php
		    		if(!empty($truk_ritase)){
		    	?>
		    	<div class="box-body table-responsive">
			        <table class="table table-hover">
			        	<thead>
			        		<tr>
			            		<?php
			        				echo $this->Html->tag('th',  __('No.'));
			        				echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_berangkat', __('Tgl Muat'), array(
			                            'escape' => false
			                        )));
			        				echo $this->Html->tag('th', __('Qty'));
			        				echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.from_city_name', __('Asal'), array(
			                            'escape' => false
			                        )));
			        				echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.to_city_name', __('Tujuan'), array(
			                            'escape' => false
			                        )));
			        				echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.driver_name', __('Nama Supir'), array(
			                            'escape' => false
			                        )));
			        				echo $this->Html->tag('th', __('Status'));
			        				echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_tiba', __('Tgl Tiba'), array(
			                            'escape' => false
			                        )));
			        				echo $this->Html->tag('th', __('LT'));
			        				echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_bongkaran', __('Tgl Bongkaran'), array(
			                            'escape' => false
			                        )));
			        				echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.tgljam_balik', __('Tgl. Balik plant'), array(
			                            'escape' => false
			                        )));
			        				echo $this->Html->tag('th', __('LT'));
			        				echo $this->Html->tag('th', __('NG'));

			        			?>
			            	</tr>
			        	</thead>
			        	<tbody>
			        		<?php
			        			$i=1;
			        			foreach ($truk_ritase as $key => $value) {
			        		?>
			        		<tr>
			        			<?php
			        				echo $this->Html->tag('td', $i++);
			        				
			        				echo $this->Html->tag('td', $this->Common->customDate($value['Ttuj']['tgljam_berangkat'], 'd M y H:i'));
			        				echo $this->Html->tag('td', $value['qty_ritase']);

			        				echo $this->Html->tag('td', $value['Ttuj']['from_city_name']);
			        				echo $this->Html->tag('td', $value['Ttuj']['to_city_name']);
			        				echo $this->Html->tag('td', $value['Ttuj']['driver_name']);

			        				if(!empty($value['qty_lku'])){
			        					$lku = $this->Html->tag('span', 'NG', array('class' => 'label label-danger'));
			        				}else if( $value['Ttuj']['is_arrive'] && $value['Ttuj']['is_bongkaran'] ){
			        					$lku = $this->Html->tag('span', 'SB', array('class' => 'label label-info'));
			        				}else if( $value['Ttuj']['is_arrive'] && !$value['Ttuj']['is_bongkaran'] ){
			        					$lku = $this->Html->tag('span', 'BB', array('class' => 'label label-warning'));
			        				}else if( !empty($value['Ttuj']['is_pool']) ){
			        					$lku = $this->Html->tag('span', 'POOL', array('class' => 'label label-success'));
			        				} else {
			        					$lku = '-';
			        				}

			        				echo $this->Html->tag('td', $lku);

			        				echo $this->Html->tag('td', $this->Common->customDate($value['Ttuj']['tgljam_tiba'], 'd M y H:i', '-'));
			        				
			        				$LT = $this->Html->tag('span', $value['arrive_lead_time'], array(
			        					'class' => 'label label-success'
			        				));
			        				if(isset($value['arrive_over_time']) && !empty($value['arrive_over_time'])){
			        					$LT = $this->Html->tag('span', $value['arrive_over_time'], array(
				        					'class' => 'label label-danger'
				        				));
			        				}
			        				echo $this->Html->tag('td', $LT);

			        				echo $this->Html->tag('td', $this->Common->customDate($value['Ttuj']['tgljam_bongkaran'], 'd M y H:i', '-'));
			        				echo $this->Html->tag('td', $this->Common->customDate($value['Ttuj']['tgljam_balik'], 'd M y H:i', '-'));

			        				$LT = $this->Html->tag('span', $value['back_lead_time'], array(
			        					'class' => 'label label-success'
			        				));
			        				if(isset($value['back_orver_time']) && !empty($value['back_orver_time'])){
			        					$LT = $this->Html->tag('span', $value['back_orver_time'], array(
			        					'class' => 'label label-danger'
			        				));
			        				}
			        				echo $this->Html->tag('td', $LT);
			        				echo $this->Html->tag('td', $value['qty_lku']);
			        			?>
			        		</tr>
			        		<?php
			        			}
			        		?>
			        	</tbody>
			        </table>
			    </div>
		    	<?php
		    		}else{
		    			echo $this->Html->tag('div', __('Data belum tersedia'), array(
		    				'class' => 'alert alert-warning text-center',
		    			));
		    		}
		    	?>
		    </div>
		</div>
	</div>
	<?php echo $this->element('pagination');?>
</div>