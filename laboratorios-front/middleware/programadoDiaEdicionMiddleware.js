export default function ({ route, redirect }) {
	if (!route?.params?.reporte) {
		redirect('/reportes/programados-dia');
	}
}
