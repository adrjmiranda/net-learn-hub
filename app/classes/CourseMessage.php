<?php

namespace app\classes;

/**
 * Class CourseMessage
 *
 * This class stores course-related message constants to maintain consistent messages.
 */
class CourseMessage
{
  // Error messages related to course operations
  const ERR_FAIL_CREATE = 'Falha ao tentar criar curso.';
  const ERR_FAIL_UPDATE = 'Falha ao tentar atualizar curso.';
  const ERR_FAIL_DELETE = 'Falha ao tentar deletar curso.';

  // Error messages related to course details validation
  const ERR_INVALID_TITLE = 'Título inválido.';
  const ERR_INVALID_DESCRIPTION = 'Descrição inválido.';
  const ERR_INVALID_IMAGE_TYPE = 'Tipo de imagem inválido. Somente jpg, jpeg ou png.';
  const ERR_INVALID_IMAGE_LENGTH = 'Tamanho da imagem inválido. Deve ter no máximo 2 MB.';

  // Error messages related to course topics
  const ERR_INVALID_TOPIC_TITLE = 'Título do tópico inválido.';
  const ERR_INVALID_TOPIC_CONTENT = 'Conteúdo do tópico inválido.';

  // Error messages related to quizzes
  const ERR_INVALID_QUIZZ_TITLE = 'Título do quiz inválido.';

  // Error messages related to questions in quizzes
  const ERR_INVALID_QUESTION_TEXT = 'Texto da questão inválido.';
  const ERR_INVALID_RIGHT_ANSWER = 'A resposta selecionada como correta é inválida.';

  // Error messages related to alternatives in quizzes
  const ERR_INVALID_LETTER = 'Alternativa inválida. Deve ser uma letra do alfabeto.';
  const ERR_INVALID_ALTERNATIVE = 'Texto para alternativa inválida.';
}
