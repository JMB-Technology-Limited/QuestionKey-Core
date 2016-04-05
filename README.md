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

```
vagrant up frontendtests
```

When ready, go the the Vagrant UI (Ubuntu). Open a terminal and type "./run". You only have to do this once.
(This must be done in the VM so it is connected to the desktop environment.)

Now to run the tests, go back to your dev machine and run:

```
vagrant ssh frontendtests
./test
```
