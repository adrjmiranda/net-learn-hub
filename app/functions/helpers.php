<?php

use app\classes\SearchQueryOptions;

function getPath(): string
{
  $vendorDir = dirname(__DIR__);
  return dirname($vendorDir);
}

function getContentType(string $type): string
{
  $contentType = '';

  switch ($type) {
    case 'css':
      $contentType = 'text/css';
      break;

    case 'js':
      $contentType = 'application/javascript';
      break;

    case 'images':
      $contentType = 'image/' . $type;
      break;

    case 'favicon.ico':
      $contentType = 'image/x-icon';
      break;
  }

  return $contentType;
}

function areValidColumns(array $columns): bool
{
  $pattern = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';

  foreach ($columns as $column) {
    if (!preg_match($pattern, $column)) {
      return false;
    }
  }

  return true;
}

function prepareSearchStatement(PDO $connect, string $table, SearchQueryOptions $options): PDOStatement
{
  $query = 'SELECT ' . (empty($options->columns) ? '*' : implode(', ', $options->columns)) . ' FROM ' . $table;

  if (
    $options->type === SearchQueryOptions::SPECIFIC &&
    !empty($options->conditions['column_name']) &&
    !empty($options->conditions['operator']) &&
    !empty($options->conditions['values'])
  ) {
    $validOperator = in_array($options->conditions['operator'], SearchQueryOptions::getAllowedOperators());

    if ($validOperator) {
      switch ($options->conditions['operator']) {
        case SearchQueryOptions::BETWEEN_OPERATOR:
          $query .= ' WHERE ' . $options->conditions['column_name'] . ' BETWEEN :value1 AND :value2';
          break;

        case SearchQueryOptions::LIKE_OPERATOR:
          $query .= ' WHERE ' . $options->conditions['column_name'] . ' LIKE :value';
          break;

        default:
          $query .= ' WHERE ' . $options->conditions['column_name'] . ' ' . $options->conditions['operator'] . ' :value';
          break;
      }
    }
  }

  $query .= (in_array($options->order, SearchQueryOptions::getAllowedOrder()) ? ' ORDER BY id ' . $options->order : ' ORDER BY id DESC');

  if ($options->limit !== null && $options->limit > 0) {
    $query .= ' LIMIT :limit';
  }

  $stmt = $connect->prepare($query);

  if (!empty($options->columns)) {
    foreach ($options->columns as $key => $column) {
      $stmt->bindParam(':column' . $key, $column, PDO::PARAM_STR);
    }
  }

  if (
    $options->type === SearchQueryOptions::SPECIFIC &&
    !empty($options->conditions['column_name']) &&
    !empty($options->conditions['operator']) &&
    !empty($options->conditions['values'])
  ) {
    if ($validOperator) {
      switch ($options->conditions['operator']) {
        case SearchQueryOptions::BETWEEN_OPERATOR:
          $stmt->bindParam(':value1', $options->conditions['values'][0], PDO::PARAM_STR);
          $stmt->bindParam(':value2', $options->conditions['values'][1], PDO::PARAM_STR);
          break;

        case SearchQueryOptions::LIKE_OPERATOR:
          $stmt->bindParam(':value', $options->conditions['values'], PDO::PARAM_STR);
          break;

        default:
          $stmt->bindParam(':value', $options->conditions['values'], PDO::PARAM_STR);
          break;
      }
    }
  }

  if ($options->limit !== null && $options->limit > 0) {
    $stmt->bindParam(':limit', $options->limit, PDO::PARAM_INT);
  }

  return $stmt;
}