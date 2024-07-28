<?php

use App\AI\Assistant;
use App\Ai\Chat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

/*
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
*/

Route::get('/image', function () {
    return view('image', [
        'messages' => session('messages', [])
    ]);
});

Route::post('/image', function () {

    $attributes = request()->validate([
      
        'description' => ['required', 'string', 'min:3']
    ]);

    /* $url = OpenAI::images()->create([
        'prompt' => $attributes['description'],
        'model' => 'dall-e-3',
    ])->data[0]->url; */

    $assistant = new Assistant(session('messages', []));
    $assistant->visualize($attributes['description']);

    session(['messages' => $assistant->messages()]);

    return redirect('/image');


});





Route::get('/chat', function () {


    $chat = new \App\Ai\Chat();
    $poem = $chat->systemMessage('You are a poetic assistant, skilled in explaining complex programming concepts with creative flair.')     
    ->send('Compose a poem that explains the concept of recursion in programming.');

    $sillyPoem= $chat->reply('Cool, can you make it much, much  sillier.?');

//dd($poem, $sillyPoem);

    return view('welcome',['poem' => $sillyPoem]);
});



Route::get('/roast', function () {
    return view('roast');
});

Route::post('/roast', function() {

   //dd(request('topic'));

    $attributes = request()->validate([
        'topic' => 'required','string','min:2','max:50'
    ]);

    $prompt = "Please roast {$attributes['topic']} in a sarcastic tone";

   $mp3 = (new Chat())->send(
    message: $prompt,
    speech: true
   );


   $name = md5($mp3);
   Storage::disk('public')->put("mp3/{$name}.mp3", $mp3);
   $file = Storage::url("mp3/{$name}.mp3");
  // dd($file);

  //  file_put_contents(public_path('file.mp3'), $mp3);
  //  $file = "/mp3/".md5($mp3).".mp3";
  //   file_put_contents(public_path($file), $mp3); 


   return redirect('/')->with([
        'file' => $file,
        'flash' => 'Roast'
        ]
    );

});