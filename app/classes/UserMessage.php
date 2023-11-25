<?php

namespace app\classes;

/**
 * Class UserMessage
 *
 * This class stores user messages constants to maintain consistent messages.
 */
class UserMessage
{
  // Error messages related to authentication
  const ERR_EMAIL_NOT_FOUND = 'Email não encontrado.';
  const ERR_INCORRECT_PASS = 'Senha incorreta.';
  const ERR_LOGIN = 'Houve um problema e não foi possivel fazer o login agora.';

  const ERR_REGISTER = 'Houve um erro e não foi possível fazer o registro agora.';

  // Error messages related to user input validation
  const ERR_INVALID_FIRST_NAME = 'Nome inválido.';
  const ERR_INVALID_LAST_NAME = 'Sobrenome inválido.';
  const ERR_INVALID_EMAIL = 'E-mail inválido.';
  const ERR_INVALID_PASS = 'Senha inválida.';
  const ERR_INVALID_PASS_CONFIRMATION = 'A confirmação da senha não corresponde.';
  const ERR_INVALID_IMAGE_TYPE = 'Tipo de imagem inválido.';
  const ERR_INVALID_IMAGE_LENGTH = 'Comprimento de imagem inválido.';
  const ERR_INVALID_DESCRIPTION = 'Descrição inválida.';

  // Error messages related to comments
  const ERR_INVALID_COMMENT = 'Comentário inválido.';
}