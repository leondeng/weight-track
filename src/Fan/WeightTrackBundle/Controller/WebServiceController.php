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
          return $this->responseJson(sprintf('{"tracks":%s,"pagination":{"count":%d,"current":%d}}',
              $this->serialize($tracks), $pagination->getPageCOunt(), $pagination->getCurrentPageNumber()));
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

        return new JsonResponse(array('status' => 'ok'));
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
    return new Response($data, 200, array('Content-Type' => 'application/json'));
  }

  private function err400($msg) {
    return new JsonResponse(array('message' => $msg), 400);
  }

  private function err401($msg) {
    return new JsonResponse(array('message' => $msg), 401);
  }

  private function err404($msg) {
    return new JsonResponse(array('message' => $msg), 404);
  }

  private function err500(\Exception $e) {
    $data = array (
      'error_code' => $e->getCode(),
      'message' => $e->getMessage()
    );

    return new JsonResponse($data, 500);
  }
}
