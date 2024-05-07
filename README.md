# Proxidize Task Setup

Follow these steps to set up the Proxidize Task project on your local machine:

## Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html) or similar local server environment installed on your machine.

## Installation

1. Clone the repository directly into the `htdocs` directory of your XAMPP installation:
    ```bash
    git clone https://github.com/mohammad-alhouwari/proxidize_task.git
    ```
    It should appear as `/xampp/htdocs/proxidize_task`

2. Create a new database named `wordpress` in your local MySQL server.

3. Import the provided `wordpress.sql` file into the `wordpress` database. You can find the SQL file in the `Datatables` directory of the cloned repository.

4. Navigate to [http://localhost/proxidize_task/wp-admin/](http://localhost/proxidize_task/wp-admin/) in your web browser.

5. Log in with the following credentials:
    - **Username:** mohammad
    - **Password:** Mm12345

That's it! You should now have the Proxidize Task project set up and ready to use on your local machine.
