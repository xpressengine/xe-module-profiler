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

	function procProfilerAdminDeleteTriggerList()
	{
		$trigger_list = executeQueryArray('profiler.getTriggerModule');
		$trigger_module = $trigger_list->data;

		foreach($trigger_module as $modules)
		{
			$module_class = getClass($modules->module);
			if(!$module_controller)
			{
				$deleted = $modules->module;
				$args->module = $deleted;
				$output = executeQuery('profiler.deleteTriggerModuleList', $args);
			}
		}

		if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON')))
		{
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminDashboard');
			header('location: ' . $returnUrl);
			return;
		}
	}
}

/* End of file profiler.admin.controller.php */
/* Location: ./modules/profiler/profiler.admin.controller.php */