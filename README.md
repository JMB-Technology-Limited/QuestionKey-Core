# QuestionKey





## Vagrant for development

Use Vagrant and Virtual Box for development

After running Vagrant, you need to run

```
vagrant rsync-auto
```
To auto sync changes between your code and the box.

The app will be available on http://localhost:8080/app_dev.php

To run tests, log in with vagrant ssh and run

```
cd /vagrant; phpunit -c app/
```

