
# Library Manager

Library Manager is a Drupal module that provides a user-friendly interface for managing library books and members. The module allows administrators to add, edit, and delete books, and manage library members. The module also provides a public-facing view for visitors to browse and search the library's book collection.

## Features

-   Add, edit, and delete books with relevant details (title, author, category, and photo)
-   Track the status of books (checked out or available)
-   Add, edit, and delete library members
-   Admin-specific interface for managing books and members
-   Public-facing interface for browsing and searching books

## Installation

1.  Download or clone the Library Manager module repository from GitHub.
2.  Place the entire `library_manager` folder in your Drupal installation's `modules` directory.
3.  Go to the **Extend** section in your Drupal admin dashboard (`/admin/modules`).
4.  Enable the **Library Manager** module and click on **Install**.

## Configuration

After installing the Library Manager module, you can access the settings by navigating to the following paths in your Drupal admin dashboard:

-   Manage library books: `/admin/content/library_manager/books`
-   Manage library members: `/admin/content/library_manager/members`
-   Add or update books: `/admin/content/library_manager/book_form`
-   Public view of the library books: `/library_manager/public_books`

## Usage

### As an administrator

1.  Add new books using the book form at `/admin/content/library_manager/book_form`.
2.  Manage books by editing or deleting entries at `/admin/content/library_manager/books`.
3.  Manage library members by editing or deleting entries at `/admin/content/library_manager/members`.

### As a public user

1.  Browse the public library collection at `/library_manager/public_books`.
2.  Search for books by title or category using the search bar.

## Contributing

If you would like to contribute to this project, please feel free to submit a pull request, report bugs, or suggest new features by opening a new issue on GitHub.

## License

This project is licensed under the [GPL-3.0 License](https://www.gnu.org/licenses/gpl-3.0.en.html).
