@extends('layouts.app')

@section('css')
    @isset($css)
        @foreach($css as $c)
            <link href="{{ asset('css/'.$c.'.css') }}" rel="stylesheet" type="text/css" />
        @endforeach
    @endisset
@endsection

@section('content')
    <student-index></student-index>
@endsection

@section('footer')
    @isset($js)
        @foreach($js as $j)
            <script src="{{ asset('js/'.$j.'.js') }}"></script>
        @endforeach
    @endisset
@endsection
