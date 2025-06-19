<template>
	<div>
		<Breadcrumbs id="externos" />

		<b-row align-v="stretch" align-h="around">
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="journal-bookmark"
					text="Cursos"
					@click.native="$router.push('/externos/cursos')"
				/>
			</b-col>
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="person-badge"
					text="Instructores"
					@click.native="$router.push('/externos/instructores')"
				/>
			</b-col>
		</b-row>
	</div>
</template>
