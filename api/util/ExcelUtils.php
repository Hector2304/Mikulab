<?php

require_once '/vendor/autoload.php';

class ExcelUtils
{
	public static function setCenteredTextAndMerge($sheet, string $cellRange, string $firstCell, string $text)
	{
		$sheet->mergeCells($cellRange);
		$sheet->setCellValue($firstCell, $text);
		$sheet->getStyle($firstCell)->getAlignment()->setVertical('center');
		$sheet->getStyle($firstCell)->getAlignment()->setHorizontal('center');
	}

	public static function setVHCenter ($sheet, string $cell) {
		$style = $sheet->getStyle($cell);
		$style
			->getAlignment()
			->setVertical('center');
		$style
			->getAlignment()
			->setHorizontal('center');
	}

	public static function setVH ($sheet, string $cell, string $v = 'center', string $h = 'center') {
		$style = $sheet->getStyle($cell);
		$style
			->getAlignment()
			->setVertical($v);
		$style
			->getAlignment()
			->setHorizontal($h);
	}

	public static function setBold($sheet, string $cell)
	{
		$sheet->getStyle($cell)->getFont()->setBold(true);
	}

	public static function setFontSize($sheet, string $cell, int $size)
	{
		$sheet->getStyle($cell)->getFont()->setSize($size);
	}

	public static function setHeight($sheet, int $row, int $height)
	{
		$sheet->getRowDimension($row)->setRowHeight($height);
	}

	public static function setGenericHeader($sheet, string $cell, string $text)
	{
		$sheet->setCellValue($cell, $text);
		$style = $sheet->getStyle($cell);
		$style
			->getAlignment()
			->setVertical('center');
		$style
			->getAlignment()
			->setHorizontal('center');
		$style
			->getFill()
			->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
			->getStartColor()
			->setARGB('d9d9d9');
		$style
			->getBorders()
			->getOutline()
			->setBorderStyle(PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
			->setColor(new PhpOffice\PhpSpreadsheet\Style\Color('000000'));
	}

	public static function setSimpleBorder($sheet, string $cell)
	{
		$sheet
			->getStyle($cell)
			->getBorders()
			->getAllBorders()
			->setBorderStyle(PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
			->setColor(new PhpOffice\PhpSpreadsheet\Style\Color('000000'));
	}

	public static function setMediumBorder($sheet, string $cell)
	{
		$sheet
			->getStyle($cell)
			->getBorders()
			->getAllBorders()
			->setBorderStyle(PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)
			->setColor(new PhpOffice\PhpSpreadsheet\Style\Color('000000'));
	}

	public static function setRightMediumBorder($sheet, string $cell)
	{
		$sheet
			->getStyle($cell)
			->getBorders()
			->getRight()
			->setBorderStyle(PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)
			->setColor(new PhpOffice\PhpSpreadsheet\Style\Color('000000'));
	}

	public static function setBottomMediumBorder($sheet, string $cell)
	{
		$sheet
			->getStyle($cell)
			->getBorders()
			->getBottom()
			->setBorderStyle(PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)
			->setColor(new PhpOffice\PhpSpreadsheet\Style\Color('000000'));
	}

	public static function setSingleCellWithColor($sheet, string $cell, string $text, string $bgColor = null, string $brColor = null)
	{
		$sheet->setCellValue($cell, $text);
		$style = $sheet->getStyle($cell);

		$style
			->getAlignment()
			->setVertical('center');

		if (!is_null($bgColor)) {
			$style
				->getFill()
				->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()
				->setARGB($bgColor);
		}

		if (!is_null($brColor)) {
			$style
				->getBorders()
				->getOutline()
				->setBorderStyle(PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHED)
				->setColor(new PhpOffice\PhpSpreadsheet\Style\Color($brColor));
		}
	}

	public static function setAutoWidth($sheet, string $from, string $to)
	{
		foreach (range($from, $to) as $columnID) {
			$sheet->getColumnDimension($columnID)
				->setAutoSize(true);
		}
	}

	public static function setWidth($sheet, string $from, string $to, int $width)
	{
		foreach (range($from, $to) as $columnID) {
			$sheet->getColumnDimension($columnID)
				->setWidth($width);
		}
	}

	public static function setWrapText($sheet, string $cell)
	{
		$sheet->getStyle($cell)
			->getAlignment()
			->setWrapText(true);
	}
}
