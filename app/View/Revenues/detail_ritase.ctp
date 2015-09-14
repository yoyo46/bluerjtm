<?php
		$this->Html->addCrumb(__('Laporan Ritase Truk'), array(
			'controller' => 'revenues',
			'action' => 'ritase_report'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->element('blocks/revenues/search_detail_ritase');
?>
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
			    				echo $this->Html->tag('div', $this->Common->photo_thumbnail(array(
									'save_path' => Configure::read('__Site.truck_photo_folder'), 
									'src' => $truk['Truck']['photo'], 
									'thumb'=>true,
									'size' => 'm',
									'thumb' => true,
								)), array(
									'class' => 'col-sm-3',
								));
		        			}
		        	?>
		        	<div class="col-sm-6">
		        		<ul class="conntent-ritase-truk">
		        			<?php
		        					$truck_id = $this->Common->filterEmptyField($truk, 'Truck', 'id');
		        					$nopol = $this->Common->filterEmptyField($truk, 'Truck', 'nopol');
		        					$tahun = $this->Common->filterEmptyField($truk, 'Truck', 'tahun');
		        					$capacity = $this->Common->filterEmptyField($truk, 'Truck', 'capacity');

		        					$brand_name = $this->Common->filterEmptyField($truk, 'TruckBrand', 'name');
		        					$category_name = $this->Common->filterEmptyField($truk, 'TruckCategory', 'name');
		        					$facility_name = $this->Common->filterEmptyField($truk, 'TruckFacility', 'name');
		        					$driver_name = $this->Common->filterEmptyField($truk, 'Driver', 'name');
		        					$customer = $this->Common->filterEmptyField($truk, 'Customer', 'name');

			        				$label = $this->Html->tag('label', __('VIN'));
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $truck_id));

			        				$label = $this->Html->tag('label', __('NOPOL'));
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $nopol));

			        				$label = $this->Html->tag('label', __('STATUS'));
			        				$status = __('AVAILABLE');

			        				if(!empty($truk['Truck']['sold'])){
			        					$status = __('TERJUAL');
			        				}

				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $status));

				        			$label = $this->Html->tag('label', 'TAHUN PERAKITAN');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $tahun));

				        			$label = $this->Html->tag('label', 'MEREK');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $brand_name));

				        			$label = $this->Html->tag('label', 'JENIS TRUK');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $category_name));

				        			$label = $this->Html->tag('label', 'KAPASITAS');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $capacity));

				        			if(!empty($facility_name)){
				        				$label = $this->Html->tag('label', 'FASILITAS');
				        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $facility_name));
				        			}

				        			if(!empty($driver_name)){
				        				$label = $this->Html->tag('label', 'Supir');
				        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $driver_name));
				        			}
				        			if(!empty($truk['Customer']['name'])){
				        				$label = $this->Html->tag('label', 'GROUP');
				        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $customer));
				        			}
			        		?>
		        		</ul>
		        		<div class="clear"></div>
		        		<ul class="second-revenue">
		        			<?php
		        					$atas_nama = $this->Common->filterEmptyField($truk, 'Truck', 'atas_nama');
		        					$no_machine = $this->Common->filterEmptyField($truk, 'Truck', 'no_machine');
		        					$no_rangka = $this->Common->filterEmptyField($truk, 'Truck', 'no_rangka');
									$bpkb = $this->Common->filterEmptyField($truk, 'Truck', 'bpkb');
									$no_stnk = $this->Common->filterEmptyField($truk, 'Truck', 'no_stnk');
									$tgl_stnk = $this->Common->filterEmptyField($truk, 'Truck', 'tgl_stnk');

			        				if(!empty($atas_nama)){
				        				$label = $this->Html->tag('label', 'KEPEMILIKAN');
				        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $atas_nama));
				        			}
			        				if(!empty($no_machine)){
				        				$label = $this->Html->tag('label', 'No. Mesin');
				        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $no_machine));
				        			}
			        				if(!empty($no_rangka)){
				        				$label = $this->Html->tag('label', 'No. Rangka');
				        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $no_rangka));
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
							        				if(!empty($bpkb)){
								        				$label = $this->Html->tag('label', 'No. BPKB');
								        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $bpkb));
								        			}
							        				if(!empty($no_stnk)){
								        				$label = $this->Html->tag('label', 'No. STNK');
								        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $no_stnk));
								        			}
							        		?>
						        		</ul>
				        			</div>
				        			<div class="col-sm-6">
				        				<ul>
				        					<?php
							        				if(!empty($tgl_stnk)){
								        				$customTglStnk = $this->Common->formatDate($tgl_stnk, 'd/m/Y');
								        				$label = $this->Html->tag('label', 'Tgl STNK');

								        				echo $this->Html->tag('li', sprintf('%s : %s', $label, $customTglStnk));
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
					        $dataColumns = array(
					            'no' => array(
					                'name' => __('No.'),
					                'field_model' => false,
					                'class' => 'text-center',
					                'display' => true,
					            ),
					            'qty' => array(
					                'name' => __('Qty'),
					                'field_model' => false,
					                'class' => 'text-center',
					                'display' => true,
					            ),
					            'from_city_name' => array(
					                'name' => __('Dari'),
					                'field_model' => false,
					                'display' => true,
					            ),
					            'to_city_name' => array(
					                'name' => __('Tujuan'),
					                'field_model' => false,
					                'display' => true,
					            ),
					            'driver_name' => array(
					                'name' => __('Supir'),
					                'field_model' => false,
					                'display' => true,
					            ),
					            'status' => array(
					                'name' => __('Status'),
					                'field_model' => false,
					                'display' => true,
					            ),
					            'tgljam_berangkat' => array(
					                'name' => __('Muat'),
					                'field_model' => false,
					                'display' => true,
					            ),
					            'tgljam_tiba' => array(
					                'name' => __('Tiba'),
					                'field_model' => false,
					                'display' => true,
					            ),
					            'lt_arrive' => array(
					                'name' => __('LT Tiba'),
					                'field_model' => false,
					                'class' => 'text-center',
					                'display' => true,
					            ),
					            'tgljam_bongkaran' => array(
					                'name' => __('Bongkaran'),
					                'field_model' => false,
					                'display' => true,
					            ),
					            'tgljam_balik' => array(
					                'name' => __('Balik'),
					                'field_model' => false,
					                'display' => true,
					            ),
					            'tgljam_pool' => array(
					                'name' => __('Sampai Pool'),
					                'field_model' => false,
					                'display' => true,
					            ),
					            'lt_kembali' => array(
					                'name' => __('LT Kembali'),
					                'field_model' => false,
					                'class' => 'text-center',
					                'display' => true,
					            ),
					            'ng' => array(
					                'name' => __('NG'),
					                'field_model' => false,
					                'class' => 'text-center',
					                'display' => true,
					            ),
					        );
        					$fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
		    	?>
		    	<div class="box-body table-responsive">
			        <table class="table table-hover">
	                    <?php
	                            if( !empty($fieldColumn) ) {
	                                echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
	                            }
	                    ?>
			        	<tbody>
			        		<?php
				        			$i=1;
				        			foreach ($truk_ritase as $key => $value) {
				        				$qty = $this->Common->filterEmptyField($value, 'qty_ritase');
				        				$lku_qty = $this->Common->filterEmptyField($value, 'Lku', 'qty', '-');

				        				$from_city = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
				        				$to_city = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
				        				$driver = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');
				        				$tgljam_berangkat = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_berangkat');
				        				$tgljam_tiba = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_tiba');
				        				$tgljam_bongkaran = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_bongkaran');
				        				$tgljam_balik = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_balik');
				        				$tgljam_pool = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_pool');

				        				$arrive_lead_time = $this->Common->filterEmptyField($value, 'ArriveLeadTime', 'total_hour');
				        				$back_lead_time = $this->Common->filterEmptyField($value, 'BackLeadTime', 'total_hour');

				        				$arrive_over_time = $this->Common->filterEmptyField($value, 'Ttuj', 'arrive_over_time');
				        				$back_orver_time = $this->Common->filterEmptyField($value, 'Ttuj', 'back_orver_time');

				        				$customTglBerangkat = $this->Common->formatDate($tgljam_berangkat, 'd/m/Y H:i:s');
				        				$customTglTiba = $this->Common->formatDate($tgljam_tiba, 'd/m/Y H:i:s');
				        				$customTglBongkaran = $this->Common->formatDate($tgljam_bongkaran, 'd/m/Y H:i:s');
				        				$customTglBalik = $this->Common->formatDate($tgljam_balik, 'd/m/Y H:i:s');
				        				$customTglPool = $this->Common->formatDate($tgljam_pool, 'd/m/Y H:i:s');
				        				$customStatus = $this->Revenue->_callStatusTTUJ($value, 'sort');

				        				if( $arrive_lead_time > $arrive_over_time ){
				        					$labelClass = 'danger';
				        				} else {
				        					$labelClass = 'success';
				        				}

				        				$arriveLeadTime = $this->Common->filterEmptyField($value, 'ArriveLeadTime', 'FormatArr');
				        				$arriveLeadTime = implode('<br>', $arriveLeadTime);
				        				$customLeadTimeArrive = $this->Html->tag('span', $arriveLeadTime, array(
				        					'class' => sprintf('block label label-%s', $labelClass),
				        				));

				        				if( $back_lead_time > $back_orver_time ){
				        					$labelClass = 'danger';
				        				} else {
				        					$labelClass = 'success';
				        				}

			        					$backLeadTime = $this->Common->filterEmptyField($value, 'BackLeadTime', 'FormatArr');
				        				$backLeadTime = implode('<br>', $backLeadTime);
				        				$customLeadTimeBack = $this->Html->tag('span', $backLeadTime, array(
				        					'class' => sprintf('block label label-%s', $labelClass),
				        				));

				        				if( !empty($lku_qty) ) {
				        					$customLku = $this->Html->link($lku_qty, array(
				        						'controller' => 'lkus',
				        						'action' => 'index',
				        						'nopol' => $nopol,
			        						), array(
			        							'class' => 'white',
			        							'target' => '_blank',
			        						));
				        				} else {
				        					$customLku = '-';
				        				}

				        				$customLku = $this->Html->tag('span', $customLku, array(
				        					'class' => 'label label-warning block',
			        					));
			        		?>
			        		<tr>
			        			<?php
				        				echo $this->Html->tag('td', $i++, array(
				        					'class' => 'text-center',
			        					));
				        				
				        				echo $this->Html->tag('td', $qty, array(
				        					'class' => 'text-center',
			        					));

				        				echo $this->Html->tag('td', $from_city);
				        				echo $this->Html->tag('td', $to_city);
				        				echo $this->Html->tag('td', $driver);
				        				echo $this->Html->tag('td', $this->Html->tag('span', $customStatus, array(
				        					'class' => 'label label-primary',
			        					)), array(
				        					'class' => 'text-center',
			        					));
				        				echo $this->Html->tag('td', $customTglBerangkat);
				        				echo $this->Html->tag('td', $customTglTiba);
				        				echo $this->Html->tag('td', $customLeadTimeArrive, array(
				        					'class' => 'text-center',
			        					));

				        				echo $this->Html->tag('td', $customTglBongkaran);
				        				echo $this->Html->tag('td', $customTglBalik);
				        				echo $this->Html->tag('td', $customTglPool);
				        				echo $this->Html->tag('td', $customLeadTimeBack, array(
				        					'class' => 'text-center',
			        					));
				        				echo $this->Html->tag('td', $customLku, array(
				        					'class' => 'text-center',
			        					));
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