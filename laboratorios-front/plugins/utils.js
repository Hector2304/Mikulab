export default (context, inject) => {
	inject('getNumericDayFromString', (d) => {
		switch (d) {
			case 'L':
				return 1;
			case 'M':
				return 2;
			case 'MC':
				return 3;
			case 'J':
				return 4;
			case 'V':
				return 5;
			case 'S':
				return 6;
			case 'D':
				return 7;
			default:
				return 0;
		}
	});

	inject('getStringDayFromNumeric', (d) => {
		switch (d) {
			case 1:
				return 'L';
			case 2:
				return 'M';
			case 3:
				return 'MC';
			case 4:
				return 'J';
			case 5:
				return 'V';
			case 6:
				return 'S';
			case 7:
			case 0:
				return 'D';
			default:
				return '';
		}
	});
};
