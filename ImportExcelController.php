<?php

namespace App\Http\Controllers;

use App\Ventas;
use App\GoalAgencies;
use App\GoalDay;
use App\JsonReportAgencies;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportExcelController extends Controller
{


    public function importExport()
    {
        return view('importExport');
    }
    public function importExportAgencies()
    {
        return view('reports.importexportagencies');
    }
    public function viewReportAgencies()
    {
        return view('reports.reportagencies');
    }

    public function importExcel(Request $request)
    {
        if($request->hasFile('import_file')){
            if($request->type=="1")
            {

                $json = "{";
                $mensaje = "";
                $path = $request->file('import_file')->getRealPath();
                $data = \Excel::load($path)->get();
                if ($data->count()) {

                    foreach ($data as $keydata => $valuedata) {

                        $json          = "{";
                        $jsoninter     = "{";
                        $keyinter      = 0;
                        $terminalinter = "";

                        foreach ($valuedata as $key => $value) {

                            $date     = $valuedata[0]->numero_empleado;
                            $terminal = $valuedata[1]->numero_empleado;

                            if ($terminal != "NACIONAL") {
                                if ($key >= 3) {

                                    $json .= '"' . ($key - 3) . '"' . ':{';
                                    $json .= '"numero_empleado' . '"' . ':"' . $value->numero_empleado . '"';
                                    $json .= ',"nombre":' . '"' . $value->nombre . '"';
                                    $json .= ',"ventaow":"' . round($value->ventaow) . '"';
                                    $json .= ',"metaow":"' . round($value->metaow) . '"';
                                    $json .= ',"ventart":"' . round($value->ventart) . '"';
                                    $json .= ',"metart":"' . round($value->metart) . '"';
                                    $json .= ',"alcanceow":"' . (round($value->alcanceow * 100)) . '"';
                                    $json .= ',"alcancert":"' . (round($value->alcancert * 100)) . '"';
                                    $json .= ',"owsiniva":"' . round($value->owsiniva) . '"';
                                    $json .= ',"rtsiniva":"' . round($value->rtsiniva) . '"';
                                    $json .= ',"dllsow":"' . round($value->dllsow) . '"';
                                    $json .= ',"dllsrt":"' . round($value->dllsrt) . '"';


                                    if ($value->nombre == "TOTALES") {
                                        $json .= "}";
                                        //aca se guardara en la base de datos

                                    } else {
                                        $json .= "},";
                                    }

                                }
                            } else {
                                $date     = $valuedata[0]->numero_empleado;
                                $terminal = "T2_" . $valuedata[1]->numero_empleado;
                                $terminalinter = "T2_" . $valuedata[$keyinter + 1]->numero_empleado;
                                

                                if ($key >= 3 && $value->nombre!="" && $terminalinter!="T2_INTERNACIONAL") {

                                        if($value->nombre!="")
                                        {

                                        $json .= '"' . ($key - 3) . '"' . ':{';
                                        $json .= '"numero_empleado' . '"' . ':"' . $value->numero_empleado . '"';
                                        $json .= ',"nombre":' . '"' . $value->nombre . '"';
                                        $json .= ',"ventaow":"' . round($value->ventaow) . '"';
                                        $json .= ',"metaow":"' . round($value->metaow) . '"';
                                        $json .= ',"ventart":"' . round($value->ventart) . '"';
                                        $json .= ',"metart":"' . round($value->metart) . '"';
                                        $json .= ',"alcanceow":"' . (round($value->alcanceow * 100)) . '"';
                                        $json .= ',"alcancert":"' . (round($value->alcancert * 100)) . '"';
                                        $json .= ',"owsiniva":"' . round($value->owsiniva) . '"';
                                        $json .= ',"rtsiniva":"' . round($value->rtsiniva) . '"';
                                        $json .= ',"dllsow":"' . round($value->dllsow) . '"';
                                        $json .= ',"dllsrt":"' . round($value->dllsrt) . '"';


                                        if ($value->nombre == "TOTALES") {
                                            $json .= "}";
                                            $keyinter = $key + 2;
                                        }else {
                                            $json .= "},";
                                        }
                                    }

                                }

                                if ($key >= $keyinter && $keyinter != 0) {

                                    $dateinter     = $valuedata[$keyinter]->numero_empleado;
                                    

                                    if ($key > ($keyinter + 2)) {
                                        if($value->nombre!="")
                                    {

                                        $jsoninter .= '"' . ($key - ($keyinter +3)) . '"' . ':{';
                                        $jsoninter .= '"numero_empleado' . '"' . ':"' . $value->numero_empleado . '"';
                                        $jsoninter .= ',"nombre":' . '"' . $value->nombre . '"';
                                        $jsoninter .= ',"ventaow":"' . round($value->ventaow) . '"';
                                        $jsoninter .= ',"metaow":"' . round($value->metaow) . '"';
                                        $jsoninter .= ',"ventart":"' . round($value->ventart) . '"';
                                        $jsoninter .= ',"metart":"' . round($value->metart) . '"';
                                        $jsoninter .= ',"alcanceow":"' . (round($value->alcanceow * 100)) . '"';
                                        $jsoninter .= ',"alcancert":"' . (round($value->alcancert * 100)) . '"';
                                        $jsoninter .= ',"owsiniva":"' . round($value->owsiniva) . '"';
                                        $jsoninter .= ',"rtsiniva":"' . round($value->rtsiniva) . '"';
                                        $jsoninter .= ',"dllsow":"' . round($value->dllsow) . '"';
                                        $jsoninter .= ',"dllsrt":"' . round($value->dllsrt) . '"';
                                    }
                                    }

                                    if ($value->nombre == "TOTALES") {
                                        $jsoninter .= "}";
                                        //aca se guardara en la base de datos

                                    } else {
                                        if($key > ($keyinter + 2))
                                        {
                                            $jsoninter .= "},";
                                        }
                                    }

                                }

                            }

                        }
                        $json .= "}";
                        $jsoninter .= "}";


                            $date=(date("Y")."-".$request->fecha);
                            $dateupdate=date("Y-m-d H:i:s");
                            
                            $update=ImportExcelController::update_datejson($date, $terminal);
                            if($update )
                            {
                                $update->json       = $json;
                                $update->updated_at = $dateupdate;
                                $update->save();                        

                            }else
                            {
                                $venta             = new Ventas();
                                $venta->terminal   = $terminal;
                                $venta->date       = $date;
                                $venta->json       = $json;
                                $venta->updated_at = $dateupdate;
                                $venta->type       = 1;
                                $ventasave         = new Ventas();
                                $ventasave->save_json($venta);
                            }

                        if ($terminalinter != "" || $terminalinter != null) {

                            $date=(date("Y")."-".$request->fecha);
                            $update=ImportExcelController::update_datejson($date, $terminalinter);
                            if($update)
                            {
                                $update->json     = $jsoninter;
                                $update->updated_at = $dateupdate;
                                $update->save();
                                $mensaje="Datos actualizados correctamente.";

                            }else
                            {
                                $venta             = new Ventas();
                                $venta->terminal   = $terminalinter;
                                $venta->date       = $date;
                                $venta->json       = $jsoninter;
                                $venta->updated_at = $dateupdate;
                                $venta->type       = 1;
                                $ventasave         = new Ventas();
                                $ventasave->save_json($venta);
                                $mensaje="Datos guardados correctamente.";
                            }
                        
                        }
                    }
                }

                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', $mensaje);
        
                return back();

            }else if($request->type=="0"){

                
                $json = "{";
                $mensaje = "";
                $path = $request->file('import_file')->getRealPath();
                $data = \Excel::load($path)->get();
                $index=0;
                if ($data->count()){

                    foreach ($data as $keydata => $valuedata){
                            if($index>=3){

                                
                                    $json .= '"' . ($keydata - 3) . '"' . ':{';
                                    $json .= '"numero_empleado' . '"' . ':"' . $valuedata->numero_empleado . '"';
                                    $json .= ',"nombre":' . '"' . $valuedata->nombre . '"';
                                    $json .= ',"venta":"' . round($valuedata->venta)  . '"';
                                    $json .= ',"meta":"' . round($valuedata->meta) . '"';
                             
                                   if (isset($valuedata->nombre) && $valuedata->nombre == "TOTALES") {
                                        $json .= "}";
                                        //aca se guardara en la base de datos

                                    } else {
                                        $json .= "},";
                                    }
                                

                            }
                            $index++;
                        //}
                    }
                    $json .= "}";

                            $date=(date("Y")."-".$request->fecha);
                            $dateupdate=date("Y-m-d H:i:s");
                            $terminal="Callcenter";
                            
                            $update=ImportExcelController::update_datejson($date, $terminal);

                            if($update)
                            {
                                $update->json       = $json;
                                $update->updated_at = $dateupdate;
                                $update->save();
                                $mensaje="Datos actualizados correctamente.";                      

                            }else
                            {
                                $venta             = new Ventas();
                                $venta->terminal   = $terminal;
                                $venta->date       = $date;
                                $venta->json       = $json;
                                $venta->updated_at = $dateupdate;
                                $venta->type       = 0;
                                $ventasave         = new Ventas();
                                $ventasave->save_json($venta);
                                $mensaje="Datos guardados correctamente.";
                            }
                    
                
                }

                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', $mensaje);
        
                return back();
                
            }
        }
       
    }

    public function importDateMeta(Request $request)
    {
        $goal = new GoalAgencies();
        $date = date($request->datepax);
        $numberpax = $request->numberpax;
        $goal = $goal->findByDate($request->datepax);
        $dateupdate = date("Y-m-d H:i:s");
        
        if($goal){
            $goalupdate             = new GoalAgencies();
            $goalupdate             = $goalupdate->find($goal->id);
            $goalupdate->meta       = $numberpax;
            $goalupdate->created_at = $dateupdate;
            $goalupdate->save();

            $mensaje = "Registro actualizado.";

        }else
        {
            $goalnew             = new GoalAgencies();
            $goalnew->date       = $date;
            $goalnew->meta       = $numberpax;
            $goalnew->created_at = $dateupdate;
            $goalnew->updated_at = $dateupdate;
            $goalnew->save();
            
            $mensaje = "Registro guardado.";
        }

        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', $mensaje);

        return back();
    }

    public function importExcelAgencies(Request $request)
    {
        $jsonReport    = new JsonReportAgencies();
        $jsonDayReport = new GoalDay();
        $path          = $request->file('excel')->getRealPath();
        $paxes         = 0;
        $date          = $request->anio."-".$request->fecha;
        $date          = (date($date));
        $dateupdate    = date("Y-m-d H:i:s");
        $collection    = (new FastExcel)->import($path);
        $jsonReport    = $jsonReport->findByDate($date);
        $jsonDayReport = $jsonDayReport->findByDate($date);
        $position      = array();
        $newRow        = array();
        $paxforday     = [];

        foreach($collection as $keydata => $valuedata){
            if(!isset($agencias[$valuedata['agencias']]) && $valuedata['agencias'] != null){
                $agencias[$valuedata['agencias']]                              = [];
                $agencias[$valuedata['agencias']]['paxesGral']                 = 0;
                $agencias[$valuedata['agencias']]['paxesColectivo']            = [];
                $agencias[$valuedata['agencias']]['paxesLuxury']               = [];
                $agencias[$valuedata['agencias']]['paxesPrivado']              = [];
                $agencias[$valuedata['agencias']]['paxesRampa']                = [];
                $agencias[$valuedata['agencias']]['paxesColectivo']["total"]   = 0;
                $agencias[$valuedata['agencias']]['paxesLuxury']["total"]      = 0;
                $agencias[$valuedata['agencias']]['paxesPrivado']["total"]     = 0;
                $agencias[$valuedata['agencias']]['paxesRampa']["total"]       = 0;
                $agencias[$valuedata['agencias']]['paxesColectivo']["llegada"] = 0;
                $agencias[$valuedata['agencias']]['paxesLuxury']["llegada"]    = 0;
                $agencias[$valuedata['agencias']]['paxesPrivado']["llegada"]   = 0;
                $agencias[$valuedata['agencias']]['paxesRampa']["llegada"]     = 0;
                $agencias[$valuedata['agencias']]['paxesColectivo']["salida"]  = 0;
                $agencias[$valuedata['agencias']]['paxesLuxury']["salida"]     = 0;
                $agencias[$valuedata['agencias']]['paxesPrivado']["salida"]    = 0;
                $agencias[$valuedata['agencias']]['paxesRampa']["salida"]      = 0;
            }                    
            if($valuedata['agencias'] != null)
            {
                $paxes += $valuedata['paxes'];
                $agencias[$valuedata['agencias']]['paxesGral']+=$valuedata['paxes'];
                if(strtoupper($valuedata['tipo_de_servicio']) == "C"){
                    $agencias[$valuedata['agencias']]['paxesColectivo']["total"]+=$valuedata['paxes'];
                    if(strtoupper($valuedata['llegada_salida']) == "LL"){
                        $agencias[$valuedata['agencias']]['paxesColectivo']["llegada"]+=$valuedata['paxes'];
                    }elseif(strtoupper($valuedata['llegada_salida']) == "S"){
                        $agencias[$valuedata['agencias']]['paxesColectivo']["salida"]+=$valuedata['paxes'];
                    }
                }elseif(strtoupper($valuedata['tipo_de_servicio']) == "LUXURY"){
                    $agencias[$valuedata['agencias']]['paxesLuxury']["total"]+=$valuedata['paxes'];
                    if(strtoupper($valuedata['llegada_salida']) == "LL"){
                        $agencias[$valuedata['agencias']]['paxesLuxury']["llegada"]+=$valuedata['paxes'];
                    }elseif(strtoupper($valuedata['llegada_salida']) == "S"){
                        $agencias[$valuedata['agencias']]['paxesLuxury']["salida"]+=$valuedata['paxes'];
                    }
                }elseif(strtoupper($valuedata['tipo_de_servicio']) == "P"){
                    $agencias[$valuedata['agencias']]['paxesPrivado']["total"]+=$valuedata['paxes'];
                    if($valuedata['llegada_salida'] == "LL"){
                        $agencias[$valuedata['agencias']]['paxesPrivado']["llegada"]+=$valuedata['paxes'];
                    }elseif(strtoupper($valuedata['llegada_salida']) == "S"){
                        $agencias[$valuedata['agencias']]['paxesPrivado']["salida"]+=$valuedata['paxes'];
                    }
                }elseif(strtoupper($valuedata['tipo_de_servicio']) == "RAMPA"){
                    $agencias[$valuedata['agencias']]['paxesRampa']["total"]+=$valuedata['paxes'];
                    if(strtoupper($valuedata['llegada_salida']) == "LL"){
                        $agencias[$valuedata['agencias']]['paxesRampa']["llegada"]+=$valuedata['paxes'];
                    }elseif(strtoupper($valuedata['llegada_salida']) == "S"){
                        $agencias[$valuedata['agencias']]['paxesRampa']["salida"]+=$valuedata['paxes'];
                    }
                }

                if(!isset($paxforday[$valuedata['fecha']->format('Y-m-d')]))
                {
                    $paxforday[$valuedata['fecha']->format('Y-m-d')]=0;
                }

                $paxforday[$valuedata['fecha']->format('Y-m-d')]+=$valuedata['paxes'];

            }
        }

         if(count($jsonReport))
        {
           $jsonReport = json_decode($jsonReport->json);
           $agenciasBd = $jsonReport->agencias;
           $paxes      = $jsonReport->sumapaxes + $paxes;
           foreach($agenciasBd as $keydata => $valueagencia){
                if(isset($agencias[$keydata]))
                {
                    $valueagencia->paxesGral+=$agencias[$keydata]['paxesGral'];
                    $valueagencia->paxesColectivo->total   += $agencias[$keydata]['paxesColectivo']['total'];
                    $valueagencia->paxesColectivo->llegada += $agencias[$keydata]['paxesColectivo']['llegada'];
                    $valueagencia->paxesColectivo->salida  += $agencias[$keydata]['paxesColectivo']['salida'];

                    $valueagencia->paxesLuxury->total   += $agencias[$keydata]['paxesLuxury']['total'];
                    $valueagencia->paxesLuxury->llegada += $agencias[$keydata]['paxesLuxury']['llegada'];
                    $valueagencia->paxesLuxury->salida  += $agencias[$keydata]['paxesLuxury']['salida'];

                    $valueagencia->paxesPrivado->total   += $agencias[$keydata]['paxesPrivado']['total'];
                    $valueagencia->paxesPrivado->llegada += $agencias[$keydata]['paxesPrivado']['llegada'];
                    $valueagencia->paxesPrivado->salida  += $agencias[$keydata]['paxesPrivado']['salida'];

                    $valueagencia->paxesRampa->total   += $agencias[$keydata]['paxesRampa']['total'];
                    $valueagencia->paxesRampa->llegada += $agencias[$keydata]['paxesRampa']['llegada'];
                    $valueagencia->paxesRampa->salida  += $agencias[$keydata]['paxesRampa']['salida'];
                }else{
                    if(isset($agencias[$keydata]))
                    {
                        array_push($agenciasBd, $agencias[$keydata]);
                    }
                }
            }
            /*Se actualiza json */
            $agencias = $agenciasBd;
            foreach ($agencias as $key => $row) {
                $position[$key] = $row->paxesGral;
                $newRow[$key]   = $row;
            }
            arsort($position);
            $json = json_encode(array("agencias" => $agencias, "sumapaxes" =>$paxes, "posiciones" => $position));
            $jsonReport             = new JsonReportAgencies();
            $jsonReport             = $jsonReport->findByDate($date);
            $jsonReport->json       = $json;
            $jsonReport->updated_at = $dateupdate;
            $jsonReport->save();
            $mensaje                = "Datos actualizados correctamente.";
        }else{
            /*Se guarda json nuevo*/
            foreach ($agencias as $key => $row) {
                $position[$key] = $row['paxesGral'];
                $newRow[$key]   = $row;
            }
            arsort($position);
            $json                   = json_encode(array("agencias" => $agencias, "sumapaxes" =>$paxes, "posiciones" => $position));
            $jsonReport             = new JsonReportAgencies();
            $jsonReport->date       = $date;
            $jsonReport->json       = $json;
            $jsonReport->updated_at = $dateupdate;
            $jsonReport->created_at = $dateupdate;
            $jsonReport->save();
            $mensaje                = "Datos guardados correctamente.";
        }

        if(count($jsonDayReport))
        {
            $dayReport = json_decode($jsonDayReport->json);
            foreach($paxforday as $keyDayReport => $valueDayReport){
                if(!isset($dayReport->$keyDayReport))
                {
                    $dayReport->$keyDayReport = $paxforday[$keyDayReport];
                }else{
                    $dayReport->$keyDayReport+=$valueDayReport;
                }
            }

            $jsonDayReport->json = json_encode($dayReport);
            $jsonDayReport->updated_at = $dateupdate;
            $jsonDayReport->save();


        }else{
             $jsonSaveDayReport             = new GoalDay();
             $jsonSaveDayReport->date       = $date;
             $jsonSaveDayReport->json       = json_encode($paxforday);
             $jsonSaveDayReport->created_at = $dateupdate;
             $jsonSaveDayReport->updated_at = $dateupdate;
             $jsonSaveDayReport->save();
        }

        $request->session()->flash('message.level', 'success');
        $request->session()->flash('message.content', $mensaje);
        
        return back();
    }

    public function get_datejson(Request $request)
    {
        $venta  = new Ventas();
        $type   = \Auth::user()->type;
        $date   = (date("Y")."-".$request->date);
        $ventas = $venta->findVentas($date, $type);
        
        return $ventas;
    }

    public function get_meta(Request $request)
    {
        $goal = new GoalAgencies();
        $date = $request->date;
        $goal = $goal->findByDate($date);

        if($goal){
            return "existe";
        }else{
            return "no existe";
        }
    }

    public function get_metaall()
    {
        $datefin  = date("Y-m-d");
        $year  = date("Y");
        $month = date("m");
        $dateini = $year."-".$month."-01"; 
        $goal = new GoalAgencies();
        $goal = $goal->findByDateAll($dateini, $datefin);

        return $goal;
    }

    public function get_metasall()
    {
        
        $goal        = new GoalAgencies();
        $date        = date("Y-m-d");
        $date        = strtotime('-1 day', strtotime($date));
        $datesd      = date('Y-m', $date);
        $datesy      = date('m-d', $date);
        $date        = date('Y-m-d', $date);
        $year        = date("Y");
        $yearPast    = $year-1;
        $datepast    = date(($year-1)."-".$datesy);
        $datesdpast  = ($year-1)."-".date('m');
        $metadia     = $goal->findByDate($date);
        $dateini     = $year."-01-01";
        $dateinipast = $yearPast."-01-01"; 
        $metaMes     = $this->get_metaall();


        $goals        = $this->get_goals($dateini, $date, $datesd);
        $goalDay      = $goals['goalDay'];
        $sumaAllYear  = $goals['sumaAllYear'];
        $sumaAllMonth = $goals['sumaAllMonth'];
        $metaanual    = $goals['metaanual'];

        $goalspast        = $this->get_goals($dateinipast, $datepast, $datesdpast);
        $goalDayPast      = $goalspast['goalDay'];
        $sumaAllYearPast  = $goalspast['sumaAllYear'];
        $sumaAllMonthPast = $goalspast['sumaAllMonth'];
        $metaanualPast    = $goalspast['metaanual'];

         return array("metames"        => intval($metaMes), 
                    "metadia"          => intval($metadia['meta']), 
                    "metaanual"        => intval($metaanual),
                    "fecha"            => $date,
                    "anioactual"       => $year,
                    "goalday"          => $goalDay,
                    "sumaAllYear"      => $sumaAllYear,
                    "sumaAllMonth"     => $sumaAllMonth,
                    "goalDayPast"      => $goalDayPast,
                    "sumaAllYearPast"  => $sumaAllYearPast,
                    "sumaAllMonthPast" => $sumaAllMonthPast);
    }

    public function get_goals($dateini, $date, $datesd){
        $sumaAllYear  = 0;
        $sumaAllMonth = 0;
        $goal         = new GoalAgencies();
        $metaanual    = $goal->findByDateAll($dateini, $date);
        $goalDay      = new GoalDay();
        $goalDay      = $goalDay->findByDate($datesd);

        if($goalDay){
            $goalDay = json_decode($goalDay->json);
            foreach($goalDay as $key => $value){
                if($key == $date)
                {
                    $goalDay = $value;
                    break;
                }else
                {
                    $goalDay = 0;
                }
            }
        }else{
            $goalDay = 0;
        }

        $goalAllDay = new GoalDay();
        $goalAllDay = $goalAllDay->findByDateAll($datesd);
        $inicioMes  = $datesd."-01";

        if($goalAllDay)
        {
            foreach ($goalAllDay as $key => $value) {
                $json = json_decode($value->json);
                foreach ($json as $keyjson => $valuejson) {
                   if($keyjson <= $date)
                    {
                        $sumaAllYear += floatval($valuejson);
                    }
                   if($keyjson >= $inicioMes && $keyjson <= $date)
                    {-
                        $sumaAllMonth += floatval($valuejson);
                    }


                }
            }
        }else{
            $sumaAllYear = 0;
        }

        return array("metaanual"   => $metaanual,
                    "goalDay"      => $goalDay,
                    "sumaAllYear"  => $sumaAllYear,
                    "sumaAllMonth" => $sumaAllMonth);
    }

    public function get_datejsonAgencies(Request $request){
        $date       = $request->anio."-".$request->mes;
        $date       = (date($date));
        $jsonReport = new JsonReportAgencies();
        $jsonReport = $jsonReport->findByDate($date);

        if(count($jsonReport)){
            return "existe";
        }else{
            return "no existe";
        }

    }

    public static function get_datejsons($date, $type)
    {
        $venta  = new Ventas();
        $ventas = $venta->findVentas($date, $type);


        return $ventas;
    }
     public static function get_datejsons_terminal($date, $type, $terminal)
    {
        $venta  = new Ventas();
        $ventas = $venta->findVentasTerminal($date, $type, $terminal);


        return $ventas;
    }

    public function update_datejson($date, $terminal)
    {
        $venta  = new Ventas();
        $ventas = $venta->findUpdate($date, $terminal);

        return $ventas;

    }

    public function showRegistrationForm()
    {
       $type_user = \Auth::user()->type_user;
       $type = \Auth::user()->type;

        if ($type_user == 8) {
           return view('adduser', array("type"=>$type));
       }else {
           return abort(404);
       }
    }

    public function addUser(Request $request)
    {

        if($request->type_terminal && $request->type_user != 1)
        {
            $id_terminal = $request->type_terminal;
        }else
        {
            $id_terminal = 0;
        }

        $create = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'code'        => $request->code,
            'type'        => $request->type,
            'type_user'   => $request->type_user,
            'password'    => bcrypt($request->password),
            'id_terminal' => $id_terminal
        ]);

        return view('auth.thanks');
    }

      public function getUsers()
    {
        $user      = new User();
        $type_user = \Auth::user()->type;
        $users     = $user->getusers($type_user);

        return view('users', array('users' => $users, 'type' => $type_user ));
    }

    public function updatepass(Request $request){

        $user           = User::find($request->user);
        $user->password = bcrypt($request->pass);
        $user->save();

    }

    public function deleteuser(Request $request){

        $user = User::destroy($request->user);
    }
    public function get_dataAgencies(){
        
        $arrayPaxes = [];
        $arrayYears = [];
        $arrayYearsComple = [];
        $arrayMeses = ['01','02','03','04','05','06','07','08','09','10','11','12'];
        $totalesAnio = [];
        $arrayGrafica1=[];
        $arrayPosiciones =[];
        $arrayTop=[];
        $array=[];
        $jsonReport = new JsonReportAgencies();
        $jsonReport = $jsonReport
        ->orderBy("date", "asc")
        ->get();

        if(count($jsonReport)>0)
        {
            foreach ($jsonReport as $key => $valueReport) {
                if(!isset($arrayPaxes[$valueReport->date]))
                {
                    $arrayPaxes[$valueReport->date]=[];
                }
                $json = json_decode($valueReport->json);
                array_push($arrayPaxes[$valueReport->date], $json->sumapaxes);
                if(!in_array(date("Y", strtotime($valueReport->date)), $arrayYears))
                {
                    array_push($arrayYears, date("Y", strtotime($valueReport->date)));
                }
                if(!isset($arrayYearsComple[$valueReport->date]))
                {
                    $arrayYearsComple[$valueReport->date]=[];
                }
                array_push($arrayYearsComple[$valueReport->date], $valueReport);

            }
        }else
        {
            return null;
        }
        foreach ($arrayYears as $keyYear => $valueYear) {
            foreach($arrayMeses as $keyMeses => $valueMeses){
                if(!isset($arrayGrafica1[$valueYear]))
                {
                    $arrayGrafica1[$valueYear]=[];
                }
                if(isset($arrayPaxes[$valueYear."-".$valueMeses]))
                {
                    array_push($arrayGrafica1[$valueYear], $arrayPaxes[$valueYear."-".$valueMeses][0]);
                }else{
                    array_push($arrayGrafica1[$valueYear], 0);
                }
            }
                
        }
        foreach($arrayYears as $keyyear => $valueyear){
            $arrayPosiciones[$valueyear]=[];
            foreach($jsonReport as $keyReport => $valueReport){
                if($valueyear == date("Y", strtotime($valueReport->date)))
                {
                    $json = json_decode($valueReport->json);
                    if(!isset($arrayPosiciones[$valueyear])){
                        $arrayPosiciones[$valueyear]=[];
                    }
                    if(!isset($arrayPosiciones[$valueyear][$valueyear])){
                        $arrayPosiciones[$valueyear][$valueyear]=[];
                    }
                    array_push($arrayPosiciones[$valueyear][$valueyear], $json->posiciones);
                }
            }
        }
       
        foreach($arrayYears as $keyyear => $valueyear){
            foreach ($arrayPosiciones as $keyPosiciones => $valuePosiciones){
                foreach ($valuePosiciones as $key => $value){
                    foreach ($value as $keyvalue => $valuefinal){
                        foreach ($valuefinal as $keyvaluef => $valuefinalf){
                            if(!isset($arrayTop[$valueyear]))
                            {
                                $arrayTop[$valueyear]=[];   
                            }
                            if(!isset($arrayTop[$valueyear][$keyvaluef]))
                            {
                                $arrayTop[$valueyear][$keyvaluef] = 0;
                            }
                            if($valueyear == $key)
                            {
                                $arrayTop[$key][$keyvaluef] += $valuePosiciones[$key][$keyvalue]->$keyvaluef;
                            }
                        }
                    }
                }
            }
        }

        foreach($arrayTop as $key => $value){
            arsort($arrayTop[$key]);
        }

        return array("paxPorMes" => $arrayGrafica1, "arrayYearsComple" => $arrayYearsComple, "arrayTop" => $arrayTop);
        
    }
      

}
