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
    'items' => ['ItemController', 'index',],
    'items/edit' => ['ItemController', 'edit', ['id']],
    'items/show' => ['ItemController', 'show', ['id']],
    'items/add' => ['ItemController', 'add',],
    'items/delete' => ['ItemController', 'delete',],
    'categories' => ['CategoryController', 'index',],
    'login' => ['UserController', 'login',],
    'logout' => ['UserController', 'logout',],
    'forgotPassword' => ['UserController', 'forgotPassword',],
    'resetPassword' => ['UserController', 'resetPassword', ['id_user', 'token']],
    'register' => ['UserController', 'register',],
    'categories/edit' => ['CategoryController', 'edit', ['id']],
    'categories/show' => ['CategoryController', 'show', ['id']], // listings des topics par categorie
    'categories/add' => ['CategoryController', 'add',],
    'categories/delete' => ['CategoryController', 'delete',],
    'categories/manage' => ['CategoryController', 'manage'],
    'comments' => ['CommentController', 'index',],
    'topics/comments/add' => ['CommentController', 'add',],
    'comments/delete' => ['CommentController', 'delete',],
    'topics' => ['TopicController', 'index', ['id']],
    'topics/add' => ['TopicController', 'add', ['id']],
    'topics/show' => ['TopicController', 'show', ['id']], // affichage d'un topic et ses commentaires
    'users' => ['UserController', 'listUsers'],
    'users/confirm-delete/{id}' => ['UserController', 'confirmDelete', ['id']],
    'users/delete' => ['UserController', 'delete'],
    'contact' => ['ContactController', 'contact'],
    'profile' => ['ProfileController', 'profile'],
    'profile/edit' => ['ProfileController', 'editProfile'],
    'notice' => ['StaticPageController', 'notice'],
    'about' => ['StaticPageController', 'about'],
    'search' => ['SearchController', 'suggestions'],
    'easter-egg' => ['EasterEggController', 'show',],
];
