<div class="checkbox-info-detail <?php echo (!empty($this->request->data['Ttuj'])) ? '' : 'hide';?>">
	<div class="box box-primary">
	    <?php 
	            echo $this->element('blocks/common/box_header', array(
	                'title' => __('Detail TTUJ'),
	            ));
	    ?>
	    <div class="box-body table-responsive">
	        <table class="table table-hover">
	        	<thead>
	        		<tr>
	        			<?php 
			                    echo $this->Html->tag('th', __('No. Ttuj'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Supir'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('NoPol'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Tgl Ttuj'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Tujuan'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Keterangan'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Muatan'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Unit'), array(
			                        'class' => 'text-center',
			                        'style' => 'width: 8%;'
			                    ));
            					
                				if( empty($disabled_edit) ) {
				                    echo $this->Html->tag('th', __('Action'), array(
				                    	'class' => 'text-center action-biaya-document',
			                    	));
				                }
			            ?>
	        		</tr>
	        	</thead>
                <tbody id="checkbox-info-table">
					<?php
							$totalUnit = 0;
							$data = $this->request->data;
							$values = $this->Common->filterEmptyField($data, 'Ttuj');

							if(!empty($values)){
								foreach ($values as $key => $value) {
									$id = $this->Common->filterEmptyField($value, 'Ttuj', 'id');
									$no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
					                $driver = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');
					                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
					                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
					                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
					                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
					                $qty = $this->Common->filterEmptyField($value, 'Ttuj', 'qty');
                        			$note = $this->Common->filterEmptyField($value, 'Ttuj', 'note');

					                $unit = $this->Common->filterEmptyField($value, 'SuratJalanDetail', 'qty');
                        			$tujuan = sprintf('%s - %s', $from_city_name, $to_city_name);

									$totalUnit += $unit;

					                $ttuj_date = $this->Common->formatDate($ttuj_date, 'd/m/Y');

					                $checkbox = isset($checkbox)?$checkbox:true;
					                $alias = sprintf('child-%s', $id);

			                        $contentInput = $this->Form->input('SuratJalanDetail.qty.',array(
			                            'label'=> false,
			                            'required' => false,
                            			'error' => false,
			                            'class'=>'form-control input_number text-center sisa-amount',
			                            'value' => $unit,
			                            'data-alert' => __('Silahkan isi jumlah unit yang diterima'),
			                        ));
			                        $contentInput .= $this->Form->hidden('SuratJalanDetail.ttuj_id.',array(
			                            'value'=> $id,
			                        ));
				    ?>
				    <tr class="child child-<?php echo $alias; ?>">
				    	<?php 
				    			echo $this->Html->tag('td', $no_ttuj);
				    			echo $this->Html->tag('td', $driver);
				    			echo $this->Html->tag('td', $nopol, array(
				    				'class' => 'text-center',
			    				));
				    			echo $this->Html->tag('td', $ttuj_date, array(
				    				'class' => 'text-center',
			    				));
				    			echo $this->Html->tag('td', $tujuan, array(
				    				'class' => 'text-center',
			    				));
				    			echo $this->Html->tag('td', $note);
				    			echo $this->Html->tag('td', $qty, array(
				    				'class' => 'text-center',
			    				));
		                        echo $this->Html->tag('td', $contentInput);
                				
                				if( empty($disabled_edit) ) {
					                echo $this->Html->tag('td', $this->Html->link($this->Common->icon('fa fa-times'), '#', array(
					                    'class' => 'delete-document-current btn btn-danger btn-xs',
					                    'escape' => false,
					                    'data-id' => sprintf('child-%s', $alias),
					                )), array(
					                    'class' => 'text-center document-table-action',
					                ));
					            }
				        ?>
				    </tr>
				    <?php
								}
							}
					?>
				</tbody>
				<tr>
					<?php 
							echo $this->Html->tag('td', __('Total'), array(
								'colspan' => 7,
								'class' => 'bold text-right',
							));
							echo $this->Html->tag('td', $totalUnit, array(
								'class' => 'text-center',
								'id' => 'total-biaya',
							));
					?>
				</tr>

	    	</table>
	    </div>
	</div>
</div>