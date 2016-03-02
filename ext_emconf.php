<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "formengine_example".
 *
 * Auto generated 17-11-2015 14:01
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'FormEngine Example Extension',
  'description' => 'This is an example extension to introduce the development with the new FormEngine',
  'category' => 'backend',
  'author' => 'Nicole Cordes',
  'author_email' => 'typo3@cordes.co',
  'author_company' => '',
  'state' => 'stable',
  'uploadfolder' => 0,
  'createDirs' => '',
  'clearCacheOnLoad' => 0,
  'version' => '0.1.0',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '7.6.0-8.99.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'autoload' => 
  array (
    'psr-4' => 
    array (
      'IchHabRecht\\FormengineExample\\' => 'Classes',
    ),
  ),
  '_md5_values_when_last_written' => 'a:6:{s:9:"ChangeLog";s:4:"ff5f";s:12:"ext_icon.gif";s:4:"df08";s:17:"ext_localconf.php";s:4:"f554";s:51:"Classes/Form/Element/TogglePasswordInputElement.php";s:4:"e111";s:45:"Resources/Private/Language/locallang_form.xlf";s:4:"59c5";s:46:"Resources/Public/JavaScript/PasswordToggler.js";s:4:"4c80";}',
);

