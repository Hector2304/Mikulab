<template>
	<div>
		<b-modal :id="modalId" size="lg" no-close-on-esc no-close-on-backdrop hide-header-close scrollable centered>
			<template #modal-header><h5 class="text-primary">Observaciones</h5></template>

			<slot name="info"></slot>

			<b-input-group v-if="editable">
				<b-form-textarea
					v-model.trim="obsModel"
					placeholder="Observaciones..."
					:disabled="loading"
					maxlength="1000"
					max-rows="2"
					size="sm"
					no-resize
				></b-form-textarea>
				<b-input-group-append>
					<b-button variant="outline-primary" :disabled="loading" @click="observar">
						<b-icon icon="arrow-right-short"></b-icon>
					</b-button>
				</b-input-group-append>
			</b-input-group>

			<hr v-if="editable && observaciones.length > 0" />

			<div v-for="(obs, i) in observaciones" :key="'obsd-' + i" :class="{ 'mt-4': i > 0 }">
				<div class="d-flex flex-row justify-content-between align-items-center mb-1">
					<div class="d-flex flex-column flex-sm-row align-items-baseline">
						<div class="comment-writer text-primary mr-3">{{ obs.usuario }}</div>
						<div class="comment-date text-secondary">
							{{ $moment(obs.fecha, 'yyyy-MM-DD HH:mm:ss').format('DD MMM yy - HH:mm:ss') }}
						</div>
					</div>
					<div>
						<b-button
							v-if="editable && currentUser === obs.username"
							variant="outline-danger"
							class="border-0"
							size="sm"
							:disabled="deleting"
							@click="eliminar(obs)"
						>
							<b-spinner v-if="deleting" small></b-spinner>
							<b-icon v-else icon="trash"></b-icon>
						</b-button>
					</div>
				</div>
				<div class="comment">{{ obs.observacion }}</div>
			</div>

			<template #modal-footer="{ cancel }">
				<b-button size="sm" variant="secondary" :disabled="loading" @click="cancel()"> Cerrar </b-button>
			</template>
		</b-modal>
	</div>
</template>

<script>
import { mapState } from 'vuex';

export default {
	props: {
		modalId: {
			type: String,
			default: 'observaciones-modal'
		},
		detalleId: {
			type: Number,
			default: undefined
		},
		editable: {
			type: Boolean,
			default: false
		},
		options: {
			type: Object,
			required: true
		},
		observaciones: {
			type: Array,
			default: () => []
		}
	},

	data() {
		return {
			loading: false,
			deleting: false,
			obsModel: ''
		};
	},

	computed: {
		...mapState('sessionStore', {
			currentUser: 'username',
			currentPersonName: 'personName'
		})
	},

	methods: {
		show() {
			this.obsModel = '';
			this.$bvModal.show(this.modalId);
		},

		hide() {
			this.$bvModal.hide(this.modalId);
		},

		observar() {
			if (this.loading) {
				return;
			}

			if (!this.obsModel || this.obsModel.length <= 0) {
				return;
			}

			this.loading = true;

			this.$axios
				.post(this.options.altaEndpoint, {
					obs: this.obsModel,
					detalleId: this.detalleId
				})
				.then((response) => {
					this.$emit('alta', {
						id: response.data.id,
						observacion: this.obsModel,
						fecha: this.$moment.tz('America/Mexico_City').format('yyyy-MM-DD HH:mm:ss'),
						usuario: this.currentPersonName,
						username: this.currentUser
					});
					this.obsModel = '';
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
				});
		},

		eliminar(item) {
			if (this.deleting) {
				return;
			}

			this.deleting = true;

			this.$axios
				.delete(this.options.bajaEndpoint, {
					data: {
						id: item.id
					}
				})
				.then((response) => {
					this.$emit('baja', item);
				})
				.catch((error) => {})
				.then(() => {
					this.deleting = false;
				});
		}
	}
};
</script>

<style scoped>
.comment {
	font-size: 0.95rem;
	line-height: 1.1rem;
}

.comment-writer {
	font-size: 0.9rem;
}

.comment-date {
	font-size: 0.75rem;
}
</style>
