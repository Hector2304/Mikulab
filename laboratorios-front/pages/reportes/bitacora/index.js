import formsMixin from '@/mixins/formsMixin';
import { required } from 'vuelidate/lib/validators';

export default {
	mixins: [formsMixin],

	validations: {
		crearForm: {
			tipo: {
				required
			},
			tipoLab: {
				required
			}
		}
	},

	data(vm) {
		return {
			loading: false,
			campos: [
				{
					key: 'bitaFecha',
					label: 'Fecha',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					sortable: true,
					formatter(value, key, item) {
						return vm.$moment(item.bitaFecha, 'yyyy-MM-DD').format('DD MMMM yy');
					},
					filterByFormatted: true
				},
				{
					key: 'bitaTipo',
					label: 'Tipo',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					sortable: true,
					formatter(value, key, item) {
						return value === 'C' ? 'Cierre' : 'Apertura';
					},
					filterByFormatted: true
				},
				{
					key: 'bitaTipoLab',
					label: 'Laboratorios',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					sortable: true
				},
				{
					key: 'actions',
					label: 'Acciones',
					thClass: 'text-center align-middle',
					tdClass: 'text-center align-middle',
					slotable: true
				}
			],
			listado: [],

			tipoOptions: [
				{ value: 'A', text: 'Apertura' },
				{ value: 'C', text: 'Cierre' }
			],

			tipoLabOptions: [
				{ value: 'K', text: 'K' },
				{ value: 'LAB', text: 'LAB' }
			],

			crearForm: {
				tipo: null,
				tipoLab: null
			},

			today: vm.$moment.tz('America/Mexico_City').format('yyyy-MM-DD')
		};
	},

	computed: {
		crearFormTipoMsgs() {
			if (!this.$v.crearForm.tipo.required) {
				return 'Requerido.';
			}
			return '';
		},
		crearFormTipoLabMsgs() {
			if (!this.$v.crearForm.tipoLab.required) {
				return 'Requerido.';
			}
			return '';
		}
	},

	mounted() {
		this.loadListado();
	},

	methods: {
		loadListado() {
			if (this.loading) {
				return;
			}

			this.loading = true;

			this.$axios
				.get('controller/reportes/bitacora/listado.php')
				.then((response) => {
					this.listado = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		},

		openCrearReporte() {
			this.$bvModal.show('crear-reporte-modal');
		},

		crearReporte(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			this.$v.crearForm.$touch();
			if (this.$v.crearForm.$anyError) {
				return;
			}

			if (this.loading) {
				return;
			}

			this.loading = true;

			this.$axios
				.post('controller/reportes/bitacora/alta.php', {
					fecha: this.today,
					tipo: this.crearForm.tipo,
					tipoLab: this.crearForm.tipoLab
				})
				.then((response) => {
					this.goToEdicion(response.data);
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		},

		goToEdicion(item) {
			this.$router.push({
				name: 'reportes-bitacora-edicion',
				params: {
					bitacora: {
						...item
					}
				}
			});
		}
	}
};
