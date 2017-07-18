<div class="wrapper-wheel-position">
	<div class="wheel-1">
		<img src="/img/wheel/wheel1.jpg">
		<div class="truck-item colt-diesel-engkel" rel="colt-diesel-engkel">
			<div class="relative">
				<div class="tire front-left"></div>
				<div class="tire front-right"></div>
				<div class="tire spare-tire"></div>
				<div class="tire back-left"></div>
				<div class="tire back-right"></div>
			</div>
		</div>
		<div class="truck-item colt-diesel-double" rel="colt-diesel-double">
			<div class="relative">
				<div class="tire front-left"></div>
				<div class="tire front-right"></div>
				<div class="tire spare-tire"></div>
				<div class="tire back-left-outside"></div>
				<div class="tire back-left-inside"></div>
				<div class="tire back-right-outside"></div>
				<div class="tire back-right-inside"></div>
			</div>
		</div>
		<div class="truck-item fuzo-hino" rel="fuzo-hino">
			<div class="relative">
				<div class="tire front-left"></div>
				<div class="tire front-right"></div>
				<div class="tire spare-tire"></div>
				<div class="tire back-left-outside"></div>
				<div class="tire back-left-inside"></div>
				<div class="tire back-right-outside"></div>
				<div class="tire back-right-inside"></div>
			</div>
		</div>
	</div>
	<div class="wheel-2">
		<img src="/img/wheel/wheel2.jpg">
		<div class="truck-item trintin" rel="trintin">
			<div class="relative">
				<div class="tire front-left"></div>
				<div class="tire front-right"></div>
				<div class="tire mid-left"></div>
				<div class="tire mid-right"></div>
				<div class="tire spare-tire"></div>
				<div class="tire back-left-outside"></div>
				<div class="tire back-left-inside"></div>
				<div class="tire back-right-outside"></div>
				<div class="tire back-right-inside"></div>
			</div>
		</div>
		<div class="truck-item trailer" rel="trailer">
			<div class="relative">
				<div class="tire front-left"></div>
				<div class="tire front-right"></div>
				<div class="tire mid-left-outside"></div>
				<div class="tire mid-left-inside"></div>
				<div class="tire mid-right-outside"></div>
				<div class="tire mid-right-inside"></div>
				<div class="tire spare-tire-first"></div>
				<div class="tire spare-tire-second"></div>
				<div class="tire first-back-left-outside"></div>
				<div class="tire first-back-left-inside"></div>
				<div class="tire first-back-right-outside"></div>
				<div class="tire first-back-right-inside"></div>
				<div class="tire second-back-left-outside"></div>
				<div class="tire second-back-left-inside"></div>
				<div class="tire second-back-right-outside"></div>
				<div class="tire second-back-right-inside"></div>
			</div>
		</div>
		<div class="truck-item gandengan" rel="gandengan">
			<div class="relative">
				<div class="tire first-front-left"></div>
				<div class="tire first-front-right"></div>
				<div class="tire first-spare-tire"></div>
				<div class="tire first-back-left-outside"></div>
				<div class="tire first-back-left-inside"></div>
				<div class="tire first-back-right-outside"></div>
				<div class="tire first-back-right-inside"></div>
				<div class="tire second-front-left-outside"></div>
				<div class="tire second-front-left-inside"></div>
				<div class="tire second-front-right-outside"></div>
				<div class="tire second-front-right-inside"></div>
				<div class="tire second-spare-tire"></div>
				<div class="tire second-back-left-outside"></div>
				<div class="tire second-back-left-inside"></div>
				<div class="tire second-back-right-outside"></div>
				<div class="tire second-back-right-inside"></div>
			</div>
		</div>
	</div>
	<div class="box-footer text-center action">
		<?php
				echo $this->Form->hidden('SpkProduct.max_qty', array(
					'value' => $qty,
					'class'=> 'wheel-position-max-qty',
				));
				echo $this->Form->hidden('SpkProduct.product_id', array(
					'value' => $id,
					'class'=> 'wheel-position-product-id',
				));
				echo $this->Form->hidden('SpkProduct.qty', array(
					'value' => $qty,
					'class'=> 'wheel-position-qty',
				));
				echo $this->Form->button(__('Submit'), array(
					'type' => 'submit',
					'class'=> 'btn btn-success submit-form btn-lg wheel-position-submit',
				));
		?>
	</div>
</div>