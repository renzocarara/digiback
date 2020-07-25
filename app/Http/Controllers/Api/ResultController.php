<?php

// ------------------------------- DOCUMENTATION ------------------------------------------------
//
// API methods: GET POST, PUT, DELETE, INFO, DUMB
// API signature: /api/HTTP/{METHOD}/{?json}
//
// GET     http://example.com/api/HTTP/GET                       get all records
// GET     http://example.com/api/HTTP/GET/{id}                  get a single record by id
// POST    http://example.com/api/HTTP/POST/                     create a new record
// PUT     http://example.com/api/HTTP/PUT/{id}                  modify a single record
// DELETE  http://example.com/api/HTTP/DELETE/{id}               delete a single record
// INFO    TBD
// DUMB    TBD


// Output example (success):
// {
//     "status": "200",
//     "data": {
//            "id":"456",
//            "url": {
//                "url": "www.renzocarara.it/contatti.php"
//            },
//            "response": {
//                "statusline": "http/1.1 200 OK",
//                "status": "200",
//                "date": "mon, 27 Jul 2009",
//                "server": "Apache/2.2.14 (win32)",
//                "location": "null"
//            },
//            "request": {
//                "method": "get",
//                "url": "www.renzocarara.it/contatti.php",
//                "domain": "renzocarara.it",
//                "scheme": "https",
//                "path": "contatti"
//            }
//     }
// }

// Output example (error):
// {
//     "status": "404",
//     "data": {
//            "id":"457",
//             "errors": {
//                  "Resource not available"
//             }
//     }
// }
//


// input examples (api_token info is mandatory):
// GET     https://heroku.app.com/digifront/api/HTTP/GET/
// GET     https://heroku.app.com/digifront/api/HTTP/GET/3
// POST    https://heroku.app.com/digifront/api/HTTP/POST/
// PUT     https://heroku.app.com/digifront/api/HTTP/PUT/5
// DELETE  https://heroku.app.com/digifront/api/HTTP/DELETE/13

// NOTA PER IL DEBUG/TESTING: per mandare richieste con POSTMAN ad API che richiedono l'autenticazione,
// devo inserire api_token=il_mio_api_token
// (l'api_token viene generato automaticamente per ogni utente che si registra sul sito)
// Per inserirlo nella richiesta, in POSTMAN, devo selezionare la TAB: "AUTHORIZATION" e scegliere TYPE="Bearer Token" ed inserire
// il token (stringa alfanumerica di 80chars) nel campo specifico. In alternativa, più semplicemente,
// posso passarglielo direttamente nella sezione params"
//

// ---------------------------- END DOCUMENTATION ------------------------------------------------

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Result;


class ResultController extends Controller

{

    public static function buildDataForResponse($record){

        // costruisco un struttura da utilizzare poi come risposta dell'API

        // accorpo i dati
        $data_request = [
            'method' => $record->method,
            'url' => $record->url,
            'domain' => $record->domain,
            'scheme' => $record->scheme,
            'path' => $record->path];
        // accorpo i dati
        $data_response = [
            'statusline' =>$record->statusline,
            'status' => $record->status,
            'date' => $record->date,
            'server' => $record->server,
            'location' => $record->location];

        $data_url = (object) ['url' => $record->url];  // trasformo la stringa in un oggetto

        // assemblo la response da restituire, inserisco anche l'id del record
        $response_data = [
            'id' => $record->id, 'url' => $data_url, 'response' => $data_response, 'request'=> $data_request
        ];

        return $response_data;
    }

    public static function buildResponse($data, $status_code) {

        return response()->json($data, $status_code)
        ->header('Access-Control-Expose-Headers', '*')  // rendo disponibili nella response tutti gli headers
        ->header('Content-Type', 'application/json')
        ->header('server', 'Apache');

    }

    public function index() {
        // ESEMPIO: "http://example.com/api/HTTP/GET"
        // richiedo l'elenco completo di tutti i risultati
        // metodo GET

        // leggo tutta la tabella 'results', e la ordino per "id"
        // $records = Result::all();
        $records = Result::orderBy('id', 'ASC')->get();

        if ($records) {
            // creo un array con tutti i record formattati come da specifica
            $formatted_records=[];
            foreach ($records as $record) {
                $response_data = $this->buildDataForResponse($record);
                array_push($formatted_records, $response_data);
            }
            // creo la risposta, ovvero un JSON da ritornare
            return $this->buildResponse($formatted_records, 200);

        } else{
            // non ci sono records nel DB, ritorno un oggetto vuoto
            return $this->buildResponse((object)[], 200);

        }
    }

    public function show($id) {
        // ESEMPIO: "http://example.com/api/HTTP/GET/44"
        // richiedo il risultato con id=44
        // metodo GET

        // recupero il risultato richiesto tramite l'id
        $record = Result::find($id);
        // se l'ho trovato restituisco il risultato
        if($record) {

            $response_data = $this->buildDataForResponse($record);
            return $this->buildResponse($response_data, 200);

        } else {
            // il record richiesto non esiste, ritorno un una risposta che segnala l'errore
            return $this->buildResponse((object)['id' => $id, 'errors' => 'No record with id: ' . $id . ' found'], 404);
        }
    }

    public function store(Request $request) {
        // ESEMPIO: http://example.com/api/HTTP/POST/
        // richiedo di memorizzare un nuovo risultato
        // metodo POST

        // estraggo i dati ricevuti in post,
        $data_received = $request->all();
        // creo un nuovo oggetto da scrivere nel DB
        $new_record = new Result();
        // valorizzo il nuovo oggetto
        $new_record->fill($data_received);
        // scrivo l'oggetto nel DB
        $is_saved = $new_record->save();
        if ($is_saved) {
            // la scrittura nel DB è andata bene, costruisco la response da ritornare
            $response_data = $this->buildDataForResponse($new_record);
            $response = $this->buildResponse($response_data, 201);
        } else {
            // il salvataggio nel DB non è riuscito
            $response = $this->buildResponse((object)['id' => '', 'errors' => 'No data saved in DB!'], 500);
        }

        return  $response;
    }

    public function update(Request $request, $id) {
        // ESEMPIO: http://example.com/api/HTTP/PUT/45
        // aggiorno il risultato con id=45
        // metodo PUT

        // recupero il risultato che l'utente vuole modificare
        $record = Result::find($id);
        // se questo record esiste, procedo
        if($record) {
            // ho trovato il risultato, leggo i dati inviati tramite api per l'aggiornamento
            $new_record = $request->all();
            // aggiorno i dati del risultato scrivendo nel DB
            // Laravel gestisce l'update() aggiornando solo i dati che riceve (come fosse una PATCH e non PUT)
            $is_updated = $record->update($new_record);

            if ($is_updated) {
                // l'aggiornamento del DB è andato bene, costruisco la response da ritornare
                $response_data = $this->buildDataForResponse($record);
                $response = $this->buildResponse($response_data, 200);
            } else {
              // l'aggiornamento del DB non è riuscito
              $response = $this->buildResponse((object)['id' => $id, 'errors' => 'No data updated in DB!'], 500);
            }

            return  $response;

        } else {
            // se non ho trovato il risultato da aggiornare all'interno del mio DB
            return $this->buildResponse((object)['id' => $id, 'errors' => 'No record with id: ' . $id . ' found'], 404);

        }
    }

    public function destroy($id) {
        // ESEMPIO: http://example.com/api/HTTP/DELETE/47
        // elimino il risultato con id=47
        // metodo DELETE

        // recupero il risultato che l'utente vuole cancellare
        $record = Result::find($id);
        // se il risultato effettivamente esiste, procedo
        if($record) {

            // cancello il post dal DB
            $is_deleted = $record->delete();

            if ($is_deleted) {
                // la cancellazione dal DB è andata bene, costruisco la response da ritornare
                $response = $this->buildResponse((object)[], 200);
            } else {
                // la cancellazione dal DB non è riuscita
                $response = $this->buildResponse((object)['id' => $id, 'errors' => 'Delete failed!'], 500);
            }

            return $response;

        } else {
            // non c'è il record da cancellare all'interno del mio DB
            return $this->buildResponse((object)['id' => $id, 'errors' => 'No record with id: ' . $id . ' found'], 404);

        }
    }

}
