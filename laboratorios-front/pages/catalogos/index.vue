<template>
	<div>
		<Breadcrumbs id="catalogos" />

		<b-row align-v="stretch" align-h="around">
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="display"
					text="Equipos de cómputo"
					@click.native="$router.push('/catalogos/equipos-computo')"
				/>
			</b-col>
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="terminal"
					text="Software"
					@click.native="$router.push('/catalogos/software')"
				/>
			</b-col>
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="building"
					text="Laboratorios"
					@click.native="$router.push('/catalogos/laboratorios')"
				/>
			</b-col>
		</b-row>
	</div>
</template>
