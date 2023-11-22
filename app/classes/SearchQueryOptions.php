<?php

namespace app\classes;

/**
 * Class SearchQueryOptions
 *
 * Sets options for search queries.
 */
class SearchQueryOptions
{
  public string $type = self::ALL;
  public array $columns = [];
  public array $conditions = [
    'columnName' => null,
    'operator' => null,
    'values' => []
  ];
  public string $order = 'DESC';
  public ?int $limit = null;

  /**
   * Constant for 'DESC' order.
   */
  const DESC = 'DESC';
  /**
   * Constant for 'ASC' order.
   */
  const ASC = 'ASC';

  /**
   * Constant for 'all' type.
   */
  const ALL = 'all';
  /**
   * Constant for 'specific' type.
   */
  const SPECIFIC = 'specific';

  /**
   * Constant for '=' operator.
   */
  const EQUAL_OPERATOR = '=';
  /**
   * Constant for '!=' operator.
   */
  const DIFFERENT_OPERATOR = '!=';
  /**
   * Constant for '>' operator.
   */
  const BIGGER_OPERATOR = '>';
  /**
   * Constant for '<' operator.
   */
  const LESS_OPERATOR = '<';
  /**
   * Constant for '>=' operator.
   */
  const BIGGER_EQUAL_OPERATOR = '>=';
  /**
   * Constant for '<=' operator.
   */
  const LESS_EQUAL_OPERATOR = '<=';
  /**
   * Constant for 'BETWEEN' operator.
   */
  const BETWEEN_OPERATOR = 'BETWEEN';
  /**
   * Constant for 'LIKE' operator.
   */
  const LIKE_OPERATOR = 'LIKE';

  /**
   * Get allowed order for search.
   *
   * @return array Allowed orders.
   */
  public static function getAllowedOrder(): array
  {
    return [
      self::DESC,
      self::ASC
    ];
  }

  /**
   * Get allowed types for search.
   *
   * @return array Allowed types.
   */
  public static function getAllowedTypes(): array
  {
    return [
      self::ALL,
      self::SPECIFIC
    ];
  }

  /**
   * Get allowed operators for search conditions.
   *
   * @return array Allowed operators.
   */
  public static function getAllowedOperators(): array
  {
    return [
      self::EQUAL_OPERATOR,
      self::DIFFERENT_OPERATOR,
      self::BIGGER_OPERATOR,
      self::LESS_OPERATOR,
      self::BIGGER_EQUAL_OPERATOR,
      self::LESS_EQUAL_OPERATOR,
      self::BETWEEN_OPERATOR,
      self::LIKE_OPERATOR
    ];
  }
}