<?php
App::uses('Component', 'Controller');

/**
 * Component for working with PHPExcel class.
 *
 * @package PhpExcel
 * @author segy
 */
class PhpExcelComponent extends Component {
    /**
     * Instance of PHPExcel class
     *
     * @var PHPExcel
     */
    protected $_xls;

    /**
     * Pointer to current row
     *
     * @var int
     */
    protected $_row = 1;

    /**
     * Internal table params
     *
     * @var array
     */
    protected $_tableParams;

    /**
     * Number of rows
     *
     * @var int
     */
    protected $_maxRow = 0;

    /**
     * Create new worksheet or load it from existing file
     *
     * @return $this for method chaining
     */
    public function createWorksheet() {
        // load vendor classes
        App::import('Vendor', 'PhpExcel.PHPExcel');

        $this->_xls = new PHPExcel();
        $this->_row = 1;

        return $this;
    }

    /**
     * Create new worksheet from existing file
     *
     * @param string $file path to excel file to load
     * @return $this for method chaining
     */
    public function loadWorksheet($file) {
        // load vendor classes
        App::import('Vendor', 'PhpExcel.PHPExcel');

        $this->_xls = PHPExcel_IOFactory::load($file);
        $this->setActiveSheet(0);
        $this->_row = $this->_xls->getActiveSheet()->getHighestRow();

        return $this;
    }

    /**
     * Set report header
     *
     * @param string $file path to excel file to load
     * @return $this for method chaining
     */
    public function setReportHeader($report_title = 'Report Title', $periods = false, $title_cell = 'A1', $periods_cell = 'A2', $title_cell_merge = false, $periods_cell_merge = false) {
        $sheet = $this->_xls->getActiveSheet();
        $sheet->setCellValue('A1', $report_title)->getStyle()->getFont()->setSize(20)->setBold(true);

        if( !empty($title_cell_merge) ) {
            $sheet->mergeCells($title_cell_merge);    
        }
        
        $sheet->getStyle($title_cell)->getAlignment()->applyFromArray(
            array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'bold' => true,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $sheet->getRowDimension('1')->setRowHeight(40);

        if( empty($periods) ) {
            // $sheet->mergeCells("A1:B1");
        } else {
            $sheet->setCellValue($periods_cell, $periods);
        }

        if( !empty($periods_cell_merge) ) {
            $sheet->mergeCells($periods_cell_merge);    
        }
        
        $sheet->getStyle($periods_cell)->getAlignment()->applyFromArray(
            array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'bold' => true,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $sheet->getRowDimension('2')->setRowHeight(25);

        return $this;
    }

    /**
     * Add sheet
     *
     * @param string $name
     * @return $this for method chaining
     */
    public function addSheet($name) {
        $index = $this->_xls->getSheetCount();
        $this->_xls->createSheet($index)
            ->setTitle($name);

        $this->setActiveSheet($index);

        return $this;
    }

    /**
     * Set active sheet
     *
     * @param int $sheet
     * @return $this for method chaining
     */
    public function setActiveSheet($sheet) {
        $this->_maxRow = $this->_xls->setActiveSheetIndex($sheet)->getHighestRow();
        $this->_row = 1;

        return $this;
    }

    /**
     * Set worksheet name
     *
     * @param string $name name
     * @return $this for method chaining
     */
    public function setSheetName($name) {
        $this->_xls->getActiveSheet()->setTitle($name);

        return $this;
    }

    /**
     * Overloaded __call
     * Move call to PHPExcel instance
     *
     * @param string function name
     * @param array arguments
     * @return the return value of the call
     */
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->_xls, $name), $arguments);
    }

    /**
     * Set default font
     *
     * @param string $name font name
     * @param int $size font size
     * @return $this for method chaining
     */
    public function setDefaultFont($name, $size) {
        $this->_xls->getDefaultStyle()->getFont()->setName($name);
        $this->_xls->getDefaultStyle()->getFont()->setSize($size);

        return $this;
    }

    /**
     * Set row pointer
     *
     * @param int $row number of row
     * @return $this for method chaining
     */
    public function setRow($row) {
        $this->_row = (int)$row;

        return $this;
    }

    /**
     * Start table - insert table header and set table params
     *
     * @param array $data data with format:
     *   label   -   table heading
     *   width   -   numeric (leave empty for "auto" width)
     *   filter  -   true to set excel filter for column
     *   wrap    -   true to wrap text in column
     * @param array $params table parameters with format:
     *   offset  -   column offset (numeric or text)
     *   font    -   font name of the header text
     *   size    -   font size of the header text
     *   bold    -   true for bold header text
     *   italic  -   true for italic header text
     * @return $this for method chaining
     */
    public function addTableHeader($data, $params = array(), $cell_end = 'H') {
        // offset
        $offset = 0;
        if (isset($params['offset']))
            $offset = is_numeric($params['offset']) ? (int)$params['offset'] : PHPExcel_Cell::columnIndexFromString($params['offset']);

        // font name
        if (isset($params['font']))
            $this->_xls->getActiveSheet()->getStyle($this->_row)->getFont()->setName($params['font']);

        // font size
        if (isset($params['size']))
            $this->_xls->getActiveSheet()->getStyle($this->_row)->getFont()->setSize($params['size']);

        // bold
        if (isset($params['bold']))
            $this->_xls->getActiveSheet()->getStyle($this->_row)->getFont()->setBold($params['bold']);

        // italic
        if (isset($params['italic']))
            $this->_xls->getActiveSheet()->getStyle($this->_row)->getFont()->setItalic($params['italic']);

        // horizontal
        if (isset($params['horizontal'])) {
            $this->_xls->getActiveSheet()->getStyle($this->_row)->getAlignment()->setHorizontal($params['horizontal']);
        }

        // text color
        if (isset($params['text_color'])) {
            $this->_xls->getActiveSheet()->getStyle(sprintf('A3:%s3', $cell_end))->getFont()->getColor()->setRGB($params['text_color']);
        }

        // fill color
        if (isset($params['fill_color'])) {
            $this->_xls->getActiveSheet()->getStyle(sprintf('A3:%s3', $cell_end))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($params['fill_color']);
        }

        // set internal params that need to be processed after data are inserted
        $this->_tableParams = array(
            'header_row' => $this->_row,
            'offset' => $offset,
            'row_count' => 0,
            'auto_width' => array(),
            'filter' => array(),
            'wrap' => array(),
            'horizontal' => false,
            'fill_color' => false,
            'text_color' => false,
        );

        if( !empty($data) ) {
            foreach ($data as $d) {
                $child = Common::hashEmptyField($d, 'child');

                // set label
                $this->_xls->getActiveSheet()->setCellValueByColumnAndRow($offset, $this->_row, $d['label']);

                // set width
                if (isset($d['width']) && is_numeric($d['width']))
                    $this->_xls->getActiveSheet()->getColumnDimensionByColumn($offset)->setWidth((float)$d['width']);
                else
                    $this->_xls->getActiveSheet()->getColumnDimensionByColumn($offset)->setAutoSize(true);
                    // $this->_tableParams['auto_width'][] = $offset;

                // filter
                if (isset($d['filter']) && $d['filter'])
                    $this->_tableParams['filter'][] = $offset;

                // wrap
                if (isset($d['wrap']) && $d['wrap'])
                    $this->_tableParams['wrap'][] = $offset;

                // fill color
                if (isset($d['fill_color']) && $d['fill_color'])
                    $this->_tableParams['fill_color'][] = $offset;

                // text color
                if (isset($d['text_color']) && $d['text_color'])
                    $this->_tableParams['text_color'][] = $offset;

                if( !empty($child) ) {
                    $childRow = $this->_row+1;

                    if( !isset($childOffset) ) {
                        $childOffset = $offset;
                    }

                    foreach ($child as $key => $val) {
                        $label = Common::hashEmptyField($val, 'label');
                        $width = Common::hashEmptyField($val, 'width');

                        $childOffsetAcii = 1+$childOffset;
                        $childOffsetAcii = Common::getNameFromNumber($childOffsetAcii);
                        $childPosition = sprintf('%s%s:%s%s', $childOffsetAcii, $childRow, $childOffsetAcii, $childRow);

                        $this->_xls->getActiveSheet()->setCellValueByColumnAndRow($childOffset, $childRow, $label);
                        
                        if (isset($width) && is_numeric($width))
                            $this->_xls->getActiveSheet()->getColumnDimensionByColumn($childOffset)->setWidth((float)$width);
                        else
                            $this->_tableParams['auto_width'][] = $childOffset;

                        // text color
                        if (isset($params['text_color'])) {
                            $this->_xls->getActiveSheet()->getStyle($childPosition)->getFont()->getColor()->setRGB($params['text_color']);
                        }

                        // fill color
                        if (isset($params['fill_color'])) {
                            $this->_xls->getActiveSheet()->getStyle($childPosition)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($params['fill_color']);
                        }

                        // horizontal
                        if (isset($params['horizontal'])) {
                            $this->_xls->getActiveSheet()->getStyle($childRow)->getAlignment()->setHorizontal($params['horizontal']);
                        }

                        $childOffset++;
                    }
                }

                if( !empty($d['rowspan']) ) {
                    $rowspan = $d['rowspan']-1;
                    $default = 1+$offset;
                    $row = $this->_row;
                    $cell_end = $row+$rowspan; // Acii A

                    $default = Common::getNameFromNumber($default);
                    $this->_xls->getActiveSheet()->mergeCells(__('%s%s:%s%s', $default, $row, $default, $cell_end));
                    // $this->_row += $rowspan;
                    // $offset++;
                }
                if( !empty($d['colspan']) ) {
                    $colspan = $d['colspan']-1;
                    $default = 1+$offset;
                    $dimensi = $default+$colspan; // Acii A
                    $row = $this->_row;

                    $default = Common::getNameFromNumber($default);
                    $cell_end = Common::getNameFromNumber($dimensi);

                    $this->_xls->getActiveSheet()->mergeCells(__('%s%s:%s%s', $default, $row, $cell_end, $row));
                    
                    $offset += $colspan;
                    // $this->_row ++;
                }

                $offset++;
            }
        }

        $child = Set::extract('/child', $data);

        if ( !empty($child) ) {
            $this->_row++;
        }
        
        $this->_row++;

        return $this;
    }

    /**
     * Write array of data to current row
     *
     * @param array $data
     * @return $this for method chaining
     */
    public function addTableRow($data, $params = array()) {
        if( !empty($this->_tableParams['offset']) ) {
            $offset = $this->_tableParams['offset'];
        } else {
            $offset = 0;
        }

        foreach ($data as $d) {

            if( is_array($d) ) {
                $text = isset($d['text'])?$d['text']:null;
                $options = !empty($d['options'])?$d['options']:null;
            } else {
                $text = $d;
                $options = null;
            }


            if( !empty($options) ) {
                $type = !empty($options['type'])?$options['type']:PHPExcel_Cell_DataType::TYPE_STRING;
                $align = !empty($options['align'])?$options['align']:PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                $colspan = !empty($options['colspan'])?$options['colspan']:null;

                switch ($type) {
                    case 'string':
                        $type = PHPExcel_Cell_DataType::TYPE_STRING;
                        break;
                }

                switch ($align) {
                    case 'center':
                        $align = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                        break;
                    case 'right':
                        $align = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                        break;
                }

                $this->_xls->getActiveSheet()->getStyle($this->_row)->getAlignment()->setHorizontal($align);
                $this->_xls->getActiveSheet()->getCellByColumnAndRow($offset, $this->_row)->setValueExplicit($text, $type);

                if( !empty($options['bold']) ) {
                    $this->_xls->getActiveSheet()->getStyle($this->_row)->getFont()->setBold(true);
                }
                if( !empty($colspan) ) {
                    $default = 65+$offset;
                    $dimensi = $default+($colspan-1); // Acii A
                    $row = $this->_row;

                    $default = chr($default);
                    $cell_end = chr($dimensi);

                    // if( $text == 'OPENING BALANCE' ) {
                    //     debug(__('%s%s:%s%s', $default, $row, $cell_end, $row));die();
                    // }

                    $this->_xls->getActiveSheet()->mergeCells(__('%s%s:%s%s', $default, $row, $cell_end, $row));
                    
                    $offset += $colspan-1;
                }
            } else {
                $this->_xls->getActiveSheet()->getStyle($this->_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $this->_xls->getActiveSheet()->setCellValueByColumnAndRow($offset, $this->_row, $text);
            }
            
            // if (isset($params['horizontal'])) {
            //     $this->_xls->getActiveSheet()->getCellByColumnAndRow($this->_row, $offset)->getStyle()->getAlignment()->setHorizontal($params['horizontal']);
            // }

            $offset++;
        }

        if( !empty($this->_tableParams['row_count']) ) {
            $row_count = $this->_tableParams['row_count'];
        } else {
            $row_count = 0;
        }

        $this->_row++;
        $this->_tableParams['row_count'] = $row_count+1;

        return $this;
    }

    /**
     * End table - set params and styles that required data to be inserted first
     *
     * @return $this for method chaining
     */
    public function addTableFooter() {
        // auto width
        foreach ($this->_tableParams['auto_width'] as $col)
            $this->_xls->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);

        // filter (has to be set for whole range)
        if (count($this->_tableParams['filter']))
            $this->_xls->getActiveSheet()->setAutoFilter(PHPExcel_Cell::stringFromColumnIndex($this->_tableParams['filter'][0]) . ($this->_tableParams['header_row']) . ':' . PHPExcel_Cell::stringFromColumnIndex($this->_tableParams['filter'][count($this->_tableParams['filter']) - 1]) . ($this->_tableParams['header_row'] + $this->_tableParams['row_count']));

        // wrap
        foreach ($this->_tableParams['wrap'] as $col)
            $this->_xls->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col) . ($this->_tableParams['header_row'] + 1) . ':' . PHPExcel_Cell::stringFromColumnIndex($col) . ($this->_tableParams['header_row'] + $this->_tableParams['row_count']))->getAlignment()->setWrapText(true);

        return $this;
    }

    /**
     * Write array of data to current row starting from column defined by offset
     *
     * @param array $data
     * @return $this for method chaining
     */
    public function addData($data, $offset = 0) {
        // solve textual representation
        if (!is_numeric($offset))
            $offset = PHPExcel_Cell::columnIndexFromString($offset);

        foreach ($data as $d)
            $this->_xls->getActiveSheet()->setCellValueByColumnAndRow($offset++, $this->_row, $d);

        $this->_row++;

        return $this;
    }

    /**
     * Get array of data from current row
     *
     * @param int $max
     * @return array row contents
     */
    public function getTableData($max = 100) {
        if ($this->_row > $this->_maxRow)
            return false;

        $data = array();

        for ($col = 0; $col < $max; $col++)
            $data[] = $this->_xls->getActiveSheet()->getCellByColumnAndRow($col, $this->_row)->getValue();

        $this->_row++;

        return $data;
    }

    /**
     * Get writer
     *
     * @param $writer
     * @return PHPExcel_Writer_Iwriter
     */
    public function getWriter($writer) {
        return PHPExcel_IOFactory::createWriter($this->_xls, $writer);
    }

    /**
     * Save to a file
     *
     * @param string $file path to file
     * @param string $writer
     * @return bool
     */
    public function save($file, $writer = 'Excel2007') {
        $objWriter = $this->getWriter($writer);
        return $objWriter->save($file);
    }

    /**
     * Output file to browser
     *
     * @param string $file path to file
     * @param string $writer
     * @return exit on this call
     */
    public function output($filename = 'export.xlsx', $writer = 'Excel2007') {
        // remove all output
        ob_end_clean();

        // headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // writer
        $objWriter = $this->getWriter($writer);
        $objWriter->save('php://output');

        exit;
    }

    /**
     * Free memory
     *
     * @return void
     */
    public function freeMemory() {
        $this->_xls->disconnectWorksheets();
        unset($this->_xls);
    }
}