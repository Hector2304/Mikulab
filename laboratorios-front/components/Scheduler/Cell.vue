<template>
	<div
		class="d-flex flex-row flex-nowrap align-items-center justify-content-center p-1"
		:class="typeClass"
		style="height: 100%; min-height: 40px"
		@click.prevent.stop="emitDefault"
	>
		<template v-if="undoable">
			<b-button variant="outline-primary" class="border-0" size="sm" :disabled="loading" @click.prevent.stop="undoThis">
				<b-spinner v-if="loading" small></b-spinner>
				<template v-else>Deshacer</template>
			</b-button>
		</template>
		<template v-else>
			<template v-if="payload.type === 'BLOCK' && payload.motivo">
				<span
					title="Motivo"
					v-b-popover.hover.top="payload.motivo"
					class="text-small text-primary ml-1 mr-1 text-truncate"
					>{{ payload.motivo }}</span
				>
			</template>
			<b-button
				v-if="showDelete"
				variant="outline-danger"
				class="border-0"
				size="sm"
				:disabled="loading"
				@click.prevent.stop="deleteThis"
			>
				<b-spinner v-if="loading" small></b-spinner>
				<b-icon v-else icon="x"></b-icon>
			</b-button>
		</template>
	</div>
</template>

<script>
export default {
	props: {
		showDelete: {
			type: Boolean,
			default: false
		},
		undoable: {
			type: Boolean,
			default: false
		},
		dayHour: {
			type: Object,
			required: true
		},
		payload: {
			type: Object,
			default: () => ({})
		}
	},

	data() {
		return {
			loading: false
		};
	},

	computed: {
		typeClass() {
			let normalClass;
			let undoableClass;

			switch (this.payload.type) {
				default:
				case 'BLOCK':
					normalClass = 'bg-disabled';
					undoableClass = 'bg-disabled-light';
					break;

				case 'GROUP':
					normalClass = 'bg-disabled-group';
					undoableClass = 'bg-disabled-group-light';
					break;

				case 'EXTERNAL':
					normalClass = 'bg-disabled-external';
					undoableClass = 'bg-disabled-external-light';
					break;

				case 'RESERVED':
					normalClass = 'bg-disabled-reserved';
					undoableClass = 'bg-disabled-reserved-light';
					break;
			}

			return this.undoable ? undoableClass : normalClass;
		}
	},

	methods: {
		emitDefault() {
			this.$emit('do-default', this.dayHour.d, this.dayHour.h, this.payload);
		},

		emitUndo() {
			this.$emit('undo', this.dayHour.d, this.dayHour.h, this.payload);
		},

		emitRemove() {
			this.$emit('remove', this.dayHour.d, this.dayHour.h, this.payload);
		},

		undoThis () {
			if (this.loading) {
				return;
			}

			this.loading = true;

			this.$emit('undelete', this.dayHour.d, this.dayHour.h, this.payload, (doUndo) => {
				if (doUndo) {
					this.emitUndo();
				}
				this.loading = false;
			});
		},

		deleteThis () {
			if (this.loading) {
				return;
			}

			this.loading = true;

			this.$emit('delete', this.dayHour.d, this.dayHour.h, this.payload, (doRemove) => {
				if (doRemove) {
					this.emitRemove();
				}
				this.loading = false;
			});
		}
	}
};
</script>