<?php 
        $value = !empty($value)?$value:false;
        $data = $this->request->data;
        $id = $this->Common->filterEmptyField($value, 'AssetSell', 'id', 0);
        $dataDetail = $this->Common->filterEmptyField($data, 'AssetSellDetail');

        $dataColumns = array(
            'name' => array(
                'name' => __('Nama'),
                'class' => 'text-center',
            ),
            'note' => array(
                'name' => __('Keterangan'),
            ),
            'perolehan' => array(
                'name' => __('Nilai Perolehan'),
                'class' => 'text-center',
                'style' => 'width:20%;',
            ),
            'penyusutan' => array(
                'name' => __('Ak. Penyusutan'),
                'class' => 'text-center',
                'style' => 'width:20%;',
            ),
            'total' => array(
                'name' => __('Harga Jual'),
                'class' => 'text-center',
                'style' => 'width:20%;',
            ),
        );

        if( empty($view) ) {
            $dataColumns = array_merge($dataColumns, array(
                'action' => array(
                    'name' => __('Action'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
            ));
        }

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="form-added temp-document-picker">
    <?php 
            if( empty($view) ) {
                echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil Asset'), $this->Html->url( array(
                    'controller'=> 'assets', 
                    'action' => 'asset_documents',
                    'payment_id' => $id,
                    'admin' => false,
                )), array(
                    'escape' => false,
                    'title' => __('Daftar Asset'),
                    'class' => 'btn bg-maroon ajaxCustomModal',
                )), array(
                    'class' => "form-group",
                ));
            }
    ?>
    <div class="temp-document-picker document-calc">
    	<div class="box box-success">
    	    <?php 
    	            echo $this->element('blocks/common/box_header', array(
    	                'title' => __('Informasi Asset'),
    	            ));
    	    ?>
    	    <div class="box-body table-responsive">
    	        <table class="table table-hover form-added" id="wrapper-write">
    		        <?php
    	                    if( !empty($fieldColumn) ) {
    	                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
    	                    }
    	            ?>
    	        	<tbody class="field-content">
                        <?php
                                $grandtotal = 0;
                                $grandtotalPerolehan = 0;
                                $grandtotalPenyusutan = 0;

                                if(!empty($dataDetail)){
                                    foreach ($dataDetail as $key => $value) {
                                        $price = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'price');
                                        $nilai_perolehan = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'nilai_perolehan');
                                        $ak_penyusutan = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'ak_penyusutan');

                                        $grandtotal += $price;
                                        $grandtotalPerolehan += $nilai_perolehan;
                                        $grandtotalPenyusutan += $ak_penyusutan;
                                        $customTotal = $this->Common->getFormatPrice($price);

                                        echo $this->element('blocks/assets/tables/sells/items', array(
                                            'modelName' => 'AssetSellDetail',
                                            'value' => $value,
                                            'total' => $customTotal,
                                            'idx' => $key,
                                        ));
                                    }
                                }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="grandtotal">
                            <?php
                                    $customGrandtotal = $this->Common->getFormatPrice($grandtotal, 0, 2);
                                    $customGrandtotalPerolehan = $this->Common->getFormatPrice($grandtotalPerolehan, 0, 2);
                                    $customGrandtotalPenyusutan = $this->Common->getFormatPrice($grandtotalPenyusutan, 0, 2);

                                    echo $this->Html->tag('td', __('Grand Total'), array(
                                        'colspan' => 2,
                                        'class' => 'text-right',
                                    ));
                                    echo $this->Html->tag('td', $customGrandtotalPerolehan, array(
                                        'class' => 'text-right calc-total',
                                        'rel' => 0,
                                        'data-decimal' => 2,
                                    ));
                                    echo $this->Html->tag('td', $customGrandtotalPenyusutan, array(
                                        'class' => 'text-right calc-total',
                                        'rel' => 1,
                                        'data-decimal' => 2,
                                    ));
                                    echo $this->Html->tag('td', $customGrandtotal, array(
                                        'class' => 'text-right total',
                                        'data-decimal' => 2,
                                    ));
                            ?>
                        </tr>
                    </tfoot>
    	    	</table>
    	    </div>
    	</div>
    </div>
</div>