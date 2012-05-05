PluginManager
=============

manage plugins in your app.

## SETUP

```php
# APP/Config/bootstrap.php

CakePlugin::load('PluginManager');
$plugins = array(
  // folder name => git repository
	'Users' => array(
		'origin' => 'git://github.com/CakeDC/users.git',
		'branch' => '2.0',
	),
	'Search' => array(
    'origin' => 'git://github.com/CakeDC/search.git',
  ),
	'Migrations' =>  => array(
    'origin' => 'git://github.com/CakeDC/migrations.git',
  ),
);
Configure::write('PluginManager.plugins', $plugins);
```

## USAGE

*You need to run this command from the toplevel of the working tree.*

```sh
./Console/cake PluginManager.plugin_manager run --dry-run
```

If execute git-submodule add, remove --dry-run option.

### OPTIONs

run
- -f , --force : execute without confirm messages
- -p , --plugindir : plugin directory. [Plugin] is default. (e.g. app/Plugin)
- --dry-run : show commands only

## Git command sample

```sh
git submodule add -b 2.0 git://github.com/CakeDC/users.git Plugin/Users
```