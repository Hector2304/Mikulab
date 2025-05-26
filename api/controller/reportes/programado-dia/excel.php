<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/Util.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/ExcelUtils.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportesProgramadoSemanaExcel extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			ExcelUtils::setCenteredTextAndMerge($sheet, 'A1:G1', 'A1', 'GRUPOS PROGRAMADOS EN LABORATORIOS DE CÓMPUTO ' . $jsonData->dia);
			ExcelUtils::setBold($sheet, 'A1');

			ExcelUtils::setGenericHeader($sheet, 'A3', 'DÍA');
			ExcelUtils::setGenericHeader($sheet, 'B3', 'LAB');
			ExcelUtils::setGenericHeader($sheet, 'C3', 'HORA');
			ExcelUtils::setGenericHeader($sheet, 'D3', 'ASIGNATURA/GRUPO/PROFESOR');
			ExcelUtils::setGenericHeader($sheet, 'E3', 'HORARIO DE APERTURA');
			ExcelUtils::setGenericHeader($sheet, 'F3', 'ASISTENCIA');
			ExcelUtils::setGenericHeader($sheet, 'G3', 'OBSERVACIONES');
			ExcelUtils::setBold($sheet, 'A3:G3');

			$i = 4;
			foreach ($jsonData->list as $row) {
				ExcelUtils::setSingleCellWithColor($sheet, 'A' . $i, $row->dia);
				ExcelUtils::setSingleCellWithColor($sheet, 'B' . $i, $row->lab);
				ExcelUtils::setSingleCellWithColor($sheet, 'C' . $i, $row->horario);
				ExcelUtils::setSingleCellWithColor($sheet, 'D' . $i, $row->grupo);
				ExcelUtils::setSingleCellWithColor($sheet, 'E' . $i, $row->apertura);
				ExcelUtils::setSingleCellWithColor($sheet, 'F' . $i, $row->asistencia);
				ExcelUtils::setSingleCellWithColor($sheet, 'G' . $i, $row->observaciones);
				ExcelUtils::setVHCenter($sheet, 'A' . $i);
				ExcelUtils::setVHCenter($sheet, 'B' . $i);
				ExcelUtils::setVHCenter($sheet, 'C' . $i);
				ExcelUtils::setVHCenter($sheet, 'E' . $i);
				ExcelUtils::setVHCenter($sheet, 'F' . $i);
				ExcelUtils::setFontSize($sheet, 'A' . $i . ':G' . $i, 9);
				ExcelUtils::setHeight($sheet, $i, -1);
				ExcelUtils::setWrapText($sheet, 'A' . $i . ':G' . $i);
				ExcelUtils::setSimpleBorder($sheet, 'A' . $i . ':G' . $i);
				$i++;
			}

			ExcelUtils::setWidth($sheet, 'A', 'A', 8);
			ExcelUtils::setWidth($sheet, 'B', 'B', 10);
			ExcelUtils::setWidth($sheet, 'C', 'C', 16);
			ExcelUtils::setWidth($sheet, 'E', 'E', 24);
			ExcelUtils::setWidth($sheet, 'F', 'F', 14);

			ExcelUtils::setWidth($sheet, 'D', 'D', 65);
			ExcelUtils::setWidth($sheet, 'G', 'G', 30);

			$writer = new Xlsx($spreadsheet);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="xlsx"');
			$writer->save('php://output');
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO, TipoUsuarioEnum::SERVIDOR_SOCIAL, TipoUsuarioEnum::VIGILANTE)))
	->execute(new ReportesProgramadoSemanaExcel);
