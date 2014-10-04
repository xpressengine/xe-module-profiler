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
		$oModuleController = getController('module');
		$oProfilerModel = getController('profiler');

		$vars = Context::getRequestVars();
		$section = $vars->_config_section;

		if($section == 'general')
		{
			// @TODO 모듈 설정에 저장
			$config = $oProfilerModel->getConfig();
			$oModuleController->updateModuleConfig('profiler', $config);
			$this->setMessage('success_updated');
		}
		else if($section == 'slowlog')
		{
			$db_info = Context::getDbInfo();
			$db_info->slowlog['enabled'] = ($vars->slowlog_enabled == 'Y') ? 'Y' : 'N';
			$db_info->slowlog['time_trigger'] = ($vars->slowlog_time_trigger > 0) ? $vars->slowlog_time_trigger : null;
			$db_info->slowlog['time_addon'] = ($vars->slowlog_time_addon > 0) ? $vars->slowlog_time_addon : null;

			$oInstallController = getController('install');
			if(!$oInstallController->makeConfigFile())
			{
				return new Object(-1, 'msg_invalid_request');
			}
		}

		if(!in_array(Context::getRequestMethod(), array('XMLRPC', 'JSON')))
		{
			$redirectUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminConfig');
			$this->setRedirectUrl($redirectUrl);
		}
	}

	function procProfilerAdminDeleteTrigger()
	{
		// 고급 삭제 옵션
		$advanced = Context::get('advanced') == 'Y' ? TRUE : FALSE;

		// 삭제할 트리거 목록 불러오기
		$oProfilerAdminModel = getAdminModel('profiler');
		$triggers_deleted = $oProfilerAdminModel->getTriggersToBeDeleted($advanced);

		// 트리거 삭제
		$oModuleController = getController('module');
		foreach ($triggers_deleted as $trigger)
		{
			$output = $oModuleController->deleteTrigger($trigger->trigger_name, $trigger->module, $trigger->type, $trigger->called_method, $trigger->called_position);
			if (!$output->toBool())
			{
				return $output;
			}
		}

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminTriggerList', 'page', Context::get('page'), 'advanced', Context::get('advanced')));
	}

	function procProfilerAdminDeleteModuleConfig()
	{
		// 삭제할 모듈 설정 목록 불러오기
		$oProfilerAdminModel = getAdminModel('profiler');
		$config_deleted = $oProfilerAdminModel->getModuleConfigToBeDeleted();

		// 모듈 설정 삭제
		foreach ($config_deleted as $module_config)
		{
			$output = executeQuery('profiler.deleteModuleConfig', $module_config);
			if (!$output->toBool())
			{
				return $output;
			}
		}

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminModuleConfigList', 'page', Context::get('page')));
	}
}

/* End of file profiler.admin.controller.php */
/* Location: ./modules/profiler/profiler.admin.controller.php */
