<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  profiler
 * @author NAVER (developers@xpressengine.com)
 * @brief  Profiler module high class.
 */

class profiler extends ModuleObject
{
	private $triggers = array(
		array('XE.writeSlowlog', 'profiler', 'controller', 'triggerWriteSlowlog', 'after'),
		array('display', 'profiler', 'controller', 'triggerBeforeDisplay', 'before')
	);

	function moduleInstall()
	{
		$oModuleController = getController('module');

		foreach($this->triggers as $trigger)
		{
			$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
		}

		return new Object();
	}

	function checkUpdate()
	{
		$oModuleModel = getModel('module');

		foreach($this->triggers as $trigger)
		{
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	function moduleUpdate()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		foreach($this->triggers as $trigger)
		{
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
			}
		}

		return new Object(0, 'success_updated');
	}

	function moduleUninstall()
	{
		return new Object();
	}

	function recompileCache()
	{
		return new Object();
	}
}

/* End of file profiler.class.php */
/* Location: ./modules/profiler/profiler.class.php */
