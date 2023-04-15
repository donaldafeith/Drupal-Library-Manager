<?php

namespace Drupal\library_manager\Controller;

use Drupal\file\Entity\File;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Link;

class LibraryController extends ControllerBase {

  public function books() {
    $build = [];

    $build['form'] = $this->formBuilder()->getForm('Drupal\library_manager\Form\LibraryManagerBookForm');
    $build['books_table'] = $this->getBooksTable();

    return $build;
  }

  public function adminBooks() {
    $build = [];

    $build['form'] = $this->formBuilder()->getForm('Drupal\library_manager\Form\LibraryManagerBookForm');
    $build['books_table'] = $this->getBooksTable(FALSE, NULL, TRUE);

    return $build;
  }

  private function getBooksTable($public = FALSE, $search = NULL, $admin = FALSE) {
    $header = [
      'id' => $this->t('ID'),
      'title' => $this->t('Title'),
      'author' => $this->t('Author'),
      'category' => $this->t('Category'),
      'photo' => $this->t('Photo'),
      'checked_out' => $this->t('Checked Out'),
    ];

    if ($admin) {
      $header['edit'] = $this->t('Edit');
    }

    $rows = [];
    $database = \Drupal::database();
    $query = $database->select('library_manager_books', 'lb')
      ->fields('lb', ['id', 'title', 'author', 'category', 'photo']);

    if ($search) {
      $group = $query->orConditionGroup()
        ->condition('title', '%' . $database->escapeLike($search) . '%', 'LIKE')
        ->condition('category', '%' . $database->escapeLike($search) . '%', 'LIKE');
      $query->condition($group);
    }

    $results = $query->execute()->fetchAll();
foreach ($results as $result) {
  $title = $public ? [
    'data' => [
      '#type' => 'link',
      '#title' => $result->title,
      '#url' => Url::fromRoute('library_manager.book', ['id' => $result->id]),
    ],
  ] : $result->title;
  
  if (!empty($result->photo)) {
    $photo = File::load($result->photo);
    if ($photo) {
      $photo_url = file_create_url($photo->getFileUri());
    }
  }
  
  $row = [
    'id' => [
      'data' => [
        '#type' => 'link',
        '#title' => $result->id,
        '#url' => Url::fromRoute('library_manager.book', ['id' => $result->id]),
        '#attributes' => ['target' => '_blank'],
      ],
    ],
    'title' => $title,
    'author' => $result->author,
    'category' => $result->category,
    'photo' => [
      'data' => [
        '#type' => 'html_tag',
        '#tag' => 'img',
        '#attributes' => [
          'src' => $photo_url,
          'alt' => $this->t('Photo of @title', ['@title' => $result->title]),
          'width' => '100',
          'height' => '100',
        ],
      ],
    ],
    // Replace the existing 'checked_out' line with the new code snippet
    'checked_out' => [
      'data' => [
        '#markup' => $result->checked_out ? $this->t('Yes') : $this->t('No'),
      ],
    ],
  ];
  
  if ($admin) {
    $edit_link = Link::createFromRoute(
      $this->t('Edit'),
      'library_manager.book_edit',
      ['book_id' => $result->id]
    )->toString();

    $row['edit'] = $edit_link;
  }

  $rows[] = $row;
}


$table = [
  '#type' => 'table',
  '#header' => $header,
  '#rows' => $rows,
  '#empty' => $this->t('No books found.'),
];
return $table;
}

public function publicBooks(Request $request) {
  $build = [];
  $build['search_form'] = $this->formBuilder()->getForm('Drupal\library_manager\Form\LibraryManagerSearchForm');
  $search = $request->query->get('search');
  $build['books_table'] = $this->getBooksTable(TRUE, $search);
  return $build;
}

public function book($id) {
  $database = \Drupal::database();
  $query = $database->select('library_manager_books', 'lb')
  ->fields('lb', ['id', 'title', 'author', 'category', 'description', 'isbn', 'photo'])
  ->condition('id', $id, '=');
  $result = $query->execute()->fetchObject();
  if ($result) {
    if ($result->photo) {
      $file = File::load($result->photo);
      $result->photo_url = file_create_url($file->getFileUri());
    }
    return [
      'title' => [
      '#type' => 'markup',
      '#markup' => '<h2>' . $this->t('Title: @title', ['@title' => $result->title]) . '</h2>',
    ],
    'author' => [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Author: @author', ['@author' => $result->author]) . '</p>',
    ],
    'isbn' => [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('ISBN: @isbn', ['@isbn' => $result->isbn]) . '</p>',
    ],
    'category' => [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Category: @category', ['@category' => $result->category]) . '</p>',
    ],
    'description' => [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Description: @description', ['@description' => $result->description]) . '</p>',
    ],
    'photo' => [
      '#type' => 'markup',
      '#markup' => $result->photo_url ? '<img src="' . $result->photo_url . '" alt="' . $result->title . '">' : '',
    ],
  ];
} else {
  return [
    '#type' => 'markup',
    '#markup' => $this->t('Book not found.'),
  ];
}
}
public function members() {
  $build = [];
  $build['books_table'] = $this->getBooksTable(TRUE);
  return $build;
}
}