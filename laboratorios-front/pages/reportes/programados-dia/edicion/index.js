import TimeLabels from '@/utils/time-labels';
import saveAs from 'file-saver';

export default {
	TimeLabels,

	middleware: 'programadoDiaEdicionMiddleware',

	data(vm) {
		return {
			loading: false,
			listado: [],
			laboratorios: {},
			observaciones: {},
			horarios: {},
			gruposE: {},
			gruposL: {},
			gruposP: {},
			gruposT: {},
			profes: {},
			detalleSelected: undefined,
			obsOptions: {
				altaEndpoint: 'controller/reportes/programado-dia/alta-observacion.php',
				bajaEndpoint: 'controller/reportes/programado-dia/baja-observacion.php'
			},
			today: vm.$moment.tz('America/Mexico_City').format('yyyy-MM-DD')
		};
	},

	computed: {
		paramReporte() {
			return this.$route?.params?.reporte;
		},

		isEditable() {
			return this.paramReporte?.reprFecha === this.today;
		},

		historicoOptions() {
			if (this.paramReporte?.reprIdReporteProgramado)
				return {
					consultaEndpoint:
						'controller/reportes/programado-dia/historico.php?reporte-id=' +
						this.paramReporte?.reprIdReporteProgramado
				};
		},

		weekDay() {
			if (!this.paramReporte?.reprFecha) return '';

			return this.$getStringDayFromNumeric(
				this.$moment.tz(this.paramReporte.reprFecha, 'yyyy-MM-DD', 'America/Mexico_City').day()
			);
		},

		formattedDay() {
			if (!this.paramReporte?.reprFecha) return '';

			return this.$moment
				.tz(this.paramReporte.reprFecha, 'yyyy-MM-DD', 'America/Mexico_City')
				.format('DD MMM yy');
		}
	},

	mounted() {
		this.load();
	},

	methods: {
		openHistorico() {
			this.$refs.reporteHistorico.show();
		},

		load() {
			if (this.loading) {
				return;
			}

			if (!this.paramReporte?.reprIdReporteProgramado) {
				return;
			}

			this.loading = true;

			this.$axios
				.get(
					`controller/reportes/programado-dia/consulta.php?reporte-id=${this.paramReporte?.reprIdReporteProgramado}`
				)
				.then((response) => {
					this.horarios = response.data.horarios;
					this.listado = response.data.detalle;
					this.laboratorios = response.data.labs;
					this.observaciones = response.data.observaciones;
					this.gruposL = response.data.gruposL;
					this.gruposP = response.data.gruposP;
					this.gruposT = response.data.gruposT;
					this.gruposE = response.data.gruposE;
					this.profes = response.data.profes;

					this.listado = this.listado.sort((a, b) => {
						let horaA = this.getHora(a.repdIdHorario);
						let horaB = this.getHora(b.repdIdHorario);

						if (horaA < horaB) {
							return -1;
						}
						if (horaA > horaB) {
							return 1;
						}

						return 0;
					});
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		},

		getLab(id) {
			return this.laboratorios[`${id}`] ?? {};
		},

		getHora(id) {
			return this.horarios[`${id}`]?.formatted ?? '';
		},

		countObs(id) {
			return this.observaciones[`${id}`]?.length ?? 0;
		},

		guardarCambios() {
			if (this.loading) {
				return;
			}

			this.loading = true;

			this.$axios
				.post(`controller/reportes/programado-dia/modificacion.php`, {
					detalle: this.listado
				})
				.then((response) => {
					this.$toastSuccess({
						text: 'Cambios guardados.'
					});
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		},

		openObservaciones(item) {
			this.detalleSelected = item;
			this.$refs.reporteObservaciones.show();
		},

		altaCallback(newObs) {
			if (this.detalleSelected) {
				if (this.observaciones[`${this.detalleSelected.repdIdDetalle}`]) {
					this.observaciones[`${this.detalleSelected.repdIdDetalle}`].unshift(newObs);
				} else {
					this.$set(this.observaciones, `${this.detalleSelected.repdIdDetalle}`, [newObs]);
				}
			}
		},

		bajaCallback(item) {
			if (this.observaciones[`${this.detalleSelected.repdIdDetalle}`]) {
				this.observaciones[`${this.detalleSelected.repdIdDetalle}`].splice(
					this.observaciones[`${this.detalleSelected.repdIdDetalle}`].indexOf(item),
					1
				);
			}
		},

		getGrupo(item) {
			if (!item.repdTipoGrupo) {
				return {};
			}

			let info;

			switch (item.repdTipoGrupo) {
				case 'E':
					info = {
						gr: this.gruposE[item.repdIdGrupo],
						pr: this.gruposE[item.repdIdGrupo]?.cuexInstructor
					};
					break;
				case 'L':
					info = {
						gr: this.gruposL[item.repdIdGrupo],
						pr: this.profes[this.gruposL[item.repdIdGrupo]?.grupIdProfesor]
					};
					break;
				case 'P':
					info = {
						gr: this.gruposP[item.repdIdGrupo],
						pr: this.profes[this.gruposP[item.repdIdGrupo]?.gruoIdProfesor]
					};
					break;
				case 'T':
					info = {
						gr: this.gruposT[item.repdIdGrupo],
						pr: this.profes[this.gruposT[item.repdIdGrupo]?.grotIdProfesor]
					};
					break;
				default:
					info = {};
			}

			info.gr = info.gr ?? {};
			info.pr = info.pr ?? {};

			return info;
		},

		descargarExcel() {
			if (this.loading) {
				return;
			}

			this.loading = true;

			let payload = {
				dia: this.formattedDay,
				list: []
			};

			for (let item of this.listado) {
				let row = {
					dia: this.weekDay,
					lab: this.getLab(item.repdIdLaboratorio).saloClave,
					horario: this.getHora(item.repdIdHorario),
					apertura: this.$moment(item.repdHoraApertura, 'HH:mm:ss').format('HH:mm'),
					asistencia: item.repdAsistencia ? '✓' : '',
					observaciones: ''
				};

				let grObj = this.getGrupo(item);

				switch (item.repdTipoGrupo) {
					case 'E':
						row.grupo = `Clave: ${grObj?.gr?.cuexClave}\nNombre: ${grObj?.gr?.cuexNombre}\nInstructor: ${grObj?.pr}`;
						break;
					case 'L':
						row.grupo = `Clave Grupo: ${grObj?.gr?.grupClave}\nPeriodo: ${grObj?.gr?.grupIdPeriodo}\nSemestre: ${grObj?.gr?.grupSemestre}\nClave Asignatura: ${grObj?.gr?.asigIdAsignatura}\nAsignatura: ${grObj?.gr?.asigNombre}\nCarrera: ${grObj?.gr?.carrNombre}\nProfesor(a): ${grObj?.pr?.nombre}`;
						break;
					case 'P':
						row.grupo = `Clave Grupo: ${grObj?.gr?.gruoClave}\nPeriodo: ${grObj?.gr?.gruoIdPeriodo}\nClave Asignatura: ${grObj?.gr?.asigIdAsignaturaP}\nAsignatura: ${grObj?.gr?.asigNombreP}\nCoordinación: ${grObj?.gr?.coorNombre}\nProfesor(a): ${grObj?.pr?.nombre}`;
						break;
					case 'T':
						row.grupo = `Clave Grupo: ${grObj?.gr?.grotClave}\nClave Módulo: ${grObj?.gr?.mootClave}\nMódulo: ${grObj?.gr?.mootNombre}\nProfesor(a): ${grObj?.pr?.nombre}`;
						break;
					default:
						row.grupo = '';
				}

				for (let obs of this.observaciones[`${item.repdIdDetalle}`] ?? []) {
					row.observaciones += `${obs.usuario}, ${this.$moment(obs.fecha, 'yyyy-MM-DD HH:mm:ss').format(
						'DD MMM yy - HH:mm:ss'
					)}:\n${obs.observacion}\n\n`;
				}

				payload.list.push(row);
			}

			this.$axios
				.post(`controller/reportes/programado-dia/excel.php`, payload, {
					responseType: 'blob'
				})
				.then((response) => {
					saveAs(
						response.data,
						`PD${this.$moment.tz('America/Mexico_City').format('yyyyMMDDHHmmss')}.xlsx`
					);
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		}
	}
};
