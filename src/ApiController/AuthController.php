<?php


namespace App\ApiController;


use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class AuthController
 * @Rest\Route(
 *     path="auth",
 *     host="api-appart.leo-gatille.com"
 * )
 */
class AuthController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     *     path="/profile",
     *     name="api_auth_profile"
     * )
     */
    public function profile()
    {
        $user = $this->getUser();
        $user = $this->normalize($user);
        return View::create($user, Response::HTTP_OK);
    }

    private function normalize($object)
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        $object = $serializer->normalize($object, 'json',
            ['attributes' => [
                'id',
                'username',
                'email',
                'roles'
            ]]);
        return $object;
    }
}
