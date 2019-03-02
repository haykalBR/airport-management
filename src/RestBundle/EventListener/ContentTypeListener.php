<?php
/**
 * Created by PhpStorm.
 * User: haykel
 * Date: 13/02/19
 * Time: 09:12 ص
 */
namespace RestBundle\EventListener;

use RestBundle\Exception\HttpContentTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ContentTypeListener extends HttpContentTypeException
{
    use \RestBundle\Helper\ControllerHelper;
    const MIME_TYPE_APPLICATION_JSON = 'application/json';


    public function onKernelRequest(GetResponseEvent $event)
    {$request = $event->getRequest();
       $uri=$request->getUri() ;
        if (strpos($uri, "api/v1") !== false) {
        if ($request->headers->contains('Content-type', self::MIME_TYPE_APPLICATION_JSON)) {
            return true;
        }
        if ($request->getMethod() === Request::METHOD_GET) {
            return true;
        }

            throw new HttpContentTypeException();
        }
    }

}