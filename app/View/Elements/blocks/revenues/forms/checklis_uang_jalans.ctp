<?php
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uj1', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 'uang_jalan',
            'div' => false,
        )).__('Uang Jalan ke 1')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uj2', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 'uang_jalan_2',
            'div' => false,
        )).__('Uang Jalan ke 2')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uje', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 'uang_jalan_extra',
            'div' => false,
        )).__('Uang Jalan Extra')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('com', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 'commission',
            'div' => false,
        )).__('Komisi')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('come', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 'commission_extra',
            'div' => false,
        )).__('Komisi Extra')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
?>