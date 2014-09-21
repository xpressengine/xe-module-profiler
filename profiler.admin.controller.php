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
}

/* End of file profiler.admin.controller.php */
/* Location: ./modules/profiler/profiler.admin.controller.php */