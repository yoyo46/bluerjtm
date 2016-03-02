<div class="sub-title" style="margin-bottom: 20px;">
    <?php 
            $no_invoice = $this->Common->filterEmptyField($value, 'Invoice', 'no_invoice');
            $customer = $this->Common->filterEmptyField($value, 'Customer', 'name');
            $customer_address = $this->Common->filterEmptyField($value, 'Customer', 'address');
            
            echo $this->element('blocks/common/tables/sub_header', array(
                'labelName' => __('No Faktur'),
                'value' => $no_invoice,
            ));
            echo $this->element('blocks/common/tables/sub_header', array(
                'labelName' => __('Nama Pelanggan'),
                'value' => __('PT. YAMAHA INDONESIA MOTOR MFG.'),
            ));
            echo $this->element('blocks/common/tables/sub_header', array(
                'labelName' => __('Alamat Pelanggan'),
                'value' => __('Jl. DR.KRT Radjiman Widyodiningrat - Jakarta'),
            ));
            echo $this->element('blocks/common/tables/sub_header', array(
                'labelName' => __('Keterangan'),
                'value' => __('Jasa Angkut Sepeda Motor Yamaha'),
            ));
    ?>
</div>