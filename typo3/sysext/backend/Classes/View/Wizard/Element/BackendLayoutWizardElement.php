<?php
namespace TYPO3\CMS\Backend\View\Wizard\Element;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BackendLayoutWizardElement
 */
class BackendLayoutWizardElement extends AbstractFormElement
{

    /**
     * @var array
     */
    protected $resultArray = [];

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var int
     */
    protected $colCount = 0;

    /**
     * @var int
     */
    protected $rowCount = 0;

    /**
     * @return array
     */
    public function render()
    {
        $this->resultArray = $this->initializeResultArray();
        $this->init();

        $lang = $this->getLanguageService();

        $json = json_encode($this->rows, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        $markup = [];
        $markup[] = '<input type="hidden" name="' . htmlspecialchars($this->data['parameterArray']['itemFormElName'])
            . '" value="' . htmlspecialchars($this->data['parameterArray']['itemFormElValue']) . '" />';
        $markup[] = '<table class="grideditor table table-bordered">';
        $markup[] = '    <tr>';
        $markup[] = '        <td class="editor_cell">';
        $markup[] = '           <div id="editor" class="t3js-grideditor" data-data="' . htmlspecialchars($json) . '" '
            . 'data-rowcount="' . (int)$this->rowCount . '" '
            . 'data-colcount="' . (int)$this->colCount . '" '
            . 'data-field="' . htmlspecialchars($this->data['parameterArray']['itemFormElName']) . '" '
            . '>';
        $markup[] = '            </div>';
        $markup[] = '        </td>';
        $markup[] = '        <td>';
        $markup[] = '            <div class="btn-group-vertical">';
        $markup[] = '               <a class="btn btn-default btn-sm t3js-grideditor-addcolumn" href="#" title="'
            . htmlspecialchars($lang->getLL('grid_addColumn')) . '">';
        $markup[] = '                <i class="fa fa-fw fa-arrow-right"></i>';
        $markup[] = '               </a>';
        $markup[] = '               <a class="btn btn-default btn-sm t3js-grideditor-removecolumn" href="#" title="'
            . htmlspecialchars($lang->getLL('grid_removeColumn')) . '">';
        $markup[] = '                <i class="fa fa-fw fa-arrow-left"></i>';
        $markup[] = '               </a>';
        $markup[] = '            </div>';
        $markup[] = '        </td>';
        $markup[] = '    </tr>';
        $markup[] = '    <tr>';
        $markup[] = '        <td colspan="2" align="center">';
        $markup[] = '            <div class="btn-group">';
        $markup[] = '               <a class="btn btn-default btn-sm t3js-grideditor-addrow" href="#" title="'
            . htmlspecialchars($lang->getLL('grid_addRow')) . '">';
        $markup[] = '                <i class="fa fa-fw fa-arrow-down"></i>';
        $markup[] = '               </a>';
        $markup[] = '               <a class="btn btn-default btn-sm t3js-grideditor-removerow" href="#" title="'
            . htmlspecialchars($lang->getLL('grid_removeRow')) . '">';
        $markup[] = '                <i class="fa fa-fw fa-arrow-up"></i>';
        $markup[] = '               </a>';
        $markup[] = '            </div>';
        $markup[] = '        </td>';
        $markup[] = '    </tr>';
        $markup[] = '    <tr>';
        $markup[] = '        <td colspan="2">';
        $markup[] = '            <a href="#" class="btn btn-default btn-sm t3js-grideditor-preview-button"></a>';
        $markup[] = '            <pre class="t3js-grideditor-preview-config grideditor-preview"><code></code></pre>';
        $markup[] = '        </td>';
        $markup[] = '    </tr>';
        $markup[] = '</table>';

        $content = implode(LF, $markup);
        $this->resultArray['html'] = $content;
        $this->resultArray['requireJsModules'][] = 'TYPO3/CMS/Backend/GridEditor';
        $this->resultArray['additionalInlineLanguageLabelFiles'][] = 'EXT:lang/locallang_wizards.xlf';
        $this->resultArray['additionalInlineLanguageLabelFiles'][]
            = 'EXT:backend/Resources/Private/Language/locallang.xlf';

        return $this->resultArray;
    }

    /**
     * Initialize wizard
     */
    protected function init()
    {
        if (empty($this->data['databaseRow']['config'])) {
            $rows = [[['colspan' => 1, 'rowspan' => 1, 'spanned' => false, 'name' => '']]];
            $colCount = 1;
            $rowCount = 1;
        } else {
            // load TS parser
            $parser = GeneralUtility::makeInstance(TypoScriptParser::class);
            $parser->parse($this->data['databaseRow']['config']);
            $data = $parser->setup['backend_layout.'];
            $rows = [];
            $colCount = $data['colCount'];
            $rowCount = $data['rowCount'];
            $dataRows = $data['rows.'];
            $spannedMatrix = [];
            for ($i = 1; $i <= $rowCount; $i++) {
                $cells = [];
                $row = array_shift($dataRows);
                $columns = $row['columns.'];
                for ($j = 1; $j <= $colCount; $j++) {
                    $cellData = [];
                    if (!$spannedMatrix[$i][$j]) {
                        if (is_array($columns) && !empty($columns)) {
                            $column = array_shift($columns);
                            if (isset($column['colspan'])) {
                                $cellData['colspan'] = (int)$column['colspan'];
                                $columnColSpan = (int)$column['colspan'];
                                if (isset($column['rowspan'])) {
                                    $columnRowSpan = (int)$column['rowspan'];
                                    for ($spanRow = 0; $spanRow < $columnRowSpan; $spanRow++) {
                                        for ($spanColumn = 0; $spanColumn < $columnColSpan; $spanColumn++) {
                                            $spannedMatrix[$i + $spanRow][$j + $spanColumn] = 1;
                                        }
                                    }
                                } else {
                                    for ($spanColumn = 0; $spanColumn < $columnColSpan; $spanColumn++) {
                                        $spannedMatrix[$i][$j + $spanColumn] = 1;
                                    }
                                }
                            } else {
                                $cellData['colspan'] = 1;
                                if (isset($column['rowspan'])) {
                                    $columnRowSpan = (int)$column['rowspan'];
                                    for ($spanRow = 0; $spanRow < $columnRowSpan; $spanRow++) {
                                        $spannedMatrix[$i + $spanRow][$j] = 1;
                                    }
                                }
                            }
                            if (isset($column['rowspan'])) {
                                $cellData['rowspan'] = (int)$column['rowspan'];
                            } else {
                                $cellData['rowspan'] = 1;
                            }
                            if (isset($column['name'])) {
                                $cellData['name'] = $column['name'];
                            }
                            if (isset($column['colPos'])) {
                                $cellData['column'] = (int)$column['colPos'];
                            }
                        }
                    } else {
                        $cellData = ['colspan' => 1, 'rowspan' => 1, 'spanned' => 1];
                    }
                    $cells[] = $cellData;
                }
                $rows[] = $cells;
                if (!empty($spannedMatrix[$i]) && is_array($spannedMatrix[$i])) {
                    ksort($spannedMatrix[$i]);
                }
            }
        }
        $this->rows = $rows;
        $this->colCount = (int)$colCount;
        $this->rowCount = (int)$rowCount;
    }
}
