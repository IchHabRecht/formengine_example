<?php
defined('TYPO3_MODE') or die();

if (TYPO3_MODE === 'BE') {
    // Add new field type to NodeFactory
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1447694008632] = [
        'nodeName' => 'togglePasswordInput',
        'priority' => '70',
        'class' => \IchHabRecht\FormengineExample\Form\Element\TogglePasswordInputElement::class,
    ];
}
