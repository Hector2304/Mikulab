<template>
	<div class="d-flex flex-column flex-sm-row justify-content-start align-items-baseline flex-wrap">
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Clave</span>
			<span class="text-small">{{ info.gr.cuexClave }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Nombre</span>
			<span class="text-small">{{ info.gr.cuexNombre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Instructor</span>
			<span v-if="info.pr" class="text-small">{{ info.pr }}</span>
			<span v-else class="text-small">No asignado</span>
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
