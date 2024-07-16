@extends('layouts.app')

@section('title', 'Titre de la Page')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de Paie</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;
            
        }
        .payslip {
            width: 600px;
            background: #fff;
            margin: auto;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .payslip h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }
        .payslip label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .payslip input , 
        .payslip select,
        [type="text"], .payslip input[type="radio"], .payslip button {
            width: calc(100% - 20px);
            margin-bottom: 5px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .payslip input[type="radio"] {
            width: auto;
            margin-right: 5px;
        }
        .payslip button {
            width: 70%
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="payslip">
        <form action="{{ route('prime.update', $prime->prime_id) }}" method="POST">
            @csrf
            @method('PUT')
            <h1>Update Prime</h1>
            <label for="salarier">Salarier :</label>
            <input type="text" name="salarier" id="salarier" value="{{ $prime->salarier->nom }} {{ $prime->salarier->prenom }}" disabled>
            
            <label for="prime">Prime :</label>
            <input type="text" name="prime" id="prime" value="{{ $prime->prime }}">
            <label >Mois :</label>
            <input type="month" name="mois" value="{{ $prime->mois }}">
            <label for="description">Description :</label>
            <input type="text" name="description" id="description" value="{{ $prime->description }}">
            
            <label for="type">Type :</label>
            @foreach($types as $item)
                <label for="type_fix">{{$item}}</label>
                <input type="radio" name="type" id="type_fix" value="{{$item}}" {{ $item === $prime->type ? 'checked' : '' }}>
            @endforeach
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
@endsection