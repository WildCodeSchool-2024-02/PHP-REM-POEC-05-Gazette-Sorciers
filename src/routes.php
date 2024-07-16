<?php

session_start();
// list of accessible routes of your application, add every new route here
// key : route to match
// values : 1. controller name
//          2. method name
//          3. (optional) array of query string keys to send as parameter to the method
// e.g route '/item/edit?id=1' will execute $itemController->edit(1)
return [
    '' => ['HomeController', 'index',],
    'login' => ['UserController', 'login',],
    'logout' => ['UserController', 'logout',],
    'register' => ['UserController', 'register',],
    'categories/edit' => ['CategoryController', 'edit', ['id']],
    'categories/show' => ['CategoryController', 'show', ['id']], // listings des topics par categorie
    'categories/add' => ['CategoryController', 'add',],
    'categories/delete' => ['CategoryController', 'delete',],
    'topics' => ['TopicController', 'index', ['id']],
    'topics/add' => ['TopicController', 'add', ['id']],
    'topics/show' => ['TopicController', 'show', ['id']], // affichage d'un topic et ses commentaires
    'profile' => ['ProfileController','profile', ['id']],
];
