<template>
	<b-card style="cursor: pointer" v-b-hover="hoverHandler" :class="{ 'shadow-sm': this.isHovered }">
		<b-card-text
			class="d-flex flex-row align-items-center"
			:class="{ 'text-primary': isHovered, 'text-muted': !isHovered }"
		>
			<b-icon :icon="icon" font-scale="3"></b-icon>

			<span class="h5 m-0 ml-4">{{ text }}</span>
		</b-card-text>
	</b-card>
</template>

<script>
export default {
	props: {
		icon: {
			type: String,
			required: true
		},
		text: {
			type: String,
			required: true
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
