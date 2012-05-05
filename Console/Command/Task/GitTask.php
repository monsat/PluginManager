<?php

class GitTask extends AppShell {
	public $plugindir = 'Plugin';
	public $dryrun = false;
	public $force = false;
	private $_commands = array();

	public function run() {
		$message = 'OK ?';
		if (!$this->dryrun && ($this->params['force'] || strtolower($this->in($message , array('y', 'n'), 'y')) === 'y')) {
			foreach ($this->_commands as $cmd) {
				$this->out(shell_exec($cmd));
			}
			$this->_commands = array();
		}
	}

	public function submoduleAdd($name, $repos, $branch = '') {
		$repos = escapeshellarg($repos);
		$plugindir = escapeshellarg($this->plugindir . DS . $name);
		if (!empty($branch)) {
			$branch = escapeshellarg($branch);
			$this->_commands[] = $cmd = "git submodule add -b $branch $repos $plugindir";
		} else {
			$this->_commands[] = $cmd = "git submodule add $repos $plugindir";
		}
		$this->out($cmd);
	}
}
