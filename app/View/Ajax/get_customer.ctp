<?php 
        if( !empty($customer) ) {
            $customer_id = !empty($customer['CustomerNoType']['id'])?$customer['CustomerNoType']['id']:false;
            $customer_name = !empty($customer['CustomerNoType']['name'])?$customer['CustomerNoType']['name']:false;
            $ppn = !empty($customer['Revenue']['ppn'])?$customer['Revenue']['ppn']:0;
            $total = !empty($customer['Revenue']['total_without_tax'])?$customer['Revenue']['total_without_tax']:0;
            $ppn = $this->Common->calcFloat($total, $ppn);
            $coa_code = !empty($coaSetting['Coa']['code'])?$coaSetting['Coa']['code']:false;
            $coa_id = !empty($coaSetting['Coa']['id'])?$coaSetting['Coa']['id']:false;
            $coa_name = !empty($coaSetting['Coa']['name'])?$coaSetting['Coa']['name']:false;

            echo $this->Html->tag('div', $customer_id, array(
                'id' => 'customer-id',
            ));
            echo $this->Html->tag('div', $customer_name, array(
                'id' => 'customer-name',
            ));
            echo $this->Html->tag('div', 'Customer', array(
                'id' => 'receiver-type',
            ));
            echo $this->Html->tag('div', $coa_code, array(
                'id' => 'coa_code',
            ));
            echo $this->Html->tag('div', $coa_id, array(
                'id' => 'coa_id',
            ));
            echo $this->Html->tag('div', $coa_name, array(
                'id' => 'coa_name',
            ));
            echo $this->Html->tag('div', $ppn, array(
                'id' => 'ppn',
            ));
        }
?>