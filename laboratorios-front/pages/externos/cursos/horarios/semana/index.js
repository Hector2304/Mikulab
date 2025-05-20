import { mapState } from 'vuex';
import CalendarLabels from '@/utils/calendar-labels';

export default {
	middleware: 'cursoHorarioMiddleware',

	CalendarLabels,

	data() {
		return {
			loading: false,
			adding: false,

			weekDate: undefined,

			dateFrom: undefined,
			dateTo: undefined,

			horariosSelectedLab: undefined,
			laboratoriosLoading: false,
			laboratorios: [],

			schOptions: {}
		};
	},

	computed: {
		...mapState('cursoExternoStore', {
			horariosSelectedCurso: 'selectedCurso'
		}),

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
		this.startEdicion();
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

		dateDisabled(ymd, date) {
			if (date.getDay() === 0) {
				return true;
			}

			return this.$moment(ymd).isBefore(this.$moment().startOf('day'));
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

		startEdicion() {
			if (this.$route?.params?.monday && this.$route?.params?.lab) {
				this.weekDate = this.$route.params.monday;
				this.horariosSelectedLab = this.$route.params.lab;
				this.loadDates(this.weekDate);
			}
		},

		selectLab(item) {
			this.horariosSelectedLab = item;
			this.loadDates(this.dateFrom);
		},

		async loadDates(newDate) {
			if (this.loading || !newDate) {
				return;
			}

			let disponibilidad = {};
			this.$refs.schedulerWeek.reset();

			let theDate = this.$moment.tz(newDate, 'yyyy-MM-DD', 'America/Mexico_City');
			let theWeek = theDate.week();
			let theYear = theDate.year();

			this.dateFrom = this.$moment().day(1).year(theYear).week(theWeek).format('yyyy-MM-DD');
			this.dateTo = this.$moment().day(7).year(theYear).week(theWeek).format('yyyy-MM-DD');

			if (!this.horariosSelectedLab) {
				return;
			}

			this.loading = true;

			try {
				const response = await this.$axios.post('controller/reserva/disponibilidad.php', {
					dias: [1, 2, 3, 4, 5, 6],
					labIds: [this.horariosSelectedLab.saloIdSalon]
				});

				let labDisp = response.data[this.horariosSelectedLab.saloIdSalon];

				if (labDisp) {
					disponibilidad['L'] = new Set();
					disponibilidad['M'] = new Set();
					disponibilidad['MC'] = new Set();
					disponibilidad['J'] = new Set();
					disponibilidad['V'] = new Set();
					disponibilidad['S'] = new Set();
					disponibilidad['D'] = new Set();

					for (let horario of labDisp) {
						let horaIni = Number(horario.horaIni) / 100;
						let horaFin = Number(horario.horaFin) / 100;

						for (let i = horaIni; i < horaFin; i++) {
							disponibilidad[this.$getStringDayFromNumeric(horario.numDia)].add(i);
						}
					}
				}
			} catch (error) {}

			for (let day in disponibilidad) {
				if (disponibilidad[day]) {
					for (let hour of disponibilidad[day]) {
						this.$refs.schedulerWeek.selectPermanent({ value: day }, { value: hour }, { type: 'GROUP' });
					}
				}
			}

			try {
				const response = await this.$axios.post('controller/laboratorios/bloq-horario-get-week.php', {
					year: theYear,
					week: theWeek,
					labId: this.horariosSelectedLab.saloIdSalon
				});

				for (let dh of response.data) {
					this.$refs.schedulerWeek.selectPermanent(
						{ value: dh.day },
						{ value: dh.hour },
						{ type: 'BLOCK', motivo: dh.motivo }
					);
				}
			} catch (error) {}

			try {
				const response = await this.$axios.post('controller/externo/curso/asignacion-horario-get.php', {
					year: theYear,
					week: theWeek,
					labId: this.horariosSelectedLab.saloIdSalon
				});

				for (let dh of response.data) {
					for (let i = dh.hour; i < dh.end; i++) {
						if (dh.tipoGrupo === 'E' && dh.idGrupo === this.horariosSelectedCurso.cuexIdCursoExterno) {
							this.$refs.schedulerWeek.select(
								{ value: dh.day },
								{ value: i },
								{ type: 'EXTERNAL', idReservacion: dh.idReservacion }
							);
						} else {
							this.$refs.schedulerWeek.selectPermanent(
								{ value: dh.day },
								{ value: i },
								{ type: 'RESERVED' }
							);
						}
					}
				}
			} catch (error) {}

			this.loading = false;
		},

		defaultCellAction(d, h, callback) {
			if (!this.horariosSelectedLab || !this.horariosSelectedCurso) {
				return;
			}

			if (this.horariosSelectedLab.saloCupo < this.horariosSelectedCurso.cuexAlumnosInscritos) {
				this.$toastError({
					text: `Cupo (${this.horariosSelectedLab.saloCupo}) menor al número de alumnos inscritos (${this.horariosSelectedCurso.cuexAlumnosInscritos}).`,
					title: 'Error'
				});
				return;
			}

			this.agregarHorario(d, h, callback);
		},

		agregarHorario(d, h, callback) {
			if (this.adding) return;

			let theDate = this.$moment.tz(this.dateFrom, 'yyyy-MM-DD', 'America/Mexico_City');
			let theWeek = theDate.week();
			let theYear = theDate.year();

			this.adding = true;

			let payload = {
				...this.horaToIniFin(h.value),
				fecha: this.$moment()
					.day(this.$getNumericDayFromString(d.value))
					.year(theYear)
					.week(theWeek)
					.format('yyyy-MM-DD'),
				labId: this.horariosSelectedLab.saloIdSalon,
				cursoId: this.horariosSelectedCurso.cuexIdCursoExterno
			};

			this.$axios
				.post('controller/externo/curso/asignacion-horario.php', payload)
				.then((response) => {
					if (callback) {
						callback({ type: 'EXTERNAL', idReservacion: response.data.idReservacion });
					}

					this.$toastSuccess({
						text: 'Horario agregado.',
						delay: 1000
					});
				})
				.catch((error) => {})
				.then(() => {
					this.adding = false;
				});
			this.adding = false;
		},

		horaToIniFin(h) {
			const toHStr = (x) => {
				if (x < 10) return `0${x}00`;
				return `${x}00`;
			};

			return {
				horaIni: toHStr(h),
				horaFin: toHStr(h + 1)
			};
		},

		undeleteCell(d, h, p, callback) {
			let theDate = this.$moment.tz(this.dateFrom, 'yyyy-MM-DD', 'America/Mexico_City');
			let theWeek = theDate.week();
			let theYear = theDate.year();

			this.$axios
				.post('controller/externo/curso/asignacion-horario.php', {
					...this.horaToIniFin(h.value),
					fecha: this.$moment()
						.day(this.$getNumericDayFromString(d.value))
						.year(theYear)
						.week(theWeek)
						.format('yyyy-MM-DD'),
					labId: this.horariosSelectedLab.saloIdSalon,
					cursoId: this.horariosSelectedCurso.cuexIdCursoExterno
				})
				.then((response) => {
					if (p) {
						p.idReservacion = response.data.idReservacion;
					}

					callback(true);

					this.$toastSuccess({
						text: 'Horario agregado.',
						delay: 1000
					});
				})
				.catch((error) => {
					callback(false);
				});
		},

		deleteCell(d, h, p, callback) {
			this.$axios
				.delete('controller/externo/curso/asignacion-horario-delete.php', {
					data: {
						idReservacion: p?.idReservacion
					}
				})
				.then((response) => {
					callback(true);
					this.$toastSuccess({
						text: 'Horario eliminado.',
						delay: 1000
					});
				})
				.catch((error) => {
					callback(false);
				});
		}
	}
};
