<?php
use \Sidney\Latchet\BaseTopic;


class DownloadProgressTopic extends BaseTopic {

	public function subscribe($connection, $topic)
	{

	}

	public function publish($connection, $topic, $message, array $exclude, array $eligible)
	{
		$this->broadcast($topic, array('msg' => 'New broadcasted message!'));
	}

	public function call($connection, $id, $topic, array $params)
	{

	}

	public function unsubscribe($connection, $topic)
	{

	}

}