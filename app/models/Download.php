<?php

use Robbo\Presenter\PresentableInterface;

class Download extends Eloquent implements PresentableInterface {
	protected $guarded = array();

	public static $rules = array();
	
	protected $table = 'downloads';
	
	protected $redis;

	public function name()
	{
		return basename(parse_url($this->url, PHP_URL_PATH));
	}
	
	public function hostname()
	{
		return parse_url($this->url, PHP_URL_HOST);
	}
	
	/**
     * Get the date the post was created.
     *
     * @param \Carbon|null $date
     * @return string
     */
    public function date($date=null)
    {
        if (is_null($date))
		{
            $date = $this->created_at;
        }

        return String::date($date);
    }

	/**
	 * Returns the date of the blog post creation,
	 * on a good and more readable format :)
	 *
	 * @return string
	 */
	public function created_at()
	{
		return $this->date($this->created_at);
	}

	/**
	 * Returns the date of the blog post last update,
	 * on a good and more readable format :)
	 *
	 * @return string
	 */
	public function updated_at()
	{
        return $this->date($this->updated_at);
	}

    public function getPresenter()
    {
        return new PostPresenter($this);
    }
	
	protected function getRedis()
	{
		if ( ! $this->redis)
		{
			$this->redis = Redis::connection();
		}
		
		return $this->redis;
	}
	
	public function key()
	{
		return sprintf('lv4_download_progress_%d', $this->id);
	}
	
	public function progress()
	{
		if ( ! $this->size)
		{
			return 'unknown';
		}
		
		if ($this->status == 3)
		{
			return 100;
		}
		
		$length = $this->getRedis()->get($this->key());
		$progress = ($length / $this->size) * 100;
		return round($progress);
	}
	
	public function startDownload()
	{
		$filepath = public_path() . '/downloads/' . $this->name();
		
		// file already exists
		if (is_file($filepath))
		{
			$this->status = 4;
			$this->save();
			return FALSE;
		}
		
		// create file
		if ( ! $fp = fopen($filepath, 'w+'))
		{
			// cannot create file, permission error
			$this->status = 5;
			$this->save();
			return FALSE;
		}
		
		// start download using curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'curlProgressHandler']);
		curl_setopt($ch, CURLOPT_NOPROGRESS, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		
		if ($result === FALSE)
		{
			// download error
			$this->status = 6;
			$this->save();
		}
		
		// mark as completed
		$this->status = 3; 
		$this->save();
		
		// remove progress key
		$this->getRedis()->del($this->key());
		
		return TRUE;
	}
	
	protected function curlProgressHandler($download_size, $downloaded, $upload_size, $uploaded)
	{
		static $lastProgress, $lastPush;
		
		// set status to downloading
		if ($this->status != 2)
		{
			$this->status = 2;
			$this->save();
		}
		
		// set download length if possible
		if ( ! $this->size AND $download_size)
		{
			$this->size = $download_size;
			$this->save();
		}
		
		$time = time();
		$progress = $this->progress();
		
		// only push when needed
		// cond: progress is 100% completed, force push
		// cond: progress has changed
		// cond: last push is more than 1 second
		if ($progress == 100 OR ($lastProgress < $progress AND ($time - $lastPush) >= 2))
		{
			Latchet::publish('progress', [
				'id' => $this->id,
				'progress' => $progress
			]);
			
			$lastProgress = $progress;
			$lastPush = $time;
		}
		
		$key = $this->key();
		$this->getRedis()->set($key, $downloaded);
	}
	
	public function status()
	{
		switch ($this->status)
		{
			case 1:
				return 'pending';
			case 2:
				return 'downloading';
			case 3:
				return 'completed';
			case 4:
				return 'file already exists';
			case 5:
				return 'cannot create file';
			case 6:
				return 'http error';
		}
	}
}
