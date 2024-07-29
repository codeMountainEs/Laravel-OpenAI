<?php

use App\AI\Assistant;
use App\Ai\Chat;
use App\Rules\SpamFree;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use OpenAI\Laravel\Facades\OpenAI;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/poem', function () {
    return view('poem');
})->name('poem');

Route::get('/replies', function () {
    return view('create-reply');
})->name('replies');

/* Route::post('/replies', function () {
    $attributes = request()->validate([
        'body' => ['required', 'string']
    ]);

    $response = OpenAI::chat()->create([
        'model' => 'gpt-3.5-turbo-1106',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a forum moderator who always responds using JSON.'],
            [
                'role' => 'user',
                'content' => <<<EOT
                    Please inspect the following text and determine if it is spam.
                    {$attributes['body']}

                    Expected Response Example:

                    {"is_spam": true|false}
                    EOT
            ]
        ],
        'response_format' => ['type' => 'json_object']
    ])->choices[0]->message->content;

    $response = json_decode($response);

    // Trigger failed validation, display a flash message, abort...
    //return $response->is_spam ? 'THIS IS SPAM!': 'VALID POST';
    if ($response->is_spam){
        throw ValidationException::withMessages(['body' => 'Spam was detected.']);
    }
    return 'Redirect wherever is nneded. Post was valid.';

});
 */

 Route::post('/replies', function () {
    request()->validate([
        'body' => [
            'required',
            'string',
            new SpamFree()
        ]
    ]);
    return 'Redirect wherever is needed. Post was valid.';
});

Route::get('/image', function () {
    return view('image', [
        'messages' => session('messages', [])
    ]);
})->name('image');

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
})->name('chat');



Route::get('/roast', function () {
    return view('roast');
})->name('roast');

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


   return redirect('/roast')->with([
        'file' => $file,
        'flash' => 'Roast'
        ]
    );

});