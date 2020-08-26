<?php
/**
 * //先引入composer require phpoffice/phpexcel
 * Created by PhpStorm.
 * User: 15213
 * Date: 2020/8/26
 * Time: 10:06
 */

namespace Qttyeah;


class Excel
{
    /**
     * 导出excel
     * @param array $keys
     * @param array $data
     * @param string $fileName
     * @param string $fileType
     */
    function exportExcel(array $keys, array $data, $fileName = '', $fileType = 'xlsx')
    {
//        $keys = [
//            [
//                'key' => '键名',
//                'name' => '标题',
//                'width' => '宽度：20',
//                'is_string' => ’是否字符：1‘,
//            ]
//        ];
        $fileName .= date('YmdHi');

        $obj = new \PHPExcel();
        // 以下内容是excel文件的信息描述信息
        $obj->getProperties()->setCreator(''); //设置创建者
        $obj->getProperties()->setLastModifiedBy(''); //设置修改者
        $obj->getProperties()->setTitle(''); //设置标题
        $obj->getProperties()->setSubject(''); //设置主题
        $obj->getProperties()->setDescription(''); //设置描述
        $obj->getProperties()->setKeywords('');//设置关键词
        $obj->getProperties()->setCategory('');//设置类型
        // 设置当前sheet
        $obj->setActiveSheetIndex(0);
        // 设置当前sheet的名称
        $obj->getActiveSheet()->setTitle($fileName);
        // 列标
        $list = [];
        $keysNum = count($keys);
        for ($i = 65; $i < 65 + $keysNum; $i++) {
            $keymultiple = ceil(($i - 64) / 26);
            if ($keymultiple > 1) {
                $strchrN = strtoupper(chr($i - ($keymultiple - 1) * 26));
                for ($m = 1; $m < $keymultiple; $m++) {
                    $strchr = $list[($keymultiple - 2)] . $strchrN;
                }
            } else {
                $strchr = strtoupper(chr($i));
            }
            $list[] = $strchr;
        }
        // 填充行数据
        $oneLine = $obj->getActiveSheet();
        // 填充第一行数据
        for ($j = 0; $j < $keysNum; $j++) {
            $oneLine->setCellValue($list[$j] . '1', $keys[$j]['name']);
        }
        // 填充第n(n>=2, n∈N*)行数据
        $length = count($data);//有多少列
        for ($i = 0; $i < $length; $i++) {
            for ($j = 0; $j < $keysNum; $j++) {
                if ($keys[$j]['is_string']) {
                    //将其设置为文本格式
                    $oneLine->setCellValue($list[$j] . ($i + 2), $data[$i][$keys[$j]['key']], \PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $oneLine->setCellValue($list[$j] . ($i + 2), $data[$i][$keys[$j]['key']]);
                }
            }
        }
        // 设置列宽
        for ($j = 0; $j < $keysNum; $j++) {
            $oneLine->getColumnDimension($list[$j])->setWidth($keys[$j]['width']);
        }
        // 导出
        ob_clean();
        if ($fileType == 'xls') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xls');
            header('Cache-Control: max-age=1');
            $objWriter = new \PHPExcel_Writer_Excel5($obj);
            $objWriter->save('php://output');
            exit;
        } elseif ($fileType == 'xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx');
            header('Cache-Control: max-age=1');
            $objWriter = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
    }
}