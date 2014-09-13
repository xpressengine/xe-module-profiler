<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  profiler
 * @author NAVER (developers@xpressengine.com)
 * @brief  Profiler module high class.
 */

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

/* End of file profiler.class.php */
/* Location: ./modules/profiler/profiler.class.php */