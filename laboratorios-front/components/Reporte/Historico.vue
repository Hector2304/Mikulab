<template>
	<div>
		<b-modal :id="modalId" no-close-on-esc no-close-on-backdrop hide-header-close scrollable centered>
			<template #modal-header><h5 class="text-primary">Histórico</h5></template>

			<div v-for="(h, i) in historico" :key="'hhh-' + i" :class="{ 'mt-1': i > 0 }">
				<div class="d-flex flex-row justify-content-between align-items-center mb-1">
					<div class="d-flex flex-column flex-sm-row align-items-baseline">
						<div class="comment-writer text-primary mr-3">{{ h.usuario }}</div>
						<div class="comment-date text-secondary mr-3">
							{{ $moment(h.fecha, 'yyyy-MM-DD HH:mm:ss').format('DD MMM yy - HH:mm:ss') }}
						</div>
						<div class="comment">{{ h.accion }}</div>
					</div>
				</div>
			</div>

			<template #modal-footer="{ cancel }">
				<b-button size="sm" variant="secondary" @click="cancel()"> Cerrar </b-button>
			</template>
		</b-modal>
	</div>
</template>

<script>
export default {
	props: {
		modalId: {
			type: String,
			default: 'historico-modal'
		},
		options: {
			type: Object,
			required: true
		}
	},

	data() {
		return {
			loading: false,
			historico: []
		};
	},

	methods: {
		show() {
			this.$bvModal.show(this.modalId);
			this.loadHistorico();
		},

		hide() {
			this.$bvModal.hide(this.modalId);
		},

		loadHistorico() {
			if (this.loading || !this.options?.consultaEndpoint) {
				return;
			}

			this.loading = true;

			this.$axios
				.get(this.options.consultaEndpoint)
				.then((response) => {
					this.historico = response.data;
				})
				.catch((error) => {})
				.then(() => {
					this.loading = false;
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