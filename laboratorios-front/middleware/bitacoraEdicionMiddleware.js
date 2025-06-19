export default function ({ route, redirect }) {
	if (!route?.params?.bitacora) {
		redirect('/reportes/bitacora');
	}
}
