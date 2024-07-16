@extends('layouts.app')

@section('title', 'Titre de la Page')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de Paie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        .salarier {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
        }
        
        .salarier table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .salarier table, .salarier th, .salarier td {
            border: 1px solid #ddd;
        }
        .salarier th, .salarier td {
            padding: 8px;
            text-align: left;
        }
        input{
            width: calc(100% - 20px);
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
</head>
<body>
    <div class="salarier">
        <h1>Liste Salarier</h1>
        <form action="{{route('salarier.search')}}" method="get">
            <input type="text" name="search" placeholder="Search Salarier">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <table>
            <tr>
                <td>Nom </td>
                <td>Prenom  </td>
                <td>Tel  </td>
                <td>Emploi </td>
                <td>Qualification</td>
                <td>Action</td>
            </tr>
            @forelse($salarier as $item)
            <tr>
                <td>{{$item->nom}}</td>
                <td>{{$item->prenom}}</td>
                <td>{{$item->tel}}</td>
                <td>{{$item->emploi}}</td>
                <td>{{$item->qualification}}</td>
                <td><a href="{{url('/salaire/create',$item->id)}}">bulletin</a></td>
                <td><a href="{{route('salarier.destroy',$item->id)}}">Delete</a></td>
            </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Aucun Salarier Trouver</td>
                </tr>
            @endforelse
        </table>
        <a  type="button" href="{{route('salarier.create')}}">Create Salarier</a>
    </div>
</body>
</html>
@endsection