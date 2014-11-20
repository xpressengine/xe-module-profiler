<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  profilerController
 * @author NAVER (developers@xpressengine.com)
 * @brief  Profiler module controller class.
 */

class profilerController extends profiler
{
	function init()
	{
	}

	/**
	 * @brief Slowlog 기록
	 * @param stdClass $args
	 * @return mixed
	 */
	function triggerWriteSlowlog($args)
	{
		$oProfilerModel = getModel('profiler');
		$config = $oProfilerModel->getConfig();

		// 슬로우 로그를 쓰지 않을경우 리턴
		if($config->slowlog->enabled != 'Y') return new Object();

		// 잘못된 인자 검사
		if(!is_object($args))
		{
			$args = new stdClass();
		}

		if($args->_log_type == 'flush')
		{
			if(!isset($GLOBALS['profiler_Slowlog']))
			{
				$GLOBALS['profiler_Slowlog'] = array();
			}
			foreach($GLOBALS['profiler_Slowlog'] as $args)
			{
				// 잘못된 인자 검사
				if(!is_object($args))
				{
					$args = new stdClass();
				}

				// hash id 생성
				$type_hash_id = md5($args->caller . '@' . $args->called);

				// type에 등록되어 있는지 확인
				$cond = new stdClass();
				$cond->hash_id = $type_hash_id;
				$output = executeQuery('profiler.getSlowlogType', $cond);

				// type에 등록되어 있지 않으면 추가
				if(!$output->data)
				{
					$slowlog_type = new stdClass();
					$slowlog_type->type = $args->_log_type;
					$slowlog_type->hash_id = $type_hash_id;
					$slowlog_type->caller = $args->caller;
					$slowlog_type->called = $args->called;
					$slowlog_type->called_extension = $args->called_extension;
					$output = executeQuery('profiler.insertSlowlogType', $slowlog_type);
					if(!$output->toBool())
					{
						unset($GLOBALS['profiler_Slowlog']);
						return $output;
					}
				}

				// 수행 시간을 기록
				$slowlog = new stdClass();
				$slowlog->type_hash_id = $type_hash_id;
				$slowlog->elapsed_time = $args->_elapsed_time;
				$slowlog->logged_timestamp = time();
				$output = executeQuery('profiler.insertSlowlog', $slowlog);
				if(!$output->toBool())
				{
					unset($GLOBALS['profiler_Slowlog']);
					return $output;
				}
			}

			unset($GLOBALS['profiler_Slowlog']);
		}
		else
		{
			if(!isset($GLOBALS['profiler_Slowlog']))
			{
				$GLOBALS['profiler_Slowlog'] = array();
			}

			if($args->_log_type == 'trigger')
			{
				if($args->_elapsed_time < $config->slowlog->time_trigger) return new Object();
			}
			else if($args->_log_type == 'addon')
			{
				if($args->_elapsed_time < $config->slowlog->time_addon) return new Object();
			}

			$GLOBALS['profiler_Slowlog'][] = $args;
		}
	}
}

/* End of file profiler.controller.php */
/* Location: ./modules/profiler/profiler.controller.php */
