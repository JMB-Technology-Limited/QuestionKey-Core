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
./test
```

### Running front end tests

To setup, run once:

```
vagrant up frontendtests
vagrant ssh frontendtests
./run
```

Now to run tests repeatedly, simply run:

```
./test
```
