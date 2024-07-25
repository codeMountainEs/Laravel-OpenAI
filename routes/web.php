<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    $messages =  [
        [
          "role" => "system",
          "content"=> "You are a poetic assistant, skilled in explaining complex programming concepts with creative flair."
        ],
      
    ];
    $messages =  [
       
        [
          "role"=> "user",
          "content"=> "Compose a poem that explains the concept of recursion in programming."
        ],
    ];

    //"model" => "gpt-4o-mini",

$poem = Http::withToken(config('services.openai.secret'))
    ->post('https://api.openai.com/v1/chat/completions', 
        [
            "model" => "gpt-3.5-turbo",
            "messages"=>  $messages
        
        ])->json('choices.0.message.content');

        $messages =  [
       
            [
              "role"=> "assitant",
              "content"=> $poem
            ],
        ];
        $messages =  [
       
            [
              "role"=> "user",
              "content"=> "Good, but can you make it much, much more silly."
            ],
        ];

        $sillyPoem = Http::withToken(config('services.openai.secret'))
    ->post('https://api.openai.com/v1/chat/completions', 
        [
            "model" => "gpt-3.5-turbo",
            "messages"=>  $messages
        
        ])->json('choices.0.message.content');

        $messages =  [
       
            [
              "role"=> "assitant",
              "content"=> $sillyPoem
            ],
        ];

//return $poem;



//dd($response['choices'][0]['message']['content']);

    return view('welcome',
    [
        'poem' => $poem,
        'sillyPoem' => $sillyPoem

]);

});
