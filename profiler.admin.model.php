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
	 * slowlog 데이터 반환
	 *
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
		if (!$output->data)
		{
			$output->data = array();
		}

		return $output;
	}
}

/* End of file profiler.admin.model.php */
/* Location: ./modules/profiler/profiler.admin.model.php */