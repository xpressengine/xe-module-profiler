<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  profilerAdminView
 * @author NAVER (developers@xpressengine.com)
 * @brief  Profiler module admin view class.
 */

class profilerAdminView extends profiler
{
	function init()
	{
		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile(strtolower(str_replace('dispProfilerAdmin', '', $this->act)));
	}

	function dispProfilerAdminDashboard()
	{
	}

	function dispProfilerAdminConfig()
	{
	}

	function dispProfilerAdminSlowlogTrigger()
	{
		$oProfilerAdminModel = getAdminModel('profiler');
		$slowlog = $oProfilerAdminModel->getStaticsSlowlog('trigger');
		Context::set('slowlog', $slowlog);
		debugPrint($slowlog);
	}
}

/* End of file profiler.admin.view.php */
/* Location: ./modules/profiler/profiler.admin.view.php */
