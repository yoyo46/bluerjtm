<?php 
		if( !empty($cnt) ) {
			$data = !empty($data)?$data:false;
			$modelName = !empty($modelName)?$modelName:false;
			$fieldName = !empty($fieldName)?$fieldName:false;

			for ($i=1; $i <= $cnt; $i++) {
				$key = $i-1;
	            $groupMotorName = !empty($data[$key]['GroupMotor']['name'])?$data[$key]['GroupMotor']['name']:false;
	            $cost = !empty($data[$key][$modelName][$fieldName])?$data[$key][$modelName][$fieldName]:false;

                echo $this->Html->tag('td', $groupMotorName);
                echo $this->Html->tag('td', $cost, array(
                    'style' => 'text-align:right;'
                ));
	        }
	    }
?>