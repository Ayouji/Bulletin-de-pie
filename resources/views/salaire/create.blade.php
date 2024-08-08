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
            border: 1px solid #aba9a9;
            border-radius: 4px;
        }
        .payslip input[type="radio"] {
            width: auto;
            margin-right: 10px;
        }
        .payslip button {
            width: 70%;
            color: #278be8;
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
        <form action="{{ route('salaire.store') }}" method="POST">
            @csrf
            <h1>Create Salaire</h1>
            <label for="salarier">Nom et Prenom:</label>
            <input type="text" value="{{ $salarier->id }}" name="salarier_id" hidden>
            <input type="text" name="nom" value="{{ $salarier->nom }} {{ $salarier->prenom }}" disabled>
            <label>Mois:</label>
            <input type="month" name="mois">
            <label>Nombre_jours:</label>
            <input type="number" name="nombre_jour">
            <label>Heure Suplimentaire:</label>
            <div id="date-fields">
                <div class="sup" data-index="0">
                    <label>Date de debut:</label>
                    <input type="datetime-local" class="form-control" name="date_debut[]" required>
                    <label>Date de fin:</label>
                    <input type="datetime-local" class="form-control" name="date_fin[]" required>
                    <label>Pourcentage:</label>
                    <input type="radio" name="multiplier[0]" value="25" required> 25%
                    <input type="radio" name="multiplier[0]" value="50" required> 50%
                    <input type="radio" name="multiplier[0]" value="100" required> 100%
                </div>
            </div>
            <button type="button" onclick="delDateField()">(-)</button>
            <button type="button" onclick="addDateField()">(+)</button>
            <button type="submit">Create</button>
        </form>
    </div>
    <script>
        let fieldCounter = 0; 
        function addDateField() {
            var dateFields = document.getElementById('date-fields');
            dateFields.hidden = false;

            var div = document.createElement('div');
            div.className = 'sup';
            div.setAttribute('data-index', fieldCounter);

            var labelDebut = document.createElement('label');
            labelDebut.textContent = 'Date de début:';
            div.appendChild(labelDebut);

            var inputDebut = document.createElement('input');
            inputDebut.type = 'datetime-local';
            inputDebut.name = 'date_debut[]';
            inputDebut.required = true;
            div.appendChild(inputDebut);

            var labelFin = document.createElement('label');
            labelFin.textContent = 'Date de fin:';
            div.appendChild(labelFin);

            var inputFin = document.createElement('input');
            inputFin.type = 'datetime-local';
            inputFin.name = 'date_fin[]';
            inputFin.required = true;
            div.appendChild(inputFin);

            var labelPourcentage = document.createElement('label');
            labelPourcentage.textContent = 'Pourcentage:';
            div.appendChild(labelPourcentage);

            var radioValues = [25, 50, 100];
            radioValues.forEach(function(value) {
                var inputRad = document.createElement('input');
                inputRad.type = 'radio';
                inputRad.name = `multiplier[${fieldCounter}]`;
                inputRad.value = value;
                inputRad.required = true;
                div.appendChild(inputRad);
                div.appendChild(document.createTextNode(value + '%'));
            });

            dateFields.appendChild(div);
            fieldCounter++;
        }

        function delDateField() {
            var dateFields = document.getElementById('date-fields');
            var lastField = dateFields.lastElementChild;
            if (lastField) {
                dateFields.removeChild(lastField);
                if (dateFields.children.length === 0) {
                    dateFields.hidden = true;
                }
            }
        }
    </script>    
</body>
</html>
@endsection