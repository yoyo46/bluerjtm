<div class="box">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', __('Informasi Grup Customer'), array(
                    'class' => 'box-title',
                ));
        ?>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Html->tag('label', __('Grup Customer'));
                            echo $this->Html->tag('div', !empty($customerGroup['CustomerGroup']['name'])?$customerGroup['CustomerGroup']['name']:'-');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>