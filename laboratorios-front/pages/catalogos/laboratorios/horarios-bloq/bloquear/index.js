import formsMixin from '@/mixins/formsMixin';
import { required, maxLength } from 'vuelidate/lib/validators';
import { mapState } from 'vuex';
import CalendarLabels from '@/utils/calendar-labels';

export default {
	middleware: 'labHorariosMiddleware',

	CalendarLabels,

	mixins: [formsMixin],

	validations: {
		bloqForm: {
			date: { required },
			motivo: { maxLength: maxLength(255) }
		}
	},

	data() {
		return {
			loading: false,
			blocking: false,

			weekDate: undefined,

			bloqForm: {
				date: undefined,
				day: undefined,
				hour: undefined,
				motivo: undefined,
				cellCallback: undefined
			},

			dateFrom: undefined,
			dateTo: undefined,

			schOptions: {}
		};
	},

	computed: {
		...mapState('labHorariosStore', {
			horariosSelectedLab: 'selectedLab'
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

		startEdicion() {
			if (this.$route?.params?.monday) {
				this.weekDate = this.$route.params.monday;
				this.loadBloqueo(this.weekDate);
			}
		},

		async loadBloqueo(newDate) {
			if (this.loading) {
				return;
			}

			this.loading = true;

			let disponibilidad = {};
			this.$refs.schedulerWeek.reset();

			let theDate = this.$moment.tz(newDate, 'yyyy-MM-DD', 'America/Mexico_City');
			let theWeek = theDate.week();
			let theYear = theDate.year();

			this.dateFrom = this.$moment().day(1).year(theYear).week(theWeek).format('yyyy-MM-DD');
			this.dateTo = this.$moment().day(7).year(theYear).week(theWeek).format('yyyy-MM-DD');

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
					this.$refs.schedulerWeek.select(
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
            this.$refs.schedulerWeek.selectPermanent(
              { value: dh.day },
              { value: i },
              {
                type: 'RESERVED',
                motivo: dh.motivo,
                reseIdReservacion: dh.idReservacion // ← cambio aquí
              }
            );
          }
        }

      } catch (error) {}

      // 🔹 Clases predeterminadas desde DAO
      try {
        const response = await this.$axios.post(
          'http://localhost:7070/api/controller/reserva/predeterminadasPorSemana.php',
          {
            year: theYear,
            week: theWeek,
            labId: this.horariosSelectedLab.saloIdSalon

          },
          {
            headers: {
              'Content-Type': 'application/json'
            },
            withCredentials: true
          }
        );

        const todas = response.data.clases || [];
        console.log(`📦 Se recibieron ${todas.length} clases predeterminadas en total`);

        for (const clase of todas) {
          const esDelLab = clase.lab_id == this.horariosSelectedLab.saloIdSalon;


          if (!esDelLab) continue;

          const diaClase = Number(this.$moment(clase.fecha).isoWeekday()); // 1 = lunes
          const horaIni = Number(clase.hora_ini) / 100;
          const horaFin = Number(clase.hora_fin) / 100;
          const diaString = this.$getStringDayFromNumeric(diaClase);

          for (let i = horaIni; i < horaFin; i++) {

            this.$refs.schedulerWeek.selectPermanent(
              { value: diaString },
              { value: i },
              {
                type: 'DEFAULT',
                motivo: 'Clase predeterminada'
              }
            );
          }

        }
      } catch (error) {
        console.error('❌ Error al cargar clases predeterminadas:', error);
      }


      this.loading = false;
		},

    defaultCellAction(d, h, p, callback) {
      const tipo = p?.type;

      if (tipo === 'RESERVED') {
        this.$bvModal.msgBoxConfirm(
          'Este horario ya está reservado por un profesor. Si decides bloquearlo, la reserva será cancelada. ¿Deseas continuar?',
          {
            title: 'Conflicto de reserva',
            size: 'md',
            buttonSize: 'md',
            okVariant: 'danger',
            okTitle: 'Sí, bloquear',
            cancelTitle: 'Cancelar',
            cancelVariant: 'secondary',
            footerClass: 'p-2',
            hideHeaderClose: false,
            centered: true
          }
        ).then(async (value) => {
          if (value) {
            // 1. Cancelar la reserva completa
            if (p?.reseIdReservacion) {
              try {
                await this.$axios.patch('controller/profesor/reserva/cancelacion.php', {
                  id: p.reseIdReservacion
                });

                this.$toastSuccess({ text: 'Reservación cancelada.' });

                // 2. Eliminar todas las celdas que tienen ese reseIdReservacion
                const keysToDelete = Object.keys(this.$refs.schedulerWeek.permanent).filter(key =>
                  this.$refs.schedulerWeek.permanent[key]?.payload?.reseIdReservacion === p.reseIdReservacion
                );

                for (const key of keysToDelete) {
                  this.$delete(this.$refs.schedulerWeek.permanent, key);
                }

              } catch (error) {
                this.$toastError({ text: 'Error al cancelar la reservación.' });
                return;
              }
            }

            // 3. Continuar con el flujo normal de bloqueo
            let theDate = this.$moment.tz(this.dateFrom, 'yyyy-MM-DD', 'America/Mexico_City');
            let theWeek = theDate.week();
            let theYear = theDate.year();

            this.bloqForm.date = this.$moment()
              .day(this.$getNumericDayFromString(d.value))
              .year(theYear)
              .week(theWeek)
              .format('yyyy-MM-DD');

            this.bloqForm.day = d;
            this.bloqForm.hour = h;
            this.bloqForm.cellCallback = callback;

            this.$bvModal.show('add-new-bloq');
          }
        });

        return;
      }

      // Si no es RESERVED, sigue como siempre
      let theDate = this.$moment.tz(this.dateFrom, 'yyyy-MM-DD', 'America/Mexico_City');
      let theWeek = theDate.week();
      let theYear = theDate.year();

      this.bloqForm.date = this.$moment()
        .day(this.$getNumericDayFromString(d.value))
        .year(theYear)
        .week(theWeek)
        .format('yyyy-MM-DD');

      this.bloqForm.day = d;
      this.bloqForm.hour = h;
      this.bloqForm.cellCallback = callback;

      this.$bvModal.show('add-new-bloq');
    },

		agregarBloq(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.blocking) return;

			this.$v.bloqForm.$touch();
			if (this.$v.bloqForm.$anyError) {
				return;
			}

			this.blocking = true;

			let payload = {
				fecha: this.bloqForm.date,
				hora: this.bloqForm.hour.value,
				labId: this.horariosSelectedLab.saloIdSalon,
				motivo: this.bloqForm.motivo
			};

			this.$axios
				.post('controller/laboratorios/bloq-horario.php', payload)
				.then((response) => {
					if (this.bloqForm.cellCallback) {
						this.bloqForm.cellCallback({ type: 'BLOCK', motivo: payload.motivo });
					}

					this.$bvModal.hide('add-new-bloq');

					this.$toastSuccess({
						text: 'Horario bloqueado.',
						delay: 1000
					});
				})
				.catch((error) => {})
				.then(() => {
					this.blocking = false;
				});
		},

		undeleteCell(d, h, p, callback) {
			let theDate = this.$moment.tz(this.dateFrom, 'yyyy-MM-DD', 'America/Mexico_City');
			let theWeek = theDate.week();
			let theYear = theDate.year();

			this.$axios
				.post('controller/laboratorios/bloq-horario.php', {
					fecha: this.$moment()
						.day(this.$getNumericDayFromString(d.value))
						.year(theYear)
						.week(theWeek)
						.format('yyyy-MM-DD'),
					hora: h.value,
					labId: this.horariosSelectedLab.saloIdSalon,
					motivo: p?.motivo
				})
				.then((response) => {
					callback(true);
					this.$toastSuccess({
						text: 'Horario bloqueado.',
						delay: 1000
					});
				})
				.catch((error) => {
					callback(false);
				});
		},

		deleteCell(d, h, p, callback) {
			let theDate = this.$moment.tz(this.dateFrom, 'yyyy-MM-DD', 'America/Mexico_City');
			let theWeek = theDate.week();
			let theYear = theDate.year();

			this.$axios
				.delete('controller/laboratorios/bloq-horario-delete-one.php', {
					data: {
						fecha: this.$moment()
							.day(this.$getNumericDayFromString(d.value))
							.year(theYear)
							.week(theWeek)
							.format('yyyy-MM-DD'),
						hora: h.value,
						labId: this.horariosSelectedLab.saloIdSalon
					}
				})
				.then((response) => {
					callback(true);
					this.$toastSuccess({
						text: 'Horario desbloqueado.',
						delay: 1000
					});
				})
				.catch((error) => {
					callback(false);
				});
		}
	}
};
