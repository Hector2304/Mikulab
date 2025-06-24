export default function ({ store, redirect }) {
	if (store.state.sessionStore.isSessionActive) {
		if (store.state.sessionStore.userType === 'PROFESOR') {
			redirect('/profesor');
		} else if (store.state.sessionStore.userType === 'SUPERUSUARIO') {
			redirect('/superusuario');
		} else if (store.state.sessionStore.userType === 'TECNICO') {
			redirect('/tecnico');
		} else if (store.state.sessionStore.userType === 'SERVIDOR_SOCIAL') {
			redirect('/servidor-social');
		}
	}
}
