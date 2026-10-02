<?php

use \setasign\Fpdi\Fpdi;



class pdf extends Fpdi
{

	private $cropMarkLength = 5;
	private $cropMarkWeight = .1;
	private $cropMarkColor = 127;
	public $paperSizes = ["Letter" => ['w' => 216, 'h' => 279]];
	public $paperSize = 'Letter';
	public $margins = 10;
	private $cropMarkOverlap = 1;


	//--------------------------------------------------------------------------------
	public function drawGrid($pdf, $grid)
	{
		if($grid == '0') {
			return;
		}
		$w = $this->paperSizes[$this->paperSize]['w'];
		$h = $this->paperSizes[$this->paperSize]['h'];
		

		for($x = 0; $x <= 8; $x+=$grid) {
			$newx = $x * 25.4;
			$pdf->Line($newx, 5, $newx, $h - 10);
		}

		for($y = 0; $y <= 10; $y+=$grid) {
			$newy = $y * 25.4;
			$pdf->Line(5, $newy, $w - 10, $newy);
		}
	}
	//--------------------------------------------------------------------------------
	public function drawCropMarks($pdf)
	{

		$w = $this->paperSizes[$this->paperSize]['w'];
		$h = $this->paperSizes[$this->paperSize]['h'];
		$a = $this->cropMarkLength;
		$overlap = $this->cropMarkOverlap;

		$marks = [];
		// top left 
		$x = $this->margins;
		$y = $this->margins;
		$marks[] = [$x, $y - $overlap, $x, $y + $a + $overlap]; // v 
		$marks[] = [$x - $overlap, $y, $x + $a + $overlap, $y]; // h
		// top right
		$x = $w - $this->margins;
		$y = $this->margins;
		$marks[] = [$x - ($a + $overlap), $y, $x + $overlap, $y]; // h
		$marks[] = [$x, $y - $overlap, $x, $y + $a + $overlap]; // v
		// bottom left 
		$x = $this->margins;
		$y = $h - $this->margins;
		$marks[] = [$x, $y - ($a + $overlap), $x, $y + $overlap]; // v
		$marks[] = [$x - $overlap, $y, $x + $a + $overlap, $y]; // h
		// bottom right
		$x = $w - $this->margins;
		$y = $h - $this->margins;
		$marks[] = [$x, $y - ($a + $overlap), $x, $y + $overlap]; // v
		$marks[] = [$x - ($overlap + $a), $y, $x + $overlap, $y]; // h
		foreach ($marks as $m) {
			$pdf->Line($m[0], $m[1], $m[2], $m[3]);
		}
	}
	//--------------------------------------------------------------------------------
	public function set($s, $v)
	{
		if (property_exists($this, $s)) {
			$this->$s = $v;
		}
	}
	//--------------------------------------------------------------------------------
	public function get($s)
	{
		if (property_exists($this, $s)) {
			return $this->$s;
		}
		return  null;
	}
	//--------------------------------------------------------------------------------
	public function save()
	{
	}
	//--------------------------------------------------------------------------------
	public function render()
	{
	}
}