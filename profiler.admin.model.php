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
		$page = (int)$page;
		$page_count = (int)$page_count;
		$list_count = (int)$list_count;
		$total_count = count($args);

		if($page < 1)
		{
			$page = 1;
		}

		if(!$page_count)
		{
			$page_count = 10;
		}

		if(!$list_count)
		{
			$list_count = 20;
		}

		if($total_count)
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

		if($page > $total_page)
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

		if(isset($args->hash_id, $args->start, $args->end))
		{
			$cond->hash_id = $args->hash_id;
			$cond->start = $args->start;
			$cond->end = $args->end;
		}

		// $cond->like_caller = $args->like_caller;
		// $cond->like_called = $args->like_called;

		$output = executeQueryArray('profiler.getStatisticSlowlog', $cond);

		return $output;
	}

	/**
	 * @brief Slowlog 데이터 반환
	 * @param stdClass $args
	 * @return array
	 */
	function getStaticsSlowlogCount($type, $count, $args = NULL)
	{
		$cond = new stdClass();
		$cond->type = $type;
		$cond->list_count = $count;

		if(isset($args->hash_id, $args->start, $args->end))
		{
			$cond->hash_id = $args->hash_id;
			$cond->start = $args->start;
			$cond->end = $args->end;
		}

		// $cond->like_caller = $args->like_caller;
		// $cond->like_called = $args->like_called;

		$output = executeQueryArray('profiler.getStatisticSlowlogCount', $cond);

		return $output;
	}

	/**
	 * @brief DB의 기록된 트리거 목록을 가져옴
	 * @return array
	 */
	function getTriggers($trigger_name, $called_position)
	{
		$triggers = false;
		$oCacheHandler = CacheHandler::getInstance('object', NULL, TRUE);

		if($oCacheHandler->isSupport())
		{
			$object_key = $trigger_name.'_'.$called_position;
			$cache_key = $oCacheHandler->getGroupKey('triggers', $object_key);
			$triggers = $oCacheHandler->get($cache_key);
		}

		if($triggers === false)
		{
			$args = new stdClass();
			$args->trigger_name = $trigger_name;
			$args->called_position = $called_position;
			$output = executeQueryArray('module.getTriggers', $args);
			$triggers = $output->data;
			if($output->toBool() && $oCacheHandler->isSupport())
			{
				$oCacheHandler->put($cache_key, $triggers);
			}
		}

		return $triggers;
	}

	/**
	 * @brief 설치된 모듈 이름 목록 반환
	 * @return array
	 */
	function getModuleList()
	{
		$oModuleModel = getModel('module');
		$modules_info = $oModuleModel->getModuleList();
		$module_list = array();

		foreach($modules_info as $module_info)
 		{
			// 모듈 이름만 배열에 추가
			$module_list[] = $module_info->module;
 		}

		return $module_list;
	}

	/**
	 * @brief DB 상의 테이블 목록 반환
	 * @return array
	 */
	function getTableList()
	{
		$table_list = array();
		$query = "";
		$oDB = DB::getInstance();
		switch($oDB->db_type)
		{
			case 'mysql':
			case 'mysql_innodb':
			case 'mysqli':
			case 'mysqli_innodb':
				$query = "select table_name as name, engine as type, table_collation as collation, table_rows as rows, data_length, index_length, data_free as overhead from information_schema.tables where table_schema = database() and table_name like '" . $oDB->prefix . "%' order by table_name asc";
				break;

			// @TODO type, collation 값 출력 연구
			case 'mssql':
				$query = "select o.name as name, i.rows as rows, i.dpages * 8192 as data_length, (i.used - i.dpages) * 8192 as index_length, (i.reserved - i.used) * 8192 as overhead from sysindexes i, sysobjects o where i.indid in (0, 1, 255) and o.id = i.id and o.name like '" . $oDB->prefix . "%' and o.xtype = 'U' order by o.name asc";
				break;

			/*
			// @TODO 쿼리문 작성
			case 'cubrid':
				break;
			*/
		}

		$result = $oDB->_query($query);
		$table_list = $oDB->_fetch($result);

		return $table_list;
	}

	/**
	 * @brief 삭제해도 상관없는 트리거 목록 반환
	 * @param boolean $advanced
	 * @return array
	 */
	function getTriggersToBeDeleted($advanced = FALSE)
	{
		// DB 상의 트리거 목록
		$trigger_list = $this->getTriggers();

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
	function getModuleConfigToBeDeleted($advanced = FALSE)
	{
		$oModuleModel = getModel('module');
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
			else if($advanced === TRUE && $config->site_srl)
			{
				$module_site_srl = $oModuleModel->getSiteInfo($config->site_srl);
				if(!in_array($module_site_srl->module, $module_list))
				{
					$invalid_module_config[] = $config;
				}
			}
		}

		return $invalid_module_config;
	}

	/**
	 * @brief 삭제해도 상관없는 애드온 설정 목록 반환
	 * @param boolean $advanced
	 * @return array
	 */
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

		$invalid_addon_config = array();
		foreach($addon_config as $config)
		{
			$addons_j_list[] = $config->addon;
			if(!in_array($config->addon, $addon_list))
			{
				$invalid_addon_config[] = $config;
			}
			else if($advanced === TRUE && $config->site_srl)
			{
				$addon_site_srl = $oModuleModel->getSiteInfo($config->site_srl);
				if(!in_array($addon_site_srl->module, $module_list))
				{
					$invalid_addon_config[] = $config;
				}
			}
		}

		return $invalid_addon_config;
	}

	/**
	 * @brief 정리해야 할 테이블 목록 반환
	 * @return array
	 */
	function getTableToBeArranged()
	{
		$oDB = DB::getInstance();

		// 설치되어 있는 모듈 목록
		$module_list = $this->getModuleList();

		// DB 상의 테이블 목록
		$table_list = $this->getTableList();
		$oAddonAdminModel = getAdminModel('addon');
		$addon_list = $oAddonAdminModel->getAddonList();

		// 실제 사용하고 있는 테이블 목록
		$valid_table_list = array();
		foreach($module_list as $module_name)
		{
			$module_path = ModuleHandler::getModulePath($module_name);
			$schemas_path = $module_path . 'schemas';
			if(file_exists(FileHandler::getRealPath($schemas_path)))
			{
				$table_files = FileHandler::readDir($schemas_path, '/(\.xml)$/');

				foreach($table_files as $table_file)
				{
					list($table_name) = explode('.', $table_file);
					if($oDB->isTableExists($table_name))
					{
						$valid_table_list[] = $table_name;
					}
				}
			}
		}

		foreach ($addon_list as $val)
		{
			$addon_path = $oAddonAdminModel->getAddonPath($val->addon_name);
			$a_schemas_path = $addon_path . 'schemas';
			if(file_exists(FileHandler::getRealPath($a_schemas_path)))
			{
				$addon_table_files = FileHandler::readDir($a_schemas_path, '/(\.xml)$/');

				foreach($addon_table_files as $a_table_file)
				{
					list($a_table_name) = explode('.', $a_table_file);
					if($oDB->isTableExists($a_table_name))
					{
						$valid_table_list[] = $a_table_name;
					}
				}
			}
		}

		// 정리해야 할 테이블 목록
		$arrange_table_list = array();
		foreach($table_list as $table_info)
		{
			$table_info->to_be_deleted = !in_array(substr($table_info->name, strlen($oDB->prefix)), $valid_table_list);
			$table_info->to_be_repaired = !!$table_info->overhead;

			if($table_info->to_be_deleted || $table_info->to_be_repaired)
			{
				$arrange_table_list[] = $table_info;
			}
		}

		return $arrange_table_list;
	}
}

/* End of file profiler.admin.model.php */
/* Location: ./modules/profiler/profiler.admin.model.php */
