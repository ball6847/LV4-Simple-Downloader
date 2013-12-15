<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class StartDownload extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'download:start';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Start download a given downloader id.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$id = $this->argument('id');
		
		$download = Download::find($id);

		if (is_null($download))
		{
			return $this->error('Not found the given download.');
		}
		
		if ( ! $download->startDownload())
		{
			return $this->error($download->status());
		}
	}
	
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('id', InputArgument::REQUIRED, 'ID of file you want to download.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}