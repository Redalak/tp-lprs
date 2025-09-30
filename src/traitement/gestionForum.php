<?php
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/PForum.php';
require_once __DIR__ . '/../modele/RForum.php';
require_once __DIR__ . '/../repository/PForumRepo.php';
require_once __DIR__ . '/../repository/RForumRepo.php';

use repository\PForumRepo;
use repository\RForumRepo;

$action = $_POST['action'] ?? '';
if ($action === 'new_post') {
    $author = trim($_POST['author'] ?? '');
    $title  = trim($_POST['title'] ?? '');
    $content= trim($_POST['content'] ?? '');
    if ($author !== '' && $title !== '' && $content !== '') {
        (new PForumRepo())->create($author,$title,$content);
    }
    header('Location: ../../vue/forum.php'); exit;
} elseif ($action === 'new_reply') {
    $post_id = (int)($_POST['post_id'] ?? 0);
    $author  = trim($_POST['author'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($post_id && $author !== '' && $content !== '') {
        (new RForumRepo())->create($post_id,$author,$content);
    }
    header('Location: ../../vue/forum.php?id='.$post_id); exit;
} else {
    header('Location: ../../vue/forum.php'); exit;
}
