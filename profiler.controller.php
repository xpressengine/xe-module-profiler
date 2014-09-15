<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

class profilerController extends profiler
{
	function init()
	{
	}

	/**
	 * slowlog 기록
	 *
	 * @param stdClass $args
	 * @return mixed
	 */
	function triggerWriteSlowlog($args)
	{
		$type_hash_id = md5($args->caller . '@' . $args->called);

		// type에 등록되어 있는지 확인
		$cond = new stdClass;
		$cond->hash_id = $type_hash_id;
		$output = executeQuery('profiler.getSlowlogType', $cond);

		// type에 등록되어 있지 않으면 추가
		if(!$output->data)
		{
			$slowlog_type = new stdClass;
			$slowlog_type->type = $args->_log_type;
			$slowlog_type->hash_id = $type_hash_id;
			$slowlog_type->caller = $args->caller;
			$slowlog_type->called = $args->called;
			$slowlog_type->called_extension = $args->called_extension;
			$output = executeQuery('profiler.insertSlowlogType', $slowlog_type);
		}

		// 수행 시간을 기록
		$slowlog = new stdClass;
		$slowlog->type_hash_id = $type_hash_id;
		$slowlog->elapsed_time = $args->_elapsed_time;
		$slowlog->logged_timestamp = time();
		$output = executeQuery('profiler.insertSlowlog', $slowlog);
	}
}
/* End of file */
