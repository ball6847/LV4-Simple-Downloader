<?php

class DownloaderController extends AuthorizedController {
    
    public function getIndex()
    {
        $downloads = Download::all();
        
        return View::make('site/downloader/index', compact('downloads'));
    }
    
    public function getAdd()
    {
        return View::make('site/downloader/add');
    }
    
    public function postAdd()
    {
        $url = Input::get('url');
        
        if ( ! filter_var($url, FILTER_VALIDATE_URL))
        {
            return Redirect::route('download.add')
                ->withInput(Input::all())
                ->with('error', 'Invalid url');
        }
        
        $dl = new Download;
        $dl->url = $url;
        $dl->size = 0;
        $dl->status = 1;
        $dl->save();
        
        putenv('ENVIRONMENT='.getenv('ENVIRONMENT'));
        shell_exec(base_path().'/artisan download:start ' . escapeshellarg($dl->id) . ' > /dev/null 2>&1 &');
        
        return Redirect::route('download.index')->with('success', 'Successfully added new download.');
    }
    
    public function getDel($id)
    {
        $dl = Download::find($id);
        $dl->delete();
        
        return Redirect::to($_SERVER['HTTP_REFERER']);
    }
    
}