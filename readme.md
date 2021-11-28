# ABDRT (Another BirthDay Remember Tool)

## Install

1. Clone project
    ```
    git clone git@github.com:aleksejs1/abdrt.git
    cd abdrt
    ```

2. Install dependencies
    ```
    composer install
    ```

3. Configure config file
    ```
    cp .env .env.local
    vi .env.local
    ```

4. Run database migrations
    ```
    php bin/console doctrine:migrations:migrate
    ```

5. Create user in database
    ```
    php bin/console abdrt:user:create [username] [password] [email (optional)]
    ```

6. Set up cron job once a day
    ```
    php bin/console abdrt:mails:send
    ```

7. Enjoy
