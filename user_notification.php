<?php

if(!defined('e107_INIT'))
{
	require_once("../../class2.php");
    require_once("../../e107_plugins/pm/pm_class.php");
}

// Assuming you have a class named 'YourCustomPrivateMessageClass'
class userNotification
{
    /*public function sendNotification($fromUserId, $toUserId, $subject, $message)
    {
        $pm = e107::getAddon('pm');
        $pmPrefs = $pm->getPrefs();

        $vars = array(
            'from_id' => $fromUserId,
            'to_info' => array('user_id' => $toUserId), // Assuming you have user information
            'pm_subject' => $subject,
            'pm_message' => $message,
            'pm_userclass' => 0, // You can set a user class if needed
            'notify_class' => $pmPrefs['notify_class'], // You can customize the notification class
        );

        // Example: Notify the post author when someone comments on their post
        $postAuthorId = $this->getPostAuthorId($params['post_id']);
        $commenterId = $params['user_id'];

        if ($postAuthorId !== $commenterId) {
            $notificationResult = $pm->add($vars);
        }

        return $notificationResult;
    }

    public function sendNotification($fromUserId, $toUserId, $subject, $message)
    {
        $pm = new private_message();

        $vars = array(
            'from_id' => $fromUserId,
            'to_info' => array('user_id' => $toUserId),
            'pm_subject' => $subject,
            'pm_message' => $message,
            'pm_userclass' => 0,
        );

        $notification = $pm->add($vars);

        return $notification;
    }*/

    public function sendNotification($fromUserId, $toUserId, $subject, $message)
    {
        $pm = new private_message();

        $vars = array(
            'from_id' => $fromUserId,
            'to_info' => array('user_id' => $toUserId),
            'pm_subject' => $subject,
            'pm_message' => $message,
            'pm_userclass' => 0,
        );

        // Example: Notify the post author when someone comments on their post
        $postAuthorId = $this->getPostAuthorId($params['post_id']);
        $commenterId = $params['user_id'];

        if ($postAuthorId !== $commenterId) {
            $notification = $pm->add($vars);
        }  

        return $notification;
    }

    public function getPostAuthorId($postId)
    {
        $sql = e107::getDb();
        $postId = (int)$postId;

        // Assuming your posts table is named 'e107_post'
        $query = "SELECT post_author FROM #your_post_table# WHERE post_id = {$postId}";
        $result = $sql->retrieve($query);

        if ($result) {
            return $result[0]['post_author'];
        }

        return 0; // Return 0 if post or author not found
    }
}

// Example of how to use it
$senderUserId = 1; // Replace with the actual sender user ID
$recipientUserId = 2; // Replace with the actual recipient user ID
$notificationSubject = 'Your Notification Subject';
$notificationMessage = 'Your Notification Message';

$pmHandler = new userNotification();
$result = $pmHandler->sendNotification($senderUserId, $recipientUserId, $notificationSubject, $notificationMessage);

// Check the result and handle accordingly
if ($result === LAN_PM_65) {
    // Handle the case when there's an error (no subject, no message body, and no uploaded files)
} else {
    // Handle the successful sending of the private message
}
?>