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

		$config->slowlogconfig = Context::get('slowlogconfig');

		$oModuleController->updateModuleConfig('profiler', $config);

		$this->setMessage('success_updated');
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispProfilerAdminConfig'));
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
		foreach ($invalid_trigger_list as $trigger)
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
		$invalid_module_config = $oProfilerAdminModel->getModuleConfigToBeDeleted();

		// 모듈 설정 삭제
		foreach ($invalid_module_config as $module_config)
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

	/**
	 * @comment 테이블을 삭제하기 전에 강력한 경고문을 보여줄 것. 현재 구현되어 있지 않음
	 */
	function procProfilerAdminDeleteTable()
	{
		// 삭제할 테이블 목록 불러오기
		$oProfilerAdminModel = getAdminModel('profiler');
		$invalid_table_list = $oProfilerAdminModel->getTableToBeDeleted();

		// DB 테이블 삭제
		$oDB = DB::getInstance();
		foreach($invalid_table_list as $table_name)
		{
			$oDB->dropTable($table_name);
		}

		$this->setMessage('success_deleted');
		$this->setRedirectUrl(getNotEncodedUrl('module', 'admin', 'act', 'dispProfilerAdminTable', 'page', Context::get('page')));
	}
}

/* End of file profiler.admin.controller.php */
/* Location: ./modules/profiler/profiler.admin.controller.php */