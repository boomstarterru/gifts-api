<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 16.01.14
 * Time: 10:13
 */

class HTTPServer {
    protected static $server = NULL;

    public static function startServer()
    {
        $descriptor = array();

        self::$server = proc_open('php -S localhost:8000 router.php', $descriptor, $pipes, __DIR__);
        sleep(2);

        return self::$server;
    }

    public static function stopServer()
    {
        $status = proc_get_status(self::$server);

        if ($status['running'] == true) {
            $ppid = $status['pid'];

            $pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);

            foreach($pids as $pid) {
                if(is_numeric($pid)) {
                    posix_kill($pid, 9); //9 is the SIGKILL signal
                }
            }
        }

        proc_terminate(self::$server);
        proc_close(self::$server);
    }
}