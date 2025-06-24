<?php

class Util
{
	public static function getStringDiaFromInt($numero)
	{
		switch ($numero) {
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
				return 'D';
			default:
				return '';
		}
	}

	public static function startsWith(string $string, string $query)
	{
		return substr($string, 0, strlen($query)) === $query;
	}
}
