<template>
	<div>
		<Breadcrumbs id="reportes" />

		<b-row align-v="stretch" align-h="around">
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1" v-if="userType !== 'SERVIDOR_SOCIAL'">
				<SimpleMenuButton
					icon="calendar-check"
					text="Bitácora"
					@click.native="$router.push('/reportes/bitacora')"
				/>
			</b-col>
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="calendar-event"
					text="Programados al día"
					@click.native="$router.push('/reportes/programados-dia')"
				/>
			</b-col>
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="calendar-week"
					text="Programados semanal"
					@click.native="$router.push('/reportes/programados-semana')"
				/>
			</b-col>
		</b-row>
	</div>
</template>

<script src="./index.js"></script>

