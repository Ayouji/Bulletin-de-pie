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
        .payslip {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .payslip h1 {
            text-align: center;
        }
        .payslip table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .payslip table, .payslip th, .payslip td {
            border: 1px solid #ddd;
        }
        .payslip th, .payslip td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="payslip">
        <h1>Prime Salarier</h1>
        <a type="button" href="{{ route('prime.create') }}">Create</a>
        <table>
            <tr>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Primes</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            @foreach($salaries as $s)
                <tr>
                    <td>{{ $s->nom }}</td>
                    <td>{{ $s->prenom }}</td>
                    <td>
                        <select>
                            <option hidden>Prime et Type</option>
                            @forelse($s->prime as $b)
                                <option value="{{ $b->prime_id }}">{{ $b->prime }} - {{ $b->type }}</option>
                            @empty
                                <option value="" hidden> Aucun Prime</option>
                            @endforelse
                        </select>
                    </td>
                    <td>
                        <select>
                            <option hidden>Description</option>
                            @forelse($s->prime as $b)
                                <option value="{{ $b->prime_id }}">{{ $b->description }}</option>
                            @empty
                                <option value="" hidden> Aucun Description</option>
                            @endforelse
                        </select>
                    </td>
                    <td>
                        @if(isset($b))
                            <a href="{{ route('prime.edit', $b->prime_id) }}">Update</a>
                            <a href="{{ route('prime.destroy', $b->prime_id) }}">Delete</a>
                        @else
                            <span>N/A</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
@endsection