<?php
namespace App\Http\Controllers;

use App\Models\BaremeIR;
use App\Models\Bulletin;
use App\Models\Prime;
use App\Models\Salaire;
use Illuminate\Http\Request;
use App\Models\Salarier;
use App\Models\Tcotis;
use Carbon\Month;

class CalculerController extends Controller
{
    public function index(){
        $salarier = Salarier::with('prime')->get();
        return view('salarier', compact('salarier'));
    }
    public function bulletin($id)
    {   
        $test = Tcotis::all();
        $employee = Salarier::where('id',$id)->first();
        if(!$employee){
            return redirect()->route('salarier')->with('error','bulletin not found');
        }
        $p = Prime::where('salarier_id', $employee->id)->get();
        $salary = Salaire::where('salarier_id', $employee->id)->first();
        $tcotisSal = Tcotis::where('type', 'salarier')->first();
        $bulltinS = Bulletin::where('salarier_id', $employee->id)->first(); 
        $base_salary = $employee->salaire_base;
        $prix_jour = $base_salary / 26;
        $prix_heure = $base_salary / 191;
        $total_heure =  $salary->nombre_jour * 7.346153846153846; 
        // dd($total_heure);
        $total_salaireH = 0 ;
        $total_salaireJ = 0 ;
        $total_salaireJ = $prix_jour * $salary->nombre_jour;
        $total_salaireH = ($prix_heure * $salary->heures_supplementaires * 25 / 100) + $prix_heure * $salary->heures_supplementaires;
        // dd($total_salaireH);
        $salaire_base = $total_salaireJ + $total_salaireH;







        // dd($salaire_base);

        $total_brut_salary = $salaire_base;
        foreach($p as $item){
            if($item){
                $total_brut_salary = $salaire_base + $p->where('type', '!=', 'bonus')->sum('prime');
            }
            else{
                $item->prime = 0;
                $total_brut_salary = $salaire_base + $item->prime;
            }
        }
        $tot = 0;
        // $toto = 0;
        if($total_brut_salary > 6000){
            $cnss = 6000 * $tcotisSal->cnss;
            $a = $total_brut_salary * ($tcotisSal->Presfamil+$tcotisSal->Taxpro+$tcotisSal->AMOoblSol+$tcotisSal->Pension+$tcotisSal->amo);
            $tot = $a+$cnss;
            $total_net_salary = $total_brut_salary - $tot;
        }
        else{
            $tot = ($tcotisSal->Presfamil+$tcotisSal->Taxpro+$tcotisSal->AMOoblSol+$tcotisSal->Pension+$tcotisSal->amo+$tcotisSal->cnss)*$total_brut_salary;
            $total_net_salary = $total_brut_salary - $tot;
        }
        // p
        $tcotisPat = Tcotis::where('type', 'employeur')->first();
        if($total_brut_salary > 6000){
            $cnssp = 6000 * $tcotisPat->cnss;
            $b = $total_brut_salary * ($tcotisPat->Presfamil+$tcotisPat->Taxpro+$tcotisPat->AMOoblSol+$tcotisPat->Pension+$tcotisPat->amo);
            $totop = $b+$cnssp;
        }
        else{
            $totop = ($tcotisPat->Presfamil+$tcotisPat->Taxpro+$tcotisPat->AMOoblSol+$tcotisPat->Pension+$tcotisPat->amo+$tcotisPat->cnss)*$total_brut_salary;
        }
        // dd($total_net_salary);
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
                $total_net_salary = $total_net_salary + $p->where('type', '=', 'bonus')->sum('prime');
        if(!$bulltinS){
            $bulltin = new Bulletin();
            $bulltin->salarier_id = $employee->id;
            $bulltin->salaire_base = $salaire_base;
            $bulltin->total_brut_salary = $total_brut_salary;
            $bulltin->total_net_salary = $total_net_salary ;
            $bulltin->total_heure = $total_heure + $salary->heures_supplementaires;
            $bulltin->tcotisSalarier = $tot;
            $bulltin->import_revenu = $IR;
            $bulltin->tcotisPatron = $totop;
            $bulltin->save();
        }
        else{
            return view('search', compact('employee','salary'));
        }
        return view('bulletin', compact('p','totop','prix_heure','tx','IR','tot','salary','salaire_base','total_heure','employee','total_net_salary','tcotisPat','tcotisSal','total_brut_salary'));
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
                'heures_supplementaires' => 'required|numeric',
                'salarier_id' => 'required', 
            ]);
                $salaire = new Salaire();
                $salaire->salarier_id = $request->salarier_id;
                $salaire->mois = $request->mois;
                $salaire->nombre_jour = $request->nombre_jour;
                $salaire->heures_supplementaires = $request->heures_supplementaires;
                $salaire->save();
                return redirect()->route('bulletinP', ['id' => $request->salarier_id]);
        
            
            }
            public function searchBulletin(Request $request)
            {
                $bulletin = Bulletin::with('salarier')->where('salarier_id', $request->salarier_id)->first();
                $salary = Salaire::where('mois', $request->mois)
                ->where('salarier_id', $bulletin->salarier_id)
                ->first();
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
                // $total_net_salary = $total_net_salary - $IR;
                // dd($tx);
                $baseRate = 100; // Le tarif de base par heure
                $date = $request->input('date');
                $startTime = $request->input('start_time');
                $endTime = $request->input('end_time');

                $hourlyRate = $this->calculateHourlyRate($baseRate, $date, $startTime, $endTime);
                $totalHours = $this->calculateTotalHours($date, $startTime, $endTime);
                $totalPay = $this->calculateTotalPay($hourlyRate, $totalHours);
                $totalPay = $totalPay - $bulletin->tcotisSalarier;
                dd($totalPay);

                return view('bulletinSalarier', compact('bulletin','salary','IR','tx','prix_heure','p','total_heure','tcotisSal','tcotisPat'));
            }
            
}
