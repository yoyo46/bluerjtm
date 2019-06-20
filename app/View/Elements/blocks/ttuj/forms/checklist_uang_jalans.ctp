<?php
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_jalan_1', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 1,
            'div' => false,
        )).__('Uang Jalan ke 1')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_jalan_2', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 1,
            'div' => false,
        )).__('Uang Jalan ke 2')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('uang_jalan_extra', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 1,
            'div' => false,
        )).__('Uang Jalan Extra')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('commission', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 1,
            'div' => false,
        )).__('Komisi')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
        echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('label', $this->Form->input('commission_extra', array(
            'type' => 'checkbox',
            'label'=> false,
            'required' => false,
            'value' => 1,
            'div' => false,
        )).__('Komisi Extra')), array(
            'class' => 'checkbox',
        )), array(
            'class' => 'col-sm-6',
        ));
?>