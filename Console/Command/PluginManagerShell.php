<?php
App::uses('Folder', 'Utility');

class PluginManagerShell extends AppShell {
	public $tasks = array('PluginManager.Git');
	protected $_errConfig = <<<EOL
######## Configure #######
# App/Config/bootstrap.php

\$plugins = array(
	// folder name => [ origin => repository ( , branch => branch ) ]
	'Users' => array(
		'origin' => 'git://github.com/CakeDC/users.git',
		'branch' => '2.0',
	),
);
Configure::write('PluginManager.plugins', \$plugins);
EOL;

	public function main() {
		$this->_checkConfig();
		$this->out($this->OptionParser->help());
	}

	public function run() {
		$this->_checkParams();
		// run
		$plugins = $this->_checkConfig();
		$pwd = trim(shell_exec('pwd'));
		$fullPluginDir = $pwd . DS . $this->Git->plugindir;
		$folder = new Folder($fullPluginDir);
		foreach ($plugins as $name => $plugin) {
			if ($folder->cd($fullPluginDir . DS . $name)) {
				$this->out($name . ' is exsist.');
				continue;
			}
			// does not exist
			if (empty($plugin['branch'])) {
				$this->Git->submoduleAdd($name, $plugin['origin']);
			} else {
				$this->Git->submoduleAdd($name, $plugin['origin'], $plugin['branch']);
			}
		}
		$this->Git->run();
	}

	public function add() {
		$branch = empty($this->args[2]) ? '' : $this->args[2];
		$this->Git->submoduleAdd($this->args[0], $this->args[1], $branch);
		$this->Git->run();
	}

	protected function _checkConfig() {
		$plugins = Configure::read('PluginManager.plugins');
		if (empty($plugins) || !is_array($plugins)) {
			$this->out($this->_errConfig);
			$this->hr();
			return array();
		}
		return $plugins;
	}

	protected function _checkParams() {
		if (!empty($this->params['plugindir'])) {
			$this->Git->plugindir = $this->params['plugindir'];
		}
		$this->Git->dryrun = $this->params['dry-run'];
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description("Add plugins using by git-submodule add.\nYou need to run this command from the toplevel of the working tree.")->
			addOption('force', array(
					'short' => 'f',
					'help' => 'execute without confirm messages',
					'boolean' => true,
				)
			)->
			addSubcommand('run', array(
				'help' => 'add plugins as git-submodule',
				'parser' => array(
					'description' => array('add plugins as git-submodule'),
					'options' => array(
						'force' => array(
							'short' => 'f',
							'help' => 'execute without confirm messages',
							'boolean' => true,
						),
						'plugindir' => array(
							'short' => 'p',
							'help' => 'plugin directory. [Plugin] is default. (e.g. app/Plugin)',
						),
						'dry-run' => array(
							'help' => 'show commands only',
							'boolean' => true,
						),
					),
				)
			))->
			addSubcommand('add', array(
				'help' => 'add one plugin as git-submodule',
				'parser' => array(
					'description' => array('add one plugin as git-submodule'),
					'arguments' => array(
						'PluginName' => array(
							'help' => 'plugin name. (e.g. Users)',
							'required' => true,
						),
						'Repository' => array(
							'help' => 'remote reopository to git-submodule add. (e.g. git://github.com/CakeDC/users.git)',
							'required' => true,
						),
						'BranchName' => array(
							'help' => 'branch name to checkout. empty is current branch. (e.g. 2.0)',
							'required' => false,
						),
					),
					'options' => array(
						'force' => array(
							'short' => 'f',
							'help' => 'execute without confirm messages',
							'boolean' => true,
						),
						'plugindir' => array(
							'short' => 'p',
							'help' => 'plugin directory. [Plugin] is default. (e.g. app/Plugin)',
						),
						'dry-run' => array(
							'help' => 'show commands only',
							'boolean' => true,
						),
					),
				)
			));
	}

}
