<?php
function callAIMatching($student, $sch) {
    $data = json_encode(['student' => $student, 'scholarship' => $sch['description']]);
    $cmd = "python3 " . __DIR__ . "/matching_model.py '$data'";
    $output = shell_exec($cmd);
    return json_decode($output, true);
}
?>