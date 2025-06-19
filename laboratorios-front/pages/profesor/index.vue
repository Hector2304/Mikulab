<template>
	<div>
		<Breadcrumbs id="profesor" />

		<b-row align-v="stretch" align-h="around">
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="calendar-plus"
					text="Nueva reservación"
					@click.native="$router.push('/profesor/reserva')"
				/>
			</b-col>
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="calendar-week"
					text="Reservaciones anteriores"
					@click.native="$router.push('/profesor/reservaciones')"
				/>
			</b-col>
		</b-row>
	</div>
</template>
