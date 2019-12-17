<?php
// Allowed origins to upload images
$accepted_origins = array("http://localhost:3000", "http://82.64.201.160", "http://recette.thomas-claireau.fr");

// Images upload path
$idUser = filter_input(INPUT_GET, 'idUser');
$idPost = filter_input(INPUT_GET, 'id');
$mainImageUpload = filter_input(INPUT_GET, 'uploadImage');
$action = filter_input(INPUT_GET, 'action');

if (!$mainImageUpload) {
    $path = 'posts_images';
} else {
    $path = 'src/assets/img/posts_images';
}

if (!file_exists($path)) {
    mkdir($path, 0777, true);
}

if (!file_exists($path . '/' . $idPost)) {
    mkdir($path . '/' . $idPost, 0777, true);
}

$imageFolder = $path . '/' . $idPost . '/';

reset($_FILES);
$temp = current($_FILES);

if (is_uploaded_file($temp['tmp_name'])) {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Same-origin requests won't set an origin. If the origin is set, it must be valid.
        if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        } else {
            header("HTTP/1.1 403 Origin Denied");
            return;
        }
    }

    // Sanitize input
    if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
        header("HTTP/1.1 400 Invalid file name.");
        return;
    }

    // Verify extension
    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
        header("HTTP/1.1 400 Invalid extension.");
        return;
    }

    // Accept upload if there was no origin, or if it is an accepted origin
    $filetowrite = $imageFolder . $temp['name'];
    move_uploaded_file($temp['tmp_name'], $filetowrite);

    // Respond to the successful upload with JSON.
    echo json_encode(array('location' => $filetowrite));
    if ($mainImageUpload) {
        $this->redirect('post', ['action' => $action, 'id' => $idPost, 'idUser' => $idUser]);
    }
} else {
    // Notify editor that the upload failed
    header("HTTP/1.1 500 Server Error");
}
