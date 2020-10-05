<?php

namespace App\ApiController;

use App\Entity\Food;
use App\Entity\Message;
use App\Repository\FoodRepository;
use App\Repository\MessageRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\TypeRepository;
use App\Repository\AllergenRepository;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/message", host="api.appart.do")
 */
class MessageController extends AbstractFOSRestController
{

  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_message_index"
   * )
   */
  public function index(MessageRepository $messageRepository): View
  {
    $data = $messageRepository->findAll();
    $messages = [];
    foreach ( $data as $message ) {
      array_push($messages, $this->normalize($message));
    }
    return View::create($messages, Response::HTTP_OK);
  }

  /**
   * @Rest\Put(
   *   path="/admin/{id}/edit",
   *   name="api_message_edit"
   * )
   */
  public function edit(
    Request $request,
    Message $message
  ): View
  {
      $em = $this->getDoctrine()->getManager();

      $message_text = $request->get('messageText');
      $message->setMessage($message_text);

      $display = $request->get('display');
      if ($display !== 1) {
        $display = 0;
      }
      $message->setDisplay($display);

      $em->persist($message);
      $em->flush();

      $message = $this->normalize($message);
      return View::create($message, Response::HTTP_CREATED);

  }

  /**
   * Patch a message
   * @Rest\Patch(
   *     path = "/admin/{id}/patch",
   *     name = "api_patch_display_message",
   * )
   * @Rest\View()
   * @param Request $request
   * @param Message $message
   * @return View;
   */
  public function patch(Request $request, Message $message): View
  {
      $em = $this->getDoctrine()->getManager();
      $display = $request->get('display');
      if ($display !== 1) {
        $display = 0;
      }
      $message->setDisplay($display);
      $em->persist($message);
      $em->flush();
      $message = $this->normalize($message);
      return View::create($message, Response::HTTP_OK);
  }



  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'message',
        'display'
      ]]);
    return $object;
  }

}
