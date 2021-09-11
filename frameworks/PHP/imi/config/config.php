<?php

use Imi\App;

$mode = App::isInited() ? App::getApp()->getType() : '';

return [
    // 项目根命名空间
    'namespace'    =>    'ImiApp',

    // 配置文件
    'configs'    =>    [
        'beans'        =>    __DIR__ . '/beans.php',
    ],

    // 扫描目录
    'beanScan'    =>    [
        'ImiApp\Listener',
    ],

    // 组件命名空间
    'components'    =>  [],

    // 主服务器配置
    'mainServer'    => 'swoole' === $mode ? [
        'namespace' =>  'ImiApp\ApiServer',
        'type'      =>  Imi\Swoole\Server\Type::HTTP,
        'host'      =>  '0.0.0.0',
        'port'      =>  8080,
        'mode'      =>  SWOOLE_BASE,
        'configs'   =>  [
            'worker_num'        => swoole_cpu_num(),
            'open_tcp_nodelay'  => true,
            'tcp_fastopen'      => true,
            'http_parse_post'   => false,
            'http_parse_cookie' => false,
            'http_parse_files'  => false,
            'http_compression'  => false,
        ],
    ] : [],

    // Workerman 服务器配置
    'workermanServer' => 'workerman' === $mode ? [
        // 服务器名，http 也可以改成 abc 等等，完全自定义
        'http' => [
            // 指定服务器命名空间
            'namespace' => 'ImiApp\ApiServer',
            // 服务器类型
            'type'      => Imi\Workerman\Server\Type::HTTP, // HTTP、WEBSOCKET、TCP、UDP
            'host'      => '0.0.0.0',
            'port'      => 8080,
            // socket的上下文选项，参考：http://doc3.workerman.net/315128
            'context'   => [],
            'configs'   => [
                // 支持设置 Workerman 参数
                'count' => shell_exec('nproc') ?: 32,
            ],
        ],
    ] : [],

    'db'    => [
        'defaultPool'   => 'Postgres' === getenv('TFB_TEST_DATABASE') ? 'pgsql' : 'mysql', // 默认连接池
        'connections'   => [
            'mysql' => [
                'host'        => 'tfb-database',
                'username'    => 'benchmarkdbuser',
                'password'    => 'benchmarkdbpass',
                'database'    => 'hello_world',
                'dbClass'     => \Imi\Db\Mysql\Drivers\Mysqli\Driver::class,
            ],
            'pgsql' => [
                'host'        => 'tfb-database',
                'username'    => 'benchmarkdbuser',
                'password'    => 'benchmarkdbpass',
                'database'    => 'hello_world',
                'dbClass'     => \Imi\Pgsql\Db\Drivers\PdoPgsql\Driver::class,
            ],
        ],
    ],

    // redis 配置
    'redis' => [
        // 默认连接池名
        'defaultPool'   => 'redis',
        'quickFromRequestContext'   =>    true, // 从当前上下文中获取公用连接
        'connections'   => [
            'redis' => [
                'host'      =>  '127.0.0.1',
                'port'      =>  6379,
                // 是否自动序列化变量
                'serialize' =>  true,
                // 密码
                'password'  =>  null,
                // 第几个库
                'db'        =>  0,
            ],
        ],
    ],

    'pools' => 'swoole' === $mode ? [
        // 连接池名称
        'mysql' => [
            'pool'    =>    [
                'class'        =>    \Imi\Swoole\Db\Pool\CoroutineDbPool::class,
                'config'    =>    [
                    // 池子中最多资源数
                    'maxResources' => intval(1024 / swoole_cpu_num()),
                    // 池子中最少资源数
                    'minResources' => class_exists(Imi\Pgsql\Main::class) ? 0 : 16,
                    'gcInterval'   => 0,
                    'checkStateWhenGetResource' =>  false,
                    'requestResourceCheckInterval' => 0,
                ],
            ],
            // resource也可以定义多个连接
            'resource'    =>    [
                'host'        => 'tfb-database',
                'username'    => 'benchmarkdbuser',
                'password'    => 'benchmarkdbpass',
                'database'    => 'hello_world',
                'dbClass'     => \Imi\Swoole\Db\Driver\Swoole\Driver::class,
            ],
        ],
        'pgsql' => [
            'pool'    =>    [
                'class'        =>    \Imi\Swoole\Db\Pool\CoroutineDbPool::class,
                'config'    =>    [
                    // 池子中最多资源数
                    'maxResources' => intval(1024 / swoole_cpu_num()),
                    // 池子中最少资源数
                    'minResources' => class_exists(Imi\Pgsql\Main::class) ? 16 : 0,
                    'gcInterval'   => 0,
                    'checkStateWhenGetResource' =>  false,
                    'requestResourceCheckInterval' => 0,
                ],
            ],
            // resource也可以定义多个连接
            'resource'    =>    [
                'host'        => 'tfb-database',
                'username'    => 'benchmarkdbuser',
                'password'    => 'benchmarkdbpass',
                'database'    => 'hello_world',
                'dbClass'     => \Imi\Pgsql\Db\Drivers\Swoole\Driver::class,
            ],
        ],
        'redis' =>  [
            'pool' => [
                // 协程池类名
                'class'    => \Imi\Swoole\Redis\Pool\CoroutineRedisPool::class,
                'config' => [
                    // 池子中最多资源数
                    'maxResources' => intval(1024 / swoole_cpu_num()),
                    // 池子中最少资源数
                    'minResources' => getenv('WITH_REDIS') ? 16 : 0,
                    'gcInterval'   => 0,
                    'checkStateWhenGetResource' =>  false,
                    'requestResourceCheckInterval' => 0,
                ],
            ],
            // 数组资源配置
            'resource' => [
                'host'      =>  '127.0.0.1',
                'port'      =>  6379,
                // 是否自动序列化变量
                'serialize' =>  true,
                // 密码
                'password'  =>  null,
                // 第几个库
                'db'        =>  0,
            ],
        ],
    ] : [],
];
