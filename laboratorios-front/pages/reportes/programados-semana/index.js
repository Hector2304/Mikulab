import CalendarLabels from '@/utils/calendar-labels';
import saveAs from 'file-saver';

export default {
	CalendarLabels,

	data() {
		return {
			loading: false,
			blocking: false,

			weekDate: undefined,

			dateFrom: undefined,
			dateTo: undefined,

			laboratorioSelected: undefined,
			laboratoriosLoading: false,
			laboratorios: [],

			reportData: undefined,
			reportBlocked: undefined,

			schOptions: {
				noSelectable: true
			},

			bgColors: [
				'#007bff14',
				'#6610f214',
				'#6f42c114',
				'#e83e8c14',
				'#dc354514',
				'#fd7e1414',
				'#ffc10714',
				'#28a74514',
				'#20c99714',
				'#17a2b814'
			],
			brColors: [
				'#007bff50',
				'#6610f250',
				'#6f42c150',
				'#e83e8c50',
				'#dc354550',
				'#fd7e1450',
				'#ffc10750',
				'#28a74550',
				'#20c99750',
				'#17a2b850'
			],

			bgExcelColors: [
				'e6f0fa',
				'eee7f9',
				'efebf5',
				'f9ebf1',
				'f8eaeb',
				'faf0e7',
				'faf5e6',
				'e9f3eb',
				'e8f6f2',
				'e7f3f5'
			],
			brExcelColors: [
				'acd2fc',
				'ccb1f8',
				'cfc0e9',
				'f5bfd8',
				'f1bcc1',
				'fcd3b2',
				'fce9ae',
				'b8e0c1',
				'b6ebdb',
				'b3dfe6'
			]
		};
	},

	computed: {
		semana() {
			if (!this.dateFrom || !this.dateTo) {
				return '';
			}

			return `${this.$moment(this.dateFrom, 'yyyy-MM-DD').format('DD MMM yy')} - ${this.$moment(
				this.dateTo,
				'yyyy-MM-DD'
			).format('DD MMM yy')}`;
		}
	},

	mounted() {
		this.loadLaboratorios();
	},

	methods: {
		highlightWeek(ymd, date) {
			if (!this.dateFrom || !this.dateTo) {
				return '';
			}

			if (this.$moment(ymd).isSameOrAfter(this.dateFrom) && this.$moment(ymd).isSameOrBefore(this.dateTo)) {
				return 'table-primary';
			}

			return '';
		},

		loadLaboratorios() {
			if (this.laboratoriosLoading) {
				return;
			}

			this.laboratoriosLoading = true;

			this.$axios
				.get('controller/laboratorios/listado.php')
				.then((response) => {
					this.laboratorios = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.laboratoriosLoading = false;
				});
		},

		selectLab(item) {
			this.laboratorioSelected = item;
			this.loadWeek(this.dateFrom);
		},

		async loadWeek(newDate) {
			if (this.loading || !newDate) {
				return;
			}

			this.$refs.schedulerWeek.reset();

			let theDate = this.$moment.tz(newDate, 'yyyy-MM-DD', 'America/Mexico_City');
			let theWeek = theDate.week();
			let theYear = theDate.year();

			this.dateFrom = this.$moment().day(1).year(theYear).week(theWeek).format('yyyy-MM-DD');
			this.dateTo = this.$moment().day(7).year(theYear).week(theWeek).format('yyyy-MM-DD');

			if (!this.laboratorioSelected) {
				return;
			}

			this.loading = true;

			try {
				const response = await this.$axios.post('controller/laboratorios/bloq-horario-get-week.php', {
					year: theYear,
					week: theWeek,
					labId: this.laboratorioSelected.saloIdSalon
				});

				this.reportBlocked = response.data;

				for (let dh of response.data) {
					this.$refs.schedulerWeek.selectPermanent(
						{ value: dh.day },
						{ value: dh.hour },
						{ type: 'BLOCK', motivo: dh.motivo }
					);
				}
			} catch (error) {}

			try {
				let colorIndex = 0;

				const { data } = await this.$axios.post(
					`controller/reportes/programado-semana/consulta.php?from=${this.dateFrom}&to=${this.dateTo}&lab-id=${this.laboratorioSelected.saloIdSalon}`
				);

				this.reportData = data;

				for (let reserva of data.reservas) {
					let info;

					switch (reserva.reseTipoGrupo) {
						case 'E':
							info = {
								type: 'E',
								gr: data.gruposE[reserva.reseIdGrupo],
								pr: data.gruposE[reserva.reseIdGrupo]?.cuexInstructor
							};
							break;
						case 'L':
							info = {
								type: 'L',
								gr: data.gruposL[reserva.reseIdGrupo],
								pr: data.profes[data.gruposL[reserva.reseIdGrupo]?.grupIdProfesor]
							};
							break;
						case 'P':
							info = {
								type: 'P',
								gr: data.gruposP[reserva.reseIdGrupo],
								pr: data.profes[data.gruposP[reserva.reseIdGrupo]?.gruoIdProfesor]
							};
							break;
						case 'T':
							info = {
								type: 'T',
								gr: data.gruposT[reserva.reseIdGrupo],
								pr: data.profes[data.gruposT[reserva.reseIdGrupo]?.grotIdProfesor]
							};
							break;
						default:
							info = {};
					}

					info.gr = info.gr ?? {};
					info.pr = info.pr ?? {};

					info.bgColor = this.bgColors[colorIndex];
					info.brColor = this.brColors[colorIndex];
					colorIndex = (colorIndex + 1) % this.bgColors.length;

					let horaIni = Number(reserva.horaIni) / 100;
					let horaFin = Number(reserva.horaFin) / 100;
					let dayValue = this.$getStringDayFromNumeric(
						this.$moment.tz(reserva.reseFecha, 'yyyy-MM-DD', 'America/Mexico_City').day()
					);

					for (let i = horaIni; i < horaFin; i++) {
						this.$refs.schedulerWeek.selectPermanent(
							{ value: dayValue },
							{ value: i },
							{ type: 'REPORT', info: info, isSmall: true }
						);
					}
				}

				for (let lg of data.labGrupos) {
					let info;

					switch (lg.tipoGrupo) {
						case 'E':
							info = {
								type: 'E',
								gr: data.gruposE[lg.idGrupo],
								pr: data.gruposE[lg.idGrupo]?.cuexInstructor
							};
							break;
						case 'L':
							info = {
								type: 'L',
								gr: data.gruposL[lg.idGrupo],
								pr: data.profes[data.gruposL[lg.idGrupo]?.grupIdProfesor]
							};
							break;
						case 'P':
							info = {
								type: 'P',
								gr: data.gruposP[lg.idGrupo],
								pr: data.profes[data.gruposP[lg.idGrupo]?.gruoIdProfesor]
							};
							break;
						case 'T':
							info = {
								type: 'T',
								gr: data.gruposT[lg.idGrupo],
								pr: data.profes[data.gruposT[lg.idGrupo]?.grotIdProfesor]
							};
							break;
						default:
							info = {};
					}

					info.gr = info.gr ?? {};
					info.pr = info.pr ?? {};

					info.bgColor = this.bgColors[colorIndex];
					colorIndex = (colorIndex + 1) % this.bgColors.length;

					let horaIni = Number(lg.horaIni) / 100;
					let horaFin = Number(lg.horaFin) / 100;
					let dayValue = this.$getStringDayFromNumeric(lg.idDia);

					for (let i = horaIni; i < horaFin; i++) {
						this.$refs.schedulerWeek.selectPermanent(
							{ value: dayValue },
							{ value: i },
							{ type: 'REPORT', info: info, isSmall: true }
						);
					}
				}
			} catch (error) {}

			this.loading = false;
		},

		descargarExcel() {
			if (this.loading || !this.reportData) {
				return;
			}

			this.loading = true;

			let payload = {
				lab: this.laboratorioSelected?.saloClave,
				semana: this.semana,
				inicioSemana: this.dateFrom,
				cellInfo: {}
			};

			for (let dh of this.reportBlocked) {
				if (!payload.cellInfo[`${dh.day}`]) {
					payload.cellInfo[`${dh.day}`] = {};
				}
				payload.cellInfo[`${dh.day}`][`${dh.hour}`] = {
					text: dh.motivo,
					bgColor: 'ececec'
				};
			}

			const data = this.reportData;
			let colorIndex = 0;

			for (let reserva of data.reservas) {
				let info = {};
				let gr;
				let pr;

				switch (reserva.reseTipoGrupo) {
					case 'E':
						gr = data.gruposE[reserva.reseIdGrupo];
						pr = data.gruposE[reserva.reseIdGrupo]?.cuexInstructor;
						info.text = `Clave: ${gr?.cuexClave}\nNombre: ${gr?.cuexNombre}\nInstructor: ${pr}`;
						break;
					case 'L':
						gr = data.gruposL[reserva.reseIdGrupo];
						pr = data.profes[data.gruposL[reserva.reseIdGrupo]?.grupIdProfesor];
						info.text = `Clave Grupo: ${gr?.grupClave}\nPeriodo: ${gr?.grupIdPeriodo}\nSemestre: ${gr?.grupSemestre}\nClave Asignatura: ${gr?.asigIdAsignatura}\nAsignatura: ${gr?.asigNombre}\nCarrera: ${gr?.carrNombre}\nProfesor(a): ${pr?.nombre}`;
						break;
					case 'P':
						gr = data.gruposP[reserva.reseIdGrupo];
						pr = data.profes[data.gruposP[reserva.reseIdGrupo]?.gruoIdProfesor];
						info.text = `Clave Grupo: ${gr?.gruoClave}\nPeriodo: ${gr?.gruoIdPeriodo}\nClave Asignatura: ${gr?.asigIdAsignaturaP}\nAsignatura: ${gr?.asigNombreP}\nCoordinación: ${gr?.coorNombre}\nProfesor(a): ${pr?.nombre}`;
						break;
					case 'T':
						gr = data.gruposT[reserva.reseIdGrupo];
						pr = data.profes[data.gruposT[reserva.reseIdGrupo]?.grotIdProfesor];
						info.text = `Clave Grupo: ${gr?.grotClave}\nClave Módulo: ${gr?.mootClave}\nMódulo: ${gr?.mootNombre}\nProfesor(a): ${pr?.nombre}`;
						break;
					default:
						info = {};
				}

				info.bgColor = this.bgExcelColors[colorIndex];
				info.brColor = this.brExcelColors[colorIndex];
				colorIndex = (colorIndex + 1) % this.bgColors.length;

				let horaIni = Number(reserva.horaIni) / 100;
				let horaFin = Number(reserva.horaFin) / 100;
				let dayValue = this.$getStringDayFromNumeric(
					this.$moment.tz(reserva.reseFecha, 'yyyy-MM-DD', 'America/Mexico_City').day()
				);

				for (let i = horaIni; i < horaFin; i++) {
					if (!payload.cellInfo[`${dayValue}`]) {
						payload.cellInfo[`${dayValue}`] = {};
					}
					payload.cellInfo[`${dayValue}`][`${i}`] = info;
				}
			}

			for (let lg of data.labGrupos) {
				let info = {};
				let gr;
				let pr;

				switch (lg.tipoGrupo) {
					case 'E':
						gr = data.gruposE[lg.idGrupo];
						pr = data.gruposE[lg.idGrupo]?.cuexInstructor;
						info.text = `Clave: ${gr?.cuexClave}\nNombre: ${gr?.cuexNombre}\nInstructor: ${pr}`;
						break;
					case 'L':
						gr = data.gruposL[lg.idGrupo];
						pr = data.profes[data.gruposL[lg.idGrupo]?.grupIdProfesor];
						info.text = `Clave Grupo: ${gr?.grupClave}\nPeriodo: ${gr?.grupIdPeriodo}\nSemestre: ${gr?.grupSemestre}\nClave Asignatura: ${gr?.asigIdAsignatura}\nAsignatura: ${gr?.asigNombre}\nCarrera: ${gr?.carrNombre}\nProfesor(a): ${pr?.nombre}`;
						break;
					case 'P':
						gr = data.gruposP[lg.idGrupo];
						pr = data.profes[data.gruposP[lg.idGrupo]?.gruoIdProfesor];
						info.text = `Clave Grupo: ${gr?.gruoClave}\nPeriodo: ${gr?.gruoIdPeriodo}\nClave Asignatura: ${gr?.asigIdAsignaturaP}\nAsignatura: ${gr?.asigNombreP}\nCoordinación: ${gr?.coorNombre}\nProfesor(a): ${pr?.nombre}`;
						break;
					case 'T':
						gr = data.gruposT[lg.idGrupo];
						pr = data.profes[data.gruposT[lg.idGrupo]?.grotIdProfesor];
						info.text = `Clave Grupo: ${gr?.grotClave}\nClave Módulo: ${gr?.mootClave}\nMódulo: ${gr?.mootNombre}\nProfesor(a): ${pr?.nombre}`;
						break;
					default:
						info = {};
				}

				info.bgColor = this.bgExcelColors[colorIndex];
				colorIndex = (colorIndex + 1) % this.bgColors.length;

				let horaIni = Number(lg.horaIni) / 100;
				let horaFin = Number(lg.horaFin) / 100;
				let dayValue = this.$getStringDayFromNumeric(lg.idDia);

				for (let i = horaIni; i < horaFin; i++) {
					if (!payload.cellInfo[`${dayValue}`]) {
						payload.cellInfo[`${dayValue}`] = {};
					}
					payload.cellInfo[`${dayValue}`][`${i}`] = info;
				}
			}

			this.$axios
				.post(`controller/reportes/programado-semana/excel.php`, payload, {
					responseType: 'blob'
				})
				.then((response) => {
					saveAs(
						response.data,
						`PS${this.$moment.tz('America/Mexico_City').format('yyyyMMDDHHmmss')}.xlsx`
					);
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		}
	}
};
