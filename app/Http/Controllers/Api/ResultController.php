<?php

// ------------------------------- DOCUMENTATION ------------------------------------------------
//
// API methods: GET POST, PUT, DELETE, INFO, DUMB
// API signature: /api/HTTP/{METHOD}/{?json}
//
//  GET     http://example.com/api/HTTP/GET                       get all records
//  GET     http://example.com/api/HTTP/GET/{id}                  get a single record by id
//  POST    http://example.com/api/HTTP/POST/                     create a new record
//          ?method={string}
//          &url={string}
//          &domain={string}
//          &scheme={string}
//          &path={string}
//          &version={string}
//          &status={string}
//          &date={string}
//          &server={string}
//          &location={string}
// PUT      http://example.com/api/HTTP/PUT/{id}?{param=val&...}   modify a single record
// DELETE   http://example.com/api/HTTP/DELETE/{id}                delete a single record
// INFO     TBD
// DUMB     TBD


// Output example:
// {
//     "status": "200 OK",
//     "errors": {},
//     "data": {
//            "url": {
//                "url": "www.renzocarara.it/contatti.php"
//            },
//            "response": {
//                "version": "http/1.1",
//                "status": "200 ok",
//                "date": "mon, 27 Jul 2009",
//                "server": "Apache/2.2.14 (win32)",
//                "location": "null"
//            },
//            "request": {
//                "method": "get",
//                "url": "www.renzocarara.it/contatti.php",
//                "domain": "renzocarara.it",
//                "scheme": "http",
//                "path": "contatti"
//            }
//     }
// }

// input examples (api_token parameter is mandatory):
// GET     https://mysite.com/api/HTTP/GET/
// GET     https://mysite.com/api/HTTP/GET/3
// POST    https://mysite.com/api/HTTP/POST/?method=post&url=github.com/Synchro/PHPMailer&domain=github.com/Synchro/PHPMailer&scheme=https&path=Synchro/PHPMailer&version=http2.0/&status=200&date="mon, 1 Jun 2020"&server=apache 7.4.13&location=null
// PUT     https://mysite.com/api/HTTP/put/5?version=HTTP1.1/&status=501
// DELETE  https://mysite.com/api/HTTP/delete/13

// NOTA PER IL DEBUG/TESTING: per mandare richieste con POSTMAN ad API che richiedono l'autenticazione, 
// devo inserire, fra i parametri della richiesta api_token=il_mio_api_token
// (l'api_token viene generato automaticamente per ogni utente che si registra sul sito)
// Per inserirlo nella richiesta, in POSTMAN, devo selezionare la TAB: "AUTHORIZATION" e scegliere TYPE="Bearer Token" ed inserire
// il token (stringa alfanumerica di 80chars) nel campo specifico. In alternativa, più semplicemente,
// posso passarglielo direttamente come parametro (api_token)
//

// ---------------------------- END DOCUMENTATION ------------------------------------------------

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Result;


class ResultController extends Controller

{

        public static function buildAPIResponse($record, $status, $error){

            // costruisco un array da utilizzare poi come risposta dell'API

            // accorpo i dati 
            $data_request = [
                'method' => $record->method,
                'url' => $record->url,
                'domain' => $record->domain,
                'scheme' => $record->scheme,
                'path' => $record->path];
            // accorpo i dati 
            $data_response = [
                'version' =>$record->version,
                'status' => $record->status,
                'date' => $record->date,
                'server' => $record->server,
                'location' => $record->location];

            $data_url = (object) ['url' => $record->url];  // trasformo la stringa in un oggetto
            
            // creo la response da restituire
            $APIResponse = [
                'status' => $status,
                'errors' => (object) $error,   // con '(object)' trasformo il dato in un oggetto
                'data' => ['url' => $data_url, 'response' => $data_response, 'request'=> $data_request]
            ];

            return $APIResponse;
        }

        public function index() {
        // ESEMPIO: "http://example.com/api/HTTP/GET"
        // richiedo l'elenco completo di tutti i risultati
        // metodo GET

        // leggo tutta la tabella 'results'
        $records = Result::all();

        // creo la risposta, ovvero un JSON da ritornare
        return response()->json(
            [
            'status' => '200',
            'errors' => (object)[],
            'data' => $records    // NOTA: qui torno un array di oggetti, ogni oggetto è un singolo risultato
            ]
            );
        }

        public function show($id) {
        // ESEMPIO: "http://example.com/api/HTTP/GET/44"
        // richiedo il risultato con id=44
        // metodo GET

        // recupero il risultato richiesto tramite l'id
        $record = Result::find($id);
        // se l'ho trovato restituisco il risultato
        if($record) {

            $APIResponse = $this->buildAPIResponse($record, "200", []);
            return response()->json($APIResponse);

        } else {
            // il record richiesto non esiste, ritorno un una risposta che segnala l'errore
            return response()->json(
                [
                'status' => '200',
                'errors' => (object)['text' => 'Il risultato con id ' . $id . ' non esiste'],
                'data' => (object)[],
                ]
            );
        }
    }

    public function store(Request $request) {
        // ESEMPIO: http://example.com/api/HTTP/POST/?param1=xxxx&param2=yyyyyyy&param3=zzzzz....etc
        // richiedo di memorizzare un nuovo risultato
        // metodo POST

        // estraggo i dati ricevuti in post,
        $data_received = $request->all();
        // creo un nuovo oggetto da scrivere nel DB
        $new_record = new Result();
        // valorizzo il nuovo oggetto
        $new_record->fill($data_received);
        // scrivo l'oggetto nel DB
        $new_record->save();

        $APIResponse = $this->buildAPIResponse($new_record, "201", []);
        return response()->json($APIResponse);

    }

    public function update(Request $request, $id) {
        // ESEMPIO: http://example.com/api/HTTP/PUT/45?version=http2.0/
        // aggiorno il campo 'version' del risultato con id=45
        // metodo PUT

        // recupero il risultato che l'utente vuole modificare
        $record = Result::find($id);
        // se questo record esiste, procedo
        if($record) {
            // ho trovato il risultato, leggo i dati inviati tramite api per l'aggiornamento
            $new_record = $request->all();
            // aggiorno i dati del risultato scrivendo nel DB
            // Laravel gestisce l'update() aggiornando solo i dati che riceve (come fosse una PATCH e non PUT)
            $record->update($new_record);

            $APIResponse = $this->buildAPIResponse($record, "200", []);
            return response()->json($APIResponse);

        } else {
            // se non ho trovato il risultato richiesto all'interno del mio DB
            return response()->json(
                [
                'status' => '200',
                'errors' => (object)['text' => 'Il risultato con id ' . $id . ' non esiste'],
                'data' => (object)[],
                ]
            );
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
            $record->delete();

             return response()->json(
                [
                'status' => '200',
                'errors' => (object)[],
                'data' => (object)[]   //restituisco un oggetto vuoto
                ]
            );
        } else {
            // se non ho trovato il risultato richiesto all'interno del mio DB 
            // rispondo con un messaggio d'errore
            return response()->json(
                [
                'status' => '200',
                'errors' => (object)['text' => 'Il risultato con id ' . $id . ' non esiste'],
                'data' => (object)[],
                ]
            );
        }
    }

}


