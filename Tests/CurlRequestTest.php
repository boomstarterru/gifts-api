<?php
/**
 * Created by PhpStorm.
 * User: vital
 * Date: 09.12.13
 * Time: 18:20
 */
require_once __DIR__ . '/../Boomstarter/API.php';

class CurlRequestTest extends PHPUnit_Framework_TestCase
{
    protected static $server = NULL;

    public static function setUpBeforeClass()
    {
        // local web server
        self::$server = self::startServer();
    }

    public static function tearDownAfterClass()
    {
        self::stopServer(self::$server);
    }

    private function startServer()
    {
        $descriptor = array();

        $server = proc_open('php -S localhost:8000 router.php', $descriptor, $pipes, __DIR__);
        sleep(2);

        return $server;
    }

    private function stopServer($server)
    {
        $status = proc_get_status($server);

        if ($status['running'] == true) {
            $ppid = $status['pid'];

            $pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);

            foreach($pids as $pid) {
                if(is_numeric($pid)) {
                    posix_kill($pid, 9); //9 is the SIGKILL signal
                }
            }
        }

        proc_terminate($server);
        proc_close($server);
    }

    public function testExecute()
    {
        $url = 'http://localhost:8000/api/v1.1/partners/gifts';

        $request = new Boomstarter\CurlRequest($url);

        $request->setOption(CURLOPT_RETURNTRANSFER, TRUE);
        $request->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $result = $request->execute();

        $this->assertTrue(is_string($result));
        $this->assertJson($result);

        $decoded = json_decode($result, TRUE);

        $this->assertEquals('/api/v1.1/partners/gifts', parse_url($decoded['debug']['server']['REQUEST_URI'], PHP_URL_PATH));
    }
}

