<?php
set_time_limit(0);
ini_set('mysql.connect_timeout', '0');
ini_set('max_execution_time', '0');
declare(ticks=1);

use Core\ErrorHandler;
use Core\Jobs;

require(__DIR__ . "/bootstrap.php");

$underSystemd = (bool) (getenv('TRAVIUM_UNDER_SYSTEMD') || getenv('INVOCATION_ID'));
global $PIDs, $loop;
$PIDs = [];
$loop = true;

if (!$underSystemd) {
    // ORIGINAL DAEMONIZE BLOCK
    $automationLogFile = dirname(ERROR_LOG_FILE) . "/automation.log";
    $autoPID = pcntl_fork();
    fclose(STDIN);
    fclose(STDOUT);
    fclose(STDERR);
    $STDIN  = fopen('/dev/null', 'r');
    $STDOUT = fopen($automationLogFile, 'wb');
    $STDERR = fopen($automationLogFile, 'wb');
    if ($autoPID) { exit(0); }
    if ($autoPID == -1) { exit(1); }
    $newSID = posix_setsid();
    if ($newSID === -1) { exit(1); }
} else {
    // UNDER SYSTEMD: do not daemonize, keep stdio attached to the journal
}

function sig_handler($signal)
{
    global $PIDs, $loop;
    $loop = false;
    foreach ($PIDs as $k => $v) {
        try {
            posix_kill($v, SIGTERM);
            unset($PIDs[$k]);
        } catch (\Exception $e) {
            ErrorHandler::getInstance()->handleExceptions($e);
        }
    }
    // Let systemd see a clean exit
    exit(0);
}

pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP, "sig_handler");

Jobs\Launcher::lunchJobs();

while ($loop) {
    pcntl_signal_dispatch();
    sleep(1);
}