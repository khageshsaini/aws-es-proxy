@extends("layouts.app")
@section('content')
<style type="text/css">
        .app-container {
            width: 100%;
            height: calc(100vh - 94px);
            border: none;
            background: white;
        }
    </style>
<iframe src="/_plugin/kibana/" class="app-container" style=""></iframe>
<div class="panel-footer text-right">
    Made with <span style="color: #e25555;">&#9829;</span> at VMock
</div>
@endsection