<?php
function File_upload($field) {
    if (empty($_FILES)) return false;
    if ($_FILES[$field]['error'] != 0) return false;
    if (is_uploaded_file($_FILES[$field]['tmp_name'])) {
        $upload_dir = __DIR__ . '/../img/';
        $file_name = basename($_FILES[$field]['name']);
        $target_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES[$field]['tmp_name'], $target_path))
            return 'img/' . $file_name;
    }
    return false;
}