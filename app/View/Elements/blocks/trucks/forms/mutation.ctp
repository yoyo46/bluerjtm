<?php 
		$data_changes = !empty($data_changes)?$data_changes:false;
		$branches = !empty($branches)?$branches:false;
		$truckCategories = !empty($truckCategories)?$truckCategories:false;
		$truckFacilities = !empty($truckFacilities)?$truckFacilities:false;
		$drivers = !empty($drivers)?$drivers:false;

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
				echo $this->Form->input('DataMutation.change_nopol',array(
					'label'=> __('No. Pol'), 
					'class'=>'form-control '.$fieldClass,
					'required' => false,
					'disabled' => $disabledChange,
				));

				echo $this->element('blocks/trucks/forms/link_activate_field', array(
					'fieldClass' => $fieldClass,
					'data_changes' => $data_changes,
				));
			} else {
				$attrBrowse = array(
	                'class' => 'ajaxModal visible-xs',
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
			?>
        </div>
        <?php 
				$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                echo $this->Html->tag('div', $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse), array(
                	'class' => 'col-sm-2 hidden-xs',
            	));
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

			if( !empty($data_changes) ) {
				$options['class'] .= ' '.$fieldClass;
				$options['disabled'] = $disabledChange;
				$options['empty'] = __('Pilih Cabang');
				$options['options'] = $branches;
				$fieldName = 'DataMutation.change_branch_id';
			} else {
				$options['id'] = 'branch_name';
				$options['type'] = 'text';
				$fieldName = 'Truck.old_driver_id';
			}

			echo $this->Form->input($fieldName, $options);
			echo $this->element('blocks/trucks/forms/link_activate_field', array(
				'fieldClass' => $fieldClass,
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

			if( !empty($data_changes) ) {
				$options['class'] .= ' '.$fieldClass;
				$options['disabled'] = $disabledChange;
				$options['empty'] = __('Pilih Jenis Truk');
				$options['options'] = $truckCategories;
				$fieldName = 'DataMutation.change_truck_category_id';
			} else {
				$options['id'] = 'truck_category';
				$options['type'] = 'text';
				$fieldName = 'Truck.category';
			}

			echo $this->Form->input($fieldName, $options);
			echo $this->element('blocks/trucks/forms/link_activate_field', array(
				'fieldClass' => $fieldClass,
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

			if( !empty($data_changes) ) {
				$options['class'] .= ' '.$fieldClass;
				$options['disabled'] = $disabledChange;
				$options['empty'] = __('Pilih Fasilitas Truk');
				$options['options'] = $truckFacilities;
				$fieldName = 'DataMutation.change_truck_facility_id';
			} else {
				$options['id'] = 'truck_facility';
				$options['type'] = 'text';
				$fieldName = 'Truck.facility';
			}

			echo $this->Form->input($fieldName, $options);
			echo $this->element('blocks/trucks/forms/link_activate_field', array(
				'fieldClass' => $fieldClass,
				'data_changes' => $data_changes,
			));
	?>
</div>
<div class="form-group relative">
	<?php 
			if( !empty($data_changes) ) {
				$fieldClass = 'change-driver';
	            $attrBrowse = array(
	                'class' => 'ajaxModal visible-xs',
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
				echo $this->Form->label('old_driver_id', __('Supir Truk ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
	?>
	<div class="row">
		<div class="col-sm-10">
			<?php 
					echo $this->Form->input('DataMutation.change_driver_id',array(
						'label'=> false, 
						'class'=>'form-control '.$fieldClass,
						'required' => false,
						'empty' => __('Pilih Supir Truk'),
						'id' => 'driverID',
						'disabled' => $disabledChange,
						'options' => $drivers,
					));
			?>
		</div>
		<?php 
	    		if( !empty($data_changes) ) {
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

			if( !empty($data_changes) ) {
				$options['class'] .= ' '.$fieldClass;
				$options['disabled'] = $disabledChange;
				$fieldName = 'DataMutation.change_capacity';
			} else {
				$options['id'] = 'truck_capacity';
				$fieldName = 'Truck.capacity';
			}

			echo $this->Form->input($fieldName, $options);
			echo $this->element('blocks/trucks/forms/link_activate_field', array(
				'fieldClass' => $fieldClass,
				'data_changes' => $data_changes,
			));
	?>
</div>