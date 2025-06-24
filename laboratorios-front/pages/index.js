import { mapActions } from 'vuex';

export default {
	middleware: 'accesosMiddleware',

	data() {
		return {
			accesoForm: {
				usuario: '',
				contrasena: ''
			},
			requesting: false
		};
	},

	methods: {
		...mapActions('sessionStore', {
			loginAction: 'login'
		}),

		login() {
			if (this.requesting) {
				return;
			}

			if (!this.accesoForm.usuario) {
				return;
			}

			if (!this.accesoForm.contrasena) {
				return;
			}

			this.requesting = true;

			this.loginAction({
				usuario: this.accesoForm.usuario,
				contrasena: this.accesoForm.contrasena
			})
				.then((response) => {})
				.catch((error) => {
					if (error?.response?.status === 401) {
						this.$toastError({
							title: 'Error',
							text: 'Usuario y/o contraseña incorrecto.'
						});
					}
				})
				.then(() => {
					this.requesting = false;
				});
		}
	}
};
