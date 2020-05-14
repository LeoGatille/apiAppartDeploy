<?php

namespace App\ApiController;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\FoodRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/event", host="api.appart.do")
 */
class EventController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_event_index"
   * )
   */
  public function index(EventRepository $eventRepository): View
  {
    $data = $eventRepository->findAll();
    $events = [];
    foreach ( $data as $event ) {
      array_push($events, $this->normalize($event));
    }
    return View::create($events, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_event_show"
   * )
   */
  public function show(Event $event) : View
  {
    $event = $this->normalize($event);
    return View::create($event, Response::HTTP_OK);
  }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_event_create"
   * )
   */
  public function create(Request $request, FoodRepository $foodRepository): View
  {
      $em = $this->getDoctrine()->getManager();

      $event  = new Event();
      $d = $request->get('eventDate');
      $event_date = $this->formatDate($d);

      $event_description = $request->get('eventDescription');

      $event_name = $request->get('eventName');

      $price_no_drinks = $request->get('priceNoDrinks');

      $price_with_drinks = $request->get('priceWithDrinks');

      $foods = $request->get('foods');
      if ($foods) {
        foreach ($foods as $foodId) {
          $food = $foodRepository->find($foodId);
          $event->addFood($food);
          $em->persist($food);
        }
      }


      $event->setEventDate($event_date);
      $event->setEventDescription($event_description);
      $event->setEventName($event_name);
      $event->setPriceWithDrinks($price_with_drinks);
      $event->setPriceNoDrinks($price_no_drinks);

      $em->persist($event);
      $em->flush();

      $event = $this->normalize($event);
      return View::create($event, Response::HTTP_CREATED);


  }

  /**
   * @Rest\Put(
   *     path="/admin/{id}/edit",
   *     name="api_event_edit"
   * )
   */
  public function edit(Event $event, Request $request, FoodRepository $foodRepository): View
  {

      $em = $this->getDoctrine()->getManager();

      $d = $request->get('eventDate');
      $event_date = $this->formatDate($d);
      $event->setEventDate(($event_date));

      $event_description = $request->get('eventDescription');
      $event->setEventDescription($event_description);

      $event_name = $request->get('eventName');
      $event->setEventName($event_name);

      $price_no_drinks = $request->get('priceNoDrinks');
      $event->setPriceNoDrinks($price_no_drinks);

      $price_with_drinks = $request->get('priceWithDrinks');
      $event->setPriceWithDrinks($price_with_drinks);

      $foods = $request->get('foods');
      $old_foods = $event->getFood();
        foreach ($old_foods as $key => $old_foods){
          $event->removeFood($old_foods);
        };
      if ($foods) {
        foreach ($foods as $foodId) {
          $food = $foodRepository->find($foodId);
          $event->addFood($food);
          $em->persist($food);
        }
      }

      $em->persist($event);
      $em->flush();

      $event = $this->normalize($event);
      return View::create($event, Response::HTTP_CREATED);
    

  }


  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_event_delete"
   * )
   */
  public function delete(Event $event): View
  {
      $em = $this->getDoctrine()->getManager();
      $em->remove($event);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);
    

  }

  private function formatDate($d) {
    $event_date = round($d/1000);
    $newDate = new \DateTime();
    $newDate->setTimestamp($event_date);
    return $newDate;
  }

  private function createDate($date)
  {
    // $date = new DateTime();
    $format = 'Y/m/d H:i:s';;
    $date = \DateTime::createFromFormat($format, $date);
//    $newDate = new \DateTime();
//    $newDate->setTimestamp($date);

    return $date;
  }

  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'eventDate' => ['timestamp'],
        'timestamp',
        'eventDescription',
        'eventName',
        'priceNoDrinks',
        'priceWithDrinks',
        'food' => ['id', 'foodName', 'foodDescription', 'type' => ['id', 'typeName']]
      ]]);
    return $object;
  }
}
