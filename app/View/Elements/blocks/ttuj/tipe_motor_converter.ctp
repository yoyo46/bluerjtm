<?php 
		$content = false;
		$idName = !empty($idName)?$idName:'list-converter-uang-jalan-extra';
		$class = !empty($class)?$class:false;

		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$id = $this->Common->filterEmptyField($value, 'GroupMotor', 'id');
				$converter = $this->Common->filterEmptyField($value, 'GroupMotor', 'converter');

				$content .= $this->Html->tag('div', $converter, array(
					'id' => sprintf('converter-uang-jalan-extra-%s', $id),
				));
			}
		}

		echo $this->Html->tag('div', $content, array(
			'id' => $idName,
			'class' => $class,
		));
?>