<template>
	<b-jumbotron style="border-radius: 0" bg-variant="primary" text-variant="white">
		<template #header>
			<span>{{ error.statusCode }}</span>
		</template>

		<template #lead>
			{{ error.message }}
		</template>

		<b-button variant="warning" to="/">Inicio</b-button>
	</b-jumbotron>
</template>

<script>
export default {
	props: {
		error: {
			type: Object,
			default: {}
		}
	},

	head() {
		return {
			title: this.error.statusCode + ' - ' + this.error.message
		};
	}
};
</script>
