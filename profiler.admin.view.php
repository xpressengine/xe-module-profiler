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

		// 페이지 핸들러
		$total_count = count($delete_trigger_list);
		$total_page = $total_count ? ceil($total_count / 10) : 1;
		$cur_page = Context::get('page') ? Context::get('page') : 1;
		$page_navigation = new PageHandler($total_count, $total_page, $cur_page);

		// 템플릿 엔진으로 값 전달
		Context::set('trigger_list', $delete_trigger_list);
		Context::set('page_navigation', $page_navigation);
	}
}

/* End of file profiler.admin.view.php */
/* Location: ./modules/profiler/profiler.admin.view.php */