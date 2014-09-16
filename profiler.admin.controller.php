<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  profilerAdminController
 * @author NAVER (developers@xpressengine.com)
 * @brief  Profiler module admin controller class.
 */

class profilerAdminController extends profiler
{
	function init()
	{
	}

	function procProfilerAdminInsertConfig()
	{
	}

	function procProfilerAdminDeleteTrigger()
	{
		$trigger_list = executeQueryArray('profiler.getTriggerModule');
		$trigger_module = $trigger_list->data;
		debugPrint($trigger_module);
		$module_list[] = FileHandler::readDir(modules);

		foreach($trigger_module as $modules)
		{


		}
	}
}

/* End of file profiler.admin.controller.php */
/* Location: ./modules/profiler/profiler.admin.controller.php */