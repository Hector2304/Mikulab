<?php

abstract class AbstractDTO
{
	public function getObjectVars()
	{
		return get_object_vars($this);
	}
}
