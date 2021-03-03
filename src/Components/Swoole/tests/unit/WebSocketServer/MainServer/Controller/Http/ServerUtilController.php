<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServer\MainServer\Controller\Http;

use Imi\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Swoole\Server\Server;

/**
 * 服务器工具类.
 *
 * @Controller("/serverUtil/")
 */
class ServerUtilController extends HttpController
{
    /**
     * @Action
     *
     * @return void
     */
    public function getServer()
    {
        $result = [];
        $server = Server::getServer();
        $result['null'] = $server->getName();
        $server = Server::getServer('main');
        $result['main'] = $server->getName();
        $server = Server::getServer('not found');
        $result['notFound'] = null === $server;

        return $result;
    }

    /**
     * @Action
     *
     * @return void
     */
    public function sendMessage()
    {
        $result = [];
        $result['sendMessageAll'] = Server::sendMessage('test');
        $result['sendMessage1'] = Server::sendMessage('test', [], 0);
        $result['sendMessage2'] = Server::sendMessage('test', [], [0, 1]);
        $result['sendMessageRawAll'] = Server::sendMessageRaw('test');
        $result['sendMessageRaw1'] = Server::sendMessageRaw('test', 0);
        $result['sendMessageRaw2'] = Server::sendMessageRaw('test', [0, 1]);

        return $result;
    }

    /**
     * @Action
     *
     * @return void
     */
    public function send($fds, $flag)
    {
        $data = [
            'data'  => 'test',
        ];
        $dataStr = json_encode($data);
        $result = [];
        $result['send1'] = Server::send($data);
        $result['send2'] = Server::send($data, $fds[0]);
        $result['send3'] = Server::send($data, $fds);
        $result['sendByFlag'] = Server::sendByFlag($data, $flag);
        $result['sendRaw1'] = Server::sendRaw($dataStr);
        $result['sendRaw2'] = Server::sendRaw($dataStr, $fds[0]);
        $result['sendRaw3'] = Server::sendRaw($dataStr, $fds);
        $result['sendRawByFlag'] = Server::sendRawByFlag($dataStr, $flag);

        $result['sendToAll'] = Server::sendToAll($data);
        $result['sendRawToAll'] = Server::sendRawToAll($dataStr);

        return $result;
    }

    /**
     * @Action
     *
     * @return void
     */
    public function sendToGroup()
    {
        $data = [
            'data'  => 'test',
        ];
        $dataStr = json_encode($data);
        $result = [];

        $result['sendToGroup'] = Server::sendToGroup('g1', $data);
        $result['sendRawToGroup'] = Server::sendRawToGroup('g1', $dataStr);

        return $result;
    }

    /**
     * @Action
     *
     * @param int    $fd
     * @param string $flag
     *
     * @return void
     */
    public function close(int $fd, string $flag)
    {
        return [
            'fd'   => Server::close($fd),
            'flag' => Server::closeByFlag($flag),
        ];
    }
}
