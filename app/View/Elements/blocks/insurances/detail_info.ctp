<?php 
        $view = !empty($view)?$view:false;

        $count = 1;
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'InsuranceDetail');
        
        $errorDetail = $this->Form->error('Insurance.item');
?>
<div class="box temp-document-picker document-calc">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail Asuransi'); ?></h3>
    </div>
    <div class="box-body">
        <?php
                if( empty($view) ) {
        ?>
        <div class="form-group">
            <?php 
                    echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Pilih Truk'), $this->Html->url( array(
                            'controller'=> 'insurances', 
                            'action' => 'trucks',
                            'bypass' => true,
                        )), array(
                            'escape' => false,
                            'allow' => true,
                            'title' => __('Pilih Truk'),
                            'class' => 'btn bg-maroon ajaxCustomModal',
                            'data-form' => '#insurance-form',
                        )), array(
                        'class' => 'form-group mb30',
                    ));

                    if( !empty($errorDetail) )  {
                        echo $this->Html->tag('div', $errorDetail, array(
                            'class' => 'mb15',
                        ));
                    }
            ?>
        </div>
        <?php
                }
        ?>
        <table class="table table-hover" id="wrapper-write">
            <thead>
                <tr>
                    <th width="15%" class="text-center">
                        <?php
                                echo $this->Html->tag('p', __('Merk Kendaraan'));
                                echo $this->Html->tag('p', __('Jenis Kendaraan'));
                                echo $this->Html->tag('p', __('Tahun Pembuatan / Warna'));
                        ?>
                    </th>
                    <th width="15%" class="text-center">
                        <?php
                                echo $this->Html->tag('p', __('Nomor Polisi'));
                                echo $this->Html->tag('p', __('Nomor Rangka'));
                                echo $this->Html->tag('p', __('Nomor Mesin'));
                        ?>
                    </th>
                    <th width="15%" class="text-center"><?php echo __('Kondisi<br>Pertanggunan');?></th>
                    <th width="10%" class="text-center"><?php echo __('Harga<br>Pertanggunan');?></th>
                    <th width="5%" class="text-center"><?php echo __('Rate<br>(%)');?></th>
                    <th width="10%" class="text-center"><?php echo __('Premi');?></th>
                    <th width="10%" class="text-center"><?php echo __('Keterangan');?></th>
                    <?php
                            if( empty($view) ) {
                    ?>
                    <th width="5%" class="text-center"><?php echo __('Action');?></th>
                    <?php
                            }
                    ?>
                </tr>
            </thead>
            <tbody class="insurance-body">
                <?php
                        $total = 0;

                        if(!empty($dataDetail)){
                            $tmp_truck_id = false;

                            foreach ($dataDetail as $key => $value) {
                                $truck_id = Common::hashEmptyField($value, 'InsuranceDetail.truck_id');
                                $premi = Common::hashEmptyField($value, 'InsuranceDetail.premi', 0);

                                $total += $premi;

                                echo $this->element('blocks/insurances/tables/items', array(
                                    'value' => $value,
                                    'idx' => $key,
                                    'tmp_truck_id' => $tmp_truck_id,
                                ));
                                
                                if( $tmp_truck_id != $truck_id ) {
                                    $tmp_truck_id = $truck_id;
                                }
                            }
                        }
                ?>
            </tbody>
            <tfoot>
                <tr id="field-total" class="grandtotal">
                    <td align="right" colspan="5"><?php echo __('Total Premi')?></td>
                    <td id="total" align="right" class="total item-calc" data-type="plus">
                        <?php 
                                echo Common::getFormatPrice($total, 2);
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="right" colspan="5"><?php echo $this->Html->tag('strong', __('Diskon'));?></td>
                    <td align="right">
                        <?php 
                                echo $this->Form->input('disc',array(
                                    'type' => 'text',
                                    'label'=> false, 
                                    'class'=>'form-control input_price text-right item-calc',
                                    'required' => false,
                                    'data-type' => 'min',
                                ));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="right" colspan="5"><?php echo $this->Html->tag('strong', __('Biaya Admin'));?></td>
                    <td align="right">
                        <?php 
                                echo $this->Form->input('admin_fee',array(
                                    'type' => 'text',
                                    'label'=> false, 
                                    'class'=>'form-control input_price text-right item-calc',
                                    'required' => false,
                                    'error' => false,
                                    'data-type' => 'plus',
                                ));
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="right" colspan="5"><?php echo $this->Html->tag('strong', __('Grandtotal'))?></td>
                    <td align="right" id="grand-total" class="grandtotal-calc">
                        <?php 
                                echo 0;
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>