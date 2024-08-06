<?php

namespace App\Controller;

use App\Model\NotificationManager;
use App\Model\TopicManager;
use App\Model\UserManager;
use PDO;

class NotificationController extends AbstractController
{
    public function getNotifications(): void
    {

        $id = $_GET['id'] ?? '';

        if (!empty($id)) {
            $notificationManager = new NotificationManager();
            $notifications = $notificationManager->getNotifications($id);
            $results = array();
            foreach ($notifications as $notification) {
                $userManager = new UserManager();
                $user = $userManager->getUserById($notification['id_user']);
                $topicManager = new TopicManager();
                $topic = $topicManager->selectOneById($notification['id_topic']);
                $result = array_merge($user, $topic);
                $result['commentCreatedAt'] = $notification['created_at'];
                $result['idNotif'] = $notification['id'];
                array_push($results, $result);
            }

            header('Content-Type: application/json');
            echo json_encode($results);
            exit();
        }

        header('Content-Type: application/json');
        echo json_encode([]);
        exit();
    }

    public function delete(): void
    {
        $id = $_GET['id'] ?? '';

        if (!empty($id)) {
            $notificationManager = new NotificationManager();
            $notificationManager->delete($_GET['id']);
            header('Content-Type: application/json');
            echo json_encode('OK');
            exit();
        }
        header('Content-Type: application/json');
        echo json_encode('');
        exit();
    }
}
