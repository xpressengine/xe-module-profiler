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
		$oProfilerModel = getModel('profiler');

		$vars = Context::getRequestVars();
		$section = $vars->_config_section;

		$config = $oProfilerModel->getConfig();
		if(!$config->slowlog) $config->slowlog = new stdClass();
		$config->slowlog->enabled = ($vars->slowlog_enabled == 'Y') ? 'Y' : 'N';
		$config->slowlog->time_trigger = ($vars->slowlog_time_trigger > 0) ? $vars->slowlog_time_trigger : null;
		$config->slowlog->time_addon = ($vars->slowlog_time_addon > 0) ? $vars->slowlog_time_addon : null;

		$oModuleController->updateModuleConfig('profiler', $config);

		$oInstallController = getController('install');
		if(!$oInstallController->makeConfigFile())
		{
			return new Object(-1, 'msg_invalid_request');
		}

		$this->setMessage('success_updated');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminConfig'));
	}

	function procProfilerAdminTruncateSlowlog()
	{
		$output = executeQuery('profiler.truncateSlowlog');

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminSlowlog'));
	}

	function procProfilerAdminDeleteTrigger()
	{
		// 고급 삭제 옵션
		$advanced = Context::get('advanced') == 'Y' ? TRUE : FALSE;

		// 삭제할 트리거 목록 불러오기
		$oProfilerAdminModel = getAdminModel('profiler');
		$invalid_trigger_list = $oProfilerAdminModel->getTriggersToBeDeleted($advanced);

		// 트리거 삭제
		$oModuleController = getController('module');
		foreach($invalid_trigger_list as $trigger)
		{
			$output = $oModuleController->deleteTrigger($trigger->trigger_name, $trigger->module, $trigger->type, $trigger->called_method, $trigger->called_position);
			if(!$output->toBool())
			{
				return $output;
			}
		}

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminTrigger', 'page', Context::get('page'), 'advanced', Context::get('advanced')));
	}

	function procProfilerAdminDeleteModuleConfig()
	{
		// 삭제할 모듈 설정 목록 불러오기
		$oProfilerAdminModel = getAdminModel('profiler');
		$invalid_module_config = $oProfilerAdminModel->getModuleConfigToBeDeleted();

		// 모듈 설정 삭제
		foreach($invalid_module_config as $module_config)
		{
			$output = executeQuery('profiler.deleteModuleConfig', $module_config);
			if(!$output->toBool())
			{
				return $output;
			}
		}

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminModuleConfig', 'page', Context::get('page')));
	}

	function procProfilerAdminDeleteTable()
	{
		// 삭제할 테이블 목록 불러오기
		$oProfilerAdminModel = getAdminModel('profiler');
		$table_list = $oProfilerAdminModel->getTableToBeArranged();

		// DB 테이블 삭제
		$oDB = DB::getInstance();
		foreach($table_list as $table_info)
		{
			if($table_info->to_be_deleted === TRUE)
			{
				$oDB->dropTable(substr($table_info->table_name, strlen($oDB->prefix)));
			}
		}

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminTable', 'page', Context::get('page')));
	}

	function procProfilerAdminRepairTable()
	{
		$table_name = Context::get('table_name');
		$output = $this->repairTable($table_name);
		if(!$output->toBool())
		{
			return $output;
		}

		$this->setMessage('success_updated');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminTable', 'page', Context::get('page')));
	}

	function procProfilerAdminDeleteAddonConfig()
	{
		// 고급 삭제 옵션
		$advanced = Context::get('advanced') == 'Y' ? TRUE : FALSE;

		$oProfilerAdminModel = getAdminModel('profiler');
		$invalid_addon_config = $oProfilerAdminModel->getAddonConfigToBeDeleted($advanced);

		// 애드온 설정 삭제
		foreach($invalid_addon_config as $addon_config)
		{
			$addon_name->addon = $addon_config->addon;
			$output = executeQuery('profiler.deleteAddonConfig', $addon_name);
			if (!$output->toBool())
			{
				return $output;
			}
		}

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminAddonConfig', 'page', Context::get('page'), 'advanced', Context::get('advanced')));
	}

	function procProfilerAdminGetTemporaryDocumentCount()
	{
	}

	function procProfilerAdminDeleteTemporaryDocument()
	{
	}

	/**
	 * @brief 테이블 복구
	 * @param string $table_name
	 * @return object
	 */
	function repairTable($table_name)
	{
		// 테이블 이름은 string 형태만 허용
		if(!is_string($table_name))
		{
			return new Object(-1, 'msg_invalid_request');
		}

		$oDB = DB::getInstance();
		$query = 'repair table ' . $table_name;
		$result = $oDB->_query($query);

		return new Object(0, 'success_updated');
	}
}

/* End of file profiler.admin.controller.php */
/* Location: ./modules/profiler/profiler.admin.controller.php */
