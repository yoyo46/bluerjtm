<?php 
		$value = !empty($value)?$value:false;
        $customer_name = $this->Common->filterEmptyField($value, 'Customer', 'name');
?>
<div class="page-header" style="margin-bottom: 20px;border: none;">
    <?php 
            echo $this->Html->tag('p', __('Perincian Pengiriman Unit'), array(
                'style' => 'font-size: 16px;margin: 0 0 5px;line-height: 20px;font-weight: 600;text-transform: uppercase;',
            ));
            echo $this->Html->tag('p', $customer_name, array(
                'style' => 'font-size: 16px;margin: 0 0 5px;line-height: 20px;font-weight: 600;text-transform: uppercase;',
            ));
    ?>
</div>