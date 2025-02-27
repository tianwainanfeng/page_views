
About: Page Views
Version: 1.0

# Structure:
  - `README.md` # this file

  - `create_page_views_table.sql` # schema to create database tables
  
  - `track_views.php` # PHP script to track views

  - `index.html` # HTML example page

# Step 1: create database tables
  MySQL (please make sure the database server is running)
  ```bash
  # Run the SQL script to create the page_views table in the specified database
  mysql -u your_username -p -D your_database_name < create_page_views_table.sql
  ```


# Step 2: create a PHP script to track views
  PHP (please make sure the PHP server is running)
  `track_views.php`

# Step 3: define security environment variables
  put the following in `.bash_profile` or `.zshrc` and source the file
  ```bash
  export PROJECT_DB_HOST="localhost"
  export PROJECT_DB_USER="your_db_user"
  export PROJECT_DB_PASS="your_db_password"
  export PROJECT_DB_NAME="your_database"
  ```

# Test:
  To test locally, you can run:
      ```php -S localhost:8000```
  then open:
    <http://localhost:8000/index.html>
    <http://localhost:8000/track_views.php>

# Further:
  Apply this to other web pages.
