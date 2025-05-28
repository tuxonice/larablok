# Larablok

**LaraBlok** is a modern blog application built with Laravel and Livewire, designed to seamlessly integrate with the Storyblok Headless CMS. The project fetches and displays dynamic content from Storyblok, providing a robust, interactive, and developer-friendly foundation for content-driven web applications.


## Initial Setup

Follow these steps to set up and run this project locally:

1. **Clone the repository:**
   ```bash
   git clone git@github.com:tuxonice/larablok.git
   cd larablok
   ```

2. **Start the containers:**
   ```bash
   docker-compose up -d
   ```

3. **Install composer dependencies (including sail):**
   ```bash
   docker-compose exec laravel.test composer install
   ```
   
4. **Create your environment file:**
   ```bash
   cp .env.example .env
   ```

5. **Generate the application key:**
   ```bash
   vendor/bin/sail artisan key:generate
   ```

6. **Run database migrations:**
   ```bash
   vendor/bin/sail artisan migrate:install
   vendor/bin/sail artisan migrate
   ```
---

## Setting up Local SSL Certificates (HTTPS)

Storyblok v2 requires your development server to run over HTTPS. Here’s how to set up SSL certificates and a local HTTPS proxy for development on **Linux**:

### 1. Install mkcert (for generating trusted SSL certs)

follow the instructions from [mkcert](https://github.com/FiloSottile/mkcert)

### 2. Set up mkcert and generate certificates
```bash
mkcert -install
mkcert localhost
```
This will create `localhost.pem` and `localhost-key.pem` in your directory.
Move this files to `docker/ssl/` folder

### 3. Install and run local-ssl-proxy
Install the proxy globally with npm:
```bash
sudo npm install -g local-ssl-proxy
```
Start the proxy:
```bash
local-ssl-proxy --source 8443 --target 80 --cert docker/ssl/localhost.pem --key docker/ssl/localhost-key.pem
```
Now, HTTPS will be available at https://localhost:8443 and will forward requests to your HTTP dev server.

> **Note:**
> - For Windows, see [Storyblok’s Windows guide](https://www.storyblok.com/faq/setup-dev-server-https-windows).
> - For macOS, see [Storyblok’s macOS guide](https://www.storyblok.com/faq/setup-dev-server-https-proxy).

---


## Usage


### Start containers without https
```
$ make start
```

### Start containers with https
```
$ make ssl
```

### Stop containers
```
$ make stop
```

### Run artisan command

```
$ ./vendor/bin/sail artisan <command>
```

### Executing PHP Commands

```
$ ./vendor/bin/sail php --version

$ ./vendor/bin/sail php script.php
```

### Executing Composer Commands

```
$ ./vendor/bin/sail composer show

$ ./vendor/bin/sail composer require <some package>
```

### Executing Node / NPM Commands

```
$ ./vendor/bin/sail node -v

$ ./vendor/bin/sail npm install
```

### Running Tests

```
$ ./vendor/bin/sail test
```

### Container CLI

```
$ ./vendor/bin/sail shell

$ ./vendor/bin/sail root-shell

$ ./vendor/bin/sail tinker
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
