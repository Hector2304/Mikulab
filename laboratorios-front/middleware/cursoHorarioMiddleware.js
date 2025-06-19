export default function ({ store, redirect }) {
	if (!store.state.cursoExternoStore.selectedCurso) {
		redirect('/externos/cursos');
	}
}
