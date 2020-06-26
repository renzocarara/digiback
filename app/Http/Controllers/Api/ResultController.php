<?php
    // ------------------------------- DOCUMENTATION ------------------------------------------------
    //
    // metodi implementati: GET POST, PUT, DELETE, INFO, DUMB
    // API signature: /api/HTTP/{METHOD}/{?json}
    //
    //  GET     http://example.com/api/HTTP/get                       get all results
    //  GET     http://example.com/api/HTTP/get/{id}                  get a single result by id
    //  POST    http://example.com/api/HTTP/post/                     create a new result
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
    // PUT      http://example.com/api/HTTP/put/{id}?{param=val&...}   modify a single result
    // DELETE   http://example.com/api/HTTP/delete/{id}                delete a single reesult
    // INFO
    // DUMB

    
    // Esempio di risposta dell'API:
    // {
    //   "status": 200,
    //   "errors": {},
    //   "data": {
    //              "url": {
    //               },
    //              "response": {
    //               },
    //              "request": {
    //               }
    //   }
    // }

    
    // stringhe di test:
    // GET     http://localhost:8000/api/HTTP/get/
    // GET     http://localhost:8000/api/HTTP/get/3
    // POST    http://localhost:8000/api/HTTP/post/?method=post&url=github.com/Synchro/PHPMailer&domain=github.com/Synchro/PHPMailer&scheme=https&path=Synchro/PHPMailer&version=http2.0/&status=200&date="mon, 1 Jun 2020"&server=apache 7.4.13&location=null
    // PUT     http://localhost:8000/api/HTTP/put/5?version=HTTP1.1/&status=501
    // DELETE  http://localhost:8000/api/HTTP/delete/13
    
    // NOTA: per mandare richieste con POSTMAN ad API che richiedono l'autenticazione, 
    // devo inserire, fra i parametri della richiesta api_token=il_mio_api_token
    // (l'api_token viene generato automaticamente per ogni utente che si registra sul sito)
    // Per inserirlo nella richiesta, in POSTMAN, devo selezionare la TAB: "AUTHORIZATION" e scegliere TYPE="Bearer Token" ed inserire
    // il token (stringa alfanumerica di 80chars) nel campo specifico. In alternativa posso passarglielo direttamente come parametro
    // (api_token)
    //
        namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Result;


class ResultController extends Controller

{

         public static function prepare_data($result){
            // creo 2 strutture da inserire nella risposta dell'api
            $request=[
                'method' => $result->method,
                'url' => $result->url,
                'domain' => $result->domain,
                'scheme' => $result->scheme,
                'path' => $result->path];

            $response=[
                'version' =>$result->version,
                'status' => $result->status,
                'date' => $result->date,
                'server' => $result->server,
                'location' => $result->location];

            return [$request, $response];  

            }

        public function index() {
        // ESEMPIO: "http://example.com/api/HTTP/get"
        // richiedo l'elenco completo di tutti i risultati
        // metodo GET

        // leggo tutta la tabella 'results'
        $results = Result::all();
        // creo la risposta, ovvero un JSON da ritornare
        return response()->json(
            [
                'status' => '200 OK',
                'errors' => null,
                'data' => $results
                ]
            );
        }

        public function show($id) {
        // ESEMPIO: "http://example.com/api/HTTP/get/44"
        // richiedo il risultato con id=44
        // metodo GET

        // recupero il risultato richiesto tramite l'id
        $result = Result::find($id);
        // se l'ho trovato restituisco il risultato
        if($result) {

            // preparo 2 strutture di informazioni lette da DB e da inserire nella risposta
            $arrays = $this->prepare_data($result);
            $request = $arrays[0];
            $response = $arrays[1];

            return response()->json(
                [
                'status' => '200 OK',
                'errors' => null,
                'data' => ['url' => $result->url, 'response' => $response, 'request'=> $request]
                ]
            );
        } else {
            // il result richiesto non esiste, ritorno un una risposta che segnala l'errore
            // e restituisce un array vuoto e un messaggio d'errore nel campo error
            return response()->json(
                [
                'status' => '200 OK',
                'errors' => 'Il risultato con id ' . $id . ' non esiste',
                'data' => [],
                ]
            );
        }
    }

    public function store(Request $request) {
        // ESEMPIO: http://example.com/api/HTTP/post/?param1=xxxx&param2=yyyyyyy&param3=zzzzz....etc
        // richiedo di memorizzare un nuovo risultato
        // metodo POST

        // estraggo i dati ricevuti in post,
        $data_received = $request->all();
        // creo un nuovo oggetto da scrivere nel DB
        $new_result = new Result();
        // valorizzo il nuovo oggetto
        $new_result->fill($data_received);
        // scrivo l'oggetto nel DB
        $new_result->save();
        // ritorno una risposta con l'oggetto appena scritto nel DB

        // preparo 2 strutture di informazioni lette da DB e da inserire nella risposta
        $arrays = $this->prepare_data($new_result);
        $request = $arrays[0];
        $response = $arrays[1];


        return response()->json(
            [
                'status' => '200 OK',
                'errors' => null,
                'data' => ['url' => $new_result->url, 'response' => $response, 'request'=> $request]
            ]
        );
    }

    public function update(Request $request, $id) {
        // ESEMPIO: http://example.com/api/HTTP/put/45?version=http2.0/
        // aggiorno il campo 'version' del risultato con id=45
        // metodo PUT

        // recupero il risultato che l'utente vuole modificare
        $result = Result::find($id);
        // se questo result esiste, procedo
        if($result) {
            // ho trovato il risultato, leggo i dati inviati tramite api per l'aggiornamento
            $new_result = $request->all();
            // aggiorno i dati del risultato scrivendo nel DB
            // Laravel gestisce l'update() aggiornando solo i dati che riceve (come fosse una PATCH e non PUT)
            $result->update($new_result);

            // preparo 2 strutture di informazioni lette da DB e da inserire nella risposta
            $arrays = $this->prepare_data($result);
            $request = $arrays[0];
            $response = $arrays[1];

            return response()->json(
                [
                'status' => '200 OK',
                'errors' => null,
                'data' => ['url' => $result->url, 'response' => $response, 'request'=> $request] //restituisco il risultato aggiornato
                ]
            );
        } else {
            // se non ho trovato il risultato richiesto all'interno del mio DB
            return response()->json(
                [
                'status' => '200 OK',
                'errors' => 'Il risultato con id ' . $id . ' non esiste',
                'data' => [],
                ]
            );
        }
    }

    public function destroy($id) {
        // ESEMPIO: http://example.com/api/HTTP/delete/47
        // elimino il risultato con id=47
        // metodo DELETE

        // recupero il risultato che l'utente vuole cancellare
        $result = Result::find($id);
        // se il risultato effettivamente esiste, procedo
        if($result) {

            // cancello il post dal DB
            $result->delete();

             return response()->json(
                [
                'status' => '200 OK',
                'errors' => null,
                'data' => [] //restituisco un array vuoto
                ]
            );
        } else {
            // se non ho trovato il risultato richiesto all'interno del mio DB 
            // invio messaggio d'errore
            return response()->json(
                [
                'status' => '200 OK',
                'errors' => 'Il risultato con id ' . $id . ' non esiste',
                'data' => [],
                ]
            );
        }
    }

}


