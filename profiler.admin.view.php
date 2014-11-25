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
		$oProfilerAdminModel = getAdminModel('profiler');
		$a_slowlog = $oProfilerAdminModel->getStaticsSlowlog('addon');
		$t_slowlog = $oProfilerAdminModel->getStaticsSlowlog('trigger');

		Context::set('a_slowlog', $a_slowlog);
		Context::set('t_slowlog', $t_slowlog);
	}

	function dispProfilerAdminConfig()
	{
		$oProfilerModel = getModel('profiler');
		$module_config = $oProfilerModel->getConfig();
		Context::set('module_config', $module_config);
	}

	function dispProfilerAdminSlowlog()
	{
		$oProfilerAdminModel = getAdminModel('profiler');
		$a_slowlog = $oProfilerAdminModel->getStaticsSlowlog('addon');
		$t_slowlog = $oProfilerAdminModel->getStaticsSlowlog('trigger');

		Context::set('a_slowlog', $a_slowlog);
		Context::set('t_slowlog', $t_slowlog);
	}

	function dispProfilerAdminTrigger()
	{
		// 고급 삭제 옵션
		$advanced = Context::get('advanced') == 'Y' ? TRUE : FALSE;

		// 삭제할 수 있는 트리거 목록
		$oProfilerAdminModel = getAdminModel('profiler');
		$invalid_trigger_list = $oProfilerAdminModel->getTriggersToBeDeleted($advanced);
		$paging = $oProfilerAdminModel->getPageNavigation($invalid_trigger_list, Context::get('page'));

		// 템플릿 엔진으로 값 전달
		Context::set('total_count', $paging->total_count);
		Context::set('total_page', $paging->total_page);
		Context::set('page', $paging->page);
		Context::set('trigger_list', $paging->data);
		Context::set('page_navigation', $paging->page_navigation);
	}

	function dispProfilerAdminModuleConfig()
	{
		// 고급 삭제 옵션
		$advanced = Context::get('advanced') == 'Y' ? TRUE : FALSE;

		// 삭제할 수 있는 모듈 설정 목록
		$oProfilerAdminModel = getAdminModel('profiler');
		$invalid_module_config = $oProfilerAdminModel->getModuleConfigToBeDeleted($advanced);
		$paging = $oProfilerAdminModel->getPageNavigation($invalid_module_config, Context::get('page'));

		// 템플릿 엔진으로 값 전달
		Context::set('total_count', $paging->total_count);
		Context::set('total_page', $paging->total_page);
		Context::set('page', $paging->page);
		Context::set('module_config_list', $paging->data);
		Context::set('page_navigation', $paging->page_navigation);
	}

	function dispProfilerAdminTable()
	{
		// 삭제할 수 있는 테이블 목록
		$oProfilerAdminModel = getAdminModel('profiler');
		$arrange_table_list = $oProfilerAdminModel->getTableToBeArranged();
		$paging = $oProfilerAdminModel->getPageNavigation($arrange_table_list, Context::get('page'));

		$oDB = DB::getInstance();
		$column_list = array();
		switch($oDB->db_type)
		{
			case 'mysql':
			case 'mysql_innodb':
			case 'mysqli':
			case 'mysqli_innodb':
				$column_list = array('name', 'type', 'collation', 'rows', 'size', 'overhead', 'repair');
				break;

			case 'mssql':
				$column_list = array('name', 'rows', 'size', 'overhead');
				break;

			case 'cubrid':
				break;
		}

		// 템플릿 엔진으로 값 전달
		Context::set('total_count', $paging->total_count);
		Context::set('total_page', $paging->total_page);
		Context::set('page', $paging->page);
		Context::set('table_list', $paging->data);
		Context::set('column_list', $column_list);
		Context::set('page_navigation', $paging->page_navigation);
	}

	function dispProfilerAdminAddonConfig()
	{
		// 고급 삭제 옵션
		$advanced = Context::get('advanced') == 'Y' ? TRUE : FALSE;

		// 삭제할 수 있는 모듈 설정 목록
		$oProfilerAdminModel = getAdminModel('profiler');
		$invalid_addon_config = $oProfilerAdminModel->getAddonConfigToBeDeleted($advanced);
		$paging = $oProfilerAdminModel->getPageNavigation($invalid_addon_config, Context::get('page'));

		// 템플릿 엔진으로 값 전달
		Context::set('total_count', $paging->total_count);
		Context::set('total_page', $paging->total_page);
		Context::set('page', $paging->page);
		Context::set('addon_config_list', $paging->data);
		Context::set('page_navigation', $paging->page_navigation);
	}

}

/* End of file profiler.admin.view.php */
/* Location: ./modules/profiler/profiler.admin.view.php */
