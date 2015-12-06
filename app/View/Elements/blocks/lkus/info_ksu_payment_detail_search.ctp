<?php 
        echo $this->Form->create('Ksu', array(
            'url'=> $this->Html->url( array(
                'controller' => 'ajax',
                'action' => 'getTtujCustomerInfoKsu',
                $customer_id
            )), 
            'role' => 'form',
            'inputDefaults' => array('div' => false),
        ));
?>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date_from',array(
                        'label'=> __('Dari Tanggal'),
                        'class'=>'form-control custom-date',
                        'required' => false,
                        'placeholder' => __('Dari')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date_to',array(
                        'label'=> __('Sampai Tanggal'),
                        'class'=>'form-control custom-date',
                        'required' => false,
                        'placeholder' => __('Sampai')
                    ));
            ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <?php 
                    echo $this->Form->input('no_doc',array(
                        'label'=> __('No KSU'),
                        'class'=>'form-control',
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
                        
                        $driver_name = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');
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
                        echo $driver_name;
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
                        echo $this->Number->currency($value['KsuDetail']['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0));

                        echo $this->Form->hidden('KsuPaymentDetail.total_biaya_klaim.'.$value['KsuDetail']['id'], array(
                            'class' => 'ksu-price-payment',
                            'value' => (!empty($value['KsuDetail']['total_price'])) ? $value['KsuDetail']['total_price'] : 0
                        ));
                    ?>
                </td>
                <td class="text-right">
                    <?php
                        $price_pay = 0;
                        if(!empty($value['KsuDetail']['ksu_has_paid'])){
                            echo $this->Number->currency($value['KsuDetail']['ksu_has_paid'], Configure::read('__Site.config_currency_code'), array('places' => 0)); 
                            $price_pay = $value['KsuDetail']['total_price'] - $value['KsuDetail']['ksu_has_paid'];
                        }else{
                            echo '-';
                            $price_pay = $value['KsuDetail']['total_price'];
                        }
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
            ?>
        </tbody>
    </table>
    <?php 
                echo $this->element('pagination', array(
                    'options' => array(
                        'data-action' => $data_action,
                        'class' => 'ajaxModal',
                    ),
                ));
            }else{
                echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan')) );
            }
    ?>
</div>