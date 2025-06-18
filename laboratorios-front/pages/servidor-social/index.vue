<template>
	<div>
		<Breadcrumbs id="servidorSocial" />

		<b-row align-v="stretch" align-h="around">
			<b-col cols="12" md="6" lg="5" xl="4" class="my-1">
				<SimpleMenuButton
					icon="file-earmark-spreadsheet"
					text="Reportes"
					@click.native="$router.push('/reportes')"
				/>
			</b-col>
		</b-row>
	</div>
</template>