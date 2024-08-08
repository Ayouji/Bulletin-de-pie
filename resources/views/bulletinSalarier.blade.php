@extends('layouts.app')

@section('title', 'Bulletin de pie')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .table-container {
            width: 900px;

        }
        th, td {
            text-align: center;
            vertical-align: middle;
            border: 1px solid #606060 !important;
        }
        .custom-header {
            font-weight: bold;
        }
        .custom-border-top {
            border-top: 2px solid #000 !important;
        }
        .custom-border-bottom {
            border-bottom: 2px solid #000 !important;
        }
        .head {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
            width: 1200px;

        }

        .label {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .value {
            text-align: left;
        }
        span{
            text-align: right;
        }
        .print-button{
            /* position: fixed; */
            margin-bottom: 10px;
            margin-left: 77%;
            right: 90%;
        }
    </style>
</head>
<body>
    <div class="container table-container" id="print">
        <table class="table table-bordered">
            <thead>
                <td class="custom-header custom-border-top">Nom et Prenom</td>
                <td class="custom-header custom-border-top">Emploi</td>
                <td class="custom-header custom-border-top">Qualification</td>
                <td class="custom-header custom-border-top">Heure de travail</td>
                <td class="custom-header custom-border-top">Mois de travail</td>
            </thead>
            <tbody>
                <td>
                    <span class="label">{{$bulletin->salarier->nom}} {{$bulletin->salarier->prenom}}</span>
                </td>
                <td>
                    <span class="label">{{$bulletin->salarier->emploi}}</span>
                </td>
                <td>
                    <span class="label">{{$bulletin->salarier->qualification}}</span>
                </td>
                <td>
                    <span class="label">{{number_format($bulletin->total_heure + $bulletin->heure_sup, 2)}}</span>
                </td>
                <td>
                    <span class="label">{{$salary->mois}}</span>
                </td>
            </tbody>
        </table>
    <table class="table table-bordered">
        <thead>
            <tr class="custom-border-bottom">
                <th rowspan="2">Rubriques</th>
                {{-- <th rowspan="2">Nombre</th> --}}
                <th rowspan="2">Base</th>
                <th colspan="3">Part salariale</th>
                <th rowspan="2">Part Employeur</th>
            </tr>
            <tr class="custom-border-bottom">
                <th>Taux</th>
                <th>Gain(+)</th>
                <th>Retenue (-)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                
                <td>Salaire de base mensuel 26 j</td>
                {{-- <td></td> --}}
                <td>{{number_format($total_heure, 2)}} + 
                    {{$bulletin->heure_sup}}</td>
                <td>{{number_format($prix_heure,2)}}</td>
                <td>{{number_format($bulletin->salaire_base,2)}}</td>
                <td></td>
                <td></td>
            </tr>
            @foreach ($p as $item)
            <tr>
                <td>{{$item->description}} ({{$item->type}})</td>
                {{-- <td></td> --}}
                <td></td>
                <td></td>
                <td>{{$item->prime}}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
            <tr class="highlight-yellow custom-header custom-border-top custom-border-bottom">
                <td>Total brut</td>
                <td colspan="5">{{number_format($bulletin->total_brut_salary,2)}}</td>
            </tr>
            <tr>
                <td>Cnss</td>
                {{-- <td></td> --}}
                <td>{{number_format($bulletin->total_brut_salary,2)}}</td>
                <td>{{$tcotisSal->cnss * 100}}%</td>
                <td>{{$tcotisPat->cnss * 100}}%</td>
                <td>{{number_format($tcotisSal->cnss * $bulletin->total_brut_salary,2)}}</td>
                <td>{{number_format($tcotisPat->cnss * $bulletin->total_brut_salary,2)}}</td>
            </tr>
            <tr>
                <td>Pension</td>
                {{-- <td></td> --}}
                <td>{{number_format($bulletin->total_brut_salary,2)}}</td>
                <td>{{$tcotisSal->Pension * 100}}%</td>
                <td>{{$tcotisPat->Pension * 100}}%</td>
                <td>{{number_format($tcotisSal->Pension * $bulletin->total_brut_salary,2)}}</td>
                <td>{{number_format($tcotisPat->Pension * $bulletin->total_brut_salary,2)}}</td>
            </tr>
            <tr>
                <td>Prestations familiales</td>
                {{-- <td></td> --}}
                <td>{{number_format($bulletin->total_brut_salary,2)}}</td>
                <td>{{$tcotisSal->Presfamil * 100}}%</td>
                <td>{{$tcotisPat->Presfamil * 100}}%</td>
                <td>{{number_format($tcotisSal->Presfamil * $bulletin->total_brut_salary,2)}}</td>
                <td>{{number_format($tcotisPat->Presfamil * $bulletin->total_brut_salary,2)}}</td>
            </tr>
            <tr>
                <td>AMO obligatoire - Solidarité</td>
                {{-- <td></td> --}}
                <td>{{number_format($bulletin->total_brut_salary,2)}}</td>  
                <td>{{$tcotisSal->AMOoblSol * 100}}%</td>
                <td>{{$tcotisPat->AMOoblSol * 100}}%</td>
                <td>{{number_format($tcotisSal->AMOoblSol * $bulletin->total_brut_salary,2)}}</td>
                <td>{{number_format($tcotisPat->AMOoblSol * $bulletin->total_brut_salary,2)}}</td>
            </tr>
            <tr>
                <td>Taxe de formation professionnelle</td>
                {{-- <td></td> --}}
                <td>{{number_format($bulletin->total_brut_salary,2)}}</td>
                <td>{{$tcotisSal->Taxpro * 100}}%</td>
                <td>{{$tcotisPat->Taxpro * 100}}%</td>
                <td>{{number_format($tcotisSal->Taxpro * $bulletin->total_brut_salary,2)}}</td>
                <td>{{number_format($tcotisPat->Taxpro * $bulletin->total_brut_salary,2)}}</td>
            </tr>
            <tr>
                <td>Amo</td>
                {{-- <td></td> --}}
                <td>{{number_format($bulletin->total_brut_salary,2)}}</td>
                <td>{{$tcotisSal->amo * 100}}%</td>
                <td>{{$tcotisPat->amo * 100}}%</td>
                <td>{{number_format($tcotisSal->amo * $bulletin->total_brut_salary,2)}}</td>
                <td>{{number_format($tcotisPat->amo * $bulletin->total_brut_salary,2)}}</td>
            </tr>
            <tr class="highlight-purple custom-header custom-border-top custom-border-bottom">
                <td>Total des cotisations et contributions</td>
                <td colspan="4">{{number_format($bulletin->tcotisSalarier,2)}}</td>
                <td>{{number_format($bulletin->tcotisPatron,2)}}</td>
            </tr>
            <tr class="highlight-purple custom-header custom-border-top custom-border-bottom">
                <td rowspan="2">IMPOT SUR REVENU</td>
                <td>Base</td>
                <td>Taux</td>
                <td></td>
                <td>Montant (IR)</td>
                <td></td>
            </tr>
            <tr>
                <td>{{number_format($bulletin->total_brut_salary - $bulletin->tcotisSalarier,2)}}</td>
                <td>{{number_format($tx,2)}}%</td>
                <td></td>
                <td>{{number_format($bulletin->import_revenu,2)}}</td>
                <td></td>
            </tr>
            @if ($p->where('type', '=', 'bonus')->isNotEmpty())
            <tr class="">
                <td>MONTANT NET</td>
                <td colspan="5">{{number_format($bulletin->total_brut_salary - $bulletin->tcotisSalarier - $bulletin->import_revenu,2)}} DH</td>
            </tr>
                <tr class="highlight-purple custom-header custom-border-top custom-border-bottom">
                    <td>Total Primes</td>
                    <td colspan="5">+ {{ number_format($p->where('type', '=', 'bonus')->sum('prime'), 2) }}</td>
                </tr>
            @endif

            <tr class="highlight-pink custom-header custom-border-top custom-border-bottom">
                <td>MONTANT NET SOCIAL</td>
                <td colspan="5">{{number_format($bulletin->total_net_salary,2)}} DH</td>
            </tr>
            {{-- <tr class="highlight-green custom-header custom-border-top custom-border-bottom">
                <td>NET A PAYER AVANT IMPOT SUR LE REVENU</td>
                <td colspan="6"></td>
            </tr> --}}
        </tbody>
    </table>
</div>
<button class="btn btn-primary print-button" onclick="printDiv('print')">Imprimer</button>
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
</body>
</html>
@endsection
