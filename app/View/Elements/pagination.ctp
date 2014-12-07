<?php
		if( !empty($url) ) {
			$options['url'] = $url;
		}
		
		if( !empty($model) ) {
			if (!isset($this->Paginator->params['paging'])) {
				return;
			}
			if (!isset($model) || $this->Paginator->params['paging'][$model]['pageCount'] < 2) {
				return;
			}
			if (!isset($options)) {
				$options = array();
			}

			$options['model'] = $model;
			$options['url']['model'] = $model;
			$this->Paginator->defaultModel = $model;
		}

		$options['show_count'] = isset($show_count)?$show_count:true;
		$optionFirst = array_merge($options, array('class' =>'first'));
		$optionPrev = array_merge($options, array('class' =>'prev'));
		$optionNumber = array_merge($options, array(
			'separator'=>'',
			'tag'=>'li',
			'modulus' => 4,
			'class' => 'page',
			'currentClass' => 'active',
			'currentTag' => 'a'
		));
		$optionNext = $options;
		$optionLast = array_merge($options, array('class' =>'last'));
?>
<div class="box-footer clearfix">
	<ul class="pagination pagination-sm no-margin pull-right">
		<?php
				if($this->Paginator->hasPrev()):
					printf('<li>%s</li>', $this->Paginator->first('« First',$optionFirst));
					printf('<li>%s</li>', $this->Paginator->prev(__('« Prev'),$optionPrev));
				endif;

				echo $this->Paginator->numbers($optionNumber);

				if($this->Paginator->hasNext()):
					printf('<li>%s</li>', $this->Paginator->next('Next »', $optionNext));
					printf('<li>%s</li>', $this->Paginator->last(__('Last »'), $optionLast));
				endif;
		?>
	</ul>
</div>