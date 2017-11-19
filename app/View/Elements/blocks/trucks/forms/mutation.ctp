<?php 
		$data_changes = !empty($data_changes)?$data_changes:false;
		$branches = !empty($branches)?$branches:false;
		$truckCategories = !empty($truckCategories)?$truckCategories:false;
		$truckFacilities = !empty($truckFacilities)?$truckFacilities:false;
		$drivers = !empty($drivers)?$drivers:false;
		$data = !empty($this->request->data)?$this->request->data:false;

		$disabled = true;
		$disabledChange = true;

		if( !empty($data_changes) ) {
			$disabled = false;
		}
?>
<div class="form-group relative">
    <?php 
    		if( !empty($data_changes) ) {
    			$fieldClass = 'change-nopol';

    			if( isset($data['DataMutation']['change_nopol']) ) {
    				$fieldIcon = 'angle-double-left';
    				$disabledChangeNopol = false;
    			} else {
    				$fieldIcon = false;
    				$disabledChangeNopol = $disabledChange;
    			}

				echo $this->Form->input('DataMutation.change_nopol',array(
					'label'=> __('No. Pol'), 
					'class'=>'form-control '.$fieldClass,
					'required' => false,
					'disabled' => $disabledChangeNopol,
				));
				// echo $this->Form->error('change_nopol');

				echo $this->element('blocks/trucks/forms/link_activate_field', array(
					'fieldClass' => $fieldClass,
					'fieldIcon' => $fieldIcon,
					'data_changes' => $data_changes,
				));
			} else {
				$attrBrowse = array(
	                'class' => 'ajaxModal visible-xs browse-docs',
	                'escape' => false,
	                'title' => __('Data Truk'),
	                'data-change' => 'truckID',
                    'data-action' => 'browse-form',
	                'id' => 'truckBrowse',
	            );
				$urlBrowse = array(
	                'controller'=> 'ajax', 
	                'action' => 'getTrucks',
	            );
	            echo $this->Form->label('truck_id', __('No. Pol ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
    ?>
    <div class="row">
        <div class="col-sm-10">
        	<?php 
        			if( !empty($id) ) {
						echo $this->Form->input('Truck.nopol',array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
							'disabled' => true,
						));
        			} else {
						echo $this->Form->input('Truck.truck_id',array(
							'label'=> false, 
							'class'=>'form-control chosen-select',
							'required' => false,
							'empty' => __('Pilih No. Pol --'),
							'div' => array(
								'class' => 'truck_id'
							),
							'id' => 'truckID',
		                	'data-action' => 'truck-mutation',
						));
					}
			?>
        </div>
        <?php 
    			if( empty($id) ) {
					$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
	                echo $this->Html->tag('div', $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse), array(
	                	'class' => 'col-sm-2 hidden-xs',
	            	));
	            }
        ?>
    </div>
    <?php 
    			echo $this->Form->error('truck_id');
    		}
    ?>
</div>
<div class="form-group relative">
	<?php 
			$options = array(
				'label'=> __('Cabang'), 
				'class'=>'form-control',
				'required' => false,
				'readonly' => $disabled,
			);
			$fieldClass = 'change-branch';

			if( isset($data['DataMutation']['change_branch_id']) ) {
				$fieldIcon = 'angle-double-left';
				$disabledChangeBranch = false;
			} else {
				$fieldIcon = false;
				$disabledChangeBranch = $disabledChange;
			}

			if( !empty($data_changes) ) {
				$options['class'] .= ' '.$fieldClass;
				$options['disabled'] = $disabledChangeBranch;
				$errorLabel = $this->Form->error('change_branch_id');

				if( !empty($id) ) {
					$fieldName = 'DataMutation.change_branch_name';
				} else {
					$fieldName = 'DataMutation.change_branch_id';
					$options['empty'] = __('Pilih Cabang');
					$options['options'] = $branches;
				}
			} else {
				$options['id'] = 'branch_name';
				$options['type'] = 'text';
				$fieldName = 'Truck.branch_name';
				$errorLabel = false;
			}

			echo $this->Form->input($fieldName, $options);
			// echo $errorLabel;
			echo $this->element('blocks/trucks/forms/link_activate_field', array(
				'fieldClass' => $fieldClass,
				'fieldIcon' => $fieldIcon,
				'data_changes' => $data_changes,
			));
	?>
</div>
<div class="form-group relative">
	<?php 
			$options = array(
				'label'=> __('Jenis Truk'), 
				'class'=>'form-control',
				'required' => false,
				'readonly' => $disabled,
			);
			$fieldClass = 'change-truck-category';
			
			if( isset($data['DataMutation']['change_truck_category_id']) ) {
				$fieldIcon = 'angle-double-left';
				$disabledChangeCategory = false;
			} else {
				$fieldIcon = false;
				$disabledChangeCategory = $disabledChange;
			}

			if( !empty($data_changes) ) {
				$options['class'] .= ' '.$fieldClass;
				$options['disabled'] = $disabledChangeCategory;
				$fieldName = 'DataMutation.change_truck_category_id';
				$errorLabel = $this->Form->error('change_truck_category_id');

				if( !empty($id) ) {
					$fieldName = 'DataMutation.change_category';
				} else {
					$fieldName = 'DataMutation.change_truck_category_id';
					$options['empty'] = __('Pilih Jenis Truk');
					$options['options'] = $truckCategories;
				}
			} else {
				$options['id'] = 'truck_category';
				$options['type'] = 'text';
				$fieldName = 'Truck.category';
				$errorLabel = false;
			}

			echo $this->Form->input($fieldName, $options);
			// echo $errorLabel;
			echo $this->element('blocks/trucks/forms/link_activate_field', array(
				'fieldClass' => $fieldClass,
				'fieldIcon' => $fieldIcon,
				'data_changes' => $data_changes,
			));
	?>
</div>
<div class="form-group relative">
	<?php 
			$options = array(
				'label'=> __('Fasilitas Truk'), 
				'class'=>'form-control',
				'required' => false,
				'readonly' => $disabled,
			);
			$fieldClass = 'change-truck-facility';
			
			if( isset($data['DataMutation']['change_truck_facility_id']) ) {
				$fieldIcon = 'angle-double-left';
				$disabledChangeFacility = false;
			} else {
				$fieldIcon = false;
				$disabledChangeFacility = $disabledChange;
			}

			if( !empty($data_changes) ) {
				$options['class'] .= ' '.$fieldClass;
				$options['disabled'] = $disabledChangeFacility;
				$fieldName = 'DataMutation.change_truck_facility_id';
				$errorLabel = $this->Form->error('change_truck_facility_id');

				if( !empty($id) ) {
					$fieldName = 'DataMutation.change_facility';
				} else {
					$fieldName = 'DataMutation.change_truck_facility_id';
					$options['empty'] = __('Pilih Fasilitas Truk');
					$options['options'] = $truckFacilities;
				}
			} else {
				$options['id'] = 'truck_facility';
				$options['type'] = 'text';
				$fieldName = 'Truck.facility';
				$errorLabel = false;
			}

			echo $this->Form->input($fieldName, $options);
			// echo $errorLabel;
			echo $this->element('blocks/trucks/forms/link_activate_field', array(
				'fieldClass' => $fieldClass,
				'fieldIcon' => $fieldIcon,
				'data_changes' => $data_changes,
			));
	?>
</div>
<div class="form-group relative">
	<?php 
			if( !empty($data_changes) ) {
				$fieldClass = 'change-driver';
	            $attrBrowse = array(
	                'class' => 'ajaxModal visible-xs browse-docs',
	                'escape' => false,
					'title' => __('Supir Truk'),
					'data-action' => 'browse-form',
					'data-change' => 'driverID',
	            );
	            $urlBrowse = array(
	                'controller'=> 'ajax', 
					'action' => 'getDrivers',
					0,
					'mutation'
	            );
				echo $this->Form->label('change_driver_id', __('Supir Truk ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
			
				if( isset($data['DataMutation']['change_driver_id']) ) {
					$fieldIcon = 'angle-double-left';
					$disabledChangeDriver = false;
				} else {
					$fieldIcon = false;
					$disabledChangeDriver = $disabledChange;
				}
	?>
	<div class="row">
		<div class="col-sm-10">
			<?php 
					if( !empty($id) ) {
						echo $this->Form->input('DataMutation.change_driver_name',array(
							'label'=> false, 
							'class'=>'form-control',
							'required' => false,
							'disabled' => $disabledChangeDriver,
						));
					} else {
						echo $this->Form->input('DataMutation.change_driver_id',array(
							'label'=> false, 
							'class'=>'form-control '.$fieldClass,
							'required' => false,
							'empty' => __('Pilih Supir Truk'),
							'id' => 'driverID',
							'disabled' => $disabledChangeDriver,
							'options' => $drivers,
						));
					}
					// echo $this->Form->error('change_driver_id');
			?>
		</div>
		<?php 
	    		if( !empty($data_changes) && empty($id) ) {
					$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
	                echo $this->Html->tag('div', $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse), array(
	                	'class' => 'col-sm-2 hidden-xs',
                	));
	            }
        ?>
	</div>
	<?php 
				echo $this->element('blocks/trucks/forms/link_activate_field', array(
					'fieldClass' => $fieldClass,
					'fieldIcon' => $fieldIcon,
					'data_changes' => $data_changes,
				));
			} else {
				echo $this->Form->input('Truck.driver_name',array(
					'label'=> __('Supir'), 
					'class'=>'form-control',
					'required' => false,
					'readonly' => $disabled,
					'type' => 'text',
					'id' => 'driver_name',
				));
			}
	?>
</div>
<div class="form-group relative">
	<?php 
			$fieldClass = 'change-capacity';
			$options = array(
				'type' => 'text',
				'label'=> __('Kapasitas'), 
				'class'=>'form-control',
				'required' => false,
				'readonly' => $disabled,
			);
			
			if( isset($data['DataMutation']['change_capacity']) ) {
				$fieldIcon = 'angle-double-left';
				$disabledChangeCapacity = false;
			} else {
				$fieldIcon = false;
				$disabledChangeCapacity = $disabledChange;
			}

			if( !empty($data_changes) ) {
				$options['class'] .= ' '.$fieldClass;
				$options['disabled'] = $disabledChangeCapacity;
				$fieldName = 'DataMutation.change_capacity';
				$errorLabel = $this->Form->error('change_capacity');
			} else {
				$options['id'] = 'truck_capacity';
				$fieldName = 'Truck.capacity';
				$errorLabel = false;
			}

			echo $this->Form->input($fieldName, $options);
			// echo $errorLabel;
			echo $this->element('blocks/trucks/forms/link_activate_field', array(
				'fieldClass' => $fieldClass,
				'fieldIcon' => $fieldIcon,
				'data_changes' => $data_changes,
			));
	?>
</div>