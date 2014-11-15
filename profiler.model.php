<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @author NAVER (developers@xpressengine.com)
 */
class profilerModel extends profiler
{
	private $config;

	function init()
	{
	}

	/**
	 * @brief 모듈 설정 반환
	 */
	function getConfig()
	{
		if(!$this->config)
		{
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('profiler');

			$config->slowlog = ($config->slowlog) ? $config->slowlog : new stdClass();

			if(!$config->slowlog->enabled)
			{
				$config->slowlog->enabled = 'N';
			}

			$this->config = $config;
		}

		return $this->config;
	}
}

/* End of file profiler.model.php */
/* Location: ./modules/profiler/profiler.model.php */
