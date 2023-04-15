<?php

namespace Drupal\library_manager\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class LibraryManagerBookEditForm extends LibraryManagerBookForm {

  public function getFormId() {
    return 'library_manager_book_edit_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $book_id = NULL) {
    $form = parent::buildForm($form, $form_state);

    // Load book data.
    $book = $this->loadBook($book_id);

    // Pre-populate form fields.
    if ($book) {
      $form['title']['#default_value'] = $book->title;
      $form['author']['#default_value'] = $book->author;
      $form['category']['#default_value'] = $book->category;
      $form['description']['#default_value'] = $book->description;
      $form['isbn']['#default_value'] = $book->isbn;
      $form['checked_out']['#default_value'] = $book->checked_out;

      if (!empty($book->photo)) {
        $form['photo']['#default_value'] = [
          'fids' => [$book->photo],
        ];
      }

      // Add a hidden field to store the book ID.
      $form['book_id'] = [
        '#type' => 'hidden',
        '#value' => $book_id,
      ];
    }

    return $form;
  }

  protected function loadBook($book_id) {
    $book = NULL;

    if (!empty($book_id)) {
      $database = \Drupal::database();
      $result = $database->select('library_manager_books', 'lb')
        ->fields('lb')
        ->condition('id', $book_id)
        ->execute()
        ->fetchObject();

      if ($result) {
        $book = $result;
      }
    }

    return $book;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $book_id = $form_state->getValue('book_id');

    // Update the book data.
    $fields = [
      'title' => $form_state->getValue('title'),
      'author' => $form_state->getValue('author'),
      'category' => $form_state->getValue('category'),
      'description' => $form_state->getValue('description'),
      'isbn' => $form_state->getValue('isbn'),
      'checked_out' => $form_state->getValue('checked_out') ? 1 : 0,
    ];

    if ($form_state->getValue('photo')) {
      $file = File::load($form_state->getValue('photo')[0]);
      $file->setPermanent();
      $file->save();
      $fields['photo'] = $file->id();
    }

    $database = \Drupal::database();
    $database->update('library_manager_books')
      ->fields($fields)
      ->condition('id', $book_id)
      ->execute();

    // Display a success message.
    $this->messenger()->addMessage($this->t('Book updated: @title by @author', [
      '@title' => $form_state->getValue('title'),
      '@author' => $form_state->getValue('author'),
    ]));

    // Redirect to the admin books page.
    $form_state->setRedirect('library_manager.admin_books');
  }
}
