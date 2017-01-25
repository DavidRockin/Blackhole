<?php

function getStatus($status, $customCss = null) {
    switch ($status) {
        case 0: return "<span class='label label-success" . $customCss . "'>Open</span>";
        case 1: return "<span class='label label-danger" . $customCss . "'>Closed</span>";
        case 2: return "<span class='label label-info" . $customCss . "'>Active Ticket</span>";
    }
}

function getCategories() {
    global $dbh;
    $getCategories = $dbh->query("SELECT * FROM categories");
    return $getCategories->fetchAll();
}

function processAttachments($messageId)
{
	global $dbh;

	if (empty($_FILES) || empty($_FILES['file']))
		return;
	
	for ($i = 0; $i < count($_FILES['file']['name']); ++$i) {
		try {
			// determine file extension and new file name
			$ext      = explode(".", $_FILES['file']['name'][$i]);
			$fileName = md5(microtime() . uniqid() . $_FILES['file']['name'][$i]) . (count($ext) > 0 ? "." . $ext[count($ext) - 1] : "");

			// todo: add valid file validation here ?!!

			// record the attachment information to each message
			$logFile = $dbh->prepare("INSERT INTO message_attachments VALUES(NULL, :messageId, :type, :name, :file, :ip, :size);");
			$logFile->execute([
				":messageId" => $messageId,
				":type"      => $_FILES['file']['type'][$i],
				":name"      => $_FILES['file']['name'][$i],
				":file"      => $fileName,
				":ip"        => $_SERVER['REMOTE_ADDR'],
				":size"      => $_FILES['file']['size'][$i],
			]);

			// move to public directory (we should isolate the file or maybe manually send to client)
			move_uploaded_file($_FILES['file']['tmp_name'][$i], PUBDIR . "/uploads/" . $fileName);
		} catch (\Exception $ex) {
			// ...
		}
	}
}
