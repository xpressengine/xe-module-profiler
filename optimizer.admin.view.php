<?php
class optimizerAdminView extends optimizer
{
	function init()
	{
		$this->setTemplatePath($this->module_path . 'tpl');
		$this->setTemplateFile(str_replace('dispOptimizerAdmin', '', $this->act));
	}
}
/* End of file */
