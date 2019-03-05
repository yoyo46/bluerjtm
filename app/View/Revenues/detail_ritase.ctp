<?php
		$this->Html->addCrumb(__('Laporan Ritase Truk'), array(
			'controller' => 'revenues',
			'action' => 'ritase_report'
		));
		$this->Html->addCrumb($sub_module_title);
		echo $this->element('blocks/revenues/search_detail_ritase');

		$photo = $this->Common->filterEmptyField($truk, 'Truck', 'photo');
		$truck_id = $this->Common->filterEmptyField($truk, 'Truck', 'id');
		$nopol = $this->Common->filterEmptyField($truk, 'Truck', 'nopol');
		$tahun = $this->Common->filterEmptyField($truk, 'Truck', 'tahun');
		$capacity = $this->Common->filterEmptyField($truk, 'Truck', 'capacity');

		$brand_name = $this->Common->filterEmptyField($truk, 'TruckBrand', 'name');
		$category_name = $this->Common->filterEmptyField($truk, 'TruckCategory', 'name');
		$facility_name = $this->Common->filterEmptyField($truk, 'TruckFacility', 'name');
		$driver_name = $this->Common->filterEmptyField($truk, 'Driver', 'name');
		$customer = $this->Common->filterEmptyField($truk, 'Customer', 'name');
?>
<div class="row">
	<div class="col-sm-12">
		<div class="box box-primary">
		    <div class="box-header">
		        <?php
		        		echo $this->Html->tag('h3', __('Data Ritase Armada'), array(
				        	'class' => 'box-title',
				        ));
		        ?>
		    </div>
		    <div class="box-body">
		        <div class="row">
		        	<?php
		        			if( !empty($photo) ){
			    				echo $this->Html->tag('div', $this->Common->photo_thumbnail(array(
									'save_path' => Configure::read('__Site.truck_photo_folder'), 
									'src' => $photo, 
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
								        				$customTglStnk = $this->Common->formatDate($tgl_stnk, 'd M Y');
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

				        			$label = $this->Html->tag('label', 'KSU Unit');
				        			echo $this->Html->tag('li', sprintf('%s : %s', $label, $total_ksu));
			        		?>
		        		</ul>
		        	</div>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-12">
    	<?php
    			if(!empty($truk_ritase)){
			        $dataColumns = array(
			            'no' => array(
			                'name' => __('No.'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'no_contract\',width:50',
			                'align' => 'center',
			            ),
			            'customer' => array(
			                'name' => __('Customer'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'customer\',width:100',
			            ),
			            'qty' => array(
			                'name' => __('Qty'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'qty\',width:70',
			                'align' => 'center',
			            ),
			            'from_city_name' => array(
			                'name' => __('Dari'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'from_city_name\',width:100',
			            ),
			            'to_city_name' => array(
			                'name' => __('Tujuan'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'to_city_name\',width:100',
        					'fix_column' => true,
			            ),
			            'driver_name' => array(
			                'name' => __('Supir'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'driver_name\',width:100',
			            ),
			            'status' => array(
			                'name' => __('Status'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'status\',width:80',
			                'align' => 'center',
			            ),
			            'ttuj_date' => array(
			                'name' => __('Tgl TTUJ'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'ttuj_date\',width:100',
			                'align' => 'center',
			            ),
			            'note' => array(
			                'name' => __('Keterangan'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'note\',width:120',
			            ),
			            'tgljam_berangkat' => array(
			                'name' => __('Muat'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'tgljam_berangkat\',width:100',
			                'align' => 'center',
			            ),
			            'tgljam_tiba' => array(
			                'name' => __('Tiba'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'tgljam_tiba\',width:100',
			                'align' => 'center',
			            ),
			            'lt_arrive' => array(
			                'name' => __('LT Tiba'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'lt_arrive\',width:80',
			                'align' => 'center',
			            ),
			            'tgljam_bongkaran' => array(
			                'name' => __('Bongkaran'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'tgljam_bongkaran\',width:100',
			                'align' => 'center',
			            ),
			            'tgljam_balik' => array(
			                'name' => __('Balik'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'tgljam_balik\',width:100',
			                'align' => 'center',
			            ),
			            'tgljam_pool' => array(
			                'name' => __('Sampai Pool'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'tgljam_pool\',width:100',
			                'align' => 'center',
			            ),
			            'lt_kembali' => array(
			                'name' => __('LT Kembali'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'lt_kembali\',width:80',
			                'align' => 'center',
			            ),
			            'ng' => array(
			                'name' => __('NG'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'ng\',width:80',
			                'align' => 'center',
			            ),
			            'ksu' => array(
			                'name' => __('KSU'),
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'ksu\',width:80',
			                'align' => 'center',
			            ),
			        );
					$fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
    	?>
    	<div class="box-body table-responsive">
	        <table class="table table-hover easyui-datagrid" style="width: 100%;height: 550px;" singleSelect="true">
                <?php
                        if( !empty($fieldColumn) ) {
                            echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn), array(
                            	'frozen' => 'true',
                        	));
                        }
                ?>
	        	<tbody>
	        		<?php
		        			$i=1;
		        			foreach ($truk_ritase as $key => $value) {
		        				$qty = $this->Common->filterEmptyField($value, 'qty_ritase');
		        				$lku_qty = $this->Common->filterEmptyField($value, 'Lku', 'qty');
		        				$ksu_qty = $this->Common->filterEmptyField($value, 'Ksu', 'qty');

		        				$customer = $this->Common->filterEmptyField($value, 'Customer', 'code');

		        				$no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
		        				$ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
		        				$from_city = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
		        				$to_city = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
		        				
		        				$driver = $this->Common->_callGetDriver($value);

		        				$tgljam_berangkat = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_berangkat');
		        				$tgljam_tiba = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_tiba');
		        				$tgljam_bongkaran = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_bongkaran');
		        				$tgljam_balik = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_balik');
		        				$tgljam_pool = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_pool');
		        				$note = $this->Common->filterEmptyField($value, 'Ttuj', 'note', '-');

		        				$arrive_leadtime = $this->Ttuj->_callLeadTime($value, 'arrive');
		        				$back_leadtime = $this->Ttuj->_callLeadTime($value, 'back');

		        				$arrive_leadtime_total = $this->Common->filterEmptyField($value, 'Ttuj', 'arrive_leadtime_total');
		        				$back_leadtime_total = $this->Common->filterEmptyField($value, 'Ttuj', 'back_leadtime_total');

		        				$arrive_over_time = $this->Common->filterEmptyField($value, 'UangJalan', 'arrive_lead_time');
		        				$back_orver_time = $this->Common->filterEmptyField($value, 'UangJalan', 'back_lead_time');

		        				$customTglBerangkat = $this->Common->formatDate($tgljam_berangkat, 'd M Y H:i:s');
		        				$customTglTiba = $this->Common->formatDate($tgljam_tiba, 'd M Y H:i:s');
		        				$customTglBongkaran = $this->Common->formatDate($tgljam_bongkaran, 'd M Y H:i:s');
		        				$customTglBalik = $this->Common->formatDate($tgljam_balik, 'd M Y H:i:s');
		        				$customTglPool = $this->Common->formatDate($tgljam_pool, 'd M Y H:i:s');
		        				$customStatus = $this->Revenue->_callStatusTTUJ($value, 'sort');
		        				$customTtujDate = $this->Common->formatDate($ttuj_date, 'd M Y');

		        				if( $arrive_leadtime_total > $arrive_over_time ){
		        					$labelClass = 'danger';
		        				} else {
		        					$labelClass = 'success';
		        				}

		        				$customLeadTimeArrive = $this->Html->tag('span', $arrive_leadtime, array(
		        					'class' => sprintf('block label label-%s', $labelClass),
		        				));

		        				if( $back_leadtime_total > $back_orver_time ){
		        					$labelClass = 'danger';
		        				} else {
		        					$labelClass = 'success';
		        				}

		        				$customLeadTimeBack = $this->Html->tag('span', $back_leadtime, array(
		        					'class' => sprintf('block label label-%s', $labelClass),
		        				));

		        				if( !empty($lku_qty) ) {
		        					$customLku = $this->Html->link($lku_qty, array(
		        						'controller' => 'lkus',
		        						'action' => 'index',
		        						'no_ttuj' => $no_ttuj,
	        						), array(
	        							'class' => 'white',
	        							'target' => '_blank',
	        						));
			        				$customLku = $this->Html->tag('span', $customLku, array(
			        					'class' => 'label label-warning block',
		        					));
		        				} else {
		        					$customLku = '-';
		        				}

		        				if( !empty($ksu_qty) ) {
		        					$customKsu = $this->Html->link($ksu_qty, array(
		        						'controller' => 'lkus',
		        						'action' => 'ksus',
		        						'no_ttuj' => $no_ttuj,
	        						), array(
	        							'class' => 'white',
	        							'target' => '_blank',
	        						));
			        				$customKsu = $this->Html->tag('span', $customKsu, array(
			        					'class' => 'label label-warning block',
		        					));
		        				} else {
		        					$customKsu = '-';
		        				}
	        		?>
	        		<tr>
	        			<?php
		        				echo $this->Html->tag('td', $i++, array(
		        					'class' => 'text-center',
	        					));
		        				echo $this->Html->tag('td', $customer);
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
		        				echo $this->Html->tag('td', $customTtujDate);
		        				echo $this->Html->tag('td', $note);
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
	        					echo $this->Html->tag('td', $customKsu, array(
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
	<?php echo $this->element('pagination');?>
</div>