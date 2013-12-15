@extends('site.layouts.default')

@section('title')
    All downloads
@stop

@section('styles')
#downloads .progress {
    margin-bottom: 0;
    position: relative;
}
#downloads .progress .progress-bar {
    position: absolute;
}
#downloads .progress .progress-label {
    text-align: center;
    position: relative;
    z-index: 1;
    font-size: 12px;
    line-height: 20px;
    color: #333333;
}

@stop

{{-- Content --}}
@section('content')
    
    <div class="page-header">
        <a href="{{ URL::route('download.add') }}" style="float:right" class="btn btn-success">+Add new download</a>
        <h1>All downloads</h1>
    </div>
    
    <table id="downloads" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>File</th>
            <th>From</th>
            <th>Progress</th>
            <th>Status</th>
            <th>Created</th>
            <th style="text-align:center;">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($downloads as $download)
        <tr id="download-{{ $download->id }}">
            <td>{{ $download->name() }}</td>
            <td>{{ $download->hostname() }}</td>
            <td>
                @if (is_numeric($download->progress()))
                    <div class="progress">
                        <div style="width: {{ $download->progress() }}%;" class="progress-bar"></div>
                        <div class="progress-label">{{ $download->progress() }}%</div>
                    </div>
                @else
                    <div class="progress">
                        <div style="width: 0%;" class="progress-bar"></div>
                        <div class="progress-label">unknown</div>
                    </div>
                @endif
            </td>
            <td class="status">
                @if ($download->status == 1)
                    <span class="label label-default">{{ $download->status() }}</span>
                @elseif ($download->status == 2)
                    <span class="label label-warning">{{ $download->status() }}</span>
                @elseif ($download->status == 3)
                    <span class="label label-success">{{ $download->status() }}</span>
                @else
                    <span class="label label-danger">{{ $download->status() }}</span>
                @endif
            </td>
            <td>{{ $download->date() }}</td>
            <td style="text-align:center;">
                <a onclick="return confirm('This cannot be undoned, are you sure ?');"
                    href="{{ URL::route('download.del', ['id' => $download->id]) }}" class="btn btn-danger btn-xs"><i class="fa">&#xf014;</i></a>
            </td>
        </tr>
        @endforeach
    </tbody>
    </table>
    
    @section('script')
        @parent
        <script>
        var pools = {};
        var getEl = function(id){
            if (typeof pools[id] === 'undefined') {
                var $el = $('#download-'+ id);
                pools[id] = {
                    '$bar': $el.find('.progress-bar'),
                    '$label': $el.find('.progress-label'),
                    '$status': $el.find('.status'),
                }
            }
            return pools[id];
        };
        
        conn = new ab.Session(
            'ws://'+ {{ json_encode($_SERVER['SERVER_NAME']) }} +':1111',
            function() { 
                conn.subscribe('progress', function(topic, event) {
                    var el = getEl(event.id),
                        progress = event.progress + '%';
                    
                    el.$bar.width(progress);
                    el.$label.html(progress);
                    
                    // change status to completed
                    if (progress == '100%') {
                        el.$status.html('<span class="label label-success">completed</span>');
                    } else {
                        el.$status.html('<span class="label label-warning">downloading</span>');
                    }
                });
            },
            function() {}, {
                // Additional parameters, we're ignoring the WAMP sub-protocol for older browsers
                'skipSubprotocolCheck': true
            }
        );
        </script>
    @stop

@stop
