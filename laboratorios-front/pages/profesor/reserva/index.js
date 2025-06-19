import CalendarLabels from '@/utils/calendar-labels';

export default {
	CalendarLabels,

	data() {
		return {
			selectedDate: undefined,
			selectedHour: undefined,

			isSabado: false,
			disponibilidad: undefined,
			disponibilidadLoading: false,

			softwareLoading: false,
			softwareListado: [],
      softwareSeleccionado: [],
      clasesPredeterminadas: [],


      grupoSelected: undefined,
			laboratorioSelected: undefined,

			gruposLoading: false,
			gruposVisibles: true,

			laboratoriosLoading: false,
			laboratoriosVisibles: true,

			resumenVisible: true,

			gruposCampos: [
				{
					key: 'selected',
					label: '',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					slotable: true
				},
				{
					key: 'grupClave',
					label: 'Clave Grupo',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'asigIdAsignatura',
					label: 'Clave Asignatura',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'asigNombre',
					label: 'Asignatura',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				},
				{
					key: 'grupSemestre',
					label: 'Semestre',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'grupIdPeriodo',
					label: 'Periodo',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'carrNombre',
					label: 'Carrera',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				},
        /*
				{
					key: 'grupAlumnosInscritos',
					label: 'Alumnos Inscritos',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				}
				*/
			],
			gruposPCampos: [
				{
					key: 'selected',
					label: '',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					slotable: true
				},
				{
					key: 'gruoClave',
					label: 'Clave Grupo',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'asigIdAsignaturaP',
					label: 'Clave Asignatura',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'asigNombreP',
					label: 'Asignatura',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				},
				{
					key: 'gruoIdPeriodo',
					label: 'Periodo',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'coorNombre',
					label: 'Coordinación',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				},
				{
					key: 'gruoAlumnosInscritos',
					label: 'Alumnos Inscritos',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				}
			],
			gruposOTCampos: [
				{
					key: 'selected',
					label: '',
					class: 'text-center align-middle',
					slotable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'grotClave',
					label: 'Clave Grupo',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'mootClave',
					label: 'Clave Módulo',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'mootNombre',
					label: 'Módulo',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle'
				},
				{
					key: 'grotCupo',
					label: 'Alumnos Inscritos',
					sortable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				}
			],

			grupos: [],
			gruposP: [],
			gruposOT: [],

			laboratoriosCampos: [
				{
					key: 'lab',
					label: 'Laboratorio',
					slotable: true,
					thClass: 'text-center align-middle',
					tdClass: 'align-middle',
					sortable: true,
					formatter(value, key, item) {
						return item.saloClave;
					},
					sortByFormatted: true
				},
				{
					key: 'software',
					label: 'Software',
					slotable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				},
				{
					key: 'horario',
					label: 'Horario',
					slotable: true,
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle'
				}
			],
			laboratorios: [],
			laboratoriosSoftware: {},

    };
	},

	computed: {
		softwareMapa() {
			let mapa = {};

			for (let sw of this.softwareListado) {
				mapa[sw.softIdSoftware] = {
					...sw
				};
			}

			return mapa;
		},

    numHorasSeleccionables() {
      const carrera = this.grupoSelected?.carrNombre;

      if (carrera === 'Negocios Internacionales') {
        return 3;
      }

      if (this.grupoSelected?.grupIdGrupo) {
        return 2;
      } else if (this.grupoSelected?.gruoIdGrupoP) {
        return 3;
      } else if (this.grupoSelected?.grotIdGrupoOt) {
        return 5;
      }

      return 0;
    },


    laboratoriosFiltered() {
      if (!this.grupoSelected) return [];

      const softwareFilters = this.softwareListado
        .filter((s) => s.selected)
        .map((s) => s.softIdSoftware);

      const esPermitidoPorCupo = (lab) => {
        if (this.grupoSelected.carrNombre === 'Informática') {
          return [43, 37, 30, 36].includes(lab.saloCupo);
        } else {
          return ![43, 37, 30, 36].includes(lab.saloCupo);
        }
      };

      return this.laboratorios.filter((lab) => {
        if (!esPermitidoPorCupo(lab)) return false;

        if (softwareFilters.length === 0) return true;

        const asociados = this.laboratoriosSoftware[lab.saloIdSalon];
        if (!asociados) return false;

        return softwareFilters.every((id) => asociados.includes(id));
      });
    },



    selectedHourTo() {
			if (!this.selectedHour) {
				return;
			}

			let current = this.selectedHour;

			while ((current = current.next)) {
				if (!current.selected) {
					break;
				}
			}

			return current?.hora;
		}
	},
  watch: {
    softwareSeleccionado(nuevos) {
      const seleccionadosIds = nuevos.map(s => s.softIdSoftware);
      this.softwareListado = this.softwareListado.map(sw => ({
        ...sw,
        selected: seleccionadosIds.includes(sw.softIdSoftware)
      }));
    }
  },


  mounted() {
		this.loadGrupos();
		this.loadLaboratorios();
		this.loadSoftwares();
		this.loadAsocLabSoft();
    this.loadClasesPredeterminadas();
	},

	methods: {
		dateDisabled(ymd, date) {
			if (date.getDay() === 0) {
				return true;
			}

			return this.$moment(ymd).isSameOrBefore(this.$moment().add(7, 'days').startOf('day'));
		},

		tbodyTrClass(item) {
			let classes = ['text-small'];

			if (item?.selected) {
				classes.push('b-table-row-selected', 'table-primary', 'text-primary');
			}

			return classes;
		},
    seleccionarGrupo(item) {
      this.$nextTick(() => {
        this.$refs.reservaListadoGrupos.clearSelected();
        this.$refs.reservaListadoGruposP.clearSelected();
        this.$refs.reservaListadoGruposOT.clearSelected();
      });

      this.deselectHoraAndLaboratorio();

      if (this.grupoSelected === item) {
        this.$set(item, 'selected', false);
        this.grupoSelected = undefined;
        // Al quitar selección, mostrar labs sin restricción info
        this.laboratoriosFiltrados = this.laboratorios.filter(
          lab => ![43, 37, 30, 36].includes(lab.saloCupo)
        );
        return;
      }

      if (this.grupoSelected) {
        this.$set(this.grupoSelected, 'selected', false);
      }

      this.$set(item, 'selected', true);
      this.grupoSelected = item;

      // Aplica filtro dependiendo la carrera
      if (item.carrNombre === 'Informática') {
        this.laboratoriosFiltrados = this.laboratorios.filter(
          lab => [43, 37, 30, 36].includes(lab.saloCupo)
        );
      } else {
        this.laboratoriosFiltrados = this.laboratorios.filter(
          lab => ![43, 37, 30, 36].includes(lab.saloCupo)
        );
      }
    },


    loadSoftwares() {
      if (this.softwareLoading) {
        return;
      }

      this.softwareLoading = true;

      this.$axios
        .get('controller/software/listado.php')
        .then((response) => {
          this.softwareListado = response.data.map(sw => ({
            ...sw,
            selected: false
          }));
        })
        .catch((error) => {})
        .then(() => {
          this.softwareLoading = false;
        });
    },


		loadGrupos() {
			if (this.gruposLoading) {
				return;
			}

			this.gruposLoading = true;

			this.$axios
				.get('controller/profesor/grupos.php')
				.then((response) => {
					this.grupos = response.data.grupos;
					this.gruposP = response.data.gruposP;
					this.gruposOT = response.data.gruposOT;
				})
				.catch((error) => {})
				.then(() => {
					this.gruposLoading = false;
				});
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

		loadAsocLabSoft() {
			this.$axios
				.get('controller/laboratorios/laboratorios-software.php?by-lab=true')
				.then((response) => {
					this.laboratoriosSoftware = response.data;
          console.log(" laboratoriosSoftware:", this.laboratoriosSoftware);



				})
				.catch((error) => {})
				.then(() => {});
		},

		asocLabSoft(item) {
			let labSoftList = this.laboratoriosSoftware[item?.saloIdSalon];

			if (!labSoftList) {
				return;
			}

			let microList = [];

			for (let i of labSoftList) {
				if (this.softwareMapa[i]) {
					microList.push(this.softwareMapa[i]);
				}
			}

			return microList;
		},

    selectHora({ hora, callback }, lab) {
      if (this.selectedHour) {
        this.deselectHora(this.selectedHour);
      }

      const size = this.numHorasSeleccionables;
      const base = 7; // hora base de inicio

      // Validar que la hora sea un inicio alineado para ese tamaño
      if ((hora.hora - base) % size !== 0) {
        return;
      }

      let current = hora;
      let seleccionable = 0;

      while (current != null && seleccionable < size) {
        if (!current.disabled) {
          seleccionable++;
        } else {
          break;
        }
        current = current.next;
      }

      if (seleccionable < size) {
        return;
      }

      if (callback) {
        callback();
      }

      this.laboratorioSelected = lab;
      this.selectedHour = hora;
    }
,

    deselectHoraAndLaboratorio() {
			this.laboratorioSelected = undefined;
			this.deselectHora(this.selectedHour);
		},

		deselectHora(hObj) {
			if (!hObj) {
				return;
			}

			if (!hObj.selected) {
				return;
			}

			this.$set(hObj, 'selected', false);

			this.deselectHora(hObj.next);
		},

    async loadDisponibilidad(newDate) {
      if (this.disponibilidadLoading) {
        return;
      }

      this.disponibilidadLoading = true;

      this.deselectHoraAndLaboratorio();

      let diaSemana = this.$moment.tz(newDate, 'yyyy-MM-DD', 'America/Mexico_City').day();
      this.isSabado = diaSemana === 6;

      let labIds = this.laboratorios.map((lab) => lab.saloIdSalon);
      let hrsNoDisp = {};

      // 🔹 Reservas reales
      try {
        const response = await this.$axios.post('controller/reserva/disponibilidad.php', {
          dias: [diaSemana],
          labIds: labIds
        });

        for (let id in response.data) {
          hrsNoDisp[id] = new Set();

          for (let horario of response.data[id]) {
            let horaIni = Number(horario.horaIni) / 100;
            let horaFin = Number(horario.horaFin) / 100;

            for (let i = horaIni; i < horaFin; i++) {
              hrsNoDisp[id].add(i);
            }
          }
        }
      } catch (error) {}

      // 🔹 Horarios bloqueados por admin
      try {
        const response = await this.$axios.post('controller/laboratorios/bloq-horario-get-day.php', {
          fecha: newDate,
          labIds: labIds
        });

        for (let horario of response.data) {
          let idStr = `${horario.labId}`;

          if (!hrsNoDisp[idStr]) {
            hrsNoDisp[idStr] = new Set();
          }

          hrsNoDisp[idStr].add(horario.hour);
        }
      } catch (error) {}

      // 🔹 Cursos externos
      try {
        const response = await this.$axios.post(
          'controller/externo/curso/asignacion-horario-get-disponibilidad.php',
          {
            fecha: newDate,
            labIds: labIds
          }
        );

        for (let horario of response.data) {
          let idStr = `${horario.labId}`;

          if (!hrsNoDisp[idStr]) {
            hrsNoDisp[idStr] = new Set();
          }

          for (let i = horario.hour; i < horario.end; i++) {
            hrsNoDisp[idStr].add(i);
          }
        }
      } catch (error) {}

      // 🔹 Clases predeterminadas simuladas como activas
      const fechaSeleccionada = this.$moment(newDate);
      const lunesSemana = fechaSeleccionada.clone().startOf('isoWeek'); // lunes

      for (let clase of this.clasesPredeterminadas) {
        const diaClase = Number(clase.hogr_id_dia); // 1=lunes...7=domingo
        const fechaClase = lunesSemana.clone().add(diaClase - 1, 'days');

        if (!fechaClase.isSame(fechaSeleccionada, 'day')) continue;

        // Podrías filtrar excepciones aquí si quieres
        const labId = clase.lab_id;
        const horaIni = Number(clase.hora_ini) / 100;
        const horaFin = Number(clase.hora_fin) / 100;

        if (!hrsNoDisp[labId]) {
          hrsNoDisp[labId] = new Set();
        }

        for (let i = horaIni; i < horaFin; i++) {
          hrsNoDisp[labId].add(i);
        }
      }
      console.log('🧠 Clases predeterminadas cargadas:', this.clasesPredeterminadas);
      console.log('📅 Fecha seleccionada:', fechaSeleccionada.format('YYYY-MM-DD'));
      console.log('🧱 Disponibilidad final hrsNoDisp:', hrsNoDisp);

      this.disponibilidad = hrsNoDisp;
      this.disponibilidadLoading = false;
    },


    confirmarReservar() {
      if (this.grupoSelected == null) {
        return;
      }

      if (this.selectedDate == null) {
        return;
      }

      if (this.laboratorioSelected == null) {
        return;
      }

      if (this.selectedHour == null) {
        return;
      }

      let cantidadHoras = this.selectedHourTo - this.selectedHour.hora;
      if (this.numHorasSeleccionables <= 0) {
        return;
      }
      if (cantidadHoras < this.numHorasSeleccionables) {
        this.$toastError({
          title: 'Error',
          text: `El tipo de grupo seleccionado requiere ${this.numHorasSeleccionables} horas para reservar.`
        });
        return;
      }

      this.$axios
        .post('controller/profesor/reserva/alta.php', {
          horaIni: this.selectedHour.hora + '00',
          horaFin: this.selectedHourTo + '00',
          fecha: this.selectedDate,
          labId: this.laboratorioSelected.saloIdSalon,
          grupoId: this.grupoSelected.grupIdGrupo,
          grupoIdP: this.grupoSelected.gruoIdGrupoP,
          grupoIdOt: this.grupoSelected.grotIdGrupoOt
        })
        .then((response) => {
          this.$toastSuccess({
            text: 'Reserva realizada.'
          });
          this.$router.push('/profesor/reservaciones');
        })
        .catch((error) => {
          if (error?.response?.status === 400 && error?.response?.data?.error === 'OVERLAP') {
            this.$toastError({
              title: 'Error',
              text: 'El horario seleccionado se traslapa con otro.'
            });
          }
        })
        .then(() => {});
    },
    async loadClasesPredeterminadas() {
      try {
        const now = new Date();
        const year = now.getFullYear();

        const firstDayOfYear = new Date(year, 0, 1);
        const pastDaysOfYear = (now - firstDayOfYear) / 86400000;
        const currentWeek = Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);

        console.log("Enviando al backend:", { year, week: currentWeek });

        const response = await this.$axios.post(
          'http://localhost:7070/api/controller/reserva/predeterminadasPorSemana.php',
          { year, week: currentWeek },
          {
            headers: {
              'Content-Type': 'application/json'
            },
            withCredentials: true
          }
        );

        this.clasesPredeterminadas = Array.isArray(response.data.clases)
          ? response.data.clases
          : [];

      } catch (error) {
        console.error("Error al cargar clases predeterminadas:", error);
        this.clasesPredeterminadas = [];
      }
    }







  }
};

