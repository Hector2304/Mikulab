<template>
	<b-card
		class="card-class m-2"
		:class="{ 'shadow-sm': this.isHovered, 'bg-primary text-white': this.selected }"
		:title="this.lab.nombre"
		v-b-hover="hoverHandler"
		@click="$emit('select')"
	>
		<b-card-text :class="{ 'text-secondary': !this.selected, 'text-white': this.selected }">
			<span class="text-small">Edificio</span> <b>{{ this.lab.edificio }}</b>
		</b-card-text>
		<b-card-text :class="{ 'text-secondary': !this.selected, 'text-white': this.selected }">
			<span class="text-small">Capacidad</span> <b>{{ this.lab.capacidad }}</b>
		</b-card-text>
	</b-card>
</template>

<script>
export default {
	props: {
		lab: {
			type: Object,
			required: true
		},
		selected: {
			type: Boolean,
			default: false
		}
	},

	data() {
		return {
			isHovered: false
		};
	},

	methods: {
		hoverHandler(isHovered) {
			this.isHovered = isHovered;
		}
	}
};
</script>

<style scoped>
.card-class {
	cursor: pointer;
	width: 150px;
}
</style>