import TimeLabels from '@/utils/time-labels';
import saveAs from 'file-saver';

export default {
	TimeLabels,

	middleware: 'bitacoraEdicionMiddleware',

	data(vm) {
		return {
			loading: false,
			loadingVigilantes: false,
			vigilantes: [],
			listado: [],
			laboratorios: {},
			observaciones: {},
			detalleSelected: undefined,
			obsOptions: {
				altaEndpoint: 'controller/reportes/bitacora/alta-observacion.php',
				bajaEndpoint: 'controller/reportes/bitacora/baja-observacion.php'
			},
			today: vm.$moment.tz('America/Mexico_City').format('yyyy-MM-DD')
		};
	},

	computed: {
		paramBitacora() {
			return this.$route?.params?.bitacora;
		},

		isEditable() {
			return this.paramBitacora?.bitaFecha === this.today;
		},

		historicoOptions() {
			if (this.paramBitacora?.bitaIdBitacora)
				return {
					consultaEndpoint:
						'controller/reportes/bitacora/historico.php?bitacora-id=' + this.paramBitacora?.bitaIdBitacora
				};
		},

		formattedDay() {
			if (!this.paramBitacora?.bitaFecha) return '';

			return this.$moment
				.tz(this.paramBitacora.bitaFecha, 'yyyy-MM-DD', 'America/Mexico_City')
				.format('DD MMM yy');
		}
	},

	mounted() {
		this.load();
		this.loadVigilantes();
	},

	methods: {
		openHistorico() {
			this.$refs.reporteHistorico.show();
		},

		load() {
			if (this.loading) {
				return;
			}

			if (!this.paramBitacora?.bitaIdBitacora) {
				return;
			}

			this.loading = true;

			this.$axios
				.get(`controller/reportes/bitacora/consulta.php?bitacora-id=${this.paramBitacora?.bitaIdBitacora}`)
				.then((response) => {
					this.listado = response.data.detalle;
					this.laboratorios = response.data.labs;
					this.observaciones = response.data.observaciones;
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		},

		loadVigilantes() {
			if (this.loadingVigilantes) {
				return;
			}

			this.loadingVigilantes = true;

			this.$axios
				.get('controller/usuario/vigilantes.php')
				.then((response) => {
					for (let v of response.data) {
						this.vigilantes.push({
							value: v.usuaIdUsuario,
							text: `${v.usuaNombre} ${v.usuaApaterno} ${v.usuaAmaterno}`
						});
					}
				})
				.catch((error) => {})
				.then(() => {
					this.loadingVigilantes = false;
				});
		},

		getLab(id) {
			return this.laboratorios[`${id}`] ?? {};
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
				.post(`controller/reportes/bitacora/modificacion.php`, {
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
				if (this.observaciones[`${this.detalleSelected.bideIdDetalle}`]) {
					this.observaciones[`${this.detalleSelected.bideIdDetalle}`].unshift(newObs);
				} else {
					this.$set(this.observaciones, `${this.detalleSelected.bideIdDetalle}`, [newObs]);
				}
			}
		},

		bajaCallback(item) {
			if (this.observaciones[`${this.detalleSelected.bideIdDetalle}`]) {
				this.observaciones[`${this.detalleSelected.bideIdDetalle}`].splice(
					this.observaciones[`${this.detalleSelected.bideIdDetalle}`].indexOf(item),
					1
				);
			}
		},

		descargarExcel() {
			if (this.loading) {
				return;
			}

			this.loading = true;

			let payload = {
				dia: this.formattedDay,
				tipoBitacora: this.paramBitacora?.bitaTipo === 'C' ? 'CIERRE' : 'APERTURA',
				tipoLab: this.paramBitacora?.bitaTipoLab,
				list: []
			};

			for (let item of this.listado) {
				let row = {
					lab: this.getLab(item.bideIdLaboratorio).saloClave,
					monitor: item.bideMonitor,
					cpu: item.bideCpu,
					teclado: item.bideTeclado,
					mouse: item.bideMouse,
					proyector: item.bideVideoProyector,
					dport: item.bideCableDport,
					canon: item.bideControlCanon,
					aire: item.bideControlAire,
					apertura: this.$moment(item.bideHoraApertura, 'HH:mm:ss').format('HH:mm'),
					cierre: this.$moment(item.bideHoraCierre, 'HH:mm:ss').format('HH:mm'),
					vigilante: item.bideVigilante,
					observaciones: ''
				};

				if (item.bideVigilante) {
					let vig = this.vigilantes.find(v => v.value === item.bideVigilante);
					row.vigilante = vig ? vig.text : '';
				} else {
					row.vigilante = '';
				}

				for (let obs of this.observaciones[`${item.bideIdDetalle}`] ?? []) {
					row.observaciones += `${obs.usuario}, ${this.$moment(obs.fecha, 'yyyy-MM-DD HH:mm:ss').format(
						'DD MMM yy - HH:mm:ss'
					)}:\n${obs.observacion}\n\n`;
				}

				payload.list.push(row);
			}

			this.$axios
				.post(`controller/reportes/bitacora/excel.php`, payload, {
					responseType: 'blob'
				})
				.then((response) => {
					saveAs(
						response.data,
						`BT${this.$moment.tz('America/Mexico_City').format('yyyyMMDDHHmmss')}.xlsx`
					);
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		}
	}
};
