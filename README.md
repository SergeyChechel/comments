# Project Name

The project Comments allows users to add comments to the page. It is possible to both add new comments and respond to existing ones. Also, when adding a comment for the first time, the user can add a file or avatar picture. You cannot edit or delete comments.

## Features

-   Feature 1: You can add an original comment by clicking on the "Добавить комментарий" button on the homepage. When you initially add a comment, you can also add an image or text file.
-   Feature 2: You can reply to another user's comment or reply to a reply by clicking on the "Ответить". In this case, the answers are arranged in cascading order. If the original comment has answers, you can view them by clicking on the button "Посмотреть ответы".
-   Feature 3: You can navigate through comment pages using the navigation menu located at the bottom of the homepage.
-   ...

## Installation

To run this project locally, follow these steps:

1. Clone the repository to your local machine using Git: https://github.com/SergeyChechel/comments.git
2. Run the MySQL server locally using default settings (for example in a Docker container)
3. Navigate to the project directory: cd project-name
4. Install dependencies using Composer: composer install
5. In the file .env in the root of the project directory, specify the desired name of DB and configure the parameters for connecting to the database on the MySQL server
6. In the root project directory run database migrations command: php artisan migrate. This creates DB, specified above, on the MySQL server
7. Serve the application using the built-in development server. Run command: php artisan serve.
8. Follow the link in the terminal.
