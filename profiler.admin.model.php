<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  profilerAdminModel
 * @author NAVER (developers@xpressengine.com)
 * @brief  Profiler module admin model class.
 */

class profilerAdminModel extends profiler
{
	function init()
	{
	}

	/**
	 * @brief 페이징 네비게이션
	 * @param array $args
	 * @param int $page
	 * @param int $page_count
	 * @param int $list_count
	 * @return stdClass
	 */
	function getPageNavigation($args = array(), $page = 1, $page_count = 10, $list_count = 20)
	{
		if ((int)$page)
		{
			$page = (int)$page;
		}
		else
		{
			$page = 1;
		}
		if ((int)$page_count)
		{
			$page_count = (int)$page_count;
		}
		else
		{
			$page_count = 10;
		}
		if ((int)$list_count)
		{
			$list_count = (int)$list_count;
		}
		else
		{
			$list_count = 20;
		}

		$total_count = count($args);
		if ($total_count)
		{
			$total_page = (int)(($total_count - 1) / $list_count) + 1;
		}
		else
		{
			$total_page = 1;
		}

		$output = new Object();
		$output->total_count = $total_count;
		$output->total_page = $total_page;
		$output->page = $page;
		$output->page_navigation = new PageHandler($total_count, $total_page, $page, $page_count);

		if ($page > $total_page)
		{
			$output->data = array();
		}
		else
		{
			$output->data = array_slice($args, ($page - 1) * $list_count, $list_count);
		}

		return $output;
	}

	/**
	 * @brief Slowlog 데이터 반환
	 * @param stdClass $args
	 * @return array
	 */
	function getStaticsSlowlog($type, $args = NULL)
	{
		$cond = new stdClass();
		$cond->type = $type;
		$cond->hash_id = $args->hash_id;

		// $cond->like_caller = $args->like_caller;
		// $cond->like_called = $args->like_called;

		$cond->start = $args->start;
		$cond->end = $args->end;

		$output = executeQueryArray('profiler.getStatisticSlowlog', $cond);
		return $output;
	}

	/**
	 * @brief 설치된 모듈 이름 목록 반환
	 * @return array
	 */
	function getModuleList()
	{
		$oModuleModel = getModel('module');
		$modules_info = $oModuleModel->getModuleList();

		foreach ($modules_info as $module_info)
 		{
			// 모듈 이름만 배열에 추가
			$module_list[] = $module_info->module;
 		}

		return $module_list;
	}

	/**
	 * @brief 삭제해도 상관없는 트리거 목록 반환
	 * @param boolean $advanced
	 * @return array
	 */
	function getTriggersToBeDeleted($advanced = FALSE)
	{
		$oModuleModel = getModel('module');

		// DB 상의 트리거 목록
		$trigger_list = $oModuleModel->getTriggers();

		// 설치되어 있는 모듈 목록
		$module_list = $this->getModuleList();

		// 삭제해도 상관없는 트리거 목록
		$triggers_deleted = array();
		foreach ($trigger_list as $trigger)
		{
			if (in_array($trigger->module, $module_list))
			{
				// 고급 삭제 옵션
				if ($advanced === TRUE)
				{
					$oModule = getModule($trigger->module, strtolower($trigger->type));
					if (!@method_exists($oModule, $trigger->called_method))
					{
						$triggers_deleted[] = $trigger;
					}
				}
			}
			else
			{
				$triggers_deleted[] = $trigger;
			}
		}

		return $triggers_deleted;
	}

	/**
	 * @brief 삭제해도 상관없는 모듈 설정 목록 반환
	 * @return array
	 */
	function getModuleConfigToBeDeleted()
	{
		// DB 상의 모듈 설정 목록
		$output = executeQueryArray('profiler.getModuleConfig');
		$module_config = $output->data;

		// 설치되어 있는 모듈 목록
		$module_list = $this->getModuleList();

		// 삭제해도 상관없는 모듈 설정 목록
		$config_deleted = array();
		foreach ($module_config as $config)
		{
			if (!in_array($config->module, $module_list))
			{
				$config_deleted[] = $config;
			}
		}

		return $config_deleted;
	}
}

/* End of file profiler.admin.model.php */
/* Location: ./modules/profiler/profiler.admin.model.php */
