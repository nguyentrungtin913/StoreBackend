<?php


namespace App\Helpers;


use App\Models\OrderDetail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportHelper
{
    const COLUMNS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

    const EXTRA_COLUMNS = ['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

    private Spreadsheet $spreadSheet;

    public function __construct()
    {
        $this->spreadSheet = new Spreadsheet();
    }


    private function exportFile($extension = "csv", $prefix)
    {
        $objWriter = IOFactory::createWriter($this->spreadSheet, 'Csv');
        $filePathS3 = FileHelper::exportFile($objWriter, $extension, $prefix);
        return $filePathS3;
    }

 private function setTitle($arr_title, $count_row, $style, $title_sheet = "Sheet 1", $is_supplier_merge = 0, $titleRow = 1)
    {
        if (is_array($arr_title)) {
            $i = 0;
            // Set basic style
            $number_col = $is_supplier_merge ? count($arr_title) * 2 - 1 : count($arr_title);
            $font_color = "000000";

            $rowheight = 30;
            $background = 'cccccc';
            $border_color = 'ecebeb';
            $line_ranger = self::COLUMNS[0] . '1:' . self::COLUMNS[$number_col - 1] . '1';// Ex: A1:F1
            $circle_ranger = self::COLUMNS[0] . '1:' . self::COLUMNS[$number_col - 1] . $count_row; //Ex: A1:F6

            foreach ($arr_title as $key => $value) {
                $col_title = self::COLUMNS[$i] . '1';

                // Set row title

                $this->spreadSheet->getActiveSheet()->setCellValue($col_title, $value);

                // Format cell title
                $this->spreadSheet->getActiveSheet()->getStyle($col_title)->getFont()->setColor(new Color($font_color));
                $this->spreadSheet->getActiveSheet()->getStyle($col_title)->getAlignment()->applyFromArray($style);

                if ($is_supplier_merge == 1) {
                    // Set size column
                    $this->spreadSheet->getActiveSheet()->getColumnDimension(self::COLUMNS[$i])->setAutoSize(false);

                    if ($i > 0) {
                        $this->spreadSheet->getActiveSheet()->mergeCells(self::COLUMNS[$i] . '1' . ":" . self::COLUMNS[$i + 1] . '1');
                        $i = $i + 2;
                    } else {
                        $i++;
                    }
                } else {
                    // Set size column
                    $this->spreadSheet->getActiveSheet()->getColumnDimension(self::COLUMNS[$i])->setAutoSize(true);

                    $i++;
                }

            }

            // Set height 
            $this->spreadSheet->getActiveSheet()->getRowDimension('1')->setRowHeight($rowheight);
            // $this->spreadSheet->getActiveSheet()->getStyle($line_ranger)->applyFromArray(
            //         array(
            //             'fill' => array(
            //                 'type' => PHPExcel_Style_Fill::FILL_SOLID,
            //                 'color' => array('rgb' => $background)
            //             )
            //         )
            //     );
            $this->spreadSheet->getActiveSheet()->getStyle($circle_ranger)->getBorders()->applyFromArray(
                array(
                    'allborders' => array(
                        'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => $border_color
                        )
                    )
                )
            );
            // Rename worksheet
            $this->spreadSheet->getActiveSheet()->setTitle($title_sheet);
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $this->spreadSheet->setActiveSheetIndex(0);
        }
    }
     public function exportDetailOrderToExcel($data)
    {
        $arr_title = array("Order ID", "Order image", "Order Price", "Amount");
       
        $style = array(
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'rotation' => 0,
            'wrap' => TRUE
        );

        $count_row = intval(count($data)) + 1;
        $this->setTitle($arr_title, $count_row, $style);
        $i = 2;

        if(!empty($data) && count($data) > 0){
            foreach ($data as $key => $item){
                $this->spreadSheet->getActiveSheet()
                    ->setCellValue('A' . $i, $item['order_id'])
                    ->setCellValue('B' . $i, $item['detail_image'])
                    ->setCellValue('C' . $i, $item['detail_price'])
                    ->setCellValue('D' . $i, $item['detail_amount']);
                $i++;
            }
        }
        return $this->exportFile('csv', 'image_report');
    }

    

    
}