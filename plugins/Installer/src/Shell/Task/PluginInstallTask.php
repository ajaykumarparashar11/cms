<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since    2.0.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://www.quickappscms.org
 * @license  http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
namespace Installer\Shell\Task;

use Cake\Console\Shell;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Network\Http\Client;
use Cake\Utility\Inflector;
use Cake\Validation\Validation;
use QuickApps\Core\Package\PluginPackage;
use QuickApps\Core\Package\Rule\RuleChecker;
use QuickApps\Core\Plugin;
use QuickApps\Event\HookAwareTrait;
use User\Utility\AcoManager;

/**
 * Plugins installer.
 *
 */
class PluginInstallTask extends Shell
{

    use HookAwareTrait;
    use ListenerHandlerTrait;

    /**
     * Flag that indicates the source package is a ZIP file.
     */
    const TYPE_ZIP = 'zip';

    /**
     * Flag that indicates the source package is a URL.
     */
    const TYPE_URL = 'url';

    /**
     * Flag that indicates the source package is a directory.
     */
    const TYPE_DIR = 'dir';

    /**
     * Contains tasks to load and instantiate.
     *
     * @var array
     */
    public $tasks = [
        'Installer.PluginToggle',
    ];

    /**
     * Path to package's extracted directory.
     *
     * @var string
     */
    protected $_workingDir = null;

    /**
     * The type of the package's source.
     *
     * @var string
     */
    protected $_sourceType = null;

    /**
     * Represents the plugins being installed.
     *
     * @var array
     */
    protected $_plugin = [
        'name' => '',
        'packageName' => '',
    ];

    /**
     * Removes the welcome message.
     *
     * @return void
     */
    public function startup()
    {
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->description(__d('installer', 'Install a new plugin.'))
            ->addOption('source', [
                'short' => 's',
                'help' => __d('system', 'Either a full path within filesystem to a ZIP file, or path to a directory representing an extracted ZIP file, or an URL from where download plugin package.'),
            ])
            ->addOption('theme', [
                'short' => 't',
                'help' => __d('installer', 'Indicates that the plugin being installed should be treated as a theme.'),
                'boolean' => true,
                'default' => false,
            ])
            ->addOption('activate', [
                'short' => 'a',
                'help' => __d('installer', 'Enables the plugin after intallation.'),
                'boolean' => true,
                'default' => false,
            ])
            ->addOption('no-callbacks', [
                'short' => 'c',
                'help' => __d('installer', 'Plugin events will not be trigged.'),
                'boolean' => true,
                'default' => false,
            ]);
        return $parser;
    }

    /**
     * Task main method.
     *
     * @return bool
     */
    public function main()
    {
        if (!$this->_init()) {
            $this->_reset();
            return false;
        }

        if (!$this->params['no-callbacks']) {
            // "before" events occurs even before plugins is moved to its destination
            $this->_attachListeners($this->_plugin['name'], normalizePath("{$this->_workingDir}/src/Event"));
            try {
                $event = $this->trigger("Plugin.{$this->_plugin['name']}.beforeInstall");
                if ($event->isStopped() || $event->result === false) {
                    $this->err(__d('installer', 'Task was explicitly rejected by the plugin.'));
                    $this->_reset();
                    return false;
                }
            } catch (\Exception $ex) {
                $this->err(__d('installer', 'Internal error, plugin did not respond to "beforeInstall" callback correctly.'));
                $this->_reset();
                return false;
            }
        }

        if (!$this->_movePackage()) {
            $this->_reset();
            return false;
        }

        $this->loadModel('System.Plugins');
        $entity = $this->Plugins->newEntity([
            'name' => $this->_plugin['name'],
            'package' => $this->_plugin['packageName'],
            'settings' => [],
            'status' => 0,
            'ordering' => 0,
        ]);

        if (!$this->Plugins->save($entity, ['atomic' => true])) {
            $this->_reset();
            return false;
        }

        if (!$this->params['no-callbacks']) {
            try {
                $event = $this->trigger("Plugin.{$this->_plugin['name']}.afterInstall");
            } catch (\Exception $ex) {
                $this->err(__d('installer', 'Plugin was installed but some errors occur.'));
            }
        }

        $pluginName = $this->_plugin['name']; // hold as _finish() erases it
        $this->_finish();

        if ($this->params['activate']) {
            $this->dispatchShell("Installer.plugins toggle -p {$pluginName} -s enable");
        }
        return true;
    }

    /**
     * Discards the install operation. Restores this class's status
     * to its initial state.
     *
     * @return void
     */
    protected function _reset()
    {
        if ($this->_sourceType !== self::TYPE_DIR && $this->_workingDir) {
            $source = new Folder($this->_workingDir);
            $source->delete();
        }

        $this->_workingDir =
        $this->_sourceType = null;
        $this->_plugin = [
            'name' => '',
            'packageName' => '',
        ];

        Plugin::dropCache();
        $this->_detachListeners();
    }

    /**
     * After installation is completed.
     *
     * @return void
     */
    protected function _finish()
    {
        global $classLoader; // composer's class loader instance
        snapshot();
        Plugin::dropCache();
        // trick: makes plugin visible to AcoManager
        $classLoader->addPsr4($this->_plugin['name'] . "\\", normalizePath(SITE_ROOT . "/plugins/{$this->_plugin['name']}/src"), true);
        AcoManager::buildAcos($this->_plugin['name']);
        $this->_reset();
    }

    /**
     * Moves the extracted package to its final destination.
     *
     * @param bool $clearDestination Set to true to delete the destination directory
     *  if already exists. Defaults to false; an error will occur if destination
     *  already exists. Useful for upgrade tasks
     * @return bool True on success
     */
    protected function _movePackage($clearDestination = false)
    {
        $source = new Folder($this->_workingDir);
        $destinationPath = normalizePath(SITE_ROOT . "/plugins/{$this->_plugin['name']}/");

        if ($this->_workingDir === $destinationPath) {
            return true;
        }

        if (!$clearDestination && file_exists($destinationPath)) {
            $this->err(__d('installer', 'Destination directory already exists, please delete manually this directory: {0}', $destinationPath));
            return false;
        } elseif ($clearDestination && file_exists($destinationPath)) {
            $destination = new Folder($destinationPath);
            if (!$destination->delete()) {
                $this->err(__d('installer', 'Destination directory could not be cleared, please check write permissions: {0}', $destinationPath));
                return false;
            }
        }

        if ($source->move(['to' => $destinationPath])) {
            return true;
        }

        $this->err(__d('installer', 'Error when moving package content.'));
        return false;
    }

    /**
     * Prepares this task and the package to be installed.
     *
     * @return bool True on success
     */
    protected function _init()
    {
        $this->params['source'] = str_replace('"', '', $this->params['source']);

        if (function_exists('ini_set')) {
            ini_set('max_execution_time', 300);
        } elseif (function_exists('set_time_limit')) {
            set_time_limit(300);
        }

        if (is_readable($this->params['source']) && is_dir($this->params['source'])) {
            $this->_sourceType = self::TYPE_DIR;
            return $this->_getFromDirectory();
        } elseif (is_readable($this->params['source']) && !is_dir($this->params['source'])) {
            $this->_sourceType = self::TYPE_ZIP;
            return $this->_getFromFile();
        } elseif (Validation::url($this->params['source'])) {
            $this->_sourceType = self::TYPE_URL;
            return $this->_getFromUrl();
        }

        $this->err(__d('installer', 'Unable to resolve the given source ({0}).', $this->params['source']));
        return false;
    }

    /**
     * Prepares install from given directory.
     *
     * @return bool True on success
     */
    protected function _getFromDirectory()
    {
        $this->_workingDir = normalizePath(realpath($this->params['source']) . '/');
        return $this->_validateContent();
    }

    /**
     * Prepares install from ZIP file.
     *
     * @return bool True on success
     */
    protected function _getFromFile()
    {
        $file = new File($this->params['source']);
        if ($this->_unzip($file->pwd())) {
            return $this->_validateContent();
        }

        $this->err(__d('installer', 'Unable to extract the package.'));
        return false;
    }

    /**
     * Prepares install from remote URL.
     *
     * @return bool True on success
     */
    protected function _getFromUrl()
    {
        try {
            $http = new Client(['redirect' => 3]); // follow up to 3 redirections
            $response = $http->get($this->params['source'], [], [
                'headers' => [
                    'X-Requested-With' => 'XMLHttpRequest'
                ]
            ]);
        } catch (\Exception $e) {
            $response = false;
        }

        if ($response && $response->isOk()) {
            $this->params['source'] = TMP . substr(md5($this->params['source']), 24) . '.zip';
            $file = new File($this->params['source']);
            $responseBody = $response->body();

            if (file_exists($file->pwd())) {
                $file->delete();
            }

            if (!empty($responseBody) &&
                $file->create() &&
                $file->write($responseBody, 'w+', true)
            ) {
                $file->close();
                return $this->_getFromFile();
                $this->err(__d('installer', 'Unable to extract the package.'));
                return false;
            }

            $this->err(__d('installer', 'Unable to download the file, check write permission on "{0}" directory.', TMP));
            return false;
        }

        $this->err(__d('installer', 'Could not download the package, no .ZIP file was found at the given URL.'));
        return false;
    }

    /**
     * Extracts the current ZIP package.
     *
     * @param  string $fule Full path to the ZIP package
     * @return bool True on success
     */
    protected function _unzip($file)
    {
        include_once Plugin::classPath('Installer') . 'Lib/pclzip.lib.php';
        $File = new File($file);
        $to = normalizePath($File->folder()->pwd() . '/' . $File->name() . '_unzip/');

        if (file_exists($to)) {
            $folder = new Folder($to);
            $folder->delete();
        } else {
            $folder = new Folder($to, true);
        }

        $PclZip = new \PclZip($file);
        $PclZip->delete(PCLZIP_OPT_BY_EREG, '/__MACOSX/');
        $PclZip->delete(PCLZIP_OPT_BY_EREG, '/\.DS_Store$/');

        if ($PclZip->extract(PCLZIP_OPT_PATH, $to)) {
            list($directories, $files) = $folder->read(false, false, true);
            if (count($directories) === 1 && empty($files)) {
                $container = new Folder($directories[0]);
                $container->move(['to' => $to]);
            }

            $this->_workingDir = $to;
            return true;
        }

        $this->err(__d('installer', 'Unzip error: {0}', $PclZip->errorInfo(true)));
        return false;
    }

    /**
     * Validates the content of working directory.
     *
     * @return bool True on success
     */
    protected function _validateContent()
    {
        if (!$this->_workingDir) {
            return false;
        }

        $errors = [];
        if (!file_exists("{$this->_workingDir}src") || !is_dir("{$this->_workingDir}src")) {
            $errors[] = __d('installer', 'Invalid package, missing "src" directory.');
        }

        if (!file_exists("{$this->_workingDir}composer.json")) {
            $errors[] = __d('installer', 'Invalid package, missing "composer.json" file.');
        } else {
            $jsonErrors = Plugin::validateJson("{$this->_workingDir}composer.json", true);
            if (!empty($jsonErrors)) {
                $errors[] = __d('installer', 'Invalid "composer.json".');
                $errors = array_merge($errors, (array)$jsonErrors);
            } else {
                $json = (new File("{$this->_workingDir}composer.json"))->read();
                $json = json_decode($json, true);
                list(, $pluginName) = packageSplit($json['name'], true);

                if ($this->params['theme'] && !str_ends_with($pluginName, 'Theme')) {
                    $this->err(__d('installer', 'The given package is not a valid theme.'));
                    return false;
                } elseif (!$this->params['theme'] && str_ends_with($pluginName, 'Theme')) {
                    $this->err(__d('installer', 'The given package is not a valid plugin.'));
                    return false;
                }

                $this->_plugin = [
                    'name' => (string)Inflector::camelize(str_replace('-', '_', $pluginName)),
                    'packageName' => $json['name'],
                ];

                if (Plugin::exists($this->_plugin['name'])) {
                    $exists = Plugin::get($this->_plugin['name']);
                    if ($exists->status) {
                        $errors[] = __d('installer', 'The plugin "{0}" is already installed.', $this->_plugin['name']);
                    } else {
                        $errors[] = __d('installer', 'The plugin "{0}" is already installed but disabled, maybe you want try to enable it?.', $this->_plugin['name']);
                    }
                }

                if (str_ends_with($this->_plugin['name'], 'Theme')) {
                    if (!file_exists("{$this->_workingDir}webroot/screenshot.png")) {
                        $errors[] = __d('installer', 'Missing "screenshot.png" file.');
                    }
                }

                if (isset($json['require'])) {
                    $checker = new RuleChecker($json['require']);
                    if (!$checker->check()) {
                        $errors[] = __d('installer', 'Plugin "{0}" depends on other packages, plugins or libraries that were not found: {1}', $this->_plugin['name'], $checker->fail(true));
                    }
                }
            }
        }

        if (!file_exists(SITE_ROOT . '/plugins') ||
            !is_dir(SITE_ROOT . '/plugins') ||
            !is_writable(SITE_ROOT . '/plugins')
        ) {
            $errors[] = __d('installer', 'Write permissions required for directory: {0}.', SITE_ROOT . '/plugins/');
        }

        foreach ($errors as $message) {
            $this->err($message);
        }

        return empty($errors);
    }
}