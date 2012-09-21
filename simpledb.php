<?php
/*
 * Simple Datbase with basic set of operations like
 * GET, SET, UNSET, NUMEQUALTO, COMMIT, ROLLBACK, BEGIN
 * 
 * @auther Mayur Ahir
 * @url https://github.com/ahirmayur/simpledb
 * 
 * USAGE:
 * You can use this script in two ways
 * 1) Use files for input
 *      $ php simpledb.php <filename> <ENTER>
 *      e.g php simpledb.php case1.txt
 * 
 * 2) Interactive Shell <ENTER>
 *      $ php simpledb.php
 */

class SimpleDatabase {

    public $transactions;
    public $database;

    public function __construct($data = null) {
        $this->transactions = array(); //Transaction Records
        $this->database = array(); //DB
    }

    public function set($name, $value = null) {
        if (!empty($this->transactions)) {
            if (isset($this->database[$name]) && !isset($this->transactions[0][$name])) {
                $this->transactions[0][$name] = $this->database[$name];
            }
            if (!isset($this->database[$name])) {
                $this->transactions[0][$name] = null;
            }
        }

        if ($value != null) {
            $this->database[$name] = $value;
        } else {
            unset($this->database[$name]);
        }
    }

    public function get($name) {
        if (isset($this->database[$name])) {
            fwrite(STDOUT, $this->database[$name] . "\n");
        } else {
            fwrite(STDOUT, "NULL\n");
        }
    }

    public function unsett($name) {
        /* unset() is PHP's reserved function so we use unsett() */
        if (isset($this->database[$name])) {
            $this->database[$name] = null;
        }
    }

    public function numequalto($value) {
        /* Count keys in current database */
        if ($count = count(array_keys($this->database, $value))) {
            fwrite(STDOUT, $count . "\n");
        } else {
            fwrite(STDOUT, "0\n");
        }
    }

    public function begin() {
        /* Start the transaction and push current database in front of transaction i.e. PUSH to front */
        if (isset($this->database)) {
            array_unshift($this->transactions, $this->database);
        } else {
            array_unshift($this->transactions, array());
        }
    }

    public function rollback() {
        if (!empty($this->transactions)) {
            foreach ($this->transactions[0] as $key => $value) {
                if ($value != null) {
                    $this->database[$key] = $value;
                } else {
                    unset($this->database[$key]);
                }
            }
            /* Move transactions to left by removing latest transaction i.e. POP from Front */
            array_shift($this->transactions);
        } else {
            fwrite(STDOUT, "INVALID ROLLBACK\n");
        }
    }

    public function commit() {
        /* Bigbang, the start! */
        $this->transactions = array();
    }

}

function CliErrorHandler($errno, $errstr, $errfile, $errline) {
    fwrite(STDERR, "$errstr\n");
}

$data = array();
fwrite(STDOUT, "*************************************************************************\n");
fwrite(STDOUT, "**************\t   Welcome to SimpleDB by Mayur Ahir\t*****************\n");
fwrite(STDOUT, "********\tGitHub: https://github.com/ahirmayur/simpledb\t*********\n");
fwrite(STDOUT, "*************************************************************************\n");

if (!empty($argv[1])) {
    fwrite(STDOUT, "\nFile Mode. Program will auto execute and will terminate on command 'END'\n\n");
    /* File Mode : We supplied an argument as filename */

    // Tell PHP to use the error handler 
    set_error_handler('CliErrorHandler');

    $file_name = $argv[1];
    if (!$file_handle = fopen($file_name, "r")) {
        exit(0); //Graceful Exit
    }

    while (!feof($file_handle)) {
        $line = trim(fgets($file_handle));
        $data[] = explode(' ', $line);
    }
    fclose($file_handle);
} else {
    fwrite(STDOUT, "\nCLI Mode. Start typing your input. Program will terminate on command 'END'\n\n");

    //Read command line input till we have END
    do {
        // A string from STDIN, ignoring whitespace characters 
        $command = trim(fgets(STDIN));
        $data[] = explode(' ', $command);
    } while (strtoupper($command) != 'END');
}

fwrite(STDOUT, "**************\tOutput\t*****************\n");

$smpldb = new SimpleDatabase();

foreach ($data as $cmd) {
    switch ($cmd[0]) {
        case 'SET':
            $smpldb->set($cmd[1], $cmd[2]);
            break;

        case 'GET':
            $smpldb->get($cmd[1]);
            break;

        case 'UNSET':
            $smpldb->unsett($cmd[1]);
            break;

        case 'NUMEQUALTO':
            $smpldb->numequalto($cmd[1]);
            break;

        case 'BEGIN':
            $smpldb->begin();
            break;

        case 'ROLLBACK':
            $smpldb->rollback();
            break;

        case 'COMMIT':
            $smpldb->commit();
            break;

        case 'END':
            exit(0);
            break;

        default:
            fwrite(STDOUT, "Sorry command $cmd[0] is invalid!\n");
            exit(0);
            break;
    }
}

exit(0);
?>
