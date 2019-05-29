<?php 
        $id = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'id');
        $no_ttuj = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'no_ttuj');
        $ttuj_date = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'ttuj_date');
        $nopol = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'nopol');
        $capacity = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'truck_capacity');
        $from_city_name = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'from_city_name');
        $to_city_name = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'to_city_name');
        $note = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'note');
        $tgl_bon_biru = Common::hashEmptyField($ttuj, 'Ttuj.tgl_bon_biru');
        $tgl_bon_biru = Common::formatDate($tgl_bon_biru, 'd/m/Y', '-');

        $potongan_tabungan = Common::hashEmptyField($ttuj, 'UangJalan.potongan_tabungan', 0);
        $laka_total = Common::hashEmptyField($ttuj, 'Laka.total');
        $debt_total = Common::hashEmptyField($ttuj, 'Debt.total');

        $data_type = !empty($data_type)?$data_type:false;
        $data_type = $this->Common->filterEmptyField($ttuj, 'Ttuj', 'data_type', $data_type);

        $customer_name_code = $this->Common->filterEmptyField($ttuj, 'Customer', 'code');
        $driver = $this->Common->_callGetDriver($ttuj);

        $checkbox = isset($checkbox)?$checkbox:true;
		$alias = sprintf('child-%s-%s', $id, $data_type);
        $addClass = !empty($isAjax)?'hide':'';

        $ttujPayment = $this->Common->filterEmptyField($this->request->data, 'TtujPayment');
        $sisaAmount = $this->Common->getBiayaTtuj( $ttuj, $data_type );
        $amountPayment = !empty($ttujPayment['amount_payment'][$idx])?$ttujPayment['amount_payment'][$idx]:$sisaAmount;
        $total_biaya = $this->Common->getBiayaTtuj( $ttuj, $data_type, false, false );
        $titipan = 0;
        $potongan_laka = 0;
        $potongan_debt = 0;

        if( !empty($amountPayment) ) {
            if( !empty($checkbox) ) {
                printf('<tr data-value="%s" data-type="%s" class="child %s">', $alias, $data_type, $alias);

    			$checkboxContent = $this->Form->checkbox('ttuj_checked.', array(
                    'class' => 'check-option',
                    'value' => $id,
                ));
                // $checkboxContent .= $this->Form->input('TtujPayment.ttuj_id.', array(
                //     'type' => 'hidden',
                //     'value' => $id,
                // ));

                echo $this->Html->tag('td', $checkboxContent, array(
                    'class' => 'checkbox-action',
                ));
            } else {
                printf('<tr class="child child-%s">', $alias);
            }

            if( $data_type == 'commission' ) {
                if( !empty($potongan_tabungan) ) {
                    $titipan = $total_biaya * ($potongan_tabungan/100);
                }
                
                if( !empty($laka_percent) && !empty($laka_total) ) {
                    $potongan_laka = $total_biaya * ($laka_percent/100);

                    if( $potongan_laka > $laka_total ) {
                        $potongan_laka = $laka_total;
                    }
                }
                if( !empty($debt_percent) && !empty($debt_total) ) {
                    $potongan_debt = $total_biaya * ($debt_percent/100);

                    if( $potongan_debt > $debt_total ) {
                        $potongan_debt = $debt_total;
                    }
                }
            }
?>
    
    <td class="hide on-show">
        <div style="width: 350px;">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                            if( !empty($capacity) ) {
                                $ttuj_capacity = $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Kap')), $capacity));
                            } else {
                                $ttuj_capacity = '';
                            }

                            echo $this->Html->tag('p', __('%s: %s %s', $this->Html->tag('strong', __('No')), $no_ttuj, $this->Html->link($this->Common->icon('question-circle'), 'javascript:void(0);', array(
                                'escape' => false,
                                'class' => 'popover-hover-top-click',
                                'data-content' => $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Tgl')), date('d M Y', strtotime($ttuj_date)))).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Tgl Masuk Bon Biru')), $tgl_bon_biru)).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Customer')), $customer_name_code)).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Dari')), $from_city_name)).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Tujuan')), $to_city_name)).
                                $ttuj_capacity.
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Jenis')), $this->Common->_callLabelBiayaTtuj($data_type))).
                                $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Total')), $this->Common->getBiayaTtuj( $ttuj, $data_type, true, false ))),
                                'data-original-title' => $this->Html->tag('strong', __('Info TTUJ')).' <span class=\'pull-right\'><a href=\'javascript:\'><i class=\'popover-close\'>Tutup</i></a></span>',
                            ))));
                            // echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Tgl')), date('d M Y', strtotime($ttuj_date))));
                            
                            // echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Customer')), $customer_name_code));
                            // echo $this->Html->tag('p', __('%s: %s - %s', $this->Html->tag('strong', __('Tujuan')), $from_city_name, $to_city_name));
                            echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Ket.')), $note));
                    ?>
                </div>
                <div class="col-sm-6">
                    
                    <?php
                            echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Supir')), $driver));
                            echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('NoPol')), $nopol));

                            // if( !empty($capacity) ) {
                            //     echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Kap')), $capacity));
                            // }

                            // echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Jenis')), $this->Common->_callLabelBiayaTtuj($data_type)));
                            // echo $this->Html->tag('p', __('%s: %s', $this->Html->tag('strong', __('Total')), $this->Common->getBiayaTtuj( $ttuj, $data_type, true, false )));
                    ?>
                </div>
            </div>
        </div>
    </td>
    <td class="on-remove"><?php echo $no_ttuj;?></td>
    <td class="on-remove"><?php echo date('d M Y', strtotime($ttuj_date));?></td>
    <td class="on-remove"><?php echo $nopol;?></td>
    <?php 
            if( !empty($capacity) ) {
                echo $this->Html->tag('td', $capacity, array(
                    'class' => 'text-center on-remove',
                ));
            }
    ?>
    <td class="on-remove"><?php echo $customer_name_code;?></td>
    <td class="on-remove"><?php echo $from_city_name;?></td>
    <td class="on-remove"><?php echo $to_city_name;?></td>
    <td class="on-remove">
    	<?php
    			echo $driver;
		?>
	</td>
    <td class="text-center on-remove">
    	<?php
                echo $this->Common->_callLabelBiayaTtuj($data_type);
		?>
	</td>
    <td class="on-remove">
        <?php
                echo $note;
        ?>
    </td>
    <td class="total-value text-right on-remove">
    	<?php
    			echo Common::getFormatPrice($total_biaya);
		?>
	</td>
    <td class="text-right">
    	<?php
                if( !empty($document_info) ) {
                    $amountPayment = $this->Common->getFormatPrice($amountPayment);
                    echo $amountPayment;
                } else {
        			echo $this->Form->input('TtujPayment.amount_payment.',array(
                        'label'=> false,
                        'class'=>'form-control input_price_min sisa-amount text-right',
                        'required' => false,
                        'value' => $amountPayment,
                    ));
                    echo $this->Form->hidden('TtujPayment.ttuj_id.',array(
                        'value'=> $id,
                    ));
                    echo $this->Form->hidden('TtujPayment.data_type.',array(
                        'value'=> $data_type,
                    ));
                }
		?>
	</td>
    <td class="text-right hide on-show">
        <?php
                echo $this->Form->input('TtujPayment.no_claim.',array(
                    'label'=> false,
                    'class'=>'form-control input_price_min no-claim text-right',
                    'required' => false,
                ));
        ?>
    </td>
    <td class="text-right hide on-show">
        <?php
                echo $this->Form->input('TtujPayment.stood.',array(
                    'label'=> false,
                    'class'=>'form-control input_price_min stood text-right',
                    'required' => false,
                ));
        ?>
    </td>
    <td class="text-right hide on-show">
        <?php
                echo $this->Form->input('TtujPayment.lainnya.',array(
                    'label'=> false,
                    'class'=>'form-control input_price_min lainnya text-right',
                    'required' => false,
                ));
        ?>
    </td>
    <td class="text-right hide on-show">
        <?php
                echo $this->Form->input('TtujPayment.titipan.',array(
                    'label'=> false,
                    'class'=>'form-control input_price_min titipan text-right',
                    'required' => false,
                    'value' => $titipan,
                ));
        ?>
    </td>
    <td class="text-right hide on-show">
        <?php
                echo $this->Form->input('TtujPayment.claim.',array(
                    'label'=> false,
                    'class'=>'form-control input_price_min claim text-right',
                    'required' => false,
                ));
        ?>
    </td>
    <!-- <td class="text-right hide on-show">
        <?php
                // echo $this->Form->input('TtujPayment.unit_claim.',array(
                //     'label'=> false,
                //     'class'=>'form-control input_price_min unit_claim text-right',
                //     'required' => false,
                // ));
        ?>
    </td> -->
    <td class="text-right hide on-show">
        <?php
                // echo $this->Form->input('TtujPayment.laka.',array(
                //     'label'=> false,
                //     'class'=>'form-control input_price_min laka text-right',
                //     'required' => false,
                //     'value' => $potongan_laka,
                // ));
                echo $this->Form->input('TtujPayment.debt.',array(
                    'label'=> false,
                    'class'=>'form-control input_price_min debt text-right',
                    'required' => false,
                    'value' => $potongan_debt,
                ));
        ?>
    </td>
    <td class="text-right hide on-show">
        <?php
                // echo $this->Form->input('TtujPayment.laka_note.',array(
                //     'label'=> false,
                //     'class'=>'form-control',
                //     'required' => false,
                // ));
                echo $this->Form->input('TtujPayment.debt_note.',array(
                    'label'=> false,
                    'class'=>'form-control',
                    'required' => false,
                ));
        ?>
    </td>
    <td class="text-right total-trans hide on-show">
        <?php
                echo Common::getFormatPrice($total_biaya-$titipan-$potongan_laka-$potongan_debt);
        ?>
    </td>
    <?php 
            if( empty($document_info) ) {
                echo $this->Html->tag('td', $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
                    'class' => 'delete-document-current btn btn-danger btn-xs',
                    'escape' => false,
                    'data-id' => sprintf('child-%s', $alias),
                )), array(
                    'class' => 'document-table-action '.$addClass,
                ));
            }
    ?>
</tr>
<?php 
        }
?>