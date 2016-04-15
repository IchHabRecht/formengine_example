<?php
namespace IchHabRecht\FormengineExample\Form\Element;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Nicole Cordes <typo3@cordes.co>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class TogglePasswordInputElement extends AbstractFormElement
{

    /**
     * This will render a single-line input password field
     * and a button to toggle password visibility
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $resultArray = $this->initializeResultArray();
        $resultArray['requireJsModules'][] = [
            'TYPO3/CMS/FormengineExample/PasswordToggler' => 'function(PasswordToggler) {
                PasswordToggler.initialize();
            }'
        ];
        $resultArray['additionalInlineLanguageLabelFiles'][] = 'EXT:formengine_example/Resources/Private/Language/locallang_form.xlf';

        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];
        $evalList = GeneralUtility::trimExplode(',', $config['eval'], true);
        $evalList = array_filter($evalList, function ($item) {
            return $item !== 'password';
        });
        $attributes = [
            'type' => 'password',
            'value' => '********',
            'autocomplete' => 'off',
        ];

        // @todo: The whole eval handling is a mess and needs refactoring
        foreach ($evalList as $func) {
            switch ($func) {
                case 'required':
                    $attributes['data-formengine-validation-rules'] = $this->getValidationDataAsJsonString(['required' => true]);
                    break;
                default:
                    // @todo: This is ugly: The code should find out on it's own whether a eval definition is a
                    // @todo: keyword like "date", or a class reference. The global registration could be dropped then
                    // Pair hook to the one in \TYPO3\CMS\Core\DataHandling\DataHandler::checkValue_input_Eval()
                    if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][$func])) {
                        if (class_exists($func)) {
                            $evalObj = GeneralUtility::makeInstance($func);
                            if (method_exists($evalObj, 'deevaluateFieldValue')) {
                                $_params = [
                                    'value' => $parameterArray['itemFormElValue'],
                                ];
                                $parameterArray['itemFormElValue'] = $evalObj->deevaluateFieldValue($_params);
                            }
                        }
                    }
            }
        }

        // set classes
        $classes = [];
        $classes[] = 'toggle-password-field';
        $classes[] = 'form-control';
        $classes[] = 't3js-clearable';
        $classes[] = 'hasDefaultValue';

        // calculate attributes
        $paramsList = [
            'field' => $parameterArray['itemFormElName'],
            'evalList' => implode(',', $evalList),
            'is_in' => trim($config['is_in']),
        ];
        $attributes['data-formengine-validation-rules'] = $this->getValidationDataAsJsonString($config);
        $attributes['data-formengine-input-params'] = json_encode($paramsList);
        $attributes['data-formengine-input-name'] = htmlspecialchars($parameterArray['itemFormElName']);
        $attributes['id'] = StringUtility::getUniqueId('formengine-input-');
        if (isset($config['max']) && (int)$config['max'] > 0) {
            $attributes['maxlength'] = (int)$config['max'];
        }
        if (!empty($classes)) {
            $attributes['class'] = implode(' ', $classes);
        }

        // This is the EDITABLE form field.
        if (!empty($config['placeholder'])) {
            $attributes['placeholder'] = trim($config['placeholder']);
        }

        if (isset($config['autocomplete'])) {
            $attributes['autocomplete'] = empty($config['autocomplete']) ? 'off' : 'on';
        }

        // Build the attribute string
        $attributeString = '';
        foreach ($attributes as $attributeName => $attributeValue) {
            $attributeString .= ' ' . $attributeName . '="' . htmlspecialchars($attributeValue) . '"';
        }

        $html = '
			<div class="input-group">
				<input' . $attributeString . ' />
			</div>';

        // This is the ACTUAL form field - values from the EDITABLE field must be transferred to this field which is the one that is written to the database.
        $html .= '<input type="hidden" name="' . $parameterArray['itemFormElName'] . '" value="' . htmlspecialchars($parameterArray['itemFormElValue']) . '" />';

        // Going through all custom evaluations configured for this field
        // @todo: Similar to above code!
        foreach ($evalList as $evalData) {
            if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][$evalData])) {
                if (class_exists($evalData)) {
                    $evalObj = GeneralUtility::makeInstance($evalData);
                    if (method_exists($evalObj, 'returnFieldJS')) {
                        $resultArray['extJSCODE'] .= LF . 'TBE_EDITOR.customEvalFunctions[' . GeneralUtility::quoteJSvalue($evalData) . '] = function(value) {' . $evalObj->returnFieldJS() . '}';
                    }
                }
            }
        }

        // Wrap a wizard around the item?
        $html = $this->renderWizards(
            [$html],
            $config['wizards'],
            $this->data['tableName'],
            $this->data['databaseRow'],
            $this->data['fieldName'],
            $parameterArray,
            $parameterArray['itemFormElName'],
            BackendUtility::getSpecConfParts($parameterArray['fieldConf']['defaultExtras'])
        );

        // Add a wrapper to remain maximum width
        $size = MathUtility::forceIntegerInRange($config['size'] ?: $this->defaultInputWidth, $this->minimumInputWidth,
            $this->maxInputWidth);
        $width = (int)$this->formMaxWidth($size);
        $html = '<div class="form-control-wrap"' . ($width ? ' style="max-width: ' . $width . 'px"' : '') . '>' . $html . '</div>';
        $resultArray['html'] = $html;

        return $resultArray;
    }

}
