<?php

    namespace App\Services;

    use Exception;
    use Illuminate\Http\Response;
    use Illuminate\Support\Facades\Http;

    Class PerroService{

        public function cargarPerro(){
            try{
                $response = Http::get('https://dog.ceo/api/breeds/image/random');


                if($response->successful()){
                    return ["body"=>$response->json(), "status"=> $response->status()];
                }
                if($response->failed()){
                    return ["body"=>"fallo de informacion", "status"=> $response->status()];

                }
                if($response->clientError()){
                    return ["body"=>" fallo de comunicacion", "status"=> $response->status()];

                }
            
            
            } catch (Exception $e) {
                return response()->json([
                    "error" => $e->getMessage(),
                    "linea"=> $e->getLine(), 
                    "file"=> $e->getFile(),
                    "metodo"=> __METHOD__
            ], Response::HTTP_BAD_REQUEST);
            }
        }
    }