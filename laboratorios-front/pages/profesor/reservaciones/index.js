export default {
	data(vm) {
		return {
			reservacionesCampos: [
				{
					key: 'reseIdGrupo',
					label: 'Grupo',
					sortable: true,
					slotable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle',
					formatter(value, key, item) {
						let g = vm.getGrupo(item);
						return (
							(g.gruoClave ?? g.grotClave ?? g.grupClave ?? '') +
							(g.asigNombre ?? g.asigNombreP ?? g.mootNombre ?? '')
						);
					},
					sortByFormatted: true,
					filterByFormatted: true
				},
				{
					key: 'reseIdLaboratorio',
					label: 'Laboratorio',
					sortable: true,
					slotable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle',
					formatter(value, key, item) {
						return vm.getLab(item).saloClave ?? '';
					},
					sortByFormatted: true,
					filterByFormatted: true
				},
				{
					key: 'reseFecha',
					label: 'Fecha y hora',
					sortable: true,
					slotable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					formatter(value, key, item) {
						return item.reseFecha + item.horaIni + item.horaFin;
					},
					sortByFormatted: true,
					filterByFormatted(value, key, item) {
						return vm.$moment(item.reseFecha, 'yyyy-MM-DD').format('DD MMM yy') + ' '+ vm.formatHorario(item);
					},
				},
				{
					key: 'reseStatus',
					label: 'Estatus',
					sortable: true,
					slotable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'actions',
					label: 'Acciones',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					slotable: true
				}
			],
			reservacionesListado: [],
			reservacionesLoading: false,

			laboratorios: {},
			gruposL: {},
			gruposP: {},
			gruposT: {}
		};
	},

	mounted() {
		this.loadReservaciones();
	},

	methods: {
		loadReservaciones() {
			if (this.reservacionesLoading) {
				return;
			}

			this.reservacionesLoading = true;

			this.$axios
				.get('controller/profesor/reserva/listado.php')
				.then((response) => {
					this.reservacionesListado = response.data.reservaciones;

					if (response.data.laboratorios) {
						this.laboratorios = response.data.laboratorios;
					} else {
						this.laboratorios = {};
					}

					if (response.data.gruposL) {
						this.gruposL = response.data.gruposL;
					} else {
						this.gruposL = {};
					}

					if (response.data.gruposP) {
						this.gruposP = response.data.gruposP;
					} else {
						this.gruposP = {};
					}

					if (response.data.gruposT) {
						this.gruposT = response.data.gruposT;
					} else {
						this.gruposT = {};
					}
				})
				.catch((error) => {})
				.then(() => {
					this.reservacionesLoading = false;
				});
		},

		getGrupo(item) {
			if (item?.reseTipoGrupo === 'L') {
				return this.gruposL[item?.reseIdGrupo] ?? {};
			} else if (item?.reseTipoGrupo === 'P') {
				return this.gruposP[item?.reseIdGrupo] ?? {};
			} else if (item?.reseTipoGrupo === 'T') {
				return this.gruposT[item?.reseIdGrupo] ?? {};
			} else {
				return {};
			}
		},

		getLab(item) {
			return this.laboratorios[item?.reseIdLaboratorio] ?? {};
		},

		formatHorario(item) {
			return `${item.horaIni[0]}${item.horaIni[1]}:00 - ${item.horaFin[0]}${item.horaFin[1]}:00`;
		},

		isReservaActiva(item) {
			let today = this.$moment().startOf('day');
			let reseFecha = this.$moment(item.reseFecha);

			return !today.isAfter(reseFecha);
		},

		showDeleteButton(item) {
			let todayPlusOne = this.$moment().add(1, 'days').startOf('day');
			let reseFecha = this.$moment(item.reseFecha);

			return !todayPlusOne.isSameOrAfter(reseFecha);
		},

    cancelarReservacion(item) {
      let fechaHora = `${this.$moment(item.reseFecha, 'yyyy-MM-DD').format('DD MMM yy')} ${this.formatHorario(item)}`;
      let g = this.getGrupo(item);
      let grupo = `${g.gruoClave ?? g.grotClave ?? g.grupClave ?? ''} - ${g.asigNombre ?? g.asigNombreP ?? g.mootNombre ?? ''}`;
      let lab = this.getLab(item).saloClave ?? '';

      this.$refs.confirmationPrompt.ask({
        title: 'Cancelar reservación',
        text: `¿Realmente desea cancelar la reservación?<br/>
    <br/><span class="text-muted text-small mr-2">Laboratorio</span>${lab}
    <br/><span class="text-muted text-small mr-2">Grupo</span>${grupo}
    <br/><span class="text-muted text-small mr-2">Fecha y hora</span>${fechaHora}`,
        onConfirm: () => {
          return this.$axios
            .patch('controller/profesor/reserva/cancelacion.php', {
              id: item.reseIdReservacion
            })
            .then((response) => {
              this.$set(item, 'reseStatus', 'C');
              this.$toastSuccess({ text: 'Reservación cancelada.' });

              if (response.data === "__POPUP__") {
                this.$root.$emit('mensaje-nuevo');
                this.obtenerTotalNoLeidos?.();
              }
            })
            .catch((error) => {
              console.error('Error al cancelar', error);
            });
        }
      });
    },


    descargarPDF(item) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'http://localhost:7070/api/controller/profesor/reserva/descargarPDF.php';
      form.target = '_blank'; // Abre en nueva pestaña
      form.style.display = 'none';

      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'id';
      input.value = item.reseIdReservacion;

      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
    }


  }

};
