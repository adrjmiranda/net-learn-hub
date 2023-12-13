<?php

namespace app\Models;

use app\classes\GlobalValues;
use app\classes\SearchQueryOptions;
use PDO;
use PDOException;

class CommentModel extends Model
{
  protected string $table = GlobalValues::COMMENTS_TABLE;

  public function store(string $comment, int $userId): bool
  {
    $stmt = $this->connect->prepare('INSERT INTO ' . $this->table . ' (comment, user_id) VALUES (:comment, :user_id)');

    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }

  public function getByUserId(int $userId): ?array
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'column_name' => 'user_id',
        'operator' => SearchQueryOptions::EQUAL_OPERATOR,
        'values' => $userId
      ];

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetchAll();

        if (!empty($data)) {
          foreach ($data as $object) {
            if (property_exists($object, 'image')) {
              $object->image = base64_encode($object->image);
            }
          }
        }
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }
}