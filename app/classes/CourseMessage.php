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
  const ERR_INVALID_TITLE = 'O título não poder ser vazio (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_TITLE . ' caracteres).';
  const ERR_INVALID_DESCRIPTION = 'A descrição não pode ser vazia (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_DESCRIPTION . ' caracteres).';
  const ERR_INVALID_IMAGE_TYPE = 'Somente imagens jpg, jpeg ou png.';
  const ERR_INVALID_IMAGE_LENGTH = 'Tamanho da imagem inválido (máximo ' . GlobalValues::MAXIMUM_IMAGE_SIZE_IN_MB . 'B).';

  // Error messages related to course topics
  const ERR_INVALID_TOPIC_CONTENT = 'Conteúdo do tópico inválido (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_DOCUMENT_BLOB . 'B).';

  // Error messages related to questions in quizzes
  const ERR_INVALID_QUESTION_TEXT = 'Texto da questão inválido (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_QUESTION . ' caracteres.';
  const ERR_INVALID_RIGHT_ANSWER = 'A resposta selecionada (dever ser uma letra do alfabeto).';

  // Error messages related to alternatives in quizzes
  const ERR_INVALID_LETTER = 'A alternativa deve ser uma letra do alfabeto.';
  const ERR_INVALID_ALTERNATIVE = 'Texto para alternativa inválido (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_DOCUMENT_BLOB . ' caracteres.';
}
