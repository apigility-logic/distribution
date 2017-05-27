<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/27
 * Time: 15:07:29
 */
namespace Meilibo\Auth;

use Zend\Http\Request;
use Zend\Http\Response;
use ZF\MvcAuth\Authentication\AbstractAdapter;
use ZF\MvcAuth\Identity;
use ZF\MvcAuth\MvcAuthEvent;

class AuthenticationAdapter extends AbstractAdapter
{
    /**
     * Authorization header token types this adapter can fulfill.
     *
     * @var array
     */
    protected $authorizationTokenTypes = ['meilibo'];


    /**
     * @inheritdoc
     */
    public function provides()
    {
        return $this->authorizationTokenTypes;
    }

    /**
     * @inheritdoc
     */
    public function matches($type)
    {
        return (in_array($type, $this->provides(), true));
    }

    /**
     * @inheritdoc
     */
    public function preAuth(Request $request, Response $response)
    {
    }

    /**
     * @inheritdoc
     */
    public function authenticate(Request $request, Response $response, MvcAuthEvent $mvcAuthEvent)
    {
        if (! $request->getHeader('Authorization', false)) {
            // No credentials were present at all, so we just return a guest identity.
            return new Identity\GuestIdentity();
        }

        return (new Identity\AuthenticatedIdentity(['Test Identity']))->setName('test_user_id');
    }
}