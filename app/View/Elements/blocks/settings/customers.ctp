<div class="box">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', __('Informasi TTUJ'), array(
                    'class' => 'box-title',
                ));
        ?>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Html->tag('label', __('Kode Customer'));
                            echo $this->Html->tag('div', $customer['Customer']['code']);
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Html->tag('label', __('Tipe Customer'));
                            echo $this->Html->tag('div', !empty($customer['CustomerType']['name'])?$customer['CustomerType']['name']:'-');
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Html->tag('label', __('Grup Customer'));
                            echo $this->Html->tag('div', !empty($customer['CustomerGroup']['name'])?$customer['CustomerGroup']['name']:'-');
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Html->tag('label', __('Nama Customer'));
                            echo $this->Html->tag('div', !empty($customer['Customer']['name'])?$customer['Customer']['name']:'-');
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Html->tag('label', __('Alamat'));
                            echo $this->Html->tag('div', $customer['Customer']['address']);
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Html->tag('label', __('Telepon'));
                            echo $this->Html->tag('div', $customer['Customer']['phone_number']);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>