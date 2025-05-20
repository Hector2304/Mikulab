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

			ExcelUtils::setCenteredTextAndMerge($sheet, 'A1:M1', 'A1', 'BITÁCORA DE ' . $jsonData->tipoBitacora . ' ' . $jsonData->tipoLab . ' DE LABORATORIOS');
			ExcelUtils::setBold($sheet, 'A1');

			ExcelUtils::setSingleCellWithColor($sheet, 'L2', 'FECHA:');
			ExcelUtils::setSingleCellWithColor($sheet, 'M2', $jsonData->dia);
			ExcelUtils::setVH($sheet, 'L2', 'center', 'right');
			ExcelUtils::setVH($sheet, 'M2');
			ExcelUtils::setBold($sheet, 'M2');

			ExcelUtils::setCenteredTextAndMerge($sheet, 'A3:D3', 'A3', 'PERSONAL DE LABORATORIOS:');
			ExcelUtils::setVH($sheet, 'A3', 'center', 'right');
			ExcelUtils::setBottomMediumBorder($sheet, 'E3:I3');
			
			ExcelUtils::setSingleCellWithColor($sheet, 'K3', 'FIRMA:');
			ExcelUtils::setVH($sheet, 'K3', 'center', 'right');
			ExcelUtils::setBottomMediumBorder($sheet, 'L3:M3');

			ExcelUtils::setSingleCellWithColor($sheet, 'A5', 'LAB');
			ExcelUtils::setSingleCellWithColor($sheet, 'B5', 'MONITOR');
			ExcelUtils::setSingleCellWithColor($sheet, 'C5', 'CPU');
			ExcelUtils::setSingleCellWithColor($sheet, 'D5', 'TECLADO');
			ExcelUtils::setSingleCellWithColor($sheet, 'E5', 'MOUSE');
			ExcelUtils::setSingleCellWithColor($sheet, 'F5', 'VIDEO PROYECTOR');
			ExcelUtils::setSingleCellWithColor($sheet, 'G5', 'CABLE D.PORT');
			ExcelUtils::setSingleCellWithColor($sheet, 'H5', 'CONTROL CAÑON');
			ExcelUtils::setSingleCellWithColor($sheet, 'I5', 'CONTROL AIRE');
			ExcelUtils::setSingleCellWithColor($sheet, 'J5', 'HORA DE APERTURA');
			ExcelUtils::setSingleCellWithColor($sheet, 'K5', 'HORA DE CIERRE');
			ExcelUtils::setSingleCellWithColor($sheet, 'L5', 'VIGILANTE');
			ExcelUtils::setSingleCellWithColor($sheet, 'M5', 'OBSERVACIONES');
			ExcelUtils::setVH($sheet, 'A5:M5');
			ExcelUtils::setBold($sheet, 'A5:M5');
			ExcelUtils::setFontSize($sheet, 'A5:M5', 9);
			ExcelUtils::setMediumBorder($sheet, 'A5:M5');
			ExcelUtils::setHeight($sheet, 5, -1);
			ExcelUtils::setWrapText($sheet, 'A5:M5');

			$i = 6;
			foreach ($jsonData->list as $row) {
				ExcelUtils::setSingleCellWithColor($sheet, 'A' . $i, $row->lab);
				ExcelUtils::setSingleCellWithColor($sheet, 'B' . $i, $row->monitor);
				ExcelUtils::setSingleCellWithColor($sheet, 'C' . $i, $row->cpu);
				ExcelUtils::setSingleCellWithColor($sheet, 'D' . $i, $row->teclado);
				ExcelUtils::setSingleCellWithColor($sheet, 'E' . $i, $row->mouse);
				ExcelUtils::setSingleCellWithColor($sheet, 'F' . $i, $row->proyector);
				ExcelUtils::setSingleCellWithColor($sheet, 'G' . $i, $row->dport);
				ExcelUtils::setSingleCellWithColor($sheet, 'H' . $i, $row->canon);
				ExcelUtils::setSingleCellWithColor($sheet, 'I' . $i, $row->aire);
				ExcelUtils::setSingleCellWithColor($sheet, 'J' . $i, $row->apertura);
				ExcelUtils::setSingleCellWithColor($sheet, 'K' . $i, $row->cierre);
				ExcelUtils::setSingleCellWithColor($sheet, 'L' . $i, $row->vigilante);
				ExcelUtils::setSingleCellWithColor($sheet, 'M' . $i, $row->observaciones);
				ExcelUtils::setVHCenter($sheet, 'A' . $i . ':K' . $i);
				ExcelUtils::setVHCenter($sheet, 'L' . $i . ':M' . $i, 'center', 'left');
				ExcelUtils::setFontSize($sheet, 'A' . $i . ':M' . $i, 9);
				ExcelUtils::setHeight($sheet, $i, -1);
				ExcelUtils::setWrapText($sheet, 'A' . $i . ':M' . $i);
				ExcelUtils::setSimpleBorder($sheet, 'A' . $i . ':M' . $i);
				$i++;
			}

			$i--;
			ExcelUtils::setBold($sheet, 'A6:A' . $i);
			ExcelUtils::setMediumBorder($sheet, 'A6:A' . $i);
			ExcelUtils::setRightMediumBorder($sheet, 'M6:M' . $i);
			ExcelUtils::setBottomMediumBorder($sheet, 'A' . $i . ':M' . $i);

			ExcelUtils::setWidth($sheet, 'A', 'I', 10);
			ExcelUtils::setWidth($sheet, 'J', 'K', 20);
			ExcelUtils::setWidth($sheet, 'L', 'L', 20);
			ExcelUtils::setWidth($sheet, 'M', 'M', 30);

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

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO)))
	->execute(new ReportesProgramadoSemanaExcel);
