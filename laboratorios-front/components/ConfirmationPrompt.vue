
<template>
	<b-modal
		:id="promptId"
		@cancel="cancelClick"
		@ok="doConfirm"
		no-close-on-esc
		no-close-on-backdrop
		hide-header-close
		scrollable
		centered
	>
		<template v-if="title" #modal-header
			><h5 class="text-primary">{{ title }}</h5></template
		>

		<span v-html="text"></span>

		<template #modal-footer="{ ok, cancel }">
			<b-button size="sm" variant="secondary" :disabled="loading" @click="cancel()"> Cancelar </b-button>
			<b-button size="sm" variant="primary" :disabled="loading" @click="ok()">
				<b-spinner v-if="loading" small></b-spinner>
				<span v-else>Confirmar</span>
			</b-button>
		</template>
	</b-modal>
</template>

<script>
export default {
	props: {
		promptId: {
			type: String,
			default: 'confirmPrompt'
		}
	},

	data() {
		return {
			loading: false,
			title: false,
			text: '',
			onConfirm: null,
			onCancel: null
		};
	},

	methods: {
		ask({ title, text, onConfirm, onCancel }) {
			this.title = title;
			this.text = text;
			this.onConfirm = onConfirm;
			this.onCancel = onCancel;
			this.$bvModal.show(this.promptId);
		},
		cancelar() {
			this.title = false;
			this.text = '';
			this.onConfirm = null;
			this.onCancel = null;
			this.$bvModal.hide(this.promptId);
		},

		cancelClick() {
			if (this.onCancel) {
				this.onCancel();
			}
			this.cancelar();
		},

		doConfirm(bvModalEvent) {
			if (bvModalEvent?.preventDefault) bvModalEvent.preventDefault();

			if (this.loading || this.onConfirm == null) {
				return;
			}

			this.loading = true;

			this.onConfirm()
				.then((resolved) => {
					this.cancelar();
				})
				.catch((rejected) => {})
				.then(() => {
					this.loading = false;
				});
		}
	}
};
</script>
