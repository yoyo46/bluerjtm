<?php 
        $title = !empty($title)?$title:false;
        echo $this->Form->create('Search', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'search',
                'getTtujCustomerInfoKsu',
                'customer_id' => $customer_id,
                'admin' => false,
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date',array(
                        'label'=> __('Tanggal'),
                        'class'=>'form-control date-range',
                        'required' => false,
                        'placeholder' => __('Tanggal'),
                        'autocomplete'=> 'off', 
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('nodoc',array(
                        'label'=> __('No KSU'),
                        'class'=>'form-control on-focus',
                        'required' => false,
                    ));
            ?>
        </div>
    </div>
</div>
<div class="form-group action">
    <?php
            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                'div' => false, 
                'class'=> 'btn btn-success btn-sm ajaxModal',
                'data-parent' => true,
                'title' => 'ksu Customer',
                'data-action' => $data_action,
                'title' => $title
            ));
            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                'controller' => 'ajax',
                'action' => 'getTtujCustomerInfo',
                $customer_id,
            ), array(
                'escape' => false, 
                'class'=> 'btn btn-default btn-sm ajaxModal',
                'data-action' => $data_action,
                'title' => $title,
            ));
    ?>
</div>
<?php 
        echo $this->Form->end();
?>
<div class="box-body table-responsive">
    <?php 
            if(!empty($ksu_details)){
    ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <?php
                        $input_all = $this->Form->checkbox('checkbox_all', array(
                            'class' => 'checkAll'
                        ));
                        echo $this->Html->tag('th', $input_all);
                ?>
                <th><?php echo __('No KSU');?></th>
                <th><?php echo __('Tgl KSU');?></th>
                <th><?php echo __('TTUJ');?></th>
                <th><?php echo __('Supir');?></th>
                <th><?php echo __('Nopol');?></th>
                <th><?php echo __('Perlengkapan');?></th>
                <th><?php echo __('Total');?></th>
                <th><?php echo __('Telah Dibayar');?></th>
            </tr>
        </thead>
        <tbody class="ttuj-info-table">
            <?php
                    $total = 0;

                    foreach ($ksu_details as $key => $value) {
                        $ksu = $value['Ksu'];

                        $price_pay = 0;

                        $driver = $this->Common->_callGetDriver($value);

                        $total_price = $this->Common->filterEmptyField($value, 'KsuDetail', 'total_price', 0);
                        $ksu_has_paid = $this->Common->filterEmptyField($value, 'KsuDetail', 'ksu_has_paid');
                        
                        if(!empty($ksu_has_paid)){
                            $price_pay = $total_price - $ksu_has_paid;
                            $price_pay_custom = $this->Number->currency($ksu_has_paid, Configure::read('__Site.config_currency_code'), array('places' => 0)); 
                        }else{
                            $price_pay = $total_price;
                            $price_pay_custom = '-';
                        }

                        if( $price_pay > 0 ) {
            ?>
            <tr class="child-search child-search-<?php echo $value['KsuDetail']['id'];?>" rel="<?php echo $value['KsuDetail']['id'];?>">
                <td class="checkbox-detail">
                    <?php
                        echo $this->Form->checkbox('ksu_detail_id.', array(
                            'class' => 'check-option',
                            'value' => $value['KsuDetail']['id']
                        ));
                    ?>
                </td>
                <td>
                    <?php
                        echo $value['Ksu']['no_doc'];

                        echo $this->Form->input('KsuPaymentDetail.ksu_detail_id.'.$value['KsuDetail']['id'], array(
                            'type' => 'hidden',
                            'value' => $value['KsuDetail']['id']
                        ));
                    ?>
                </td>
                <td>
                    <?php
                        echo date('d F Y', strtotime($value['Ksu']['tgl_ksu']));
                    ?>
                </td>
                <td>
                    <?php
                        echo $value['Ttuj']['no_ttuj'];
                    ?>
                </td>
                <td>
                    <?php
                        echo $driver;
                    ?>
                </td>
                <td>
                    <?php
                        if(!empty($value['Ttuj']['nopol'])){
                            echo $value['Ttuj']['nopol'];
                        }else{
                            echo '-';
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if(!empty($value['KsuDetail']['Perlengkapan']['name'])){
                            echo $value['KsuDetail']['Perlengkapan']['name'];
                        }else{
                            echo '-';
                        }
                    ?>
                </td>
                <td class="text-right">
                    <?php
                        echo $this->Number->currency($total_price, Configure::read('__Site.config_currency_code'), array('places' => 0));

                        echo $this->Form->hidden('KsuPaymentDetail.total_biaya_klaim.'.$value['KsuDetail']['id'], array(
                            'class' => 'ksu-price-payment',
                            'value' => $total_price
                        ));
                    ?>
                </td>
                <td class="text-right">
                    <?php
                            echo $price_pay_custom;
                    ?>
                </td>
                <td class="text-right action-search hide" valign="top">
                    <?php
                        echo $this->Form->input('KsuPaymentDetail.total_biaya_klaim.'.$value['KsuDetail']['id'], array(
                            'type' => 'text',
                            'label' => false,
                            'div' => false,
                            'required' => false,
                            'class' => 'form-control input_price document-pick-price',
                            'value' => (!empty($this->request->data['KsuPaymentDetail']['total_biaya_klaim'][$value['KsuDetail']['id']])) ? $this->request->data['KsuPaymentDetail']['total_biaya_klaim'][$value['KsuDetail']['id']] : $price_pay
                        ));

                        if(!empty($this->request->data['KsuPaymentDetail']['total_biaya_klaim'][$key])){
                            $total += str_replace(',', '', $this->request->data['KsuPaymentDetail']['total_biaya_klaim'][$key]);
                        }
                    ?>
                </td>
                <td class="action-search hide">
                    <?php
                        echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                            'class' => 'delete-custom-field btn btn-danger btn-xs',
                            'escape' => false,
                            'action_type' => 'ksu_first'
                        ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }
            ?>
        </tbody>
    </table>
    <?php 
                echo $this->element('pagination', array(
                    'options' => array(
                        'data-action' => $data_action,
                        'class' => 'ajaxModal',
                        'title' => $title,
                    ),
                ));
            }else{
                echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan')) );
            }
    ?>
</div>