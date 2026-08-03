<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';


require $gh_path.'include/plugin/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// 새로운 Spreadsheet 객체 생성
$spreadsheet = new Spreadsheet();

// 현재 활성 시트
$sheet = $spreadsheet->getActiveSheet();


// 행 간격 설정: 각 행의 높이 조정
$sheet->getRowDimension(1)->setRowHeight(35);  // 첫 번째 행 높이 (헤더)

// 필요한 만큼 행 높이 조정

// 열 간격 설정: 각 열의 너비 조정
$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(40);
$sheet->getColumnDimension('C')->setWidth(40);
$sheet->getColumnDimension('D')->setWidth(40);
$sheet->getColumnDimension('E')->setWidth(40);
$sheet->getColumnDimension('F')->setWidth(40);
$sheet->getColumnDimension('G')->setWidth(20);

// 제목 행 설정 (rowspan과 colspan을 사용)
$sheet->setCellValue('A1', '번호');
$sheet->setCellValue('B1', '분류');
$sheet->setCellValue('C1', '이름');
$sheet->setCellValue('D1', '회사명');
$sheet->setCellValue('E1', '이메일');
$sheet->setCellValue('F1', '모바일');
$sheet->setCellValue('G1', '등록일');

// 스타일 적용
$style_array = [
    'font' => [
        'bold' => true,
        'size' => 12,
        'color' => ['rgb' => '333333'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'f3f4f8'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'd4d6de'],
        ],
    ],
];

// 첫 번째 행의 스타일 적용
$sheet->getStyle('A1:G1')->applyFromArray($style_array);




// 데이터 행 추가
$row = 2;  // 첫 번째 데이터 행

$table_name = 'gh_inquiry_table';

$where = "where 1=1 ";

if($page_type){
	$where .= " and page_type = :pageType ";
	$bind_param[] = array('pageType', $page_type);
}

if($cate){
	$where .= " and category = :cate ";
	$bind_param[] = array('cate', $cate);
}

// 검색 컬럼 — escapeQuery 화이트리스트 후 LIKE
$func_library->appendWhereLike($where, $bind_param, $key_type, $keyword, 'i_name');

/*
if($key_type && $keyword){
	$where = $where." and SUBSTRING_INDEX(SUBSTRING_INDEX(form_value, '|', $key_type),'|',-1)  like :keyword ";
	$bind_param[] = array('keyword', $keyword,'like');
}*/

if($start_date && $end_date){
	$where = $where." and (substring(regdate,1,10) BETWEEN :start_date AND :end_date) ";
	$bind_param[] = array('start_date', $start_date);
	$bind_param[] = array('end_date', $end_date);
}else{
	if($start_date && !$end_date){
		$where = $where." and (substring(regdate,1,10) >= :start_date) ";
		$bind_param[] = array('start_date', $start_date);
	}else if(!$start_date && $end_date){
		$where = $where." and (substring(regdate,1,10) <= :end_date) ";
		$bind_param[] = array('end_date', $end_date);
	}
}

$list_result = $query_library->getList($where,$bind_param,$table_name,'',1,100);
$number = $list_result['number'];
foreach($list_result['result'] as $d){
	$regdate= substr($d['regdate'],0,10);
	if($d['status'] == '1'){
		$status = '<span style="color:blue">확인중</span>';
	}else{
		$status = '<span style="color:red">완료</span>';
	}

	$category_where = " where c_code = :c_code and category = 'inquiry' and depth = '1' ";
	$category_bind_param = array();
	$category_bind_param[] = array('c_code', $d['category']);
	$category_data = $query_library->getData2($category_where,$category_bind_param,'gh_category_table');

	$sheet->setCellValue('A' . $row, $number);
	$sheet->setCellValue('B' . $row, $category_data['c_name'] ?? '');
	$sheet->setCellValue('C' . $row, $d['r_name'] ?? '');
	$sheet->setCellValue('D' . $row, $d['r_company'] ?? '');
	$sheet->setCellValue('E' . $row, $d['r_email'] ?? '');
	$sheet->setCellValue('F' . $row, $d['r_mobile'] ?? '');
	$sheet->setCellValue('G' . $row, $regdate= substr($d['regdate'] ?? '',0,10));

	// 2번째 줄 이후로는 자동으로 높이를 30으로 설정
	$sheet->getRowDimension($row)->setRowHeight(30);

	// 각 셀에 대해 가운데 정렬을 적용
	$sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
		'alignment' => [
			'horizontal' => Alignment::HORIZONTAL_CENTER,  // 수평 가운데 정렬
			'vertical' => Alignment::VERTICAL_CENTER,      // 수직 가운데 정렬
		]
	]);
	$number--;
	$row++;
}

// 테두리 스타일 적용
$sheet->getStyle('A1:G' . ($row - 1))->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'd4d6de'],
        ],
    ],
]);

// Excel 파일 생성
$writer = new Xlsx($spreadsheet);

// 파일 저장
$filename = 'inquiry_list.xlsx';
//$writer->save($filename);

// 파일 다운로드를 위한 헤더 설정 (다운로드 자동 시작)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>