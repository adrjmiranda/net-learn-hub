<?php

use app\classes\GlobalValues;

/**
 * Checks if a text is valid according to the maximum allowed size for a specific field.
 *
 * @param string $text The text to be validated.
 * @param string $field The specific field for which the text is being validated.
 * @return bool Returns true if the text is valid; false otherwise.
 */
function isValidText(string $text, string $field): bool
{
  // Filters and sanitizes the text to prevent SQL Injection
  $filteredText = strip_tags($text);

  $maximumSizes = [
    'title' => GlobalValues::MAXIMUM_SIZE_OF_THE_TITLE,
    'description' => GlobalValues::MAXIMUM_SIZE_OF_THE_DESCRIPTION,
    'comment' => GlobalValues::MAXIMUM_SIZE_OF_THE_COMMENT,
    'first_name' => GlobalValues::MAXIMUM_SIZE_OF_THE_FIRST_NAME,
    'last_name' => GlobalValues::MAXIMUM_SIZE_OF_THE_LAST_NAME,
    'alternative' => GlobalValues::MAXIMUM_SIZE_OF_THE_ALTERNATIVE,
    'question' => GlobalValues::MAXIMUM_SIZE_OF_THE_QUESTION,
  ];

  // Check if the field exists in the maximumSizes array
  if (array_key_exists($field, $maximumSizes)) {
    $maximumSize = $maximumSizes[$field];
  } else {
    // Field not found, consider it invalid
    return false;
  }

  // Checks if the text is empty or exceeds the maximum size for the field
  return !empty($filteredText) && strlen($filteredText) <= $maximumSize;
}

/**
 * Checks if a BLOB data is valid according to the maximum allowed size for a specific type.
 *
 * @param mixed $blobData The BLOB data to be validated.
 * @param string $field The specific type of BLOB for which the data is being validated.
 * @return bool Returns true if the BLOB data is valid; false otherwise.
 */
function isValidBlob(mixed $blobData, string $field): bool
{
  $maximumSizes = [
    'image' => GlobalValues::MAXIMUM_SIZE_OF_THE_IMAGE_BLOB,
    'document' => GlobalValues::MAXIMUM_SIZE_OF_THE_DOCUMENT_BLOB,
  ];

  if (array_key_exists($field, $maximumSizes)) {
    $maximumSize = $maximumSizes[$field];
  } else {
    // BLOB type not found, considers invalid
    return false;
  }

  // Checks whether BLOB data is empty or exceeds the maximum size allowed
  return !empty($blobData) && strlen($blobData) <= $maximumSize;
}

/**
 * Checks if an ID is valid (positive integer number).
 *
 * @param int $id The ID to be validated.
 * @return bool Returns true if the ID is valid; false otherwise.
 */
function isValidId(int $id): bool
{
  // Checks that the ID is not empty and is a positive integer (greater than zero)
  return !empty($id) && is_int($id) && $id > 0;
}

/**
 * Checks if an email is valid.
 *
 * @param string $email The email to be validated.
 * @return bool Returns true if the email is valid; false otherwise.
 */
function isValidEmail(string $email): bool
{
  // Checks that the email is not empty and is a valid email address
  return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Checks if a password is valid.
 *
 * @param string $password The password to be validated.
 * @return bool Returns true if the password is valid; false otherwise.
 */
function isValidPassword(string $password): bool
{
  // Validation criteria
  $lengthCheck = !(empty($password) || strlen($password) < 8 || strlen($password) > 20);
  $patternCheck = preg_match('/^[a-zA-Z0-9!@#$%^&*()_+=\-{}\[\]:;<>,.?\/~]+$/', $password);

  // Checks if the password meets all criteria
  return $lengthCheck && $patternCheck;
}

/**
 * Validates if an input is not empty and consists of a single uppercase or lowercase letter.
 *
 * @param string $input The input string to be validated.
 * @return bool Returns true if the input is a single letter; false otherwise.
 */
function isValidSingleLetter(string $input): bool
{
  // Checks if the input is not empty and has a length of 1
  if (!empty($input) && strlen($input) === 1) {
    // Checks if the input is a single uppercase or lowercase letter
    return preg_match('/^[a-zA-Z]$/', $input) === 1;
  }

  return false;
}



