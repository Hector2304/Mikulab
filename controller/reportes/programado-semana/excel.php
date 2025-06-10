<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/PeriodoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/PeriodoDTO.php";

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

			$periodoString = '';

			$periodoDAO = new PeriodoDAO(LaboratoriosFCABD::getInstance());
			$periodoDTO = $periodoDAO->periodoPorFecha($jsonData->inicioSemana);
			if (!is_null($periodoDTO) && !is_null($periodoDTO->getPeriIdPeriodo())) {
				$periodoString = $periodoDTO->getPeriIdPeriodo();
			}

			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			ExcelUtils::setCenteredTextAndMerge($sheet, 'A1:P1', 'A1', 'UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO');
			ExcelUtils::setCenteredTextAndMerge($sheet, 'A2:P2', 'A2', 'FACULTAD DE CONTADURÍA Y ADMINISTRACIÓN');
			ExcelUtils::setCenteredTextAndMerge($sheet, 'A3:P3', 'A3', 'CENTRO DE INFORMÁTICA');
			ExcelUtils::setCenteredTextAndMerge($sheet, 'A4:P4', 'A4', 'SEMESTRE ' . $periodoString);
			ExcelUtils::setCenteredTextAndMerge($sheet, 'A5:P5', 'A5', 'LABORATORIO ' . $jsonData->lab);
			ExcelUtils::setCenteredTextAndMerge($sheet, 'A6:P6', 'A6', 'Programación semanal ' . $jsonData->semana);
			ExcelUtils::setBold($sheet, 'A1:A6');

			ExcelUtils::setGenericHeader($sheet, 'A8', 'DÍAS/HORAS');
			ExcelUtils::setGenericHeader($sheet, 'A9', 'LUNES');
			ExcelUtils::setGenericHeader($sheet, 'A10', 'MARTES');
			ExcelUtils::setGenericHeader($sheet, 'A11', 'MIÉRCOLES');
			ExcelUtils::setGenericHeader($sheet, 'A12', 'JUEVES');
			ExcelUtils::setGenericHeader($sheet, 'A13', 'VIERNES');
			ExcelUtils::setGenericHeader($sheet, 'A14', 'SÁBADO');
			ExcelUtils::setGenericHeader($sheet, 'B8', '07:00 - 08:00');
			ExcelUtils::setGenericHeader($sheet, 'C8', '08:00 - 09:00');
			ExcelUtils::setGenericHeader($sheet, 'D8', '09:00 - 10:00');
			ExcelUtils::setGenericHeader($sheet, 'E8', '10:00 - 11:00');
			ExcelUtils::setGenericHeader($sheet, 'F8', '11:00 - 12:00');
			ExcelUtils::setGenericHeader($sheet, 'G8', '12:00 - 13:00');
			ExcelUtils::setGenericHeader($sheet, 'H8', '13:00 - 14:00');
			ExcelUtils::setGenericHeader($sheet, 'I8', '14:00 - 15:00');
			ExcelUtils::setGenericHeader($sheet, 'J8', '15:00 - 16:00');
			ExcelUtils::setGenericHeader($sheet, 'K8', '16:00 - 17:00');
			ExcelUtils::setGenericHeader($sheet, 'L8', '17:00 - 18:00');
			ExcelUtils::setGenericHeader($sheet, 'M8', '18:00 - 19:00');
			ExcelUtils::setGenericHeader($sheet, 'N8', '19:00 - 20:00');
			ExcelUtils::setGenericHeader($sheet, 'O8', '20:00 - 21:00');
			ExcelUtils::setGenericHeader($sheet, 'P8', '21:00 - 22:00');
			ExcelUtils::setBold($sheet, 'A8:P8');
			ExcelUtils::setBold($sheet, 'A8:A14');
			ExcelUtils::setSimpleBorder($sheet, 'A9:P14');

			for ($i = 9; $i <= 14; $i++) {
				ExcelUtils::setHeight($sheet, $i, -1);
			}
			
			foreach($jsonData->cellInfo as $diaKey => $diaValue) { 
				foreach($diaValue as $horaKey => $info) { 
					$cell = $this->cellMapping($diaKey, $horaKey);
					ExcelUtils::setSingleCellWithColor($sheet, $cell, $info->text, $info->bgColor, $info->brColor);
					ExcelUtils::setFontSize($sheet, $cell, 9);
				}
			}

			ExcelUtils::setWrapText($sheet, 'A9:P14');

			ExcelUtils::setWidth($sheet, 'A', 'A', 16);
			ExcelUtils::setWidth($sheet, 'B', 'P', 20);

			$writer = new Xlsx($spreadsheet);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="xlsx"');
			$writer->save('php://output');
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}

	/**
	 * Mapea dia hora a celda en Excel
	 * Ej:
	 * 		MC17 -> Miércoles 17 horas
	 * 		Mapea a -> L11
	 */
	private $horas = array(
		'7' => 'B',
		'8' => 'C',
		'9' => 'D',
		'10' => 'E',
		'11' => 'F',
		'12' => 'G',
		'13' => 'H',
		'14' => 'I',
		'15' => 'J',
		'16' => 'K',
		'17' => 'L',
		'18' => 'M',
		'19' => 'N',
		'20' => 'O',
		'21' => 'P'
	);
	private $dias = array(
		'L' => '9',
		'M' => '10',
		'MC' => '11',
		'J' => '12',
		'V' => '13',
		'S' => '14'
	);
	private function cellMapping(string $dia, string $hora)
	{
		return $this->horas[$hora] . $this->dias[$dia];
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO, TipoUsuarioEnum::SERVIDOR_SOCIAL)))
	->execute(new ReportesProgramadoSemanaExcel);
