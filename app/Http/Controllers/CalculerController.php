<?php
namespace App\Http\Controllers;

use App\Models\BaremeIR;
use App\Models\Bulletin;
use App\Models\Heure_sup;
use App\Models\Prime;
use App\Models\Salaire;
use Illuminate\Http\Request;
use App\Models\Salarier;
use App\Models\Tcotis;
use Carbon\Carbon;
use Carbon\Month;
use Exception;
use Illuminate\Support\Facades\Log;

class CalculerController extends Controller
{
    public function index(){
        $salarier = Salarier::with('prime')->get();
        return view('salarier', compact('salarier'));
    }
    public function bulletin($id)
{
    $test = Tcotis::all();
    $employee = Salarier::find($id);

    if (!$employee) {
        return redirect()->route('salarier')->with('error', 'Bulletin not found');
    }

    $p = Prime::where('salarier_id', $employee->id)->get();
    $salary = Salaire::where('salarier_id', $employee->id)->where('mois', date('Y-m'))->first();
    $tcotisSal = Tcotis::where('type', 'salarier')->first();
    $bulltinS = Bulletin::where('salarier_id', $employee->id)
        ->whereMonth('created_at', date('m'))
        ->whereYear('created_at', date('Y'))
        ->first();

    $base_salary = $employee->salaire_base;
    $prix_jour = $base_salary / 26;
    $prix_heure = $base_salary / 191;
    $total_heure = $salary ? $salary->nombre_jour * 7.346153846153846 : 0;
    $total_salaireJ = $prix_jour * ($salary ? $salary->nombre_jour : 0);
    $salaire_base = $total_salaireJ;
    $total_salaireH = 0;
    $debug_data = [];
    $total_hours = 0;
    $total_minutes = 0;
    if ($salary) {
        
        foreach ($salary->date_sup as $h) {
            preg_match('/(\d+)h\s*(\d+)m/', $h['diff_hours'], $matches);
            if (count($matches) === 3) {
                $hours = (int)$matches[1];
                $minutes = (int)$matches[2];
                $sup_salary = ($prix_heure * $hours) + ($prix_heure * ($minutes / 60));
                $total_salaireH += $sup_salary;
                $total_salaireH += $sup_salary * ($h['pcentage'] / 100);
                $salaire_base = $total_salaireJ + $total_salaireH;
                $debug_data[] = [
                    'total_salaireH' => $total_salaireH,
                    'total_salaireJ' => $total_salaireJ,
                    'hours' => $hours,
                    'minutes' => $minutes,
                    'prix_heure' => $prix_heure,
                    'salaire_base' => $salaire_base
                ];
                $total_hours += $hours;
                $total_minutes += $minutes;
            }
            
        }
    }
    // dd($salaire_base, $total_hours,'h',$hours,'m' ,$minutes, $total_minutes,$total_salaireH,$total_salaireJ,$prix_heure,$sup_salary);

    $total_brut_salary = $salaire_base;
    // dd($debug_data);

    foreach ($p as $prime) {
        if ($prime->type != 'bonus') {
            $total_brut_salary += $prime->prime;
        }
    }

    $total_deductions = 0;
    if ($total_brut_salary > 6000) {
        $cnss = 6000 * $tcotisSal->cnss;
        $deductions = $total_brut_salary * ($tcotisSal->Presfamil + $tcotisSal->Taxpro + $tcotisSal->AMOoblSol + $tcotisSal->Pension + $tcotisSal->amo);
        $total_deductions = $deductions + $cnss;
    } else {
        $total_deductions = ($tcotisSal->Presfamil + $tcotisSal->Taxpro + $tcotisSal->AMOoblSol + $tcotisSal->Pension + $tcotisSal->amo + $tcotisSal->cnss) * $total_brut_salary;
    }

    $total_net_salary = $total_brut_salary - $total_deductions;

    $tcotisPat = Tcotis::where('type', 'employeur')->first();
    $total_deductions_pat = 0;

    if ($total_brut_salary > 6000) {
        $cnssp = 6000 * $tcotisPat->cnss;
        $deductions_pat = $total_brut_salary * ($tcotisPat->Presfamil + $tcotisPat->Taxpro + $tcotisPat->AMOoblSol + $tcotisPat->Pension + $tcotisPat->amo);
        $total_deductions_pat = $deductions_pat + $cnssp;
    } else {
        $total_deductions_pat = ($tcotisPat->Presfamil + $tcotisPat->Taxpro + $tcotisPat->AMOoblSol + $tcotisPat->Pension + $tcotisPat->amo + $tcotisPat->cnss) * $total_brut_salary;
    }

    $IR = $total_net_salary > 6500 ? $total_net_salary * 0.25 : $total_net_salary * 0.35;
    $i = $total_net_salary - $IR;
    $bareme = BaremeIR::whereRaw('? BETWEEN tranche_min AND tranche_max', [$i])->first();
    $IR = ($i * $bareme->taux) / 100;
    $tx = $IR ? $IR / $i * 100 : 0;
    $total_net_salary -= $IR;
    $total_net_salary += $p->where('type', 'bonus')->sum('prime');

    if (!$bulltinS) {
        $bulltin = new Bulletin();
        $bulltin->salarier_id = $employee->id;
        $bulltin->salaire_base = $salaire_base;
        $bulltin->total_brut_salary = $total_brut_salary;
        $bulltin->total_net_salary = $total_net_salary;
        $bulltin->total_heure = $total_heure;
        $bulltin->heure_sup = $total_hours + $total_minutes * 0.01;
        $bulltin->tcotisSalarier = $total_deductions;
        $bulltin->import_revenu = $IR;
        $bulltin->tcotisPatron = $total_deductions_pat;
        // dd($bulltin);
        $bulltin->save();
    } else {
        return view('search', compact('employee', 'salary'));
    }

    return view('bulletin', compact('p', 'total_deductions', 'prix_heure', 'tx', 'minutes', 'hours', 'IR', 'total_deductions_pat', 'salary', 'salaire_base', 'total_heure', 'employee', 'total_net_salary', 'tcotisPat', 'tcotisSal', 'total_brut_salary'));
}

    public function prime(){
        $salaries = Salarier::with('prime')->get();
        return view('prime.index', compact('salaries'));
    }
    public function edit(string $prime_id){
        $prime = Prime::where('prime_id',$prime_id)->first();
        if(!$prime){
            return redirect()->route('prime.index')->with('error','Prime not found');
        }
        $types = Prime::$types;
        return view('prime.edit', compact('prime','types'));
    }
    public function update(Request $request, string $prime_id){
        $request->validate([
            'prime' => 'required|numeric',
            'description' => 'required',
            'type' => 'required'
        ]);
        $prime = Prime::where('prime_id', $prime_id)->first();
        $prime->prime = $request->prime;
        $prime->salarier_id = $prime->salarier_id;
        $prime->description = $request->description;
        $prime->type = $request->type;
        $prime->save();
        return redirect()->route('prime.index');
    }
    public function create(){
        $salarier = Salarier::all();
        $type = Prime::$types;
        return view('prime.create', compact('salarier','type'));
    }
    public function store(Request $request){
        $request->validate([
            'prime' => 'required',
            'description' => 'required',
            'mois' => 'required',
            'salarier_id' => 'required',
            'type' => 'required'
        ]);
        // if(){
            
        // }
        $prime = new Prime();
        $prime->prime = $request->prime;
        $prime->salarier_id = $request->salarier_id;
        $prime->mois = $request->mois;
        $prime->description = $request->description;
        $prime->type = $request->type;
        $prime->save();
        return redirect()->route('prime.index');
    }
    public function destroy(string $prime_id){
        $prime = Prime::where('prime_id', $prime_id)->first();
        $prime->delete();
        return redirect()->back();
        }
        public function search(Request $request)
        {
            $search = $request->input('search');
    
            $salarier = Salarier::where('nom', 'like', '%' . $search . '%')
                      ->orWhere('prenom', 'like', '%' . $search . '%')
                      ->orWhere('tel', 'like', '%' . $search . '%')
                      ->orWhere('emploi', 'like', '%' . $search . '%')
            ->get();
            // dd($salaries);
            return view('salarier', compact('salarier'));
        }
        public function createSalarier(){
            $salarier = Salarier::$emplois;
            return view('create', compact('salarier'));
        }
        public function storeSalarier(Request $request){
            $request->validate([
                'nom' => 'required',
                'prenom' => 'required',
                'tel' => 'required|numeric',
                'salaire_base' => 'required|numeric',
                'emploi' => 'required',
                'qualification' => 'required',
                'date_emboche' => 'required',

                ]);
                $salarier = new Salarier();
                $salarier->nom = $request->nom;
                $salarier->prenom = $request->prenom;
                $salarier->tel = $request->tel;
                $salarier->salaire_base = $request->salaire_base;
                $salarier->emploi = $request->emploi;
                $salarier->qualification = $request->qualification;
                $salarier->date_emboche = $request->date_emboche;
                $salarier->save();
                return redirect()->route('salarier');
        }
        public function destroySalarier(string $id){
            $salarier = Salarier::where('id', $id)->first();
            $salarier->delete();
            return redirect()->back();
        }
        public function salaire(string $id) {
            $salaireExists = Salaire::where('salarier_id', $id)
                                     ->whereMonth('created_at', date('m'))
                                     ->whereYear('created_at', date('Y'))
                                     ->first();
            if ($salaireExists) {
                return redirect()->route('bulletinP', ['id' => $id]);
            } else {
                $salarier = Salarier::findOrFail($id);
                if ($salarier) {
                    return view('salaire.create', compact('salarier'));
                } else {
                    return redirect()->route('salarier'); 
                }
            }
        }
        
        
        public function storesalaire(Request $request) {
            $request->validate([
                'mois' => 'required|date_format:Y-m', 
                'nombre_jour' => 'required|numeric|max:26',
                'salarier_id' => 'required',
            ]);
            $salaireExist = Salaire::where('salarier_id', $request->salarier_id)
                           ->where('mois', $request->mois)
                           ->first();

                if ($salaireExist) {
                    return redirect()->back()->withErrors(['error' => 'Le salaire pour ce mois existe déjà pour ce salarié.']);
                }
            $dates = [];
            if (is_array($request->date_debut) && is_array($request->date_fin) && is_array($request->multiplier)) {
                $count = min(count($request->date_debut), count($request->date_fin), count($request->multiplier));
                for ($i = 0; $i < $count; $i++) {
                    try {
                        $dateDebut = Carbon::parse($request->date_debut[$i]);
                        $dateFin = Carbon::parse($request->date_fin[$i]);
                        $diffInMinutes = $dateDebut->diffInMinutes($dateFin);
                        $hours = intdiv($diffInMinutes, 60);
                        $minutes = $diffInMinutes % 60;
                        $dates[] = [
                            'date_debut' => $request->date_debut[$i],
                            'date_fin' => $request->date_fin[$i],
                            'diff_hours' => "{$hours}h {$minutes}m",
                            'pcentage' => $request->multiplier[$i]
                        ];
                    } catch (Exception $e) {
                        Log::error("Error processing dates: " . $e->getMessage());
                    }
                }
            } else {
                $dates = [];
            }

                $salaire = new Salaire();
                $salaire->salarier_id = $request->salarier_id;
                $salaire->mois = $request->mois;
                $salaire->nombre_jour = $request->nombre_jour;
                $salaire->date_sup = $dates;
                $salaire->save();
                return redirect()->route('bulletinP', ['id' => $request->salarier_id]);
        
            
            }
            public function searchBulletin(Request $request)
            {
                $bulletin = Bulletin::with('salarier')->where('salarier_id', $request->salarier_id)
                ->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->first();
                $salary = Salaire::where('mois', $request->mois)->where('salarier_id', $bulletin->salarier_id)
                ->first();
                if(!$salary){
                    return back()->with('error', 'bulletin not found !!');
                }
                $p = Prime::where('salarier_id', $bulletin->salarier_id)
                ->where('mois', $request->mois)->get();
                $tcotisSal = Tcotis::where('type', 'salarier')->first();
                $tcotisPat = Tcotis::where('type', 'employeur')->first();
                $total_heure =  $salary->nombre_jour * 7.346153846153846;
                $prix_heure = $bulletin->salarier->salaire_base / 191;
                $total_net_salary = $bulletin->total_brut_salary - $bulletin->tcotisSalarier;
                if($total_net_salary > 6500){
                    $IR =  $total_net_salary * 25 / 100;
                }
                else{
                    $IR =  $total_net_salary * 35 / 100;
                }
                $i = $total_net_salary - $IR;
                
                $bareme = BaremeIR::whereRaw('? BETWEEN tranche_min AND tranche_max', [$i])->first();
                $IR = ($i * $bareme->taux) / 100;
                $tx = $IR ? $IR / $i * 100 : 0;
                $total_net_salary = $total_net_salary - $IR;
                return view('bulletinSalarier', compact('bulletin','salary','IR','tx','prix_heure','p','total_heure','tcotisSal','tcotisPat'));
            }
}
