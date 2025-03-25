<?php

require_once("../../class2.php");

if (!defined('e107_INIT'))
{
    exit;
}

header('Content-Type: application/json');

if $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['entry_id']) && isset($_POST['field']) && isset($_POST['value']))
{
    $sql = e107::getDb();
    $tp = e107::getParser();

    $entryID = intval($_POST['entry_id']);
    $field = $_POST['field'];
    $value = $tp->toDB($_POST['value']);

    // Only allow updating of "rank" or "notes"
    if ($field !== 'rank' && $field !== 'notes')
    {
        echo json_encode(['message' => 'Invalid field.']);
        exit;
    }

    // Update the specified field
    $updateData = [
        'data' => [
            $field => $value
        ],
        'WHERE' => 'entry_id = ' . $entryID
    ];

    $success = $sql->update('user_game_list_entries', $updateData);
    if($success)
    {
        echo json_encode(['message' => 'Entry updated successfully.']);
    }
    else
    {
        echo json_encode(['message' => 'Update failed.']);
    }
}
else
{
    echo json_encode(['message' => 'Invalid request.']);
}

?>