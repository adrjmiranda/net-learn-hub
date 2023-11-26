<?php

namespace app\classes;

use app\classes\GlobalValues;

/**
 * Class UserMessage
 *
 * This class stores user-related message constants to maintain consistent messages.
 */
class UserMessage
{
  // Error messages related to email lookup
  const ERR_EMAIL_NOT_FOUND = 'Email não encontrado. Não existe um usuário com esse email.';
  const ERR_INCORRECT_PASS = 'A seenha está incorreta.';
  const ERR_LOGIN = 'Houve um problema e não foi possível fazer o login agora.';

  // Error messages related to email registration
  const ERR_EMAIL_ALREADY_REGISTERED = 'E-mail already registered.';
  const ERR_REGISTER = 'Houve um erro e não foi possível fazer o registro agora.';

  // Error messages related to user details validation
  const ERR_INVALID_FIRST_NAME = 'Nome inválido. Somente letras e espaços em branco.';
  const ERR_INVALID_LAST_NAME = 'Sobrenome inválido. Somente letras e espaços em branco.';
  const ERR_INVALID_EMAIL = 'Formato de email inválido.';
  const ERR_INVALID_PASS = 'Senha inválida. Deve ter de ' . GlobalValues::MINIMUM_PASSWORD_SIZE . ' a ' . GlobalValues::MAXIMUM_PASSWORD_SIZE . ' caracteres (com letras e números).';
  const ERR_INVALID_PASS_CONFIRMATION = 'A confirmação da senha não corresponde.';
  const ERR_INVALID_IMAGE_TYPE = 'Tipo de imagem inválido. Somente jpg, jpeg ou png.';
  const ERR_INVALID_IMAGE_LENGTH = 'Tamanho da imagem inválido. Deve ter no máximo ' . GlobalValues::MAXIMUM_IMAGE_SIZE_IN_MB . ' MB.';
  const ERR_INVALID_DESCRIPTION = 'Descrição inválida. Somente texto com no máximo ' . GlobalValues::MAXIMUM_SIZE_OF_THE_DESCRIPTION . 'caracteres.';

  // Error message related to comment
  const ERR_INVALID_COMMENT = 'Comentário inválido. Somente texto com no máximo ' . GlobalValues::MAXIMUM_SIZE_OF_THE_COMMENT . ' caracteres.';
}
