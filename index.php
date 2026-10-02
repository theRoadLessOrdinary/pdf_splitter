<?php
error_reporting(E_ALL);

require_once(__DIR__ . "/vendor/fpdf182/fpdf.php");
require_once(__DIR__ . "/vendor/FPDI-2.6.1/src/autoload.php");
require_once(__DIR__ . "/cls/splitter.class.php");

define('TODO_DIR', __DIR__ . '/todo');

//--------------------------------------------------------------------------------
function _main()
{

	$cmd = $_GET['cmd'] ?? '';

	if ($cmd == '') {
		recordIP();
		drawInterface();
	}

	if ($cmd == 'upload') {
		upload_handler();
	}

	if ($cmd == 'split') {
		split_handler();
	}
}

//--------------------------------------------------------------------------------
function divide($a, $b) {
	$c = intdiv($a, $b);
	$d = $a % $b;
	if($d > 0){
		$c++;
	}
	return $c;
}
//--------------------------------------------------------------------------------
function recordIP() {
	$ts = @file_get_contents("https://freeipapi.com/api/json/" . $_SERVER['REMOTE_ADDR']);
	$js = json_decode($ts ?: '{}', 1) + ['cityName' => '', 'countryName' => ''];
	$fh = fopen(__DIR__ . "/usage.txt", 'a');
	fwrite($fh, date("Y-m-d H:i") . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $js['cityName'] . ', ' . $js['countryName'] . "\t" . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL);
	fclose($fh);
}

//--------------------------------------------------------------------------------
function drawInterface()
{
	$tmp = file_get_contents(__DIR__ . '/tpl/main.template.html');
	$tmp = str_replace("#nowstr#", Date('His'), $tmp);
	echo $tmp;
}

//--------------------------------------------------------------------------------
// Saves a dropped PDF into todo/ and returns the stored file name as JSON
function upload_handler()
{
	header('Content-Type: application/json');
	$file = $_FILES['file'] ?? null;
	if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
		http_response_code(400);
		die(json_encode(['ok' => false, 'error' => 'Upload failed.']));
	}
	if ($file['size'] >= 5000000) {
		http_response_code(400);
		die(json_encode(['ok' => false, 'error' => 'The file size is too large. 5mb max.']));
	}
	$name = str_replace(' ', '_', basename($file['name']));
	$ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
	$head = file_get_contents($file['tmp_name'], false, null, 0, 5);
	if ($ext !== 'pdf' || $head !== '%PDF-') {
		http_response_code(400);
		die(json_encode(['ok' => false, 'error' => 'Only PDF files can be split.']));
	}
	$name = preg_replace('/[^A-Za-z0-9._()#-]/', '_', $name);
	if (file_exists(TODO_DIR . '/' . $name)) {
		$name = buildNewFilename(TODO_DIR, $name);
	}
	move_uploaded_file($file['tmp_name'], TODO_DIR . '/' . $name);
	echo json_encode(['ok' => true, 'name' => $name]);
}

//--------------------------------------------------------------------------------
function buildNewFilename($fdr, $n)
{
	$ext  = pathinfo($n, PATHINFO_EXTENSION);
	$base = pathinfo($n, PATHINFO_FILENAME);
	for ($i = 1; $i < 9999; $i++) {
		if (!file_exists("$fdr/$base($i).$ext")) {
			return "$base($i).$ext";
		}
	}
}

//--------------------------------------------------------------------------------
function split_handler()
{
	// Only files already in todo/ can be split
	$src = basename($_GET['src'] ?? '');
	if ($src == '' || !is_file(TODO_DIR . '/' . $src)) {
		die('<p>No document was entered.</p>');
	}
	$src = TODO_DIR . '/' . $src;
	$grid = $_GET['grid'] ?? '0';
	if($grid == '') {
		$grid = '0';
	}
// 	$src = "./robert lee pattern 2021.pdf";
	$nowstr = date('H-i-s');
	$dest = "./split.pdf";

	// initiate FPDI
	$pdf = new pdf();
	// add a page

	// set the source file
	$pdf->setSourceFile($src);

	$pdf->set("paperSize", 'Letter');
	$pdf->set("margins", 10);
	$pdf->save();

	$pdf->SetFont('Arial', '', 8);

	$pdf->SetLeftMargin(10);
	$pdf->SetTopMargin(10);
	// import page 1
	$tplIdx = $pdf->importPage(1);
	$size = $pdf->getImportedPageSize($tplIdx);

	$size['width'] = ceil($size['width']);
	$size['height'] = ceil($size['height']);

	$workingWidth = $pdf->paperSizes[$pdf->paperSize]['w'] - ($pdf->margins * 2);
	$workingHeight = $pdf->paperSizes[$pdf->paperSize]['h'] - ($pdf->margins * 2);
	$txtpagesAcross = divide($size['width'], $workingWidth);
	$txtpagesDown =  divide($size['height'], $workingHeight);
	$pagesAcross = intdiv($size['width'], $workingWidth);
	$pagesDown =  intdiv($size['height'], $workingHeight);
	$totalPages = $txtpagesAcross * $txtpagesDown;
	
	// use the imported page and place it at point 10,10 with a width of 100 mm
	$curPage = 1;
	for ($y = 0; $y <= $pagesDown; $y++) {
		for ($x = 0; $x <= $pagesAcross; $x++) {
			$pdf->AddPage("P", "Letter");
			$ax = - ($x * $workingWidth);
			$ay = - ($y * $workingHeight);
			// echo('<pre>[64114] [$ax, $ay]:'  . print_r([$ax, $ay], 1)) . '</pre>';
			$pdf->useImportedPage($tplIdx, $ax,  $ay); // , 200, 275, false);
			$pdf->Text(14, 14, "Page $curPage of $totalPages        " . ($x + 1) . ":" . ($y + 1));
			$pdf->setDrawColor(127);
			$pdf->drawCropMarks($pdf);
			$pdf->setDrawColor(223);
			$pdf->drawGrid($pdf, $grid);
			$curPage++;
		}
	}

	// $pdf->Output("F", $dest);
	$pdf->Output("I");
	// unlink($src);
/* 
	unset($pdf);

	echo "<p><a href='$dest'>$dest</a></p>"; */
}




_main();
