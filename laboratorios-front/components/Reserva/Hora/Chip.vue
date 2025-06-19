<template>
	<div :class="clazz" style="min-height: 40px; min-width: 60px" v-b-hover="hoverHandler" @click="selectThis">
		<span>{{ hora.hora }}</span>
		<span class="text-half-small">&nbsp;:&nbsp;00</span>
	</div>
</template>
<script>
export default {
	props: {
		hora: {
			type: Object,
			required: true
		},
		tamanhoSeleccion: {
			type: Number,
			default: 1
		}
	},

	data() {
		return {
			firstHovered: false
		};
	},

	computed: {
		isHovered() {
			return !!this.hora.isHovered;
		},

		clazz() {
			let theClazz = [
				'm-0',
				'py-2',
				'pl-2',
				'pr-4',
				'd-flex',
				'flex-row',
				'justify-content-start',
				'align-items-center',
				'text-noselect'
			];

			if (this.hora.disabled) {
				theClazz.push('border-left', 'bg-disabled-light', 'text-muted-disabled', 'border-disabled');
				return theClazz;
			}

			if (this.hora.selected) {
				theClazz.push('border-primary', 'bg-primary', 'text-white');
			} else if (this.isHovered) {
				theClazz.push('border', 'border-primary', 'border-dashed', 'bg-light', 'text-muted');
				if (!this.firstHovered) {
					theClazz.push('border-left-0');
				}
			} else {
				theClazz.push('border-left', 'bg-light', 'text-muted');
			}

			theClazz.push('cursor-pointer');

			return theClazz;
		}
	},

	methods: {
		hoverHandler(isHovered) {
			this.firstHovered = isHovered;
			this.setRecursiveHover(this.hora, isHovered, this.tamanhoSeleccion);
		},

		setRecursiveHover(hObj, isHovered, max) {
			if (max <= 0) {
				return;
			}

			if (!hObj) {
				return;
			}

			if (hObj.disabled) {
				return;
			}

			this.$set(hObj, 'isHovered', isHovered);

			this.setRecursiveHover(hObj.next, isHovered, max - 1);
		},

		selectThis() {
			if (this.hora.disabled) {
				return;
			}

			if (this.tamanhoSeleccion <= 0) {
				return;
			}

			this.$emit('select', {
				hora: this.hora,
				callback: () => {
					this.recursiveSelect(this.hora, this.tamanhoSeleccion);
				}
			});
		},

		recursiveSelect(hObj, max) {
			if (max <= 0) {
				return;
			}

			if (!hObj) {
				return;
			}

			if (hObj.disabled) {
				return;
			}

			this.$set(hObj, 'selected', true);

			this.recursiveSelect(hObj.next, max - 1);
		}
	}
};
</script>
