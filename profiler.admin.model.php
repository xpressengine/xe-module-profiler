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

		foreach($modules_info as $module_info)
 		{
			// 모듈 이름만 배열에 추가
			$module_list[] = $module_info->module;
 		}

		return $module_list;
	}

	function getTableList()
	{
		$table_list = array();

		$oDB = DB::getInstance();
		switch($oDB->db_type)
		{
			case 'mysql':
			case 'mysql_innodb':
			case 'mysqli':
			case 'mysqli_innodb':
				$query[] = "SELECT table_name AS 'table_name'";
				$query[] = 'FROM information_schema.tables';
				$query[] = 'WHERE table_schema = DATABASE()';
				$query[] = "AND table_name LIKE '" . $oDB->prefix . "%'";
				$query = implode(' ', $query);

				$result = $oDB->_query($query);
				$temp = $oDB->_fetch($result);

				foreach($temp as $val)
				{
					$arr = explode($oDB->prefix, $val->table_name);
					$table_list[] = $arr[1];
				}
				break;
			/*
			// 아래 쿼리문은 테스트 환경이 없어서 작성하지 못 함
			// 차후에 테스트 환경을 구축해서 쿼리문 작성 요망
			case 'mssql':
				break;

			case 'cubrid':
				$query[] = "SELECT class_name AS 'table_name'";
				$query[] = 'FROM db_class';
				$query[] = "WHERE class_name LIKE '" . $oDB->prefix . "%'";

				break;
			*/
		}

		return $table_list;
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
		$invalid_trigger_list = array();
		foreach($trigger_list as $trigger)
		{
			if(in_array($trigger->module, $module_list))
			{
				// 고급 삭제 옵션
				if($advanced === TRUE)
				{
					$oModule = getModule($trigger->module, strtolower($trigger->type));
					if(!@method_exists($oModule, $trigger->called_method))
					{
						$invalid_trigger_list[] = $trigger;
					}
				}
			}
			else
			{
				$invalid_trigger_list[] = $trigger;
			}
		}

		return $invalid_trigger_list;
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
		$invalid_module_config = array();
		foreach($module_config as $config)
		{
			if(!in_array($config->module, $module_list))
			{
				$invalid_module_config[] = $config;
			}
		}

		return $invalid_module_config;
	}

	/**
	 * @brief 삭제해도 상관없는 테이블 목록 반환
	 * @return array
	 */
	function getTableToBeDeleted()
	{
		$oDB = DB::getInstance();

		// 설치되어 있는 모듈 목록
		$module_list = $this->getModuleList();

		// DB 상의 테이블 목록
		$table_list = $this->getTableList();

		// 실제 사용하고 있는 테이블 목록
		$valid_table_list = array();
		foreach($module_list as $key => $module_name)
		{
			$module_path = ModuleHandler::getModulePath($module_name);
			if(file_exists(FileHandler::getRealPath($module_path . 'schemas')))
			{
				$table_files = FileHandler::readDir($module_path . 'schemas', '/(\.xml)$/');
				for($i = 0; $i < count($table_files); $i++)
				{
					list($table_name) = explode('.', $table_files[$i]);
					if($oDB->isTableExists($table_name))
					{
						$valid_table_list[] = $table_name;
					}
				}
			}
		}

		// 삭제해도 상관없는 테이블 목록
		$invalid_table_list = array_diff($table_list, $valid_table_list);

		return $invalid_table_list;
	}

	function getAddonConfigToBeDeleted($advanced = FALSE)
	{
		$oAddonAdminModel = getAdminModel('addon');
		$addon_foreach = $oAddonAdminModel->getAddonList();
		$oModuleModel = getModel('module');

		foreach($addon_foreach as $list)
		{
			$addon_list[] = $list->addon;
		}

		$output = executeQueryArray('profiler.getAddonConfigList');
		$addon_config = $output->data;

		$module_list = $this->getModuleList();

		foreach($addon_config as $config)
		{
			$addons_j_list[] = $config->addon;
			if(!in_array($config->addon, $addon_list))
			{
				$invalid_addon_config[] = $config;
			}
			else
			{
				if($advanced === TRUE && $config->site_srl)
				{
					$addon_site_srl = $oModuleModel->getSiteInfo($config->site_srl);
					if(!in_array($addon_site_srl->module, $module_list))
					{
						$invalid_addon_config[] = $config;
					}
				}
			}
		}

		return $invalid_addon_config;
	}

	function getTemporaryDocumentCount($DocumentType, $args = NULL)
	{
		$obj = new StdClass();
		$obj->start_time = $args->document_start_time;
		$obj->end_time = $args->document_end_time;

		switch($DocumentType)
		{
			case 'temporary':
				$output = executeQuery('profiler.getTemporaryDocumentCounts', $obj);
				break;

			case 'autosave':
				$output = executeQuery('profiler.getAutosaveDocumentCounts', $obj);
				break;

			default:
				return false;
				break;
		}

		if(!$output->data)
		{
			$output->bool = false;
		}
		return $output;
	}

	function deleteTemporaryDocuments($documentType, $args = NULL)
			case 'temporary':
				$output = executeQuery('profiler.deleteTemporaryDocuments', $obj);
				break;

			case 'autosave':
				$output = executeQuery('profiler.deleteAutosaveDocuments', $obj);
				break;

			default:
				return false;
				break;
		}

		if(!$output->data)
		{
			$output->data = false;
		}
		return $output;
	}
}

/* End of file profiler.admin.model.php */
/* Location: ./modules/profiler/profiler.admin.model.php */