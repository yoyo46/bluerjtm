<?php 
        $show = !empty($this->request->data['Ksu']['kekurangan_atpm']) ? 'hide' : '';
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail KSU'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <div class="form-group">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
                        'class' => 'add-custom-field btn btn-success btn-xs',
                        'action_type' => 'ksu_perlengkapan',
                        'escape' => false
                    ));
            ?>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?php echo __('Perlengkapan');?></th>
                    <th><?php echo __('No. Rangka');?></th>
                    <th><?php echo __('Keterangan');?></th>
                    <th><?php echo __('Jumlah');?></th>
                    <th class="hide-atpm <?php echo $show; ?>"><?php printf(__('Biaya Klaim (%s)'), Configure::read('__Site.config_currency_code'));?></th>
                    <th class="hide-atpm <?php echo $show; ?>"><?php  printf(__('Total (%s)'), Configure::read('__Site.config_currency_code')) ;?></th>
                    <th><?php echo __('Action');?></th>
                </tr>
            </thead>
            <tbody class="perlengkapan-table">
                <?php
                    $count = 1;
                    if(!empty($this->request->data['KsuDetail'])){
                        $count = count($this->request->data['KsuDetail']);
                    }
                    $total = 0;
                    for ($i=0; $i < $count; $i++) { 
                        $price = (isset($this->request->data['KsuDetail'][$i]['price']) && !empty($this->request->data['KsuDetail'][$i]['price'])) ? $this->request->data['KsuDetail'][$i]['price'] : 0;
                        if(!empty($this->request->data['Ksu']['kekurangan_atpm'])){
                            $price = 0;
                        }
                        $qty = (isset($this->request->data['KsuDetail'][$i]['qty']) && !empty($this->request->data['KsuDetail'][$i]['qty'])) ? $this->request->data['KsuDetail'][$i]['qty'] : 0;
                ?>
                <tr class="ksu-detail ksu-detail-<?php echo $i+1;?>" rel="<?php echo $i+1;?>">
                    <td>
                        <?php
                            echo $this->Form->input('KsuDetail.perlengkapan_id.', array(
                                'options' => !empty($perlengkapans)?$perlengkapans:false,
                                'label' => false,
                                'empty' => __('Pilih Perlengkapan'),
                                'class' => 'ksu-choose-tipe-motor form-control',
                                'required' => false,
                                'value' => (isset($this->request->data['KsuDetail'][$i]['perlengkapan_id']) && !empty($this->request->data['KsuDetail'][$i]['perlengkapan_id'])) ? $this->request->data['KsuDetail'][$i]['perlengkapan_id'] : ''
                            ));
                        ?>
                    </td>
                    <td>
                        <?php 
                            echo $this->Form->input('KsuDetail.no_rangka.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control',
                                'required' => false,
                                'value' => (isset($this->request->data['KsuDetail'][$i]['no_rangka']) && !empty($this->request->data['KsuDetail'][$i]['no_rangka'])) ? $this->request->data['KsuDetail'][$i]['no_rangka'] : ''
                            ));
                        ?>
                    </td>
                    <td>
                        <?php 
                            echo $this->Form->input('KsuDetail.note.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control',
                                'required' => false,
                                'value' => (isset($this->request->data['KsuDetail'][$i]['note']) && !empty($this->request->data['KsuDetail'][$i]['note'])) ? $this->request->data['KsuDetail'][$i]['note'] : ''
                            ));
                        ?>
                    </td>
                    <td class="qty-perlengkapan" align="center">
                        <?php
                            echo $this->Form->input('KsuDetail.qty.', array(
                                'placeholder' => __('Jumlah Klaim'),
                                'class' => 'claim-number form-control',
                                'div' => false,
                                'label' => false,
                                'value' => !empty($qty) ? $qty : 0
                            ));
                        ?>
                    </td>
                    <td class="hide-atpm <?php echo $show; ?> text-right">
                        <?php 
                            echo $this->Form->input('KsuDetail.price.', array(
                                'type' => 'text',
                                'label' => false,
                                'class' => 'form-control price-perlengkapan input_number input_price '.$show,
                                'required' => false,
                                'value' => $price,
                            ));
                        ?>
                    </td>
                    <td class="hide-atpm <?php echo $show; ?> total-price-claim text-right">
                        <?php 
                            if(empty($this->request->data['Ksu']['kekurangan_atpm'])){
                                $value_price = 0;
                                if(!empty($price) && !empty($qty)){
                                    $value_price = $price * $qty;
                                    $total += $value_price;
                                }

                                echo $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0));
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
                                'action_type' => 'lku_first'
                            ));
                        ?>
                    </td>
                </tr>
                <?php
                    }
                ?>
                <tr id="field-grand-total-ksu">
                    <td align="right" colspan="5" class="total-ksu" style="display:<?php echo ($show != 'hide') ? 'table-cell' : 'none';?>;"><?php echo __('Total Biaya Klaim')?></td>
                    <td align="right" id="grand-total-ksu" class="total-ksu" style="display:<?php echo ($show != 'hide') ? 'table-cell' : 'none';?>;">
                        <?php 
                            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
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
                        echo $this->Form->input('KsuDetail.perlengkapan_id.', array(
                            'options' => $perlengkapans,
                            'label' => false,
                            'empty' => __('Pilih Perlengkapan'),
                            'class' => 'ksu-choose-tipe-motor form-control',
                            'required' => false
                        ));
                    ?>
                </td>
                <td>
                    <?php 
                        echo $this->Form->input('KsuDetail.no_rangka.', array(
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control',
                            'required' => false
                        ));
                    ?>
                </td>
                <td>
                    <?php 
                        echo $this->Form->input('KsuDetail.note.', array(
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control',
                            'required' => false,
                        ));
                    ?>
                </td>
                <td class="qty-perlengkapan" align="center">
                    <?php
                        echo $this->Form->input('KsuDetail.qty.', array(
                            'placeholder' => __('Jumlah Klaim'),
                            'class' => 'claim-number form-control',
                            'div' => false,
                            'label' => false,
                            'value' => 0
                        ));
                    ?>
                </td>
                <td align="right">
                    <?php 
                        echo $this->Form->input('KsuDetail.price.', array(
                            'type' => 'text',
                            'label' => false,
                            'class' => 'form-control price-perlengkapan input_number input_price',
                            'required' => false
                        ));
                    ?>
                </td>
                <td class="total-price-claim" align="right"></td>
                <td>
                    <?php
                        echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                            'class' => 'delete-custom-field btn btn-danger btn-xs',
                            'escape' => false,
                            'action_type' => 'lku_first'
                        ));
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>