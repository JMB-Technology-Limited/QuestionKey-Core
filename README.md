# QuestionKey


## What is it?

This is a Symfony app that lets you run Decision Trees on your own website.

A decision tree is a series of questions asked of the user that result in them being shown a conclusion.

eg.

  *  https://scotland.shelter.org.uk/get_advice/downloads_and_tools/online_checkers/does_the_council_have_to_house_you


Install this app somewhere, and use it's admin interface to build your trees. Then use one of the JavaScript widgets
included with the tool to embed a tree on your own website!

## Trees

The app can host multiple trees.

## Tree Versions

Each tree has multiple versions.

When looking at a tree version, there is an option to make a new version from this - this will copy all existing data ready for editing.

Only one version can be published at once.

In this way, admin's can work on a new version of a tree and the live version will be the same. When the new version is
ready and has been tested (preview links are available for this) then the new version can be published.

Some bits of the published Tree Version can not be edited through the admin interface. This is to safeguard the live tree from breakages.

## Nodes and Node Options

Each tree is made up of Nodes and Node Options.
The user starts at the starting Node, and then jumps to other nodes in the tree until they get to a conclusion.
They move between Nodes via Node Options - these present options to the user of where to go next.

There is nothing to enforce that the user can reach all nodes from the starting node, or that the user won't get stuck in a infinite loop going around the same set of nodes. It's up to the tree designer to make sure this won't happen.

## Feature

When editing a Tree Version, you can turn "features" on and off. Features make a tree more complicated, so they are off by default.

## Feature - Variables

TODO

## Feature - Library Content

Normally each Node has a body, and each body is separate. This is a pain if you have the same bit of content that should be on many nodes. Now, when it changes you have to edit the content many times in different places.

Library Content solves this problem. You can create a new bit of content in the Library. The Library is shared across the whole Tree Version. You can then use the library content on as many nodes as you want. Now, when you want to edit the content there is now only one place you need to make the edit.

## Showing trees on your website using the Normal Widget

The Javascript for this is available from:

    /js/external/normal-jquery-v1.js

After loading jQuery and the widget, add a div to the page where the content will go.

Then call:

    <script>
        var tree;
        $( document ).ready(function() {
            tree = new QuestionKeyNormalTree("#DemoHere", {'treeId':'demo'}, {});
            tree.start();
        });
    </script>

The first parameter is the jQuery selector for where the tree should appear. The second is a directory of options. The third is a directory of theme overrides.

Required options are only some actual tree data.
You can provide this by passing the "treeServer" parameter (a domain name) and the "treeId" (a string). The tree data will be loaded from there.
Or you can pass in the data directly in the "treeData" parameter.

Optional options are:

  *  "hasSSL" - does the server have SSL? Default true. (Cos it's all SSL these days right? Right?)
  *  "forceSSL" - TODO
  *  "serverURLSSL" -this should be a full URL starting with "https:". If this is not given, it will be constructed from treeServer.
  *  "serverURLNotSSL" -this should be a full URL starting with "http:". If this is not given, it will be constructed from treeServer.
  *  "logUserActions" -  if true, all actions will be logged back to the server. Only works if server URL set. (So not if "treeData" used.)
  *  "showPreviousAnswers" - if true, previous answers will be shown. This allows the user to jump back in the tree.
  *  "callBackAfterDrawNode" - a javascript function that is called after the user makes a selection and the new node is drawn.
  *  "scrollIntoViewOnChange" - if true, when a user makes a selection the new node will scrolled into view using JavaScript.
  *  "browserHistoryRewrite" - if true, the browser history will be rewritten so that the back button in the browser takes the user back an option in the tree. For this to work, some extra javascript must be set in the document ready function.

    <script>
        $( document ).ready(function() {
            window.onpopstate = function(event) {
                tree.onWindownPopState(event);
            };
        });
    </script>

If you want to change the HTML produced by the widget, you can hopefully do this simply by providing some options to the theme.

Every option in the theme is one off:
  *  function that takes raw data and produces HTML.
  *  a string that sets the jQuery selector that can be used to find a place to add more content.

The default theme/functions are in the widget - check the source code.

For example, to change the loading message:

    { 'loadingHTML': function(data) { return '<div class="loadingMyMessage">We are working!</div>'; } }

## Showing trees on your website using the Cascade Widget

The Javascript for this is available from:

    /js/external/cascade-jquery-v1.js

Most options are the same as the normal widget.

## Admin

Currently users marked as admin have access to all trees via the admin interface.

Marking a user as admin must be done via the command line.

   php app/console fos:user:promote user_name_here ROLE_ADMIN

The user must log in and out afterwards.

## Admin - export and import trees

Admin's can export a particular tree version as JSON data. They can then take this data to a new server, and import it by creating a new tree. The new tree will have one version which is the original tree.

This lets admin's move trees between servers.

## How to give admin access to a user

Get the user to register in the browser at /register

In the command line, run

    php app/console fos:user:promote

Enter the new users name and for a role enter: ROLE_ADMIN

The user will have to log out and in again to see the difference.

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
