<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

class profiler extends ModuleObject
{
	function moduleInstall()
	{
		return new Object();
	}

	function checkUpdate()
	{
		return false;
	}

	function moduleUpdate()
	{
		return new Object();
	}
}
/* End of file */
