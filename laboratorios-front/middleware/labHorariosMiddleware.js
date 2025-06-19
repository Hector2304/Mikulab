export default function ({ store, redirect }) {
	if (!store.state.labHorariosStore.selectedLab) {
		redirect('/catalogos/laboratorios');
	}
}
