<?php
require_once('jsonserver/JSONServer.php');
require_once('model/JSONInterface.php');

$s = new JsonServer();
$s->addInterface('contact', new JSONInterface());
$s->handle();

?>