<?php
namespace Imi\Server\Http\Listener;

use Imi\App;
use Imi\Bean\Annotation\Parser;
use Imi\Server\Http\Route\HttpRoute;
use Imi\Server\Http\Route\RouteParam;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\RequestEventParam;
use Imi\Server\Route\Parser\ControllerParser;
use Imi\Server\Event\Listener\IRequestEventListener;
use Imi\Util\Call;
use Imi\Controller\HttpController;

/**
 * http服务器路由处理
 * @ClassEventListener(className="Imi\Server\Http\Server",eventName="request")
 */
class Route implements IRequestEventListener
{
	/**
	 * 事件处理方法
	 * @param RequestEventParam $e
	 * @return void
	 */
	public function handle(RequestEventParam $e)
	{
		$route = $e->getTarget()->getBean('HttpRoute');
		$param = new RouteParam($e->request);
		$result = $route->parse($param);
		if(null === $result)
		{
			$e->response->end('404');
		}
		else
		{
			if(isset($result['callable'][0]) && $result['callable'][0] instanceof HttpController)
			{
				$result['callable'][0] = clone $result['callable'][0];
				$result['callable'][0]->request = $e->request;
				$result['callable'][0]->response = $e->response;
			}
			Call::callUserFuncArray($result['callable'], $this->prepareActionParams($e, $result));
		}
	}

	private function prepareActionParams(RequestEventParam $e, $routeResult)
	{
		try{
			if(is_array($routeResult['callable']))
			{
				$ref = new \ReflectionMethod($routeResult['callable'][0], $routeResult['callable'][1]);
			}
			else if(!$routeResult['callable'] instanceof \Closure)
			{
				$ref = new \ReflectionFunction($routeResult['callable']);
			}
			else
			{
				return [];
			}
		}
		catch(\Throwable $ex)
		{
			return [];
		}
		$result = [];
		foreach($ref->getParameters() as $param)
		{
			if(isset($routeResult['params'][$param->name]))
			{
				$result[] = $routeResult['params'][$param->name];
			}
			else if(isset($e->request->post[$param->name]))
			{
				$result[] = $e->request->post[$param->name];
			}
			else if(isset($e->request->get[$param->name]))
			{
				$result[] = $e->request->get[$param->name];
			}
			else if($param->isOptional())
			{
				$result[] = $e->request->get[$param->getDefaultValue()];
			}
			else
			{
				$result[] = null;
			}
		}
		return $result;
	}
}