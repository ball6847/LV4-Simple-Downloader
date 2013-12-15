@extends('site.layouts.default')

@section('title')
    Add Downlad Source
@stop  

{{-- Content --}}
@section('content')
    
    
    <div class="page-header">
        <h1>Add Downlad Source</h1>
    </div>
    
    <div class="col-md-8">
        <div class="row">
            <div class="well">
                <form class="form-horizontal" method="POST" action="{{ URL::route('download.add.post') }}" accept-charset="UTF-8">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <fieldset>
                        
                        <legend>Download</legend>
                        
                        @if ( Session::get('error') )
                            <div class="alert alert-danger">{{ Session::get('error') }}</div>
                        @endif
                            
                        <div class="form-group">
                            <label class="col-md-2 control-label" for="url">URL</label>
                            <div class="col-md-10">
                                <input class="form-control" tabindex="1" placeholder="URL" type="text" name="url" id="url" value="{{ Input::old('url') }}">
                            </div>
                        </div>
                
                        @if ( Session::get('notice') )
                            <div class="alert">{{ Session::get('notice') }}</div>
                        @endif
                
                        <div class="form-group" style="margin-bottom: 0">
                            <div class="col-md-offset-2 col-md-10">
                                <button class="btn btn-default">Cancel</button>
                                <button tabindex="3" type="submit" class="btn btn-primary">Start !</button>
                            </div>
                        </div>
                    </fieldset>
                </form>    
            </div>
        </div>
    </div>    

@stop