<?php

namespace Drupal\library_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class LibraryManagerSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'library_manager_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search'),
      '#description' => $this->t('Search by book title or category'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
public function submitForm(array &$form, FormStateInterface $form_state) {
  // Redirect to the public books page with the search parameter.
  $search_text = $form_state->getValue('search_text');
  $url = Url::fromRoute('library_manager.public_books', [], ['query' => ['search' => $search_text]]);
  $form_state->setRedirectUrl($url);
}


  // Insert the book data into the database.
  \Drupal::database()->insert('library_manager_books')
    ->fields([
      'title' => $form_state->getValue('title'),
      'author' => $form_state->getValue('author'),
      'category' => $form_state->getValue('category'),
      'description' => $form_state->getValue('description'),
      'isbn' => $form_state->getValue('isbn'),
      'photo' => $photo,
    ])
    ->execute();
}
}
