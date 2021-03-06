<?php

namespace Fan\WeightTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Symfony\Component\HttpFoundation;
use Fan\WeightTrackBundle\Entity\User;
use Fan\WeightTrackBundle\Entity\Goal;
use Fan\WeightTrackBundle\Entity\Track;

class WebServiceController extends Controller
{
  const ERROR_CODE_BASE = 900;

  private function saveEntity($object) {
    $this->getDoctrine()->getManager()->persist($object);
    $this->getDoctrine()->getManager()->flush();
  }

  public function createGoalAction($id, Request $request) {
    try {
      $em = $this->getDoctrine()->getManager();
      if ($user = $em->getRepository('Fan\WeightTrackBundle\Entity\User')->find($request->request->get('id', $id))) {
        $data = json_decode($request->getContent(), true);
        if (!$goal = $user->getGoal()) { // update goal when existing
          $goal = new Goal();
        }
        $goal->setGoal($data['goal']);
        $goal->setUser($user);

        $validator = $this->get('validator');
        $errors = $validator->validate($goal);
        if (count($errors) > 0) {
          $error = $errors->get(0);
          throw new \Exception($error->getMessage(), self::ERROR_CODE_BASE + 1);
        }
        $this->saveEntity($goal);

        return $this->responseJson($this->serialize($goal));
      } else {
        return $this->err404('User not found!');
      }
    } catch ( \Exception $e ) {
      return $this->err500($e);
    }
  }

  public function createTrackAction($id, Request $request) {
  try {
      $em = $this->getDoctrine()->getManager();
      if ($user = $em->getRepository('Fan\WeightTrackBundle\Entity\User')->find($request->request->get('id', $id))) {
        $data = json_decode($request->getContent(), true);
        $track = new Track();
        $track->setWeight($data['weight']);
        $track->setDate(date_create($data['date']));
        $track->setUser($user);

        $validator = $this->get('validator');
        $errors = $validator->validate($track);
        if (count($errors) > 0) {
          $error = $errors->get(0);
          throw new \Exception($error->getMessage(), self::ERROR_CODE_BASE + 2);
        }
        $this->saveEntity($track);

        return $this->responseJson($this->serialize($track));
      } else {
        return $this->err404('User not found!');
      }
    } catch ( \Exception $e ) {
      return $this->err500($e);
    }
  }

  public function listTracksAction($id, $page, Request $request) {
    try {
      if (!$page) $page = 1;
      $em = $this->getDoctrine()->getManager();
      if ($user = $em->getRepository('Fan\WeightTrackBundle\Entity\User')->find($request->query->get('id', $id))) {
        $qb = $em->createQueryBuilder();
        $qb->select('t.id, t.weight, t.date')
          ->from('Fan\WeightTrackBundle\Entity\Track', 't')
          ->where($qb->expr()->eq('t.user', '?1'))
          ->setParameter(1, $user)
          ->orderBy('t.date', 'DESC');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', $page), //page number
            10 //limit per page
        );
        $tracks = $pagination->getItems();
        if (count($tracks) > 0) {
          return $this->responseJson(sprintf(
            '{"tracks":%s,"pagination":{"count":%d,"current":%d,"total":%d}}',
            $this->serialize($tracks),
            $pagination->getPageCount(),
            $pagination->getCurrentPageNumber(),
            $pagination->getTotalItemCount()
          ));
        } else {
          return $this->err404('Tracks not found!');
        }
      } else {
        return $this->err404('User not found!');
     }
    } catch (\Exception $e) {
      return $this->err500($e);
    }
  }

  public function updateTrackAction($id, $date, Request $request) {
    try {
      $userId = $request->request->get('id', $id);
      $date = date_create($request->request->get('date', $date));

      $data = json_decode($request->getContent(), true);

      $em = $this->getDoctrine()->getManager();
      if ($track = $em->getRepository('Fan\WeightTrackBundle\Entity\Track')->findOneBy(array('user' => $userId, 'date' => $date))) {
        $track->setWeight($data['weight']);
        $track->setDate($date);

        $validator = $this->get('validator');
        $errors = $validator->validate($track);
        if (count($errors) > 0) {
          $error = $errors->get(0);
          throw new \Exception($error->getMessage(), self::ERROR_CODE_BASE + 2);
        }
        $this->saveEntity($track);

        return $this->responseJson($this->serialize($track));
     } else {
        return $this->err404('Track not found!');
      }
    } catch ( \Exception $e ) {
      return $this->err500($e);
    }
  }

  public function deleteTrackAction($id, $date, Request $request) {
    try {
      $userId = $request->request->get('id', $id);
      $date = date_create($request->request->get('date', $date));

      $em = $this->getDoctrine()->getManager();
      if ($track = $em->getRepository('Fan\WeightTrackBundle\Entity\Track')->findOneBy(array('user' => $userId, 'date' => $date))) {
        $em->remove($track);
        $em->flush();

        $response = new JsonResponse(array('status' => 'ok'));
        return $this->generateCrossDomainHeader($response);
      } else {
        return $this->err404('Track not found!');
     }
    } catch (\Exception $e) {
      return $this->err500($e);
    }

  }

  private function serialize($object) {
    $serializer = $this->container->get('jms_serializer');
    return $serializer->serialize($object, 'json');
  }

  private function responseJson($data) {
    $response = new Response($data, 200, array('Content-Type' => 'application/json'));
    return $this->generateCrossDomainHeader($response);
  }

  private function generateCrossDomainHeader(Response $response) {
    $allow_request_headers = 'x-requested-with';
    $response->headers->set('Access-Control-Allow-Origin', $this->getHttpOrigin());
    $response->headers->set('Access-Control-Allow-Credentials', 'true');
    $response->headers->set('Access-Control-Max-Age', 1728000);
    $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, HEAD');
    $response->headers->set('Access-Control-Allow-Headers', $allow_request_headers);

    return $response;
  }

  private function getHttpOrigin() {
    $httpOrigin = '';

    $server = $this->container->get('request_stack')->getCurrentRequest()->server->getHeaders();
    if (isset($server['ORIGIN'])) {
      $httpOrigin = $server['ORIGIN'];
    } else if (isset($server['REFERER'])) {
      $httpOrigin = implode('/', array_slice(explode('/', $server['REFERER']), 0, 3));
    }

    return $httpOrigin;
  }

  private function err400($msg) {
    $response = new JsonResponse(array('message' => $msg), 400);

    return $this->generateCrossDomainHeader($response);
  }

  private function err401($msg) {
    $response = new JsonResponse(array('message' => $msg), 401);

    return $this->generateCrossDomainHeader($response);
  }

  private function err404($msg) {
    $response = new JsonResponse(array('message' => $msg), 404);

    return $this->generateCrossDomainHeader($response);
  }

  private function err500(\Exception $e) {
    $data = array (
      'error_code' => $e->getCode(),
      'message' => $e->getMessage()
    );
    $response = new JsonResponse($data, 500);

    return $this->generateCrossDomainHeader($response);
  }
}
