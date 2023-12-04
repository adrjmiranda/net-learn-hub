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
  const ERR_FAIL_CREATE_COURSE = 'Falha ao tentar criar curso.';
  const ERR_FAIL_UPDATE_COURSE = 'Falha ao tentar atualizar curso.';
  const ERR_FAIL_DELETE_COURSE = 'Falha ao tentar deletar curso.';

  const ERR_FAIL_CREATE_TOPIC = 'Falha ao tentar criar tópico.';
  const ERR_FAIL_UPDATE_TOPIC = 'Falha ao tentar atualizar tópico.';
  const ERR_FAIL_DELETE_TOPIC = 'Falha ao tentar deletar tópico.';

  const ERR_FAIL_CREATE_QUIZ = 'Falha ao tentar criar quiz.';
  const ERR_FAIL_UPDATE_QUIZ = 'Falha ao tentar atualizar quiz.';
  const ERR_FAIL_DELETE_QUIZ = 'Falha ao tentar deletar quiz.';

  const ERR_FAIL_CREATE_QUESTION = 'Falha ao tentar criar questão.';
  const ERR_FAIL_UPDATE_QUESTION = 'Falha ao tentar atualizar questão.';
  const ERR_FAIL_DELETE_QUESTION = 'Falha ao tentar deletar questão.';

  // Error messages related to course details validation
  const ERR_COURSE_NOT_POSSIBLE_TOPIC = 'O curso não pode ser ativado pois não possui tópicos!';
  const ERR_FAIL_TO_CHANGE_COURSE_VISIBILITY = 'Falha ao tentar mudar visibilidade do curso!';
  const ERR_COURSE_INEXISTENT = 'Este curso não existe!';
  const ERR_TOPIC_INEXISTENT = 'Este tópico não existe!';
  const ERR_QUIZ_INEXISTENT = 'Este quiz não existe!';
  const ERR_QUESTION_INEXISTENT = 'Esta questão não existe!';
  const ERR_INVALID_TITLE = 'O título não poder ser vazio (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_TITLE . ' caracteres).';
  const ERR_INVALID_WORKLOAD = 'A carga horária deve ser um número inteiro de horas não nulo.';
  const ERR_INVALID_DESCRIPTION = 'A descrição não pode ser vazia (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_DESCRIPTION . ' caracteres).';
  const ERR_INVALID_IMAGE_TYPE = 'Somente imagens jpg, jpeg ou png.';
  const ERR_INVALID_IMAGE_LENGTH = 'Tamanho da imagem inválido (máximo ' . GlobalValues::MAXIMUM_SIZE_OF_THE_IMAGE_BLOB . ' bytes).';
  const ERR_TITLE_ALREADY_EXISTS = 'Já existe uma entidade de mesma natureza com esse título.';

  // Error messages related to course topics
  const ERR_INVALID_TOPIC_CONTENT = 'Conteúdo do tópico inválido (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_DOCUMENT_BLOB . ' bytes).';

  // Error messages related to questions in quizzes
  const ERR_INVALID_QUESTION_TEXT = 'Texto da questão inválido (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_QUESTION . ' caracteres.';
  const ERR_INVALID_RIGHT_ANSWER = 'A resposta selecionada (dever ser uma letra do alfabeto).';

  // Error messages related to alternatives in quizzes
  const ERR_INVALID_QUESTION_NUMBER = 'Número da alternativa selecionada não é válido.';
  const ERR_INVALID_ALTERNATIVE = 'Texto para alternativa inválido (máximo de ' . GlobalValues::MAXIMUM_SIZE_OF_THE_ALTERNATIVE . ' caracteres.';
  const ERR_WHEN_SAVING_ONE_OF_ALTERNATIVES = 'Houve um erro ao salvar uma das alternativas da questão.';

  // successes messages to users
  const SUCCESS_CREATE_COURSE = 'Curso criado com sucesso!';
  const SUCCESS_UPDATE_COURSE = 'Curso atualizado com sucesso!';
  const SUCCESS_DELETE_COURSE = 'Curso removido com sucesso!';

  const SUCCESS_CREATE_TOPIC = 'Tópico criado com sucesso!';
  const SUCCESS_UPDATE_TOPIC = 'Tópico atualizado com sucesso!';
  const SUCCESS_DELETE_TOPIC = 'Tópico removido com sucesso!';

  const SUCCESS_CREATE_QUIZ = 'Quiz criado com sucesso!';
  const SUCCESS_UPDATE_QUIZ = 'Quiz atualizado com sucesso!';
  const SUCCESS_DELETE_QUIZ = 'Quiz removido com sucesso!';

  const SUCCESS_TO_CHANGE_COURSE_VISIBILITY = 'Visibilidade do curso aterada com sucesso!';

  const SUCCESS_CREATE_QUESTION = 'Questão criado com sucesso!';
  const SUCCESS_UPDATE_QUESTION = 'Questão atualizado com sucesso!';
  const SUCCESS_DELETE_QUESTION = 'Questão removido com sucesso!';
}
