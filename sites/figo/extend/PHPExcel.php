<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PHPExcel
 *
 * @author David
 */
class PHPExcel
{
    public $export_dir = '';
    public $save_path = '';

    public function __construct()
    {
        $this->export_dir = APP_PATH . 'Vendors/PHPExcel/export/';

    }

    /**
     * @param $data
     * @param null $save_path
     * @param int $sheet
     * @return null|string
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    function export($data, $save_path = null, $sheet = 0)
    {
        $this->save_path = is_null($save_path) ? $this->export_dir . uniqid() . '.xlsx' : $save_path;
        /** Include PHPExcel */
        require_once APP_PATH . 'Vendors/PHPExcel/PHPExcel.php';
        $excel = new \PHPExcel();
        $excel->setActiveSheetIndex($sheet);//设置活动单指数到第一个表
        $objActSheet = $excel->getActiveSheet();

        //设置宽度
//        $widthArr = array(14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14);
//        if (!empty($widthArr)) {
//            $key = ord("A");
//            foreach ($widthArr as $v) {
//                $colum = chr($key);
//                $objActSheet->getColumnDimension($colum)->setWidth($v);
//                $key += 1;
//            }
//        }

        $i = 1;
        foreach ($data as $row) { //行写入
            $j = ord('A');
            foreach ($row as $value) {// 列写入
                $objActSheet->setCellValue(chr($j) . $i, $value);
                $j++;
            }
            $i++;
        }
        $fileName = uniqid() . '.xlsx';
        $fileName = iconv("utf-8", "gb2312", $fileName);
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        if ($this->save_path === 'php://output') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$fileName\"");
            header('Cache-Control: max-age=0');
        }
        $objWriter->save($this->save_path);
        return $this->save_path;
    }

    function import($filepath){
        /** PHPExcel_IOFactory */
        require_once VENDORS_PATH . 'PHPExcel/PHPExcel/IOFactory.php';
        $reader = \PHPExcel_IOFactory::createReader('Excel2007'); //设置以Excel5格式(Excel97-2003工作簿)
        $PHPExcel = $reader->load($filepath); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumm = $sheet->getHighestColumn(); // 取得总列数
        $highestColumm = \PHPExcel_Cell::columnIndexFromString($highestColumm); //字母列转换为数字列 如:AA变为27
        $data = [];
        /** 循环读取每个单元格的数据 */
        for ($row = 1; $row <= $highestRow; $row++) {//行数是以第1行开始
            for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
                $columnName = \PHPExcel_Cell::stringFromColumnIndex($column);
                $data[$row - 1][$column] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
            }
        }
        return $data;
    }
}
