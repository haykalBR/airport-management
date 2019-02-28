<?php
/**
 * Created by PhpStorm.
 * User: haykel
 * Date: 12/02/19
 * Time: 03:40 م
 */
namespace RestBundle\Service;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
class Jwt
{
    private $container;
    protected $request;
    public function __construct(Container $container,RequestStack $request_stack){
        $this->container = $container;
        $this->request = $request_stack->getCurrentRequest();
    }
    /**
     * Returns token expiration datetime.
     *
     * @return string Unixtmestamp
     */
    public function getTokenExpiryDateTime()
    {
        $tokenTtl = $this->container->getParameter('lexik_jwt_authentication.token_ttl');
        $now = new \DateTime();
        $now->add(new \DateInterval('PT'.$tokenTtl.'S'));

        return $now->format('U');
    }
    /**
     * Returns token for user.
     *
     * @param User $user
     *
     * @return array
     */
    public function getToken( $mac)
    {
        return $this->container->get('lexik_jwt_authentication.encoder')
            ->encode([
                'mac' => $mac,
                'exp' => $this->getTokenExpiryDateTime(),
            ]);
    }
    /**
     * Return if Bearer exisit in Authorization
     * @return bool|false|string|string[]|void|null
     */
    public function getCredentials()
    {

        $request=$this->request;
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );
        $token = $extractor->extract($request);
        if (!$token) {

            throw new CustomUserMessageAuthenticationException('Invalid Authorization Bearer ');
        }
        return $token;
    }
    public function decodejwt(){

        $data = $this->container->get('lexik_jwt_authentication.encoder')->decode($this->getCredentials());
         if ($data)
            return $data;
         else
             throw new CustomUserMessageAuthenticationException('Expired Token');

    }

}