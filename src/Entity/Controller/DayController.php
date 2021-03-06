<?php

/**
 * @file
 * Contains Drupal\mespronos\Entity\Controller\GameListController.
 */

namespace Drupal\mespronos\Entity\Controller;


use Drupal\Core\Database\Database;

class DayController {

  public static function getNextDaysToBet($nb = 5) {
    $day_storage = \Drupal::entityManager()->getStorage('day');
    $injected_database = Database::getConnection();
    $now = new \DateTime(null, new \DateTimeZone("UTC"));

    $query = $injected_database->select('mespronos__game','g');
    $query->addExpression('min(game_date)','day_date');
    $query->addExpression('count(id)','nb_game_left');
    $query->groupBy('day');
    $query->fields('g',array('day'));

    $query->condition('game_date',$now->format('Y-m-d\TH:i:s'),'>');
    $query->orderBy('day_date','ASC');
    $query->range(0,$nb);
    $results = $query->execute();
    $results = $results->fetchAllAssoc('day');
    $days = $day_storage->loadMultiple(array_keys($results));

    foreach($results as $key => &$day_data) {
      $day_data->nb_game = $days[$key]->getNbGame();
      $day_data->entity = $days[$key];
    }
    return $results;
  }

  public static function getlastDays($nb = 5) {
    $day_storage = \Drupal::entityManager()->getStorage('day');
    $injected_database = Database::getConnection();
    $now = new \DateTime(null, new \DateTimeZone("UTC"));

    $query = $injected_database->select('mespronos__game','g');
    $query->addExpression('min(game_date)','day_date');
    $query->addExpression('count(id)','nb_game_over');
    $query->groupBy('day');
    $query->fields('g',array('day'));

    $query->condition('game_date',$now->format('Y-m-d\TH:i:s'),'<');
    $query->orderBy('day_date','DESC');
    $query->range(0,$nb);
    $results = $query->execute();
    $results = $results->fetchAllAssoc('day');
    $days = $day_storage->loadMultiple(array_keys($results));

    foreach($results as $key => &$day_data) {
      $day_data->nb_game = $days[$key]->getNbGame();
      $day_data->nb_game_with_score = $days[$key]->getNbGameWIthScore();
      $day_data->entity = $days[$key];
    }
    return $results;
  }
}
