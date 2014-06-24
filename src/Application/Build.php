<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/nicksagona/PopPHP
 * @category   Pop
 * @package    Pop_Application
 * @author     Nick Sagona, III <info@popphp.org>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Application;

/**
 * Application build class
 *
 * @category   Pop
 * @package    Pop_Application
 * @author     Nick Sagona, III <info@popphp.org>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
class Build
{

    /**
     * CLI error codes & messages
     * @var array
     */
    protected static $cliErrorCodes = array(
        0 => 'Unknown error.',
        1 => 'You must pass a source folder and a output file to generate a class map file.',
        2 => 'The source folder passed does not exist.',
        3 => 'The output file passed must be a PHP file.',
        4 => 'You must pass an install file to install the project.',
        5 => 'Unknown option: ',
        6 => 'You must pass at least one argument.',
        7 => 'That folder does not exist.',
        8 => 'The folder argument is not a folder.'
    );

    /**
     * Install the project based on the available config files
     *
     * @param string $installFile
     * @return void
     */
    public static function install($installFile)
    {
        // Display instructions to continue
        $dbTables = array();
        self::instructions();

        $input = self::cliInput();
        if ($input == 'n') {
            echo 'Aborted.' . PHP_EOL . PHP_EOL;
            exit(0);
        }

        // Get the install config.
        $installDir = realpath(dirname($installFile));
        $install = include $installFile;

        // Check if a project folder already exists.
        if (file_exists($install->project->name)) {
            echo wordwrap('Application folder exists. This may overwrite any project files you may already have under that project folder.', 70, PHP_EOL) . PHP_EOL;
            $input = self::cliInput();
        } else {
            $input = 'y';
        }

        // If 'No', abort
        if ($input == 'n') {
            echo 'Aborted.' . PHP_EOL . PHP_EOL;
            exit(0);
        // Else, continue
        } else {
            $db = false;
            $databases = array();

            // Test for a database creds and schema, and ask
            // to test and install the database.
            if (isset($install->databases)) {
                $databases =  $install->databases->asArray();
                echo 'Database credentials and schema detected.' . PHP_EOL;
                $input = self::cliInput('Test and install the database(s)?' . ' (Y/N) ');
                $db = ($input == 'n') ? false : true;
            }

            // Handle any databases
            if ($db) {
                // Get current error reporting setting and set
                // error reporting to E_ERROR to suppress warnings
                $oldError = ini_get('error_reporting');
                error_reporting(E_ERROR);

                // Test the databases
                echo 'Testing the database(s)...' . PHP_EOL;

                foreach ($databases as $dbname => $db) {
                    echo 'Testing' . ' \'' . $dbname . '\'...' . PHP_EOL;
                    if (!isset($db['type']) || !isset($db['database'])) {
                        echo 'The database type and database name must be set for the database ' . '\'' . $dbname . '\'.' . PHP_EOL . PHP_EOL;
                        exit(0);
                    }
                    $check = Build\Dbs::check($db);
                    if (null !== $check) {
                        echo $check . PHP_EOL . PHP_EOL;
                        exit(0);
                    } else {
                        echo 'Database' . ' \'' . $dbname . '\' passed.' . PHP_EOL;
                        echo 'Installing database' .' \'' . $dbname . '\'...' . PHP_EOL;
                        $tables = Build\Dbs::install($dbname, $db, $installDir, $install);
                        if (count($tables) > 0) {
                            $dbTables = array_merge($dbTables, $tables);
                        }
                    }
                }
                // Return error reporting to its original state
                error_reporting($oldError);
            }

            // Install base folder and file structure
            Build\Base::install($install);

            // Install project files
            Build\Applications::install($install, $installDir);

            // Install table class files
            if (count($dbTables) > 0) {
                Build\Tables::install($install, $dbTables);
            }

            // Install controller class files
            if (isset($install->controllers)) {
                Build\Controllers::install($install, $installDir);
            }

            // Install form class files
            if (isset($install->forms)) {
                Build\Forms::install($install);
            }

            // Install model class files
            if (isset($install->models)) {
                Build\Models::install($install);
            }

            // Create 'bootstrap.php' file
            Build\Bootstrap::install($install);

            echo 'Application install complete.' . PHP_EOL . PHP_EOL;
        }

    }

    /**
     * Display CLI instructions
     *
     * @return string
     */
    public static function instructions()
    {
        $msg1 = "This process will create and install the base foundation of your project under the folder specified in the install file. Minimally, the install file should return a Pop\\Config object containing your project install settings, such as project name, folders, forms, controllers, views and any database credentials.";
        $msg2 = "Besides creating the base folders and files for you, one of the main benefits is ability to test and install the database and the corresponding configuration and class files. You can take advantage of this by having the database SQL files in the same folder as your install file, like so:";
        echo wordwrap($msg1, 70, PHP_EOL) . PHP_EOL . PHP_EOL;
        echo wordwrap($msg2, 70, PHP_EOL) . PHP_EOL . PHP_EOL;
        echo 'projectname' . DIRECTORY_SEPARATOR . 'project.install.php' . PHP_EOL;
        echo 'projectname' . DIRECTORY_SEPARATOR . '*.sql' . PHP_EOL . PHP_EOL;
    }

    /**
     * Print the CLI help message
     *
     * @return void
     */
    public static function cliHelp()
    {
        echo ' -c --check                     ' . 'Check the current configuration for required dependencies' . PHP_EOL;
        echo ' -h --help                      ' . 'Display this help' . PHP_EOL;
        echo ' -i --install file.php          ' . 'Install a project based on the install file specified' . PHP_EOL;
        echo ' -l --lang fr                   ' . 'Set the default language for the project' . PHP_EOL;
        echo ' -m --map folder file.php       ' . 'Create a class map file from the source folder and save to the output file' . PHP_EOL;
        echo ' -s --show                      ' . 'Show project install instructions' . PHP_EOL;
        echo ' -v --version                   ' . 'Display version of Pop PHP Framework and latest available' . PHP_EOL . PHP_EOL;
    }

    /**
     * Return a CLI error message based on the code
     *
     * @param int    $num
     * @param string $arg
     * @return string
     */
    public static function cliError($num = 0, $arg = null)
    {
        $i = (int)$num;
        if (!array_key_exists($i, self::$cliErrorCodes)) {
            $i = 0;
        }
        $msg = self::$cliErrorCodes[$i] . $arg . PHP_EOL .
               'Run \'.' . DIRECTORY_SEPARATOR . 'pop -h\' for help.' . PHP_EOL . PHP_EOL;
        return $msg;
    }

    /**
     * Return the (Y/N) input from STDIN
     *
     * @param  string $msg
     * @return string
     */
    public static function cliInput($msg = null)
    {
        echo ((null === $msg) ? 'Continue?' . ' (Y/N) ' : $msg);
        $input = null;

        while (($input != 'y') && ($input != 'n')) {
            if (null !== $input) {
                echo $msg;
            }
            $prompt = fopen("php://stdin", "r");
            $input = fgets($prompt, 5);
            $input = substr(strtolower(rtrim($input)), 0, 1);
            fclose ($prompt);
        }

        return $input;
    }

    /**
     * Return the location of the bootstrap file from STDIN
     *
     * @return string
     */
    public static function getBootstrap()
    {
        $msg = 'Enter the folder where the \'bootstrap.php\' is located in relation to the current folder: ';
        echo $msg;
        $input = null;

        while (!file_exists($input . '/bootstrap.php')) {
            if (null !== $input) {
                echo 'Bootstrap file not found. Try again.' . PHP_EOL . $msg;
            }
            $prompt = fopen("php://stdin", "r");
            $input = fgets($prompt, 255);
            $input = rtrim($input);
            fclose ($prompt);
        }

        return $input;
    }

    /**
     * Return the location of the vendor folder and the Pop PHP framework from STDIN
     *
     * @return string
     */
    public static function getPop()
    {
        $msg = 'Enter the folder where the \'vendor\' folder is contained in relation to the current folder: ';
        echo $msg;
        $input = null;

        while (!file_exists($input . '/vendor/PopPHPFramework/src/Pop/Loader/Autoloader.php')) {
            if (null !== $input) {
                echo 'Pop PHP Framework not found. Try again.' . PHP_EOL . $msg;
            }
            $prompt = fopen("php://stdin", "r");
            $input = fgets($prompt, 255);
            $input = rtrim($input);
            fclose ($prompt);
        }

        return $input;
    }

    /**
     * Return the two-letter language code from STDIN
     *
     * @param array $langs
     * @return string
     */
    public static function getLanguage($langs)
    {
        $msg = 'Enter the numeric code for the default language: ';
        echo $msg;
        $lang = null;
        $keys = array_keys($langs);

        while (!array_key_exists($lang, $keys)) {
            if (null !== $lang) {
                echo $msg;
            }
            $prompt = fopen("php://stdin", "r");
            $lang = fgets($prompt, 5);
            $lang = rtrim($lang);
            fclose ($prompt);
        }

        return $lang;
    }

    /**
     * Method to convert the string from under_score to camelCase format
     *
     * @param  string $string
     * @return string
     */
    public static function underscoreToCamelcase($string)
    {
        $strAry = explode('_', $string);
        $camelCase = null;
        $i = 0;

        foreach ($strAry as $word) {
            if ($i == 0) {
                $camelCase .= $word;
            } else {
                $camelCase .= ucfirst($word);
            }
            $i++;
        }

        return $camelCase;
    }

}
