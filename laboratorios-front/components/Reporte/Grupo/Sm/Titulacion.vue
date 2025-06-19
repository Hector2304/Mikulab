<template>
	<div class="d-flex flex-column flex-sm-row justify-content-start align-items-baseline flex-wrap">
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Clave Grupo</span>
			<span class="text-small">{{ info.gr.grotClave }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Clave Módulo</span>
			<span class="text-small">{{ info.gr.mootClave }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Módulo</span>
			<span class="text-small">{{ info.gr.mootNombre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-smaller text-secondary mr-1">Profesor(a)</span>
			<span class="text-small">{{ info.pr.nombre }}</span>
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
