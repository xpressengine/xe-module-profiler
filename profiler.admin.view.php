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
	}

	function dispProfilerAdminTriggerList()
	{
		// 고급 삭제 옵션
		$advanced = Context::get('advanced') == 'Y' ? TRUE : FALSE;

		// 삭제할 수 있는 트리거 목록
		$oProfilerAdminModel = getAdminModel('profiler');
		$delete_trigger_list = $oProfilerAdminModel->getDeleteTriggerList($advanced);
		$paging = $oProfilerAdminModel->getPageNavigation($delete_trigger_list, Context::get('page'));

		// 템플릿 엔진으로 값 전달
		Context::set('total_count', $paging->total_count);
		Context::set('total_page', $paging->total_page);
		Context::set('page', $paging->page);
		Context::set('trigger_list', $paging->data);
		Context::set('page_navigation', $paging->page_navigation);
	}
}

/* End of file profiler.admin.view.php */
/* Location: ./modules/profiler/profiler.admin.view.php */