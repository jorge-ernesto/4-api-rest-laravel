<h1>{{$titulo}}</h1>
<ul>
    @foreach($dataAnimales as $key=>$value)
        <li>{{$dataAnimales[$key]}}</li>
    @endforeach
</ul>
