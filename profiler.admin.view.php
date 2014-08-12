<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

class profilerAdminView extends profiler
{
	function init()
	{
		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile(str_replace('dispProfilerAdmin', '', $this->act));
	}
}
/* End of file */
