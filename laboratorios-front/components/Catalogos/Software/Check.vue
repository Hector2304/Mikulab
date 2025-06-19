<template>
	<b-form-checkbox v-model="status" class="mx-3 my-2" :disabled="saving" @change="onChangeByUser" switch>
		<span
			class="ml-1"
			style="width: 150px; display: block; overflow: hidden; white-space: nowrap; text-overflow: ellipsis"
		>
			<b-spinner v-if="saving" small></b-spinner>
			{{ software.softNombre }}
		</span>
	</b-form-checkbox>
</template>

<script>
export default {
	props: {
		software: {
			type: Object,
			required: true
		},
		idLaboratorio: {
			type: Number,
			required: true
		},
		initValue: {
			type: Boolean,
			default: false
		}
	},

	data() {
		return {
			status: false,
			saving: false
		};
	},

	mounted() {
		this.status = this.initValue;
	},

	methods: {
		onChangeByUser(newValue) {
			if (this.saving) {
				return;
			}

			this.saving = true;

			let payload = {
				idLaboratorio: this.idLaboratorio,
				idSoftware: this.software.softIdSoftware
			};

			if (newValue) {
				this.$axios
					.post('controller/software/laboratorio-post.php', payload)
					.then((response) => {
						this.$toastPrimary({
							text: this.software.softNombre + ' asignado.',
							delay: 1500
						});
					})
					.catch((error) => {})
					.then(() => {
						this.saving = false;
					});
			} else {
				this.$axios
					.delete('controller/software/laboratorio-delete.php', {
						data: payload
					})
					.then((response) => {
						this.$toastPrimary({
							text: this.software.softNombre + ' removido.',
							delay: 1500
						});
					})
					.catch((error) => {})
					.then(() => {
						this.saving = false;
					});
			}
		}
	}
};
</script>