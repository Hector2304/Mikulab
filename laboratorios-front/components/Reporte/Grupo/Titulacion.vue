<template>
	<div class="d-flex flex-column flex-sm-row justify-content-start align-items-baseline flex-wrap">
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Clave Grupo</span>
			<span>{{ info.gr.grotClave }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Clave Módulo</span>
			<span>{{ info.gr.mootClave }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Módulo</span>
			<span>{{ info.gr.mootNombre }}</span>
		</div>
		<div class="d-flex flex-nowrap mx-2 align-items-baseline">
			<span class="text-small text-secondary mr-1">Profesor(a)</span>
			<span>{{ info.pr.nombre }}</span>
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
