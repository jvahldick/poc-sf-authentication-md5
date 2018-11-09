POC SF: Md5 Authentication
==========================

The Symfony project is a POC to demonstrate two different flows:
* Authentication of users using MD5
* A way to change the users passwords from MD5 to BCRYPT

**PS:** All the commands must be ran from inside the **PHP Docker container**.

### Spinning the containers

It is quite simple to make the application work, you can just run the simple command:

```bash
$ docker-compose up -d
```

Once you have the containers up and running, connect to your PHP container:

```bash
$ docker-compose exec php-fpm bash
```

### Dependencies

First of all, we need to install all the third party libraries that our project is dependent on.

```bash
$ composer install
```

### Migrations

We need to have our database and tables in place to start creating users.
Thanks for doctrine migrations, we can have it easily running the command:

```bash
$ bin/console doctrine:migrations:migrate
```

### Creating the user

After connecting to the container, we can start creating initial data.

The first requirement for us, is creating a new user into the database. 
To do so, we can simply run the following command:

```bash
$ bin/console doctrine:query:sql 'INSERT INTO users(`id`, `username`, `password`, `is_active`, `legacy_user`) VALUES ("
60eabe5a-c5d8-4469-8278-e778df069732", "foo@bar.com", md5("12345"), 1, 1);'
```

Check if the user was created.

```bash
$ bin/console doctrine:query:sql 'SELECT * FROM users;'
```

As result, you gonna see the details of our user, that should be something similar to the array below:
```php
array(1) {
  [0]=>
  array(5) {
    ["id"]=>
    string(37) "60eabe5a-c5d8-4469-8278-e778df069732"
    ["username"]=>
    string(11) "foo@bar.com"
    ["password"]=>
    string(60) "827ccb0eea8a706c4c34a16891f84e7b"
    ["is_active"]=>
    string(1) "1"
    ["legacy_user"]=>
    string(1) "1"
  }
}
```

We can conclude that our user is foo@bar.com, and it is a legacy user.

### Testing the authentication

To grant our user can be authenticated, we will make a POST request to the /authenticate endpoint.
The response coming from the server is empty, so if you want to see an error message, just the password to anything else.

```bash
$ curl -X POST \
    http://nginx/authenticate \
    -H 'content-type: multipart/form-data;' \
    -F _username=foo@bar.com \
    -F _password=12345
```

Checking your user again you will see that its password changed, as the legacy user field. Great, now our user is using BCRYPT instead MD5, and it still been authenticated.

```bash
$ bin/console doctrine:query:sql 'SELECT password, legacy_user FROM users WHERE username = "foo@bar.com";'
```

### How does it work?

There are four main files that you can check.
* [security.yaml](/app/config/packages/security.yaml)
* [User](/app/src/Entity/User.php)
* [UserProvider](/app/src/Security/Providers/UserProvider.php)
* [AuthenticationSubscriber](/app/src/EventListener/AuthenticationSubscriber.php)

**security.yaml**

It is responsible to define the configuration on the security of our application.
[You can check the symfony documentation for more information](https://symfony.com/doc/current/security.html)


**User**

The user entity extends the UserInterface, that is required-ish to you use the Symfony security for authentication.

The main detail on this class is the implementation of the [EncoderAwareInterface](https://symfony.com/doc/current/security/named_encoders.html) interface, and that is used to the define which encoder the security component should use to validate the users password.


**UserProvider**

This is the class responsible to authenticate the user.


**AuthenticationSubscriber**

The AuthenticationSubscriber is the class that changes the user information, encryption the new password, and also changing the legacyUser field to false, which means the users password was successfully changed.