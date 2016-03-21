# QuestionKey





## Vagrant for development

Use Vagrant and Virtual Box for development

### Seeing app

```
vagrant up normal
```

The app will be available on http://localhost:8080/app_dev.php

### Running tests

```
vagrant up normal
vagrant ssh normal
cd /vagrant; phpunit -c app/phpunit.xml.dist
```

### Running front end tests

```
vagrant up frontendtests
```

When ready, go the the Vagrant UI (Ubuntu). Open a terminal and type "./run". You only have to do this once.

Now to run the tests, go back to your dev machine and run:

```
vagrant ssh frontendtests
cd /vagrant; phpunit -c app/phpunit.frontend.xml
```

Note these tests run in both Symfony's "dev" and "prod" environments so you will nead to clear your caches if you
change the app.
