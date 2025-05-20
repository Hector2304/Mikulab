<template>
	<span class="text-secondary h6 ml-1">
		{{ grupo.grupClave }}{{ grupo.gruoClave }}{{ grupo.grotClave }}
		-
		{{ grupo.asigNombre }}{{ grupo.asigNombreP }}{{ grupo.mootNombre }}
	</span>
</template>

<script>
export default {
	props: {
		grupo: {
			type: Object,
			default: () => ({})
		}
	}
};
</script>
