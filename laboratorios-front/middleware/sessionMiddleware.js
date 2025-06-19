export default function ({ store, route, redirect }) {
	if (store.state.sessionStore.isSessionActive) {
		let path = route?.path ?? '';

		if (store.state.sessionStore.userType === 'PROFESOR') {
			if (!path.startsWith('/profesor')) {
				redirect('/profesor');
			}
		} else if (store.state.sessionStore.userType === 'SUPERUSUARIO') {
			if (path.startsWith('/profesor')) {
				redirect('/superusuario');
			}
		} else if (store.state.sessionStore.userType === 'TECNICO') {
			if (path.startsWith('/tecnico') || path.startsWith('/reportes')) {
			} else {
				redirect('/tecnico');
			}
		} else if (store.state.sessionStore.userType === 'SERVIDOR_SOCIAL') {
			if (path.startsWith('/servidor-social') || path.startsWith('/reportes')) {
				if (path.startsWith('/reportes/bitacora')) {
					redirect('/servidor-social');
				}
			} else {
				redirect('/servidor-social');
			}
		}
	}
}
