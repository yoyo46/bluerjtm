<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail Info LKU'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <div class="form-group">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
                        'class' => 'add-custom-field btn btn-success btn-xs',
                        'action_type' => 'lku_ttuj',
                        'escape' => false
                    ));
            ?>
        </div>
        <table class="table table-hover">
        	<thead>
        		<tr>
        			<th width="20%"><?php echo __('Tgl TTUJ');?></th>
                    <th><?php echo __('Nopol Truk');?></th>
                    <th><?php echo __('Dari');?></th>
                    <th><?php echo __('Tujuan');?></th>
                    <th><?php echo __('Total Klaim');?></th>
                    <th><?php echo __('Total Biaya Klaim');?></th>
                    <th><?php echo __('Action');?></th>
        		</tr>
        	</thead>
        	<tbody class="ttuj-info-table">
                <?php
                    $count = 1;
                    if(!empty($this->request->data['LkuPaymentDetail'])){
                        $count = count($this->request->data['LkuPaymentDetail']);
                    }
                    $total = 0;
                    for ($i=0; $i < $count; $i++) { 
                ?>
        		<tr>
                    <td>
                        <?php
                            echo $this->Form->input('LkuPaymentDetail.lku_id.', array(
                                'options' => $lkus,
                                'class' => 'form-control lku-choose-ttuj',
                                'label' => false,
                                'empty' => __('Pilih Tanggal TTUJ'),
                                'required' => false,
                                'value' => (isset($this->request->data['LkuPaymentDetail'][$i]['lku_id']) && !empty($this->request->data['LkuPaymentDetail'][$i]['lku_id'])) ? $this->request->data['LkuPaymentDetail'][$i]['lku_id'] : ''
                            ));
                        ?>
                    </td>
        			<td class="data-nopol">
                        <?php
                            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Ttuj']['nopol'])){
                                echo $this->request->data['LkuPaymentDetail'][$i]['Ttuj']['nopol'];
                            }else{
                                echo '-';
                            }
                        ?>
                    </td>
                    <td class="data-from-city">
                        <?php
                            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Ttuj']['from_city_name'])){
                                echo $this->request->data['LkuPaymentDetail'][$i]['Ttuj']['from_city_name'];
                            }else{
                                echo '-';
                            }
                        ?>
                    </td>
                    <td class="data-to-city">
                        <?php
                            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Ttuj']['to_city_name'])){
                                echo $this->request->data['LkuPaymentDetail'][$i]['Ttuj']['to_city_name'];
                            }else{
                                echo '-';
                            }
                        ?>
                    </td>
                    <td class="data-total-claim">
                        <?php
                            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Lku']['total_klaim'])){

                                echo $this->Form->hidden('LkuPaymentDetail.total_klaim.', array(
                                    'empty' => __('Pilih Jumlah Klaim'),
                                    'class' => 'lku-claim-number form-control',
                                    'div' => false,
                                    'label' => false,
                                    'value' => $this->request->data['LkuPaymentDetail'][$i]['Lku']['total_klaim']
                                ));

                                echo $this->request->data['LkuPaymentDetail'][$i]['Lku']['total_klaim'];
                            }else{
                                echo '-';
                            }
                        ?>
                    </td>
                    <td class="data-total-price-claim" align="right">
                        <?php
                            if(!empty($this->request->data['LkuPaymentDetail'][$i]['Lku']['total_price'])){
                                $max_qty = $this->request->data['LkuPaymentDetail'][$i]['Lku']['total_price'];
                                echo  $this->Form->hidden('LkuPaymentDetail.total_biaya_klaim.', array(
                                    'type' => 'text',
                                    'label' => false,
                                    'class' => 'form-control price-lku input_number',
                                    'required' => false,
                                    'max_price' => $max_qty,
                                    'placeholder' => sprintf(__('maksimal pembayaran : %s'), $max_qty),
                                    'value' => $max_qty
                                ));
                                $total += $max_qty;

                                echo $this->Number->currency($max_qty, Configure::read('__Site.config_currency_code'), array('places' => 0));
                            }else{
                                echo '-';
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                                'class' => 'delete-custom-field btn btn-danger btn-xs',
                                'escape' => false,
                                'action_type' => 'lku_second'
                            ));
                        ?>
                    </td>
        		</tr>
                <?php
                    }
                ?>
                <tr id="field-grand-total-ttuj">
                    <td align="right" colspan="5"><?php echo __('Grand Total')?></td>
                    <td align="right" id="grand-total-payment"><?php printf('%s %s', Configure::read('__Site.config_currency_code'), $total); ?></td>
                    <td>&nbsp;</td>
                </tr>
        	</tbody>
    	</table>
    </div>
</div>

<div class="hide">
    <table>
        <tbody id="first-row">
            <tr>
                <td>
                    <?php
                        echo $this->Form->input('LkuPaymentDetail.lku_id.', array(
                            'options' => $lkus,
                            'class' => 'form-control lku-choose-ttuj',
                            'label' => false,
                            'empty' => __('Pilih Tanggal TTUJ'),
                            'required' => false,
                        ));
                    ?>
                </td>
                <td class="data-nopol">-</td>
                <td class="data-from-city">-</td>
                <td class="data-to-city">-</td>
                <td class="data-total-claim">-</td>
                <td class="data-total-price-claim" align="right">-</td>
                <td>
                    <?php
                        echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                            'class' => 'delete-custom-field btn btn-danger btn-xs',
                            'escape' => false,
                            'action_type' => 'lku_second'
                        ));
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>