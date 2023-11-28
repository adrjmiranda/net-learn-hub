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
  // Error messages related to invalid csrf token
  const INVALID_CSRF_TOKEN = 'Houve um problema de segurança na sua solicitação.';

  // Error messages related to email lookup
  const ERR_EMAIL_NOT_FOUND = 'Usuário não encontrado.';
  const ERR_INCORRECT_PASS = 'A seenha está incorreta.';
  const ERR_LOGIN = 'Senha ou email incorreto.';

  // Error messages related to email registration
  const ERR_EMAIL_ALREADY_REGISTERED = 'Este e-mail já está registrado.';
  const ERR_REGISTER = 'Houve um erro e não foi possível fazer o registro agora.';

  // Error messages related to user details validation
  const ERR_INVALID_FIRST_NAME = 'Sobrenome deve ser somente texto (máximo ' . GlobalValues::MAXIMUM_SIZE_OF_THE_FIRST_NAME . ' de caracteres).';
  const ERR_INVALID_LAST_NAME = 'Sobrenome deve ser somente texto (máximo ' . GlobalValues::MAXIMUM_SIZE_OF_THE_LAST_NAME . ' de caracteres).';
  const ERR_INVALID_EMAIL = 'E-mail não pode ser vazio e deve ter um formato válido.';
  const ERR_INVALID_PASS = 'Senha incorreta e/ou o formato é inválido (deve ter de ' . GlobalValues::MINIMUM_PASSWORD_SIZE . ' a ' . GlobalValues::MAXIMUM_PASSWORD_SIZE . ' caracteres).';
  const ERR_INVALID_PASS_CONFIRMATION = 'A confirmação da senha não corresponde.';
  const ERR_INVALID_IMAGE_TYPE = 'Tipo de imagem inválido. Somente jpg, jpeg ou png.';
  const ERR_INVALID_IMAGE_LENGTH = 'Tamanho da imagem inválido. Deve ter no máximo ' . GlobalValues::MAXIMUM_IMAGE_SIZE_IN_MB . 'B.';
  const ERR_INVALID_DESCRIPTION = 'Descrição inválida. Somente texto com no máximo ' . GlobalValues::MAXIMUM_SIZE_OF_THE_DESCRIPTION . 'caracteres.';

  // Error message related to comment
  const ERR_INVALID_COMMENT = 'Comentário inválido. Somente texto com no máximo ' . GlobalValues::MAXIMUM_SIZE_OF_THE_COMMENT . ' caracteres.';
}
