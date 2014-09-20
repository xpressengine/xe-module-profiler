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