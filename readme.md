# Test app

## How to use
- install composer things
- run the migrations:
  - ```bash
    php bin/console doctrine:migrations:migrate
    ```

- start the message worker
  - ```bash
    php bin/console messenger:consume async
    ```

- run the Import command
  - ```bash
    php bin/console ImportCommand
    ```

- run the Scrape command
    - ```bash
      php bin/console ImportCommand
      ```

## To test the api
- I only got to the point to test it with the built-in server,    
  but at least it responds quickly

```bash
php -S localhost:80 -t public
```
