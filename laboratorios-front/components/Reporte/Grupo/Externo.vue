<template>
	<div class="d-flex flex-column flex-sm-row justify-content-start align-items-baseline flex-wrap">
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Clave</span>
			<span>{{ info.gr.cuexClave }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Nombre</span>
			<span>{{ info.gr.cuexNombre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Instructor</span>
			<span v-if="info.pr">{{ info.pr }}</span>
			<span v-else>No asignado</span>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		info: {
			type: Object,
			default: () => ({})
		}
	}
};
</script>
