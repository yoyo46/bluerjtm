<div class="box-header">
    <?php 
            $title = __('Pencarian');

            echo $this->Html->tag('h3', $title, array(
                'class' => 'box-title',
            ));
            echo $this->Html->tag('div', $this->Form->button($this->Common->icon('minus'), array(
                'class' => 'btn btn-default btn-sm',
                'data-widget' => 'collapse',
                'data-toggle' => 'tooltip',
                'title' => $title,
            )), array(
                'class' => 'box-tools pull-right',
            ));
    ?>
</div>