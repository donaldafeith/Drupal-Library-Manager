<?php

namespace Drupal\library_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File; // This imports the File class

class LibraryManagerBookForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'library_manager_book_form';
  }

  /**
   * {@inheritdoc}
   */
public function buildForm(array $form, FormStateInterface $form_state) {
  $form['title'] = [
    '#type' => 'textfield',
    '#title' => $this->t('Title'),
    '#required' => TRUE,
  ];

  $form['author'] = [
    '#type' => 'textfield',
    '#title' => $this->t('Author'),
    '#required' => TRUE,
  ];

  $form['category'] = [
    '#type' => 'textfield',
    '#title' => $this->t('Category'),
    '#required' => TRUE,
  ];

  $form['description'] = [
    '#type' => 'textarea',
    '#title' => $this->t('Description'),
    '#required' => TRUE,
  ];

  $form['isbn'] = [
    '#type' => 'textfield',
    '#title' => $this->t('ISBN'),
    '#maxlength' => 13,
    '#size' => 13,
  ];
  
  $form['photo'] = [
    '#type' => 'managed_file',
    '#title' => $this->t('Book Cover Photo'),
    '#upload_location' => 'public://library_manager/books/',
    '#default_value' => isset($book->photo) ? $book->photo : NULL,
    '#upload_validators' => [
    'file_validate_extensions' => ['jpg jpeg png gif'],
  ],
];
    // Add the 'checked_out' checkbox field
    $form['checked_out'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Checked Out'),
      '#default_value' => 0,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Implement custom validation if needed.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the form values
    $title = $form_state->getValue('title');
    $author = $form_state->getValue('author');
    $category = $form_state->getValue('category');
    $description = $form_state->getValue('description');
    $isbn = $form_state->getValue('isbn');
    $checked_out = $form_state->getValue('checked_out') ? 1 : 0;

    // Process the photo file, if any
    if ($form_state->getValue('photo')) {
      $file = File::load($form_state->getValue('photo')[0]);
      $file->setPermanent();
      $file->save();
      $photo = $file->id();
    } else {
      $photo = NULL;
    }

    // Insert the book data into the database
    \Drupal::database()->insert('library_manager_books')
      ->fields([
        'title' => $title,
        'author' => $author,
        'category' => $category,
        'description' => $description,
        'isbn' => $isbn,
        'photo' => $photo,
        'checked_out' => $checked_out, // Add the 'checked_out' field value
      ])
      ->execute();

    // Display a success message
    $this->messenger()->addMessage($this->t('Book added: @title by @author', [
      '@title' => $title,
      '@author' => $author,
    ]));
  }

}

