<?php

namespace Application;

use Zend\Console\Response;
use Zend\Mvc\Application;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\SessionManager;
use Zend\Validator\AbstractValidator;
use Zend\View\Model\ViewModel;

class Module {

          public function onBootstrap(MvcEvent $e) {
        $config = $e->getApplication()
                ->getServiceManager()
                ->get('Configuration');

        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config['session_config']);
        $sessionManager = new SessionManager($sessionConfig);
        
        // Set save handler to memcached?
//        $sessionHandler = isset($config['session_handler']) ? $config['session_handler'] : null;
//        if ($sessionHandler == 'memcache') {
//            $memcached = $e->getApplication()
//                    ->getServiceManager()->get('memcached');
//            $saveHandler = new Cache($memcached);
//            $sessionManager->setSaveHandler($saveHandler);
//        }

        $sessionManager->start();
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        AbstractValidator::setDefaultTranslator($translator);

        /**
         * Optional: If you later want to use namespaces, you can already store the
         * Manager in the shared (static) Container (=namespace) field
         */
        Container::setDefaultManager($sessionManager);

        $eventManager = $e->getApplication()->getEventManager();

        //--------Set to get layout from 'module_layouts' config key
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);

        //--------Set event to check authentication
        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAuthenticated'));

        //--------Set event to catch dispatch error & render error
        $eventManager->attach(
                array(MvcEvent::EVENT_DISPATCH_ERROR, MvcEvent::EVENT_RENDER_ERROR, MvcEvent::EVENT_RENDER)
                , array($this, 'handleError'));
        //--------
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * Check user logged in
     * @param MvcEvent $e
     */
    public function checkAuthenticated(MvcEvent $e)
    {
        $ignoreCheck = array('user/insert', 'user/insert-completed', 'home');

        $routeName = $e->getRouteMatch()->getMatchedRouteName();
        $routeAction = $e->getRouteMatch()->getParam('action');

        $isPasswordActions = $routeName == 'password' &&
            ($routeAction == 'password_reset' ||
            $routeAction == 'password_setting');

        if ($isPasswordActions) {
            return;
        }
        if (in_array($routeName, $ignoreCheck) || in_array($routeName . '/' . $routeAction, $ignoreCheck)) {
            return;
        }

        if (!$e->getApplication()->getServiceManager()->get('AuthService')->hasIdentity()) {
            $url = $e->getRouter()->assemble(array(), array('name' => 'home'));
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            $response->sendHeaders();
            $e->stopPropagation(true);
            return $response;
        }
    }

    /**
     *
     * @param MvcEvent $e
     */
    public function handleError(MvcEvent $e) {

        $error = $e->getError();
        $response = $e->getResponse();

        if ($response instanceof Response) {
            return;
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return;
        }

        $logSv = $e->getApplication()->getServiceManager()->get('SystemLogService');
        $exception = $e->getParam('exception');
        if (null != $exception) {
            $logSv->err($exception->getMessage());
            $logSv->err($exception->getTraceAsString());
            $previousException = $exception->getPrevious();
            if ( null != $previousException ) {
                $logSv->err($previousException->getMessage());
                $logSv->err($previousException->getTraceAsString());
            }
        }
//        if ($e->getRouteMatch()->getMatchedRouteName() == 'api') {
//            exit();
//        }

        $evn = getenv('APPLICATION_ENV');

        if ($evn == "production") {
            /*
              $baseModel = new ViewModel();
              $baseModel->setTemplate('layout/layout');

              $model = new ViewModel();
              if ($statusCode == 404) {
              $model->setTemplate('error/production/404');
              }  else {
              $model->setTemplate('error/production/index');
              }

              $baseModel->addChild($model);
              $baseModel->setTerminal(true);

              $e->setViewModel($baseModel);
              $result = $e->getResult();

              $logSv = $e->getApplication()->getServiceManager()->get('SystemLogService');
              $exception = $e->getParam('exception');
              if (null != $exception) {
              $logSv->err($exception->getMessage());
              $logSv->err($exception->getTraceAsString());
              }

              if (get_class($result) == "Zend\View\Model\ViewModel") {
              $response = $e->getResponse();
              return $response;
              }
             *
             */



            $model = $e->getResult();
            if ($model instanceof ViewModel) {
                if ($statusCode == 404) {
                    $model->setTemplate('error/production/404');
                } else {
                    $model->setTemplate('error/production/index');
                }
            }
        } else {
            if ($error == Application::ERROR_CONTROLLER_NOT_FOUND) {
                //there is no controller named $e->getRouteMatch()->getParam('controller')
                $logText = 'The requested controller '
                        . $e->getRouteMatch()->getParam('controller') . '  could not be mapped to an existing controller class.';

                //you can do logging, redirect, etc here..
            }

            if ($error == Application::ERROR_CONTROLLER_INVALID) {
                //the controller doesn't extends AbstractActionController
                $logText = 'The requested controller '
                        . $e->getRouteMatch()->getParam('controller') . ' is not dispatchable';

                //you can do logging, redirect, etc here..
            }

            if ($error == Application::ERROR_ROUTER_NO_MATCH) {
                // the url doesn't match route, for example, there is no /foo literal of route
                $logText = 'The requested URL could not be matched by routing.';
                //you can do logging, redirect, etc here...
            }

            if ($error == Application::ERROR_EXCEPTION) {
                //echo $error;
            }
        }
    }

}
