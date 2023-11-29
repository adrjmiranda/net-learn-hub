<?php

namespace app\classes;

/**
 * Class GlobalValues
 * This class contains constants representing table names and tokens used throughout the codebase.
 */
class GlobalValues
{
  // Table names in the database

  /**
   * Name of the administrators' table.
   */
  const ADMINISTRATORS_TABLE = 'administrators';

  /**
   * Name of the users' table.
   */
  const USERS_TABLE = 'users';

  /**
   * Name of the courses' table.
   */
  const COURSES_TABLE = 'courses';

  /**
   * Name of the courses' topics table.
   */
  const COURSES_TOPICS_TABLE = 'courses_topics';

  /**
   * Name of the quizzes' table.
   */
  const QUIZZES_TABLE = 'quizzes';

  /**
   * Name of the questions' table.
   */
  const QUESTIONS_TABLE = 'questions';

  /**
   * Name of the alternatives' table.
   */
  const ALTERNATIVES_TABLE = 'alternatives';

  /**
   * Name of the users-courses relation table.
   */
  const USERS_COURSES_RELATION_TABLE = 'users_courses_relation';

  /**
   * Name of the users-quizzes relation table.
   */
  const USERS_QUIZZES_RELATION_TABLE = 'users_quizzes_relation';

  // Token names

  /**
   * Name of the CSRF token.
   */
  const CSRF_TOKEN = 'csrf_token';

  /**
   * Name of the admin token.
   */
  const ADMIN_TOKEN = 'admin_token';

  /**
   * Name of the user token.
   */
  const USER_TOKEN = 'user_token';

  /**
   * Name of the session message.
   */
  const SESSION_MESSAGE = 'session_message';
  const SESSION_MESSAGE_CONTENT = 'session_message_content';

  /**
   * Minimum  password size.
   */
  const MINIMUM_PASSWORD_SIZE = 8;
  /**
   * Maximum password size.
   */
  const MAXIMUM_PASSWORD_SIZE = 20;

  /**
   * Maximum size of the comment.
   */
  const MAXIMUM_SIZE_OF_THE_COMMENT = 255;
  /**
   * Maximum size of the title.
   */
  const MAXIMUM_SIZE_OF_THE_TITLE = 100;
  /**
   * Maximum size of the description.
   */
  const MAXIMUM_SIZE_OF_THE_DESCRIPTION = 255;
  /**
   * Maximum size of the question.
   */
  const MAXIMUM_SIZE_OF_THE_QUESTION = 65535;
  /**
   * Maximum size of the alternative.
   */
  const MAXIMUM_SIZE_OF_THE_ALTERNATIVE = 65535;
  /**
   * Maximum size of the first name.
   */
  const MAXIMUM_SIZE_OF_THE_FIRST_NAME = 100;
  /**
   * Maximum size of the last name.
   */
  const MAXIMUM_SIZE_OF_THE_LAST_NAME = 255;

  /**
   * Maximum image size in MB
   */
  const MAXIMUM_SIZE_OF_THE_IMAGE_BLOB = 2097152;

  /**
   * Maximum image size in MB
   */
  const MAXIMUM_SIZE_OF_THE_DOCUMENT_BLOB = 5242880;

  /**
   * Maximum image size in MB
   */
  const CSRF_TOKEN_IS_INVALID = 'csrf_token_is_invalid';
}